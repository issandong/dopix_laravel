@extends('layouts.app')

@section('content')
   
<x-progress_barr :step="$step ?? 1" />
 @if ($errors->any())
    <div class="text-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif
<div class="max-w-md mx-auto ">
    {{-- Formulaire de saisie --}}
     <form action="{{ route('verification.manualHandle') }}" method="POST" enctype="multipart/form-data">
            @csrf <!-- protection CSRF -->

            <label for="imageUpload" class="cursor-pointer rounded-2xl border border-gray-200 bg-[#F7FAFD] flex flex-col items-center justify-center" style="height:220px;">
                <div class="flex flex-col items-center justify-center w-full h-full">
                    <div class="rounded-full bg-gray-100 flex items-center justify-center" style="width:70px; height:70px;">
                     <input type="text" class="form-control" name="medicine_name" placeholder="ex: All-Vent Syrup" required value="{{ old('medicine_name', $medicine_name ?? '') }}">
                    </div>
                </div>
            </label>

            

            <button type="submit"  class="block mx-auto bg-blue-500 hover:bg-blue-600 text-white py-3 px-8 rounded-full font-bold text-lg shadow-md transition mb-1 ">
                Analyser le nom
            </button>

            
        </form>

    @if(isset($ingredients) && is_array($ingredients) && count($ingredients) > 0)
        <h3  style="margin-left: 22px; margin-top: 34px; margin-bottom: 18px;"><strong>Ingrédients extraits :</strong></h3>
        @foreach($ingredients as $ing)
                <div style="background: #f4f8fc; border-radius: 16px; padding: 12px 18px; font-size: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500; margin-bottom: 16px;">
                    <span style="font-size: 22px;">💊</span>
                    <span>{{ is_array($ing) ? $ing['name'] : $ing }}</span>
                </div>
        @endforeach

        {{-- Bouton pour continuer vers la vérification WADA --}}
        <form action="{{ route('manual.analyzeWithWada') }}" method="POST">
            @csrf
            <input type="hidden" name="medicine_name" value="{{ $medicine_name }}">
            <input type="hidden" name="ingredients" value="{{ json_encode($ingredients) }}">
            <button type="submit"  class="block mx-auto bg-blue-500 hover:bg-blue-600 text-white py-3 px-8 rounded-full font-bold text-lg shadow-md transition mb-1 ">
               Continuer à vérification par WADA
            </button>
        </form>
    @endif
</div>
@endsection
