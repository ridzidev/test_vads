<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ServiceDeskController extends Controller
{
    /**
     * Show the service desk dashboard
     */
    public function dashboard()
    {
        try {
            // Fetch real-time data from the Node.js realtime server
            $realtimeServerUrl = env('REALTIME_SERVER_URL', 'http://localhost:3000');
            $response = Http::timeout(5)->get($realtimeServerUrl . '/api/dashboard-data');

            if ($response->successful()) {
                $apiData = $response->json();

                if ($apiData['success']) {
                    $data = $apiData['data'];

                    // For completed today and average rating, we might need to fetch from database
                    // For now, set defaults since realtime server doesn't track these yet
                    $data['completedToday'] = $data['completedToday'] ?? 0;
                    $data['averageRating'] = $data['averageRating'] ?? null;

                    return view('service-desk-dashboard', $data);
                }
            }

            // Fallback to empty data if API call fails
            $data = [
                'queueCount' => 0,
                'activeChats' => 0,
                'completedToday' => 0,
                'averageRating' => null,
                'queueList' => [],
                'activeSessions' => [],
            ];

            return view('service-desk-dashboard', $data);

        } catch (\Exception $e) {
            // Log the error and show empty dashboard
            \Log::error('Failed to fetch dashboard data from realtime server: ' . $e->getMessage());

            $data = [
                'queueCount' => 0,
                'activeChats' => 0,
                'completedToday' => 0,
                'averageRating' => null,
                'queueList' => [],
                'activeSessions' => [],
            ];

            return view('service-desk-dashboard', $data);
        }
    }
}
