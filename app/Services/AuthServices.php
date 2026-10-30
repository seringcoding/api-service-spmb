<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Exception;

class AuthServices
{
    // Proses login
    public function login(array $credentials): array {
        $user = User::where('email', $credentials['email'])->first();

        // Cek user dan password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new Exception('Email atau password salah.');
        }

        // Membuat kondisi jika email belum terverifikasi maka lempar exception
        if(!$user->hasVerifiedEmail()) {
            throw new Exception('Akun anda belum diverifikasi.');
        }
        // Simpan token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    // Proses Registrasi
    public function register(array $data): User {
        // Membuat user baru
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        // Kirim email notifikasi dengan data user
        event(new Registered($user));

        // Kembalikan respon ke user
        return $user;
    }

    // Proses verifikasi email
    public function verifyEmail(int $id, string $hash, ?int $expires = null): string {
        // Cari user
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new Exception('Link verifikasi tidak valid.', 400);
        }

        if ($user->hasVerifiedEmail()) {
            return 'already_verified';
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return 'verified_success';
    }

    // Kirim ulang email verifikasi
    public function resendVerification(string $email): string {
        // Mencari email
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new Exception('Akun tidak ditemukan', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return 'Email sudah terverifikasi';
        }

        $user->sendEmailVerificationNotification();

        return 'resent_success';
    }

    // Kirim link lupa password
    public function sendResetLink(string $email): bool {
        $status = Password::sendResetLink(['email' => $email]);
        return $status === Password::RESET_LINK_SENT;
    }

    // Reset password user
    public function resetPassword(array $data): bool {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET;
    }
}
