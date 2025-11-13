<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Services\PasienService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PasienController extends Controller
{
    protected $pasienService;

    public function __construct(PasienService $pasienService)
    {
        $this->pasienService = $pasienService;
    }
    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $pasien = $this->pasienService->login($request->only('username','password'));

    if (!$pasien) {
        return response()->json(['error' => 'Username atau password salah'], 401);
    }

    $customClaims = [
        'sub' => (string) $pasien->id,
        'role' => 'pasien',
        'username' => $pasien->username,
        'nama' => $pasien->nama,
    ];

    $token = auth('pasien')->claims($customClaims)->fromUser($pasien);
    $cookie = cookie('token', $token, 60 * 24);

    return response()->json([
        'message' => 'Login berhasil',
        'pasien' => [
            'no_reg' => $pasien->no_reg,
            'username' => $pasien->username,
            'nama' => $pasien->nama,
        ],
        'token' => $token
    ])->withCookie($cookie);
}
}
