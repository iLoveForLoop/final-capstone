<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is suspended
            if ($user->status === 'suspended') {
                // Check if suspension is temporary and has expired
                if ($user->suspended_until && now()->greaterThan($user->suspended_until)) {
                    // Auto-reactivate user if suspension period has passed
                    $user->update([
                        'status' => 'active',
                        'suspended_until' => null,
                        'suspension_reason' => null,
                    ]);

                    // Log auto-reactivation
                    activity()
                        ->performedOn($user)
                        ->withProperties([
                            'auto_reactivated' => true,
                            'previous_suspension_reason' => $user->suspension_reason,
                        ])
                        ->log('user_auto_reactivated');
                } else {

                    // Debug logging
                    // \Log::debug('Suspension Check', [
                    //     'user_id' => $user->id,
                    //     'suspended_until' => $user->suspended_until,
                    //     'suspended_until_timezone' => $user->suspended_until->timezoneName ?? 'N/A',
                    //     'now' => now(),
                    //     'now_timezone' => now()->timezoneName,
                    //     'is_past' => now()->greaterThan($user->suspended_until),
                    //     'difference' => $user->suspended_until ? now()->diffInMinutes($user->suspended_until, false) . ' minutes' : 'N/A'
                    // ]);

                    // User is still suspended
                    Auth::logout();

                    throw ValidationException::withMessages([
                        'email' => $this->getSuspendedMessage($user),
                    ]);

                    // return redirect()->route('login')
                    //     ->withErrors([
                    //         'email' => $this->getSuspendedMessage($user)
                    //     ]);
                }
            }

            // Check if user is banned
            if ($user->status === 'banned') {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'Your account has been permanently banned. Please contact support for more information.',
                ]);
            }
        }

        return $next($request);
    }

    /**
     * Get appropriate suspension message
     */
    private function getSuspendedMessage($user): string
    {
        if ($user->suspended_until) {
            $suspensionEnd = $user->suspended_until->format('F j, Y \a\t g:i A');
            return "Your account has been suspended until {$suspensionEnd}. Reason: {$user->suspension_reason}";
        }

        return "Your account has been suspended indefinitely. Reason: {$user->suspension_reason}";
    }
}
