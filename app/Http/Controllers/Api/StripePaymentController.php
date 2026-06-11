<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSession(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'order_type' => 'required|string',
        ]);

        $frontendUrl = config('app.frontend_url', 'http://127.0.0.1:8000');

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) ($request->amount * 100),
                    'product_data' => [
                        'name' => 'DineFlow Order ('.$request->order_type.')',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'invoice_creation' => ['enabled' => true],
            'success_url' => $frontendUrl.'/sales-management?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $frontendUrl.'/pos-system?stripe_cancel=1',
        ]);

        return response()->json(['success' => true, 'checkout_url' => $session->url]);
    }

    public function paymentStatus(Request $request)
    {
        $request->validate(['session_id' => 'required|string']);

        try {
            $session = StripeSession::retrieve([
                'id' => $request->session_id,
                'expand' => ['payment_intent', 'invoice'],
            ]);

            if ($session->payment_status === 'paid') {
                return response()->json([
                    'success' => true,
                    'payment_intent_id' => $session->payment_intent->id ?? $session->payment_intent,
                    'receipt_url'       => $session->invoice->invoice_pdf ?? null,
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Payment not completed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
