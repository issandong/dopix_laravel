@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
@if (session('success'))
    <div 
        x-data="{ show: true }" 
        x-init="setTimeout(() => show = false, 4000)" 
        x-show="show"
        x-transition
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center"
    >
        {{ session('success') }}
    </div>
@endif
<div class="container ">
  <div class="welcome">Bienvenue, {{ Auth::user()->name ?? 'Jean' }} !</div>
  <a class="cta-btn" href="{{ route('verification') }}">Vérifier un médicament</a>
  <h2>Historique récent</h2>
 <!-- Table pour les grands écrans -->
<div class="hidden sm:block">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-100 text-gray-800">
      <tr>
        <th class="px-4 py-2 text-left font-semibold">Médicament</th>
        <th class="px-4 py-2 text-left font-semibold">Résultat</th>
        <th class="px-4 py-2 text-left font-semibold">Date</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($verifications as $verification)
      <tr class="bg-white border-b">
        <td class="px-4 py-2">{{ $verification->medicine_name }}</td>
        <td class="px-4 py-2">
          <span class="status @if ($verification->overall_status === 'Autorisé') ok @elseif ($verification->overall_status === 'Interdit') bad @else warn @endif">
            {{ $verification->overall_status }}
          </span>
        </td>
        <td class="px-4 py-2">{{ $verification->check_date->format('d/m/Y') }}</td>
      </tr>
      @empty
      <tr><td colspan="3" class="text-center py-4">Aucune vérification</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Cartes pour les petits écrans -->
<div class="sm:hidden space-y-4">
  @forelse ($verifications as $verification)
  <div class="bg-white rounded-xl shadow p-4 flex flex-col space-y-2 text-sm border border-gray-200">
    <div class="flex justify-between">
      <div>
        <div class="text-gray-500 font-semibold">Médicament</div>
        <div>{{ $verification->medicine_name }}</div>
      </div>
      <div>
        <div class="text-gray-500 font-semibold">Résultat</div>
        <div>
          <span class="status @if ($verification->overall_status === 'Autorisé') ok @elseif ($verification->overall_status === 'Interdit') bad @else warn @endif">
            {{ $verification->overall_status }}
          </span>
        </div>
      </div>
    </div>
    <div>
      <div class="text-gray-500 font-semibold">Date</div>
      <div>{{ $verification->check_date->format('d/m/Y') }}</div>
    </div>
  </div>
  @empty
  <div class="text-center text-gray-500">Aucune vérification</div>
  @endforelse
</div>

  <a class="history-link" href="{{ route('historique') }}">Voir tout l’historique →</a>

 <div class="bg-white p-4 rounded shadow mb-6">
    <h2 class="text-lg font-bold mb-2">Quota de vérifications</h2>

    @if(auth()->user()->isPro())
        <p class="text-green-600">🔓 Forfait Pro : vérifications illimitées</p>
    @else
        <p class="mb-2">
            <strong>{{ $usedThisMonth }}</strong> / 3 vérifications utilisées ({{ $progressPercent }}%)
        </p>

        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
            <div class="h-4 rounded-full bg-blue-500 transition-all duration-300 ease-in-out"
                 style="width: {{ $progressPercent }}%"></div>
        </div>

        @if($remaining == 0)
            <p class="text-red-500 mt-2">⚠️ Quota mensuel atteint. Passez au plan Pro pour continuer.</p>

            <a href="{{ route('subscription.show') }}"
               class="inline-block mt-3 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                🚀 Passer au plan Pro
            </a>
        @endif
    @endif
</div>



</div>
@endsection