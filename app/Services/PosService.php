<?php

namespace App\Services;

use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosKot;
use App\Models\PosKotItem;
use App\Models\PosSession;
use App\Models\OrderStatusHistory;
use App\Models\TableSession;
use App\Models\Item;
use App\Models\Tax;
use App\Models\RestaurantSettings;
use Illuminate\Support\Facades\DB;

class PosService
{
    public function generateOrderNo(): string
    {
        $prefix = 'ORD-' . now()->format('ymd') . '-';
        $last = PosOrder::where('order_no', 'like', $prefix . '%')
            ->orderByDesc('id')->value('order_no');
        $seq = $last ? (intval(substr($last, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateInvoiceNo(): string
    {
        $settings = RestaurantSettings::first();
        $prefix = $settings ? strtoupper(substr(preg_replace('/\s+/', '', $settings->restaurant_name), 0, 3)) : 'INV';
        $prefix .= '-' . now()->format('ymd') . '-';
        $last = PosOrder::where('invoice_no', 'like', $prefix . '%')
            ->orderByDesc('id')->value('invoice_no');
        $seq = $last ? (intval(substr($last, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateKotNo(int $orderId): string
    {
        $count = PosKot::where('pos_order_id', $orderId)->count();
        return 'KOT-' . str_pad($orderId, 5, '0', STR_PAD_LEFT) . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    public function resolveItemTax(Item $item, float $unitPrice, int $qty, ?int $userId = null): array
    {
        $taxPercent = 0;
        $taxType = $item->tax_type ?? 'Exclusive';

        if ($item->tax) {
            $taxQuery = Tax::where(fn($q) => $q->where('description', $item->tax)->orWhere('hsn_code', $item->tax));
            if ($userId) {
                $taxQuery->where('created_by', $userId);
            }
            $tax = $taxQuery->first();
            if ($tax) {
                $taxPercent = (float) $tax->tax_percent;
                if ($taxPercent == 0) {
                    $taxPercent = (float)($tax->cgst + $tax->sgst + $tax->igst + $tax->vat);
                }
            } elseif (is_numeric($item->tax)) {
                $taxPercent = (float) $item->tax;
            }
        }

        $lineBase  = $unitPrice * $qty;
        $taxAmount = 0;

        if ($taxPercent > 0) {
            if ($taxType === 'Inclusive') {
                $taxAmount = $lineBase - ($lineBase / (1 + $taxPercent / 100));
            } else {
                $taxAmount = $lineBase * $taxPercent / 100;
            }
        }

        return [
            'tax_percent' => round($taxPercent, 2),
            'tax_amount'  => round($taxAmount, 2),
            'tax_type'    => $taxType,
        ];
    }

    public function calculateTotals(array $cartItems, float $discount, string $discountType = 'flat', ?int $userId = null): array
    {
        $subtotal     = 0;
        $taxAmount    = 0;
        $taxBreakdown = [];

        foreach ($cartItems as $ci) {
            $item = Item::find($ci['item_id']);
            if (!$item) continue;

            $unitPrice = (float) $item->restaurant_price;
            $qty       = (int) $ci['quantity'];
            $taxData   = $this->resolveItemTax($item, $unitPrice, $qty, $userId);
            $lineBase  = $unitPrice * $qty;

            $subtotal  += $lineBase;
            $taxAmount += $taxData['tax_amount'];

            $taxKey = $taxData['tax_percent'] . '%';
            $taxBreakdown[$taxKey] = round(($taxBreakdown[$taxKey] ?? 0) + $taxData['tax_amount'], 2);
        }

        $discountAmt = $discountType === 'percent'
            ? round($subtotal * $discount / 100, 2)
            : round($discount, 2);

        $total      = round($subtotal + $taxAmount - $discountAmt, 2);
        $rounded    = (float) round($total);
        $roundOff   = round($rounded - $total, 2);
        $netPayable = $rounded;

        return [
            'subtotal'      => round($subtotal, 2),
            'tax_amount'    => round($taxAmount, 2),
            'tax_breakdown' => $taxBreakdown,
            'discount'      => $discountAmt,
            'total'         => $total,
            'round_off'     => $roundOff,
            'net_payable'   => $netPayable,
        ];
    }

    public function createOrder(array $data): PosOrder
    {
        return DB::transaction(function () use ($data) {
            $userId = $data['created_by'] ?? null;
            $totals = $this->calculateTotals(
                $data['cart_items'],
                (float)($data['discount'] ?? 0),
                $data['discount_type'] ?? 'flat',
                $userId
            );

            $paymentStatus = (($data['cash_amt'] ?? 0) + ($data['card_amt'] ?? 0) + ($data['upi_amt'] ?? 0) + ($data['others_amt'] ?? 0)) >= $totals['net_payable'] ? 'Paid' : 'Unpaid';
            $orderStatus = $paymentStatus === 'Paid' ? 'Completed' : 'Pending';

            $order = PosOrder::create([
                'created_by'       => $userId,
                'order_no'         => $this->generateOrderNo(),
                'invoice_no'       => $this->generateInvoiceNo(),
                'session_id'       => $data['session_id'] ?? null,
                'customer_id'      => $data['customer_id'] ?? null,
                'customer_name'    => $data['customer_name'] ?? 'Walk-In Guest',
                'customer_phone'   => $data['customer_phone'] ?? null,
                'table_id'         => $data['table_id'] ?? null,
                'table_label'      => $data['table_label'] ?? null,
                'waiter_id'        => $data['waiter_id'] ?? null,
                'waiter_name'      => $data['waiter_name'] ?? null,
                'order_type'       => $data['order_type'] ?? 'Dine In',
                'covers'           => $data['covers'] ?? 1,
                'notes'            => $data['notes'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_charge'  => $data['delivery_charge'] ?? 0,
                'delivery_notes'   => $data['delivery_notes'] ?? null,
                'subtotal'         => $totals['subtotal'],
                'tax_amount'       => $totals['tax_amount'],
                'tax_breakdown'    => $totals['tax_breakdown'],
                'discount'         => $totals['discount'],
                'discount_type'    => $data['discount_type'] ?? 'flat',
                'total'            => $totals['total'],
                'round_off'        => $totals['round_off'],
                'net_payable'      => $totals['net_payable'],
                'status'           => $orderStatus,
                'payment_status'   => $paymentStatus,
                'bill_pay_type'    => $data['bill_pay_type'] ?? 'Cash',
                'cash_amt'         => $data['cash_amt'] ?? 0,
                'card_ref'         => $data['card_ref'] ?? null,
                'card_amt'         => $data['card_amt'] ?? 0,
                'others_type'      => $data['others_type'] ?? null,
                'others_ref'       => $data['others_ref'] ?? null,
                'others_amt'       => $data['others_amt'] ?? 0,
                'upi_amt'          => $data['upi_amt'] ?? 0,
                'upi_ref'          => $data['upi_ref'] ?? null,
                'payment_note'     => $data['payment_note'] ?? null,
                'last_accessed_by' => $data['last_accessed_by'] ?? 'Administrator',
            ]);

            foreach ($data['cart_items'] as $ci) {
                $item = Item::find($ci['item_id']);
                if (!$item) continue;

                $unitPrice = (float) $item->restaurant_price;
                $qty       = (int) $ci['quantity'];
                $taxData   = $this->resolveItemTax($item, $unitPrice, $qty, $userId);
                $lineBase  = $unitPrice * $qty;

                PosOrderItem::create([
                    'pos_order_id'  => $order->id,
                    'item_id'       => $item->id,
                    'item_name'     => $item->item_name,
                    'item_code'     => $item->code,
                    'category'      => $item->category,
                    'unit_price'    => $unitPrice,
                    'quantity'      => $qty,
                    'discount'      => $ci['item_discount'] ?? 0,
                    'discount_type' => $ci['item_discount_type'] ?? 'flat',
                    'tax_percent'   => $taxData['tax_percent'],
                    'tax_amount'    => $taxData['tax_amount'],
                    'tax_type'      => $taxData['tax_type'],
                    'line_total'    => $lineBase,
                    'notes'         => $ci['notes'] ?? null,
                    'kot_printed'   => false,
                ]);
            }

            if ($order->order_type === 'Dine In' && $order->table_id) {
                TableSession::where('table_id', $order->table_id)
                    ->where('status', 'Open')
                    ->update(['status' => 'Closed', 'closed_at' => now()]);

                if ($orderStatus !== 'Completed') {
                    TableSession::create([
                        'table_id'     => $order->table_id,
                        'pos_order_id' => $order->id,
                        'order_no'     => $order->order_no,
                        'status'       => 'Open',
                        'covers'       => $order->covers,
                        'waiter_id'    => $order->waiter_id,
                        'waiter_name'  => $order->waiter_name,
                        'opened_at'    => now(),
                    ]);
                }
            }

            OrderStatusHistory::create([
                'pos_order_id' => $order->id,
                'from_status'  => null,
                'to_status'    => $orderStatus,
                'changed_by'   => $data['last_accessed_by'] ?? 'Administrator',
            ]);

            if ($order->session_id) {
                $this->updateSessionTotals($order->session_id);
            }

            return $order->load(['items', 'kots']);
        });
    }

    public function generateKot(PosOrder $order, array $kotItems, ?string $notes = null): PosKot
    {
        return DB::transaction(function () use ($order, $kotItems, $notes) {
            $kot = PosKot::create([
                'kot_no'        => $this->generateKotNo($order->id),
                'pos_order_id'  => $order->id,
                'order_no'      => $order->order_no,
                'table_label'   => $order->table_label,
                'order_type'    => $order->order_type,
                'customer_name' => $order->customer_name,
                'waiter_name'   => $order->waiter_name,
                'notes'         => $notes,
                'status'        => 'Pending',
            ]);

            foreach ($kotItems as $ki) {
                PosKotItem::create([
                    'pos_kot_id' => $kot->id,
                    'item_id'    => $ki['item_id'],
                    'item_name'  => $ki['item_name'],
                    'quantity'   => $ki['quantity'],
                    'notes'      => $ki['notes'] ?? null,
                ]);
            }

            $itemIds = collect($kotItems)->pluck('item_id')->toArray();
            PosOrderItem::where('pos_order_id', $order->id)
                ->whereIn('item_id', $itemIds)
                ->update(['kot_printed' => true]);

            return $kot->load('items');
        });
    }

    public function updateOrderStatus(PosOrder $order, string $newStatus, string $by = 'Administrator'): PosOrder
    {
        return DB::transaction(function () use ($order, $newStatus, $by) {
            $oldStatus = $order->status;
            $order->update(['status' => $newStatus]);

            OrderStatusHistory::create([
                'pos_order_id' => $order->id,
                'from_status'  => $oldStatus,
                'to_status'    => $newStatus,
                'changed_by'   => $by,
            ]);

            if (in_array($newStatus, ['Completed', 'Cancelled']) && $order->table_id) {
                TableSession::where('pos_order_id', $order->id)
                    ->where('status', 'Open')
                    ->update(['status' => 'Closed', 'closed_at' => now()]);
            }

            return $order->fresh(['items', 'kots', 'statusHistories']);
        });
    }

    public function updateSessionTotals(int $sessionId): void
    {
        $session = PosSession::find($sessionId);
        if (!$session) return;

        $orders = PosOrder::where('session_id', $sessionId)
            ->whereNotIn('status', ['Cancelled'])
            ->get();

        $session->total_sales  = $orders->sum('net_payable');
        $session->total_cash   = $orders->sum('cash_amt');
        $session->total_card   = $orders->sum('card_amt');
        $session->total_upi    = $orders->sum('upi_amt');
        $session->total_others = $orders->sum('others_amt');
        $session->save();
    }

    public function getActiveSession(?int $userId = null): ?PosSession
    {
        return PosSession::where('status', 'Open')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->first();
    }

    public function openSession(array $data): PosSession
    {
        return PosSession::create([
            'session_code'     => 'POS-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'user_id'          => $data['user_id'] ?? null,
            'counter_name'     => $data['counter_name'] ?? 'Main Counter',
            'branch_name'      => $data['branch_name'] ?? 'Main Branch',
            'opening_balance'  => $data['opening_balance'] ?? 0,
            'status'           => 'Open',
            'opened_at'        => now(),
            'last_accessed_by' => $data['last_accessed_by'] ?? 'Administrator',
        ]);
    }

    public function closeSession(PosSession $session, float $closingBalance): PosSession
    {
        $this->updateSessionTotals($session->id);
        $session->refresh();
        $session->update([
            'status'          => 'Closed',
            'closing_balance' => $closingBalance,
            'closed_at'       => now(),
        ]);
        return $session->fresh();
    }

    public function getTableStatuses(): array
    {
        $openSessions = TableSession::where('status', 'Open')
            ->with('order')
            ->get()
            ->keyBy('table_id');

        return $openSessions->map(fn($s) => [
            'table_id'  => $s->table_id,
            'status'    => 'Occupied',
            'order_no'  => $s->order_no,
            'covers'    => $s->covers,
            'waiter'    => $s->waiter_name,
            'opened_at' => $s->opened_at?->toDateTimeString(),
        ])->values()->toArray();
    }
}
