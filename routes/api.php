<?php

use App\Http\Controllers\VerificationMedicamentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;

// Toutes les routes sont protégées par authentification
Route::middleware('auth:sanctum')->group(function () {

    // Vérification à partir d'une image (POST)
    Route::post('/verification/verify-image', [VerificationMedicamentController::class, 'verifyImage'])
        ->name('verification.verifyImage');

    // Vérification à partir d'une saisie manuelle (POST)
    Route::post('/verification/verify-manual', [VerificationMedicamentController::class, 'verifyManual'])
        ->name('verification.verifyManual');

    // Validation de la liste d'ingrédients (POST)
    Route::post('/verification/validate', [VerificationMedicamentController::class, 'validateIngredients'])
        ->name('verification.validate');

    // Vérification WADA d'une liste d'ingrédients (POST)
    Route::post('/verification/wada-check', [VerificationMedicamentController::class, 'wadaCheck'])
        ->name('verification.wadaCheck');

    // Liste des vérifications de l'utilisateur (GET)
    Route::get('/verification', [VerificationMedicamentController::class, 'index'])
        ->name('verification.index');

    // Détail d'une vérification (GET)
    Route::get('/verification/{id}', [VerificationMedicamentController::class, 'show'])
        ->name('verification.show');

      
     
        
});
  