@extends('layouts.app')

@section('title', 'Upload Image')

@section('content')
 <x-progress_barr :step="$step ?? 1" />
<div class="min-h-screen bg-white">
    @if ($errors->any())
    <div class="text-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if(isset($error_message))
    <div class="text-danger">{{ $error_message }}</div>
@endif
    <div class="max-w-md mx-auto ">
         <h2 class="font-extrabold text-indigo-700 mb-3 text-center mt-2">Aperçu de l'image</h2>

        <form action="{{ route('verification.handleImageForm') }}" method="POST" enctype="multipart/form-data">
            @csrf <!-- protection CSRF -->

            <label for="imageUpload" class="cursor-pointer rounded-2xl border border-gray-200 bg-[#F7FAFD] flex flex-col items-center justify-center" style="height:220px;">
                <div class="flex flex-col items-center justify-center w-full h-full">
                    <div class="rounded-full bg-gray-100 flex items-center justify-center" style="width:70px; height:70px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <rect x="3" y="7" width="18" height="12" rx="2" stroke-width="2" stroke="currentColor" fill="none"/>
                            <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-width="2" stroke="currentColor" fill="none"/>
                            <circle cx="12" cy="13" r="3" stroke-width="2" stroke="currentColor" fill="none"/>
                        </svg>
                    </div>
                </div>
            </label>

            <input type="file" id="imageUpload" name="medicine_image" class="hidden" accept="image/*" required />

            <div id="fileName" class="text-gray-400 text-center mt-2 mb-8">Aucun fichier sélectionné</div>

            <button type="submit" class="block mx-auto bg-blue-500 hover:bg-blue-600 text-white py-3 px-8 rounded-full font-bold text-lg shadow-md transition mb-1">
                Analyser l'image
            </button>

            <div class="text-center text-gray-400 mt-1">Notre IA extraira la liste des ingrédients</div>
        </form>
       @if(isset($ingredients) && count($ingredients))
       <h3  style="margin-left: 22px; margin-top: 34px; margin-bottom: 18px;"><strong>Ingrédients extraits :</strong></h3>
            @foreach($ingredients as $ing)
                <div style="background: #f4f8fc; border-radius: 16px; padding: 12px 18px; font-size: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500; margin-bottom: 16px;">
                    <span style="font-size: 22px;">💊</span>
                    <span>{{ is_array($ing) ? $ing['name'] : $ing }}</span>
                </div>
            @endforeach
    <form action="{{ route('manual.analyzeWithWada') }}" method="POST">
        @csrf
        <input type="hidden" name="medicine_name" value="Image">
        <input type="hidden" name="ingredients" value="{{ json_encode($ingredients) }}">
        <button type="submit"  class="block mx-auto bg-blue-500 hover:bg-blue-600 text-white py-3 px-8 rounded-full font-bold text-lg shadow-md transition mb-1 ">
               Continuer à vérification par WADA
        </button>
    </form>
@endif
    </div>
</div>

<script>
    document.getElementById('imageUpload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const fileNameDiv = document.getElementById('fileName');
        fileNameDiv.textContent = file ? file.name : 'Aucun fichier sélectionné';
    });
</script>
@endsection


