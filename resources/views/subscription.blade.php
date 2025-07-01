@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10 bg-white rounded-2xl shadow-md p-8">
    <h2 class="text-2xl font-bold text-center mb-2">Choisissez votre offre</h2>
    <p class="text-center mb-6 text-gray-600">Sélectionnez le plan qui vous convient</p>

    <div class="flex flex-col md:flex-row gap-3 mb-6">
        <!-- Plan Gratuit -->
        <div class="flex-1 border border-blue-200 rounded-xl p-4 bg-[#F7FAFD]">
            <h3 class="font-bold text-lg mb-1">Free Plan
                @if($currentTier === 'Free')
                    <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-700 rounded">ACTUEL</span>
                @endif
            </h3>
            <div class="text-blue-700 text-xl font-semibold mb-2">0,00 €</div>
            <ul class="text-gray-700 text-sm mb-2">
                <li>✓ 3 vérifications AI/mois</li>
                <li>✓ OCR OpenAI</li>
                <li>✗ Pas de téléchargements en lot</li>
            </ul>
        </div>

        <!-- Plan Pro -->
        <div class="flex-1 border border-blue-300 rounded-xl p-4 bg-blue-50">
            <h3 class="font-bold text-lg mb-1">Pro Plan
                @if($currentTier === 'Pro')
                    <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-700 rounded">ACTUEL</span>
                @endif
            </h3>
            <div class="text-blue-700 text-xl font-semibold mb-2">9,99 € <span class="text-base font-normal">/mois</span></div>
            <ul class="text-gray-700 text-sm mb-2">
                <li>✓ Vérifications illimitées</li>
                <li>✓ Historique complet</li>
                <li>✓ Export des données</li>
                <li>✓ OCR prioritaire</li>
            </ul>
            @if($currentTier !== 'Pro')
                <button id="upgrade-btn" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded w-full font-bold mt-1">🚀 Passer au Pro</button>
            @endif
        </div>
    </div>

    <div class="mb-4">
        <strong>Utilisation actuelle</strong>
        <div class="w-full bg-gray-100 rounded h-2 my-2">
            <div class="bg-blue-500 h-2 rounded" style="width:{{ min($usage / 3 * 100, 100) }}%"></div>
        </div>
        <span class="text-sm text-gray-600">{{ $usage }} sur 3 vérifications utilisées ce mois</span>
    </div>

    <!-- Stripe Modal -->
    <div id="stripe-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-[1000] p-4">
        <div class="bg-white p-6 rounded shadow max-w-sm w-full">
            <form id="subscription-form">
                <div id="card-element" class="mb-4"></div>
                <button type="submit" id="pay-btn" class="bg-blue-600 text-white px-4 py-2 rounded w-full font-bold">Payer & Passer au Pro</button>
                <div id="card-errors" class="text-red-500 mt-2 text-sm"></div>
            </form>
            <button id="close-modal" class="mt-4 text-blue-600 underline block w-full text-center">Annuler</button>
        </div>
    </div>

</div>

<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    const STRIPE_PUBLIC_KEY = "{{ config('services.stripe.key') }}";
    const PRICE_ID = "{{ config('services.stripe.price_id') }}";

    const stripe = Stripe(STRIPE_PUBLIC_KEY);
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    document.getElementById('upgrade-btn')?.addEventListener('click', () => {
        document.getElementById('stripe-modal').style.display = 'flex';
    });

    document.getElementById('close-modal')?.addEventListener('click', () => {
        document.getElementById('stripe-modal').style.display = 'none';
    });

    document.getElementById('subscription-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: card,
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            return;
        }

        // Envoyer au backend
        const response = await fetch('/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_method_id: paymentMethod.id,
                price_id: PRICE_ID
            })
        });

        const result = await response.json();

        if (result.requires_action) {
            const {error: confirmError} = await stripe.confirmCardPayment(result.payment_intent_client_secret);
            if (confirmError) {
                document.getElementById('card-errors').textContent = confirmError.message;
                return;
            }
            // Redirection ou rechargement si succès
            window.location.reload();
        } else if (result.error) {
            document.getElementById('card-errors').textContent = result.error;
        } else {
            window.location.reload();
        }
    });
</script>
@endsection
