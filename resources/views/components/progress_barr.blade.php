@php
    $steps = [
        1 => ['label' => 'Extraction', 'icon' => '1'],
        2 => ['label' => 'Vérification', 'icon' => '2'],
        3 => ['label' => 'Résultat', 'icon' => '3'],
    ];
@endphp
<div class="progress-container" style="margin-top: 1rem; margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: center;">
        @foreach ($steps as $i => $stepData)
            <div style="text-align: center; width: 100px;">
                <div style="
                    margin: 0 auto;
                    width: 36px; height: 36px; border-radius: 50%;
                    background: {{ ($step ?? 1) >= $i ? '#2196F3' : '#e0e0e0' }};
                    color: {{ ($step ?? 1) >= $i ? '#fff' : '#888' }};
                    display: flex; align-items: center; justify-content: center;
                    font-weight: bold; font-size: 1.2rem;
                    transition: background 0.3s;
                ">
                    {{ $stepData['icon'] }}
                </div>
                <div style="font-size: 0.95rem; margin-top: 6px; color: {{ ($step ?? 1) >= $i ? '#2196F3' : '#888' }};">
                    {{ $stepData['label'] }}
                </div>
            </div>
            @if ($i < count($steps))
                <div style="
                    flex: 1; height: 4px;
                    background: {{ ($step ?? 1) > $i ? '#2196F3' : '#e0e0e0' }};
                    transition: background 0.3s;
                "></div>
            @endif
        @endforeach
    </div>
</div>