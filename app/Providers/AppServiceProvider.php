<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Rate Limiter for general API requests
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate Limiter for authentication & login attempts (friendly for schools/labs sharing one IP)
        RateLimiter::for('auth', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email', '')));
            $ip = $request->ip();

            if (!empty($email)) {
                return [
                    Limit::perMinute(10)->by('auth_' . $email . '|' . $ip),
                    Limit::perMinute(60)->by('auth_ip_' . $ip),
                ];
            }

            return Limit::perMinute(20)->by('auth_ip_' . $ip);
        });

        // Rate Limiter for OTP sending/resending (per-account limit + generous IP limit)
        RateLimiter::for('otp', function (Request $request) {
            $email = strtolower(trim((string) (
                $request->input('email')
                ?: ($request->hasSession() ? $request->session()->get('register_details.email', $request->session()->get('reset_email', '')) : '')
            )));
            $ip = $request->ip();

            if (!empty($email)) {
                return [
                    Limit::perMinute(3)->by('otp_' . $email),
                    Limit::perMinute(30)->by('otp_ip_' . $ip),
                ];
            }

            return Limit::perMinute(5)->by('otp_ip_' . $ip);
        });
    }
}
