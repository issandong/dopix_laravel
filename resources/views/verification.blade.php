@extends('layouts.app')

@section('title', 'Check a Medicine')

@section('content')
<div class="min-h-screen bg-white flex flex-col justify-between">
    <div>
        
        <div class="text-center mb-7">
             <h2 class="text-2xl font-extrabold text-indigo-700 mb-8 text-center">Comment souhaitez-vous vérifier ?</h2>
        </div>
        <div class="max-w-3xl mx-auto flex flex-col md:flex-row gap-8 justify-center">
            <!-- Upload Image Option -->
            <a href="{{ route('verification.imageForm') }}" class="flex-1">
                <div class="rounded-2xl border border-gray-200 px-6 py-7 flex flex-col items-center shadow hover:shadow-lg transition cursor-pointer h-full">
                    <div class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-full mb-3">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l.4-1.2A2 2 0 017.3 4h9.4a2 2 0 011.9 1.8L21 7h2a1 1 0 011 1v11a2 2 0 01-2 2H3a2 2 0 01-2-2V8a1 1 0 011-1z"/><circle cx="12" cy="13" r="4"/></svg>
                    </div>
                    <div class="text-lg font-bold text-gray-900">Télécharger l'image</div>
                    <div class="text-gray-400 text-center text-base mt-1">Prenez une photo de l'étiquette du médicament</div>
                </div>
            </a>
            <!-- Enter Trade Name Option -->
            <a href="{{ route('verification.manualForm') }}" class="flex-1">
                <div class="rounded-2xl border border-gray-200 px-6 py-7 flex flex-col items-center shadow hover:shadow-lg transition cursor-pointer h-full">
                    <div class="w-16 h-16 bg-gray-100 flex items-center justify-center rounded-full mb-3">
                       <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4-4.03 7-9 7-1.11 0-2.18-.13-3.18-.37L3 21l1.37-3.6C3.51 16.07 3 14.85 3 13.5c0-4 4.03-7 9-7s9 3 9 7z" />
                       </svg>
                    </div>
                    <div class="text-lg font-bold text-gray-900">Entrer le nom commercial</div>
                    <div class="text-gray-400 text-center text-base mt-1">Tapez le nom de médicament</div>
                </div>
            </a>
        </div>
        <div class="text-center text-gray-400 mt-8 text-base">
           Choisissez l'option qui vous convient le mieux
        </div>
        
    
        @foreach ($errors->all() as $error)
            <div class="text-center text-gray-400 mt-8 text-base">{{ $error }}</div>
        @endforeach
    

    </div>
   
</div>
@endsection