<?php

namespace App\Http\Controllers;
use App\Models\Abonnement;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Customer;

use Illuminate\Http\Request;

class AbonnementController extends Controller
{
    public function subscribe(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $user = auth()->user();

    // Créer un client Stripe s’il n’existe pas
    $customer = Customer::create([
        'email' => $user->email,
        'name' => $user->name,
    ]);

    // Créer un abonnement Stripe
    $subscription = Abonnement::create([
        'customer' => $customer->id,
        'items' => [['price' => 'price_xxxxxx']],
        'payment_behavior' => 'default_incomplete',
        'expand' => ['latest_invoice.payment_intent'],
    ]);

    // Créer une subscription locale
    $user->subscription()->create([
        'id' => Str::uuid(),
        'tier' => 'Pro',
        'status' => 'PendingPayment',
        'stripe_subscription_id' => $subscription->id,
        'start_date' => now(),
        'auto_renew' => true,
        'payment_method' => 'card',
    ]);

    return response()->json([
        'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
    ]);
}
}
