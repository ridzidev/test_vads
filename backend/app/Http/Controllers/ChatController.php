<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Store customer registration data
     * POST /api/register
     */
    public function registerCustomer(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|regex:/^[0-9\-\+\(\)\s]+$/',
            ]);

            // Store to session or temp database
            // For now, return success response
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'sessionId' => 'session_' . time(),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get master customer data from randomuser.me API
     * GET /api/customers
     */
    public function getCustomers(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $results = $request->query('results', 10);

            $response = file_get_contents("https://randomuser.me/api?results=$results&page=$page");
            $data = json_decode($response, true);

            if (!$data || !isset($data['results'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch customer data',
                ], 500);
            }

            // Flatten customer data
            $customers = array_map(function($user) {
                return [
                    'name' => $user['name']['title'] . ' ' . $user['name']['first'] . ' ' . $user['name']['last'],
                    'email' => $user['email'],
                    'login' => [
                        'uuid' => $user['login']['uuid'],
                        'username' => $user['login']['username'],
                        'password' => $user['login']['password'],
                    ],
                    'phone' => $user['phone'],
                    'cell' => $user['cell'],
                    'picture' => $user['picture']['medium'],
                    'picture_large' => $user['picture']['large'],
                    'gender' => $user['gender'],
                    'nat' => $user['nat'],
                    'location_city' => $user['location']['city'],
                    'location_country' => $user['location']['country'],
                    'dob_age' => $user['dob']['age'],
                ];
            }, $data['results']);

            return response()->json([
                'success' => true,
                'data' => $customers,
                'meta' => [
                    'page' => $page,
                    'results_count' => count($customers),
                    'seed' => $data['info']['seed'] ?? null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customers: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get chat history for a session
     * GET /api/chat-history/{sessionId}
     */
    public function getChatHistory($sessionId)
    {
        try {
            // In a real app, fetch from database
            // For now, return empty history
            return response()->json([
                'success' => true,
                'data' => [
                    'sessionId' => $sessionId,
                    'messages' => [],
                    'status' => 'active'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chat history: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save chat message to database
     * POST /api/chat-message
     */
    public function saveChatMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'sessionId' => 'required|string',
                'customerId' => 'required|string',
                'message' => 'required|string|max:1000',
                'type' => 'required|in:customer,sd,bot',
                'sender' => 'required|string',
            ]);

            // Save to database (would be in real app)
            // For now, just return success

            return response()->json([
                'success' => true,
                'message' => 'Message saved',
                'data' => [
                    'id' => uniqid(),
                    'timestamp' => now()->toIso8601String(),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving message: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get SD agents list
     * GET /api/sd-agents
     */
    public function getSDAgents()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    // This will be populated by Socket.io server
                    // For now, return empty
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching agents: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Health check endpoint
     * GET /api/health
     */
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'app' => 'Test VADS - Live Chat',
            'version' => '1.0.0',
        ], 200);
    }
}
