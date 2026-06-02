<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantSettings;
use Illuminate\Http\Request;

class RestaurantSettingsController extends Controller
{
    public function index()
    {
        $settings = RestaurantSettings::first();
        
        if (!$settings) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_unit' => 'required|string|max:255',
            'restaurant_name' => 'required|string',
            'gst_no' => 'required|string|max:255',
            'bill_footer_text' => 'required|string',
            'guest_bill' => 'required|string|in:Disabled,Enabled',
        ]);

        $settings = RestaurantSettings::first();

        if (!$settings) {
            $settings = new RestaurantSettings();
        }

        $settings->business_unit = $request->business_unit;
        $settings->restaurant_name = $request->restaurant_name;
        $settings->gst_no = $request->gst_no;
        $settings->bill_footer_text = $request->bill_footer_text;
        $settings->guest_bill = $request->guest_bill;
        $settings->last_accessed_by = 'Administrator';
        $settings->save();
        $settings->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Restaurant settings saved successfully',
            'data' => $settings,
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_unit' => 'required|string|max:255',
            'restaurant_name' => 'required|string',
            'gst_no' => 'required|string|max:255',
            'bill_footer_text' => 'required|string',
            'guest_bill' => 'required|string|in:Disabled,Enabled',
        ]);

        $settings = RestaurantSettings::first();

        if (!$settings) {
            $settings = new RestaurantSettings();
        }

        $settings->business_unit = $request->business_unit;
        $settings->restaurant_name = $request->restaurant_name;
        $settings->gst_no = $request->gst_no;
        $settings->bill_footer_text = $request->bill_footer_text;
        $settings->guest_bill = $request->guest_bill;
        $settings->last_accessed_by = 'Administrator';
        $settings->save();
        $settings->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Restaurant settings updated successfully',
            'data' => $settings,
        ], 200);
    }
}
