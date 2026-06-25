<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantSettings;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:settings.view')->only('index');
        $this->middleware('permission:settings.manage')->only(['store', 'update']);
    }

    public function index()
    {
        $userId = auth()->id();

        $restaurantSettings = RestaurantSettings::withPermissionCheck()->first();

        // Fetch stripe settings
        $stripeSettings = Setting::where('created_by', $userId)
            ->whereIn('key', [
                'stripe_mode',
                'stripe_key',
                'stripe_secret',
            ])
            ->pluck('value', 'key');

        $data = $restaurantSettings ? $restaurantSettings->toArray() : [];
        $data['settings'] = [
            'stripe_mode' => $stripeSettings['stripe_mode'] ?? null,
            'stripe_key' => $stripeSettings['stripe_key'] ?? null,
            'stripe_secret' => $stripeSettings['stripe_secret'] ?? null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_unit' => 'required|string|max:255',
            'restaurant_name' => 'required|string',
            'gst_no' => 'required|string|max:255',
            'bill_footer_text' => 'required|string',
            'guest_bill' => 'required|string|in:Disabled,Enabled',

            'settings.stripe_mode' => 'nullable|string',
            'settings.stripe_key' => 'nullable|string',
            'settings.stripe_secret' => 'nullable|string',
        ]);

        \DB::beginTransaction();

        try {
            $userId = auth()->id();

            $settings = RestaurantSettings::withPermissionCheck()->first();

            if (! $settings) {
                $settings = new RestaurantSettings;
                $settings->created_by = $userId;
            }

            // Restaurant settings update
            $settings->fill([
                'business_unit' => $request->business_unit,
                'restaurant_name' => $request->restaurant_name,
                'gst_no' => $request->gst_no,
                'bill_footer_text' => $request->bill_footer_text,
                'guest_bill' => $request->guest_bill,
                'last_accessed_by' => auth()->user()->name,
            ])->save();

            // Save stripe settings in settings table
            if ($request->has('settings')) {

                foreach ($request->settings as $key => $value) {

                    Setting::updateOrCreate(
                        [
                            'key' => $key,
                            'created_by' => $userId,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Restaurant settings updated successfully',
                'data' => $settings->fresh(),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
