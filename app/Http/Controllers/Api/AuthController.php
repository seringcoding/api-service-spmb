<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

use App\Http\Controllers\Controller;
use App\Services\AuthServices;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AuthController extends Controller
{
    protected AuthServices $authServices;

    public function __construct(AuthServices $authServices) {
        $this->authServices = $authServices;
    }

    // Method untuk login
    public function login(Request $request) {
        // Membuat validasi
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        try {
            // Ambil email dan password untuk dicocokan melalui fungsi login dari Auth Services
            $result = $this->authServices->login($request->only('email', 'password'));

            // Kirim response success
            return response()->json([
                'status' => 'success',
                'message' => 'Login Berhasil',
                'data' => $result,
            ], 200);
        } catch(Exception $e) {
            // Kirim response failed
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], $e->getCode()?: 401);
        }
    }

    // Method untuk registrasi user baru
    public function register(Request $request) {
        // Buat Validasi input
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            $user = $this->authServices->register($validator);

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil, Silahkan cek email anda untuk melakukan verifikasi akun.!',
                'data' => ['user' => $user]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ], 400);
        }
    }

    // Method untuk verifikasi email user
    public function verify(Request $request, $id, $hash) {
        try {
            $expires = $request->query('expires');

            $status = $this->authServices->verifyEmail((int)$id, (string)$hash, (int)$expires);

            if ($status === 'already_verified') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Email sudah di verifikasi sebelumnya'
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Verifikasi email berhasil ! Silahkan login dan lanjutkan pendaftaran.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun tidak ditemukan.',
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ], 404);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ], 400);

        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ], 400);
        }
    }

    // Method resend email verifikasi
    public function resendVerification(Request $request) {
        // Validasi input
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $status = $this->authServices->resendVerification($request->email);

            if ($status === 'already_verified') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Email sudah di verifikasi sebelumnya'
                ], 201);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Email verifikasi sudah dikirimkan ulang. Periksa kembali folder masuk atau spam'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ], 400);
        }
    }

    // Method request lupa password
    public function forgotPassword(Request $request) {
        // Membuat validasi input
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $sent = $this->authServices->sendResetLink($request->email);

            if ($sent) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Link reset password telah dikirim ke email anda.'
                ], 200);
            }

            return response()->json([
                'status' => 'failed',
                'message' => 'email tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            // Kembalikan response jika gagal
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal mengirim link reset password.',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ], 400);
        }
    }

    // Ambil token, email dan password baru
    public function resetPassword(Request $request) {
        // Membuat validasi input
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            $reset = $this->authServices->resetPassword(
                $request->only('token', 'email', 'password', 'password_confirmed')
            );

            if ($reset) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password berhasil diubah.'
                ], 200);
            }
        } catch (\Exception $e) {
            // Jika token tidak cocok, email salah dan link kadaluarsa kembalikan response failed
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ], 400);
        }
    }
}
