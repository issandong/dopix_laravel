<?php

namespace App\Services;

use App\Services\OpenAIService;
use Smalot\PdfParser\Parser as PdfParser;

class WadaCheckerService
{
    protected $openAI;
    protected $wadaText;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
        $this->wadaText = $this->loadWadaText();
    }

    /**
     * Charge et extrait le texte du PDF WADA.
     * @return string
     */
    protected function loadWadaText()
{
    $pdfPath = storage_path('app/wada/2025list_en_final_clean_12_september_2024.pdf');
    if (!file_exists($pdfPath)) {
        throw new \Exception('Fichier WADA introuvable à ' . $pdfPath);
    }

    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($pdfPath);
    $text = $pdf->getText();

   
    return $text;
}


    /**
     * Vérifie un ingrédient contre la liste WADA via OpenAI.
     * @param string $ingredient
     * @return array [status, detection_window]
     */
  public function checkIngredient(string $ingredient): array
{
    // Limite le texte extrait pour ne pas surcharger le prompt
    $excerpt = mb_substr($this->wadaText, 0, 3500);

    $prompt = <<<EOT
Tu vérifies si un ingrédient figure sur la Liste des substances interdites de la WADA 2025.

Pour l’ingrédient '{$ingredient}', classe-le dans l’une des catégories suivantes :
- Interdit
- Attention
- Autorisé

Réponds avec un seul mot : soit "Interdit", "Attention" ou "Autorisé".

EXTRAIT DE LA LISTE WADA 2025 :
{$excerpt}
EOT;

    

    $response = $this->openAI->analyzeTextPrompt($prompt, 20, 0.1);
    $rawVerdict = strtolower(trim($response['content'] ?? ''));

  

    // Conversion du verdict en anglais pour la logique interne
    $verdict = match ($rawVerdict) {
        'interdit'    => 'prohibited',
        'attention'   => 'caution',
        'autorisé'    => 'allowed',
        default       => 'allowed', // fallback par sécurité
    };

    $detection_window = null;

    if ($verdict === 'prohibited') {
        // On récupère la fenêtre de détection (toujours en anglais pour précision)
        $detectPrompt = "Combien de temps '{$ingredient}' est détectable dans les tests antidopage ? Donne uniquement une plage courte. Exemple : '2-5 jours', '1-2 semaines', 'jusqu’à 6 mois'. Si différent entre sang et urine, écris : 'X jours dans le sang, Y jours dans l’urine'. Pas plus de 10 mots.";
        $detectResp = $this->openAI->analyzeTextPrompt($detectPrompt, 30, 0.1);
        $detection_window = trim($detectResp['content'] ?? '');
    }

    return [
        'status' => $verdict,
        'detection_window' => $detection_window,
    ];
}


    /**
     * Vérifie une liste d'ingrédients.
     * @param array $ingredients
     * @return array
     */
    public function checkAll(array $ingredients): array
    {
        $results = [];
        foreach ($ingredients as $ingredient) {
            $ingredientName = is_array($ingredient) ? $ingredient['name'] : $ingredient;
            $results[] = [
                'name' => $ingredientName,
                ...$this->checkIngredient($ingredientName),
            ];
        }
        return $results;
    }

    protected function findWadaExcerpt(string $ingredient): string
{
    $ingredient = strtolower($ingredient);
    $text = strtolower($this->wadaText);

    // Recherche de l'ingrédient dans le texte complet
    $pos = strpos($text, $ingredient);

    if ($pos === false) {
        // Si non trouvé, retourne le début du texte (fallback)
        return mb_substr($this->wadaText, 0, 3500);
    }

    // Extrait ~3000 caractères autour du mot-clé
    $start = max(0, $pos - 1500);
    return mb_substr($this->wadaText, $start, 3000);
}

}