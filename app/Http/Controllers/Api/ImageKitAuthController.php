<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageKitAuthController extends Controller
{
    /**
     * Generate ImageKit authentication signature for client-side upload.
     */
    public function getAuthenticationParameters()
    {
        $privateKey = env('IMAGEKIT_PRIVATE_KEY');

        // Provide a random string as token
        $token = bin2hex(random_bytes(16));
        
        // Expiration timestamp in seconds (e.g., 30 minutes from now)
        $expire = time() + 1800;

        // Generate the HMAC SHA1 signature
        $signature = hash_hmac('sha1', $token . $expire, $privateKey);

        return response()->json([
            'token' => $token,
            'expire' => $expire,
            'signature' => $signature,
        ]);
    }
}
