<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentMethod;
use App\Models\Abonnement;

class StripeController extends Controller
{
   public function subscribe(Request $request)
{
    try {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
        }

        $paymentMethodId = $request->input('payment_method_id');
        $priceId = $request->input('price_id');

        if (!$paymentMethodId || !$priceId) {
            return response()->json(['error' => 'Informations de paiement manquantes.'], 400);
        }

        // Créer le client Stripe si inexistant
        if (!$user->stripe_id) {
            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'payment_method' => $paymentMethodId,
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            ]);
           $user->stripeCustomerId = $customer->id;
            $user->save();
        } else {
            $customer = \Stripe\Customer::retrieve($user->stripe_id);
        }

        // Créer l’abonnement Stripe
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [[ 'price' => $priceId ]],
            'default_payment_method' => $paymentMethodId,
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        // Vérifie si 3D Secure est requis
        $paymentIntent = $subscription->latest_invoice->payment_intent ?? null;
        if ($paymentIntent && $paymentIntent->status === 'requires_action') {
            return response()->json([
                'requires_action' => true,
                'payment_intent_client_secret' => $paymentIntent->client_secret,
                'subscription_id' => $subscription->id,
            ]);
        }

        // Enregistrer l’abonnement localement
        \App\Models\Abonnement::updateOrCreate(
            ['user_id' => $user->id],
            [
                'tier' => 'Pro',
                'status' => 'Active',
                'stripe_subscription_id' => $subscription->id,
                'payment_method' => $paymentMethodId,
                'start_date' => now(),
                'auto_renew' => true,
            ]
        );

        // Mettre à jour les infos dans le modèle utilisateur si besoin
        $user->subscriptionTier = 'Pro';
        $user->subscriptionStatus = 'Active';
        $user->save();

        return response()->json([
            'success' => true,
            'subscription' => $subscription,
        ]);

    } catch (\Exception $e) {
        // Loggue et retourne une erreur propre
        \Log::error('Erreur abonnement Stripe : ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'error' => 'Erreur lors de la création de l’abonnement.',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function showSubscription(Request $request)
{
    $user = $request->user();

    // À adapter selon ta logique métier et tes modèles
    $currentTier = $user->subscriptionTier ?? 'Free';
    $usage = $user->monthlyTokenUsage(); // depuis ton modèle User


    return view('subscription', [
        'currentTier' => $currentTier,
        'usage' => $usage,
    ]);
}

    public function cancelSubscription(Request $request)
    {
        $user = $request->user();
        $abonnement = Abonnement::where('user_id', $user->id)->first();

        if ($abonnement && $abonnement->stripe_subscription_id) {
            $subscription = Subscription::retrieve($abonnement->stripe_subscription_id);
            $subscription->cancel();

            // Mettre à jour le statut de l'abonnement local
            $abonnement->status = 'Cancelled';
            $abonnement->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'No active subscription found'], 404);
    }
}