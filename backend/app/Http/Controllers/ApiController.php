<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    // Secret Key untuk encode JWT (Simulasi)
    private $jwt_secret = 'VADS_SKILL_TEST_SECRET';

    /**
     * Soal 4: Generate Token
     */
    public function getToken(Request $request)
    {
        $name = $request->input('name');
        $dateRequest = $request->input('date_request');

        // Setup Payload Token (Exp 1 jam)
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        
        $payload = [
            'iss' => 'laravel-api',
            'sub' => $name,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        ];

        // Generate Token
        $token = $this->generateJWT($payload);

        return response()->json([
            'name' => $name,
            'date_request' => $dateRequest,
            'token' => $token,
            'exp' => (string)$expirationTime 
        ]);
    }

    /**
     * Soal 5: Get Data Transaction
     */
    public function getData(Request $request)
    {
        // 1. Validasi Secret Key (Header)
        $clientSecret = $request->header('secretKey');
        if ($clientSecret !== 'Qw3rty09!@#') {
            return response()->json(['error' => 'Invalid Secret Key'], 403);
        }

        // 2. Validasi Bearer Token (Header)
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
        
        $token = $matches[1];
        if (!$this->validateJWT($token)) {
            return response()->json(['error' => 'Invalid or Expired Token'], 401);
        }

        // 3. Logic Query Database (Step 1)
        $nameCustomer = $request->input('name_customers');

        // Variabel Diskon
        $d1 = 0.02;
        $d2 = 0.035;
        $d3 = 0.05;

        $data = DB::table('master_items as m')
            ->join('user as u', 'm.id_name', '=', 'u.id')
            ->where('u.name', $nameCustomer)
            ->select(
                'u.name as name_customers',
                'm.items',
                // Raw SQL Case When
                DB::raw("CASE 
                    WHEN m.estimate_price < 50000 THEN $d1
                    WHEN m.estimate_price >= 50000 AND m.estimate_price <= 1500000 THEN $d2
                    WHEN m.estimate_price > 1500000 THEN $d3
                END as discount_val"),
                DB::raw("(m.estimate_price - (m.estimate_price * CASE 
                    WHEN m.estimate_price < 50000 THEN $d1
                    WHEN m.estimate_price >= 50000 AND m.estimate_price <= 1500000 THEN $d2
                    WHEN m.estimate_price > 1500000 THEN $d3
                END)) as fix_price_val")
            )
            ->get();

        // 4. Formatting Result (Sesuai Screenshot PDF Hal 9)
        $result = $data->map(function($item) {
            return [
                'name_customers' => $item->name_customers,
                'items' => $item->items,
                // Typo 'dicount' disengaja sesuai screenshot PDF
                // Ubah format "0.02" jadi "0,02" (koma)
                'dicount' => str_replace('.', ',', (string)(0 + $item->discount_val)), 
                // Fix price bulat tanpa desimal
                'fix_price' => (string)round($item->fix_price_val) 
            ];
        });

        return response()->json([
            'result' => $result
        ]);
    }

    // --- Helper Manual JWT ---

    private function generateJWT($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->jwt_secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    private function validateJWT($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        $header = $parts[0];
        $payload = $parts[1];
        $signatureProvided = $parts[2];
        $signature = hash_hmac('sha256', $header . "." . $payload, $this->jwt_secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        if ($base64UrlSignature !== $signatureProvided) return false;
        
        $payloadData = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payload)), true);
        // Cek Expired
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }
        return true;
    }
}