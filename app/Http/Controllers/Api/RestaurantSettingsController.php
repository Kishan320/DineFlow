<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantSettings;
use Illuminate\Http\Request;

class RestaurantSettingsController extends Controller
{
    public function index()
    {
        $settings = RestaurantSettings::forUser(auth()->id())->first();
        return response()->json(['success' => true, 'data' => $settings]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_unit'    => 'required|string|max:255',
            'restaurant_name'  => 'required|string',
            'gst_no'           => 'required|string|max:255',
            'bill_footer_text' => 'required|string',
            'guest_bill'       => 'required|string|in:Disabled,Enabled',
        ]);

        $userId   = auth()->id();
        $settings = RestaurantSettings::forUser($userId)->first()
            ?? new RestaurantSettings(['created_by' => $userId]);

        $settings->fill([
            'business_unit'    => $request->business_unit,
            'restaurant_name'  => $request->restaurant_name,
            'gst_no'           => $request->gst_no,
            'bill_footer_text' => $request->bill_footer_text,
            'guest_bill'       => $request->guest_bill,
            'last_accessed_by' => auth()->user()->name,
        ])->save();

        return response()->json(['success' => true, 'message' => 'Restaurant settings saved successfully', 'data' => $settings->fresh()], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_unit'    => 'required|string|max:255',
            'restaurant_name'  => 'required|string',
            'gst_no'           => 'required|string|max:255',
            'bill_footer_text' => 'required|string',
            'guest_bill'       => 'required|string|in:Disabled,Enabled',
        ]);

        $userId   = auth()->id();
        $settings = RestaurantSettings::forUser($userId)->first()
            ?? new RestaurantSettings(['created_by' => $userId]);

        $settings->fill([
            'business_unit'    => $request->business_unit,
            'restaurant_name'  => $request->restaurant_name,
            'gst_no'           => $request->gst_no,
            'bill_footer_text' => $request->bill_footer_text,
            'guest_bill'       => $request->guest_bill,
            'last_accessed_by' => auth()->user()->name,
        ])->save();

        return response()->json(['success' => true, 'message' => 'Restaurant settings updated successfully', 'data' => $settings->fresh()]);
    }
}
