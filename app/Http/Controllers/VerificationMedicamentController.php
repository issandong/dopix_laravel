<?php

namespace App\Http\Controllers;

use App\Models\UtilisationToken;
use App\Models\VerificationMedicament;
use Illuminate\Validation\Rule;


use App\Services\OpenAIService;
use App\Services\WadaCheckerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationMedicamentController extends \Illuminate\Routing\Controller
{
    // Dépendances injectées : services OpenAI et WADA
    protected OpenAIService $openAI;
    protected WadaCheckerService $wadaChecker;

    /**
     * Constructeur : injection des services et middleware auth obligatoire
     */
    public function __construct(OpenAIService $openAI, WadaCheckerService $wadaChecker)
    {
        $this->middleware('auth'); // l'utilisateur doit être connecté
        $this->openAI = $openAI;
        $this->wadaChecker = $wadaChecker;
    }
     /**
     * Affiche les option de verification.
     */
    public function showOptionVerification()
    {
        
        return view('verification');
    }

    /**
     * Affiche le formulaire d'upload d'image de médicament.
     */
    public function showImageForm()
    {
         $user = auth()->user();

        if (!$user->isPro() && $user->remainingFreeChecks() <= 0) {
            return redirect()->back()->withErrors([
                'quota' => '❌ Vous avez atteint votre quota de 3 vérifications gratuites ce mois-ci.',
            ]);
        }
        return view('verifier-image');
    }


    /**
     * Affiche le formulaire de saisie manuelle du nom de médicament.
     */
    public function showManualForm()
    {
        $user = auth()->user();

        if (!$user->isPro() && $user->remainingFreeChecks() <= 0) {
            return redirect()->back()->withErrors([
                'quota' => '❌ Vous avez atteint votre quota de 3 vérifications gratuites ce mois-ci.',
            ]);
        }
        return view('verifier-name');
    }

    /**
     * Traite la saisie manuelle, extrait les ingrédients via OpenAI,
     * et affiche la vue avec les ingrédients extraits.
     */
    public function handleManualForm(Request $request)
    {
        // Validation du champ "medicine_name"
        $request->validate([
            'medicine_name' => 'required|string|max:255',
        ]);

        // Appel au service OpenAI pour extraire les ingrédients du nom
        $result = $this->openAI->analyzeText($request->medicine_name);
        $ingredients = $this->normalizeIngredients($result['ingredients'] ?? []);

        // Affichage de la même vue, avec la liste extraite
        return view('verifier-name', [
            'medicine_name' => $request->medicine_name,
            'ingredients' => $ingredients,
            'step' => 2, // ← Première étape
        ]);
    }

    
    /**
     * Traite l’upload d’image, appelle OpenAI pour extraction OCR/vision,
     * et affiche la vue avec la liste des ingrédients extraits.
     */
    public function handleImageForm(Request $request)
    {
        // Validation de l'image uploadée
        $request->validate([
            'medicine_image' => 'required|image|max:5120',
        ]);

        // Stocker l’image dans storage/app/public/medicine_images
        $path = $request->file('medicine_image')->store('medicine_images', 'public');
        $imageUrl = Storage::url($path); // pour affichage dans la vue

        // Lecture et conversion de l’image en base64 (pour l’API OpenAI)
        $filePath = storage_path('app/public/' . $path);
        $imageData = base64_encode(file_get_contents($filePath));
        $mimeType = $request->file('medicine_image')->getMimeType();
        $base64Image = "data:{$mimeType};base64,{$imageData}";

        // Extraction des ingrédients grâce à OpenAI Vision
        $result = $this->openAI->analyzeImage($base64Image);
      
        $ingredients = $this->normalizeIngredients($result['ingredients'] ?? []);

        // Facultatif : message d'erreur si vide
        $error_message = null;
        if (!$ingredients) {
          $error_message = "Aucun ingrédient détecté. Essayez une autre image ou réessayez.";
        }
        // Rendu de la vue avec image + ingrédients
        return view('verifier-image', [
            'image_url' => $imageUrl,
            'ingredients' => $ingredients,
             'error_message' => $error_message,
             'step' => 2, // ← Première étape
        ]);
    }

    /**
     * Traite la vérification WADA pour une liste d’ingrédients (depuis nom ou image).
     * Affiche le résultat de la vérification.
     */
    public function handleWadaVerification(Request $request)
    {
        $user = $request->user();

        // Validation des champs reçus
        $request->validate([
            'medicine_name' => 'required|string|max:255',
            'ingredients' => 'required|string', // JSON encodé
        ]);

        // Décodage JSON sécurisé des ingrédients
        $ingredients = $this->normalizeIngredients(json_decode($request->ingredients, true) ?? []);

        // Appel au service WADA pour chaque ingrédient
        $wadaIngredients = $this->wadaChecker->checkAll($ingredients);
        
        
        // ✅ Détection d'ingrédients interdits
    // 🛡️ Vérifie si les statuts sont en anglais ou français
    $contains_prohibited = collect($wadaIngredients)->contains(function ($ingredient) {
        $status = strtolower($ingredient['status'] ?? '');
        return in_array($status, ['prohibited', 'interdit']);
    });
    
    // Détection correcte de la source (image ou texte)
    $source = strtolower($request->input('source', 'text'));
    $source = in_array($source, ['image', 'text']) ? $source : 'text'; // sécurité

        $sourceMap = [
        'text' => 'Texte',
        'image' => 'Image',
        ];

        $inputSource = strtolower($request->input('source', 'text'));
        $mappedSource = $sourceMap[$inputSource] ?? 'Texte';
                UtilisationToken::create([
        'user_id' => $user->id,
        'period' => 'monthly',
        'medicine_name' => $request->medicine_name,
        'ingredients' => $ingredients, // tableau ou JSON
        'check_date' => now(),
        'source' => $mappedSource,
        'tokens_used' => $result['tokens_used'] ?? 0, // si tu l’as
        'ai_confidence' =>  0.0,
    ]);


        // Affichage de la vue résultat
        return view('wada-result', [
            'medicine_name' => $request->medicine_name,
            'ingredients' => $ingredients,
            'wada_ingredients' => $wadaIngredients,
            'contains_prohibited' => $contains_prohibited, // ✅ obligatoire ici
            'source' => $source,
             'step' => 3, // ← Deuxième étape
        ]);
    }

    /**
     * (Optionnel) Affiche l’historique des vérifications d’un utilisateur.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // À personnaliser avec le modèle correspondant si besoin
        // Exemple :
        $verifications = VerificationMedicament::where('user_id', Auth::id())->take(4)->orderBy('created_at', 'desc')->get();
          // Nombre de vérifications ce mois via UtilisationToken
        $usedThisMonth = $user->utilisationTokens()
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();

    // Nombre restant
    $remaining = $user->isPro() ? '∞' : max(3 - $usedThisMonth, 0);

    // Pourcentage de progression
    $progressPercent = $user->isPro()
        ? 100
        : min(round(($usedThisMonth / 3) * 100), 100);

    return view('dashboard', compact('verifications', 'usedThisMonth', 'remaining', 'progressPercent'));
    }

    /**
     * Utilitaire : normalise tous les formats d’ingrédients possibles en array de strings
     */
    protected function normalizeIngredients($ingredients)
    {
        // Si c'est une chaîne JSON, on décode
        if (is_string($ingredients)) {
            $decoded = json_decode($ingredients, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $ingredients = $decoded;
            } else {
                return [];
            }
        }
        // Si c'est un tableau d'objets avec 'name', on aplati
        if (is_array($ingredients) && isset($ingredients[0]['name'])) {
            return array_map(fn($i) => $i['name'], $ingredients);
        }
        // Si c'est déjà un tableau de chaines
        if (is_array($ingredients)) {
            return $ingredients;
        }
        return [];
    }


    public function store(Request $request)
{  
     // Mapping anglais => français pour le statut global
        $statusMap = [
            'allowed' => 'Autorisé',
            'attention' => 'Attention',
            'prohibited' => 'Interdit',
            'interdit' => 'Interdit',
            'autorisé' => 'Autorisé',
        ];

        $sourceMap = [
            'text' => 'Texte',
            'image' => 'Image',
            'texte' => 'Texte',
            'image' => 'Image',
        ];

        $inputStatus = strtolower($request->overall_status);
        $inputSource = strtolower($request->source ?? 'text');

        $mappedStatus = $statusMap[$inputStatus] ?? 'Autorisé';
        $mappedSource = $sourceMap[$inputSource] ?? 'Texte';

        // Fusionner les données corrigées dans la requête
        $request->merge([
            'overall_status' => $mappedStatus,
            'source' => $mappedSource,
        ]);

        // Validation finale
                
    
    $request->validate([
        'medicine_name' => 'required|string',
        'ingredients' => 'required',
        'overall_status' => ['required', Rule::in(['Autorisé', 'Attention', 'Interdit'])],
        'check_date' => 'required|date',
        'source' => ['nullable', Rule::in(['Texte', 'Image'])],
    ]);

    VerificationMedicament::create([
        'user_id' => auth()->id(),
        'medicine_name' => $request->medicine_name,
        'ingredients' => json_decode($request->ingredients, true),
        'overall_status' => $request->overall_status,
        'check_date' => $request->check_date,
        'source' => $request->source,
    ]);

    return redirect()->route('dashboard')->with('success', 'Vérification enregistrée.');
}

function history(Request $request)
{
    $user = $request->user();
    
    // Récupérer les vérifications de l'utilisateur
    $verifications = VerificationMedicament::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10); // Pagination pour l'historique

    return view('historique', compact('verifications'));

}
}