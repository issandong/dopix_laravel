@extends('layouts.app')

@section('content')
    <div class="mt-8 mb-6">
        <x-progress_barr :step="$step ?? 3" />
    </div>

    <div class="container" style="max-width: 540px; margin: 0 auto;">
        {{-- Résultat final en haut, design alert stylisé --}}
        @if(isset($contains_prohibited) && $contains_prohibited)
            <div style="background: #ffd6d6; border-radius: 18px; padding: 22px 18px; margin-bottom: 28px; text-align: left;">
                <div style="font-size: 2rem; color: #b71c1c; font-weight: bold;">
                    ❌Ne prenez pas ce médicament.
                </div>
                <div style="color: #b71c1c; margin-top: 6px; font-size: 1.18rem;">
                    Il contient une substance interdite.
                </div>
            </div>
        @else
            <div style="background: #d6ffd6; border-radius: 18px; padding: 22px 18px; margin-bottom: 28px; text-align: left;">
                <div style="font-size: 2rem; color: #2e7d32; font-weight: bold;">
                    ✅ Ce médicament est autorisé.
                </div>
                <div style="color: #2e7d32; margin-top: 6px; font-size: 1.18rem;">
                   Aucune substance interdite détectée.
                </div>
            </div>
        @endif

        {{-- Titre section --}}
        <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.5rem; color: #232b3d;">Contrôle de conformité de l'AMA</div>
        <div style="color: #7b8591; font-size: 1.15rem; margin-bottom: 1.2rem;">{{ $medicine_name }}</div>

        {{-- Liste des ingrédients --}}
        @if(isset($wada_ingredients) && is_array($wada_ingredients) && count($wada_ingredients))
            <div style="display: flex; flex-direction: column; gap: 18px; margin-bottom: 30px;">
                @foreach($wada_ingredients as $ingredient)
                    @php
                        $status = strtolower($ingredient['status'] ?? '');
                        $style = match ($status) {
                            'prohibited' => 'background: #ffd6d6; color: #b71c1c;',
                            'caution'    => 'background: #fff9d6; color: #b09500;',
                            'allowed'    => 'background: #d6ffd6; color: #2e7d32;',
                            default      => 'background: #f0f0f0; color: #232b3d;',
                        };
                        $emoji = match ($status) {
                            'prohibited' => '❌',
                            'caution'    => '⚠️',
                            'allowed'    => '✅',
                            default      => '❓',
                        };
                        $label = match ($status) {
                            'prohibited' => 'Interdit',
                            'caution'    => 'prudence',
                            'allowed'    => 'Autorisé',
                            default      => 'Inconnu',
                        };
                    @endphp
                    <div style="border-radius: 15px; padding: 18px 15px; {{ $style }} font-size: 1.15rem;">
                        <div style="font-size: 1.2rem; font-weight: bold;">
                            {{ $emoji }} {{ $ingredient['name'] ?? 'Unknown' }}
                        </div>
                        <div style="font-size: 1rem; margin-top: 5px;">
                            <span>{{ $label }}</span>
                            @if($status === 'prohibited' && !empty($ingredient['detection_window']))
                                <span style="display: block; font-size: 0.98rem; color: #b71c1c; margin-top: 5px;">🕒 Detectable: {{ $ingredient['detection_window'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Formulaire d'enregistrement --}}
            <form action="{{ route('verifications.store') }}" method="POST" style="margin-top: 18px;">
                @csrf
                <input type="hidden" name="medicine_name" value="{{ $medicine_name }}">
                <input type="hidden" name="ingredients" value="{{ json_encode($wada_ingredients) }}">
                <input type="hidden" name="overall_status" value="{{ $contains_prohibited ? 'prohibited' : 'allowed' }}">
                <input type="hidden" name="source" value="{{ $source ?? 'text' }}">
                <input type="hidden" name="check_date" value="{{ now() }}">

                <button type="submit" style="display: block; margin: 0 auto; background: #088be6; color: white; border: none; border-radius: 24px; padding: 17px 0; width: 100%; font-size: 1.35rem; font-weight: bold; box-shadow: 0 2px 8px #0001; transition: background 0.2s;">
                    Enrigistrer 
                </button>
            </form>
        @else
            <div class="alert alert-warning mt-3">No WADA results to display.</div>
        @endif
        @if(auth()->user()->isPro())
    <span>✅ Vérifications illimitées (Pro)</span>
@else
    <span>🧪 {{ auth()->user()->remainingFreeChecks() }} vérifications gratuites restantes ce mois</span>
@endif

    </div>
@endsection