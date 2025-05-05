<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings(Request $request)
    {
        $setting = \DB::table('settings')->first();

        if (! $setting) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => ! empty($setting->api_key)
        ]);
    }

    public function apiSave(Request $request)
    {
        // 1) Validate presence & basic format
        $request->validate([
            'api_key' => ['required', 'string', 'min:20', 'max:100'],
        ]);


        $apiKey = $request->input('api_key');

        // 2) Test the key with a minimal NewsAPI call
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
            ])->get('https://newsapi.org/v2/top-headlines', [
                'pageSize' => 1,
                'country'  => 'us',
            ]);
        } catch (\Exception $e) {
            // Network or DNS failure
            return response()->json([
                'error' => 'Unable to reach NewsAPI service. Please try again later.'
            ], 500);
        }

        if ($response->status() === 401) {
            // Unauthorized = bad key
            return response()->json([
                'error' => 'Invalid API key. Please check and try again.'
            ], 401);
        }
        // dd($request->all());


        if ($response->failed()) {
            // Other API error (e.g. rate limit)
            return response()->json([
                'error' => 'NewsAPI error: ' . $response->json('message', 'Unknown error or rate limit has been reached.')
            ], $response->status());
        }

        // 3) No errors → persist the key
        // If you only ever store one setting row, you can truncate first:
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            'api_key'    => $apiKey,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4) Return success
        return response()->json([
            'message' => 'Your API key has been successfully saved.'
        ]);
    }

    public function apiDelete()
    {
        DB::table('settings')->truncate();
        return response()->json(['message'=>'API key removed']);
    }
}
