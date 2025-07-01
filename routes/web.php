<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificationMedicamentController;

// Page d'accueil
Route::get('/', function () {
    return view('auth.login');
});

// Dashboard (protégé)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [VerificationMedicamentController::class, 'index'])->name('dashboard');
Route::get('/historique', [VerificationMedicamentController::class, 'history'])->name('historique');


// Page de choix vérification (optionnel, peut pointer vers ta vue d’option image/nom)
Route::get('/verifier-medicament', [VerificationMedicamentController::class, 'showOptionVerification'])->name('verification');

// Formulaire saisie manuelle du nom commercial
Route::get('/verification/verifier-name', [VerificationMedicamentController::class, 'showManualForm'])->name('verification.manualForm');
Route::post('/verification/verifier-name', [VerificationMedicamentController::class, 'handleManualForm'])->name('verification.manualHandle');

// Vérification WADA à partir d’une liste d’ingrédients (POST)
Route::post('/analyse-wada', [VerificationMedicamentController::class, 'handleWadaVerification'])->name('manual.analyzeWithWada');



// Analyse d’image pour extraction d’ingrédients
// ✅ Route GET : afficher le formulaire
Route::get('/verification/verify-image', [VerificationMedicamentController::class, 'showImageForm'])->name('verification.imageForm');

// ✅ Route POST : traiter le formulaire
Route::post('/verification/verify-image', [VerificationMedicamentController::class, 'handleImageForm'])->name('verification.handleImageForm');

Route::post('/verifications', [VerificationMedicamentController::class, 'store'])->name('verifications.store');



// Profil utilisateur (auth obligatoire)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/abonnement', [\App\Http\Controllers\StripeController::class, 'showSubscription'])
    ->middleware('auth')
    ->name('subscription.show');
    // Dans routes/web.php
use App\Http\Controllers\StripeController;

Route::middleware('auth')->post('/subscribe', [StripeController::class, 'subscribe'])->name('stripe.subscribe');


require __DIR__.'/auth.php';