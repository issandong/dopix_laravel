<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\VerificationMedicament;

class CheckSubscriptionLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->subscriptionTier === 'Pro') {
            return $next($request); // ✅ accès illimité
        }

        // Compter les vérifications ce mois-ci
        $thisMonth = Carbon::now()->startOfMonth();

        $verificationCount = VerificationMedicament::where('user_id', $user->id)
            ->where('created_at', '>=', $thisMonth)
            ->count();

        if ($verificationCount >= 3) {
            return redirect()->route('dashboard')
                ->with('error', 'Limite mensuelle atteinte pour le plan gratuit (3 vérifications).');
        }

        return $next($request);
    }
}
