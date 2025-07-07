<?php

namespace App\Services;

use GuzzleHttp\Client as HttpClient;

class OpenAIService
{
    protected string $apiKey;
    protected HttpClient $httpClient;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');

        if (!$this->apiKey) {
            throw new \Exception("Clé OpenAI manquante. Vérifie ton .env");
        }

        $this->httpClient = new HttpClient([
            'base_uri' => 'https://api.openai.com/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
        ]);
    }


    /**
     * Analyse une image en base64 via OpenAI Vision.
     *
     * @param string $base64Image avec préfixe mime type (ex: data:image/png;base64,...)
     * @return array ['ingredients' => [...]]
     */
    public function analyzeImage(string $base64Image): array
    {
      $prompt = "Lis l’image et donne uniquement un tableau JSON contenant les ingrédients actifs du médicament, exemple : [\"paracetamol\", \"ibuprofene\"] ou [\"INGREDIENT1\", \"INGREDIENT2\"] sans aucune explication ni phrase autour.";

        $payload = [
           'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => ['url' => $base64Image]],
                    ],
                ],
            ],
            'max_tokens' => 500,
        ];

        try {
            $response = $this->httpClient->post('v1/chat/completions', [
                'json' => $payload,
            ]);

            $body = json_decode((string)$response->getBody(), true);

            $content = $body['choices'][0]['message']['content'] ?? '';

           $ingredients = json_decode($content, true);

if (!is_array($ingredients)) {
    // Essaye d'extraire un tableau JSON d'une chaîne contenant autre chose
    if (preg_match('/(\[.*\])/', $content, $matches)) {
        $ingredients = json_decode($matches[1], true);
    }
}

// Si ce n'est toujours pas un tableau plat, fallback lignes
if (!is_array($ingredients)) {
    $ingredients = $this->extractIngredientsFromText($content);
}

            return ['ingredients' => $ingredients];
        } catch (\Exception $e) {
            \Log::error('OpenAI API error (Vision): ' . $e->getMessage());
            return ['ingredients' => []];
        }
    }

    /**
     * Analyse un nom de médicament via OpenAI Chat.
     *
     * @param string $medicineName
     * @return array ['ingredients' => [...]]
     */
    public function analyzeText(string $medicineName): array
{
    $prompt = "Donne uniquement un tableau JSON (exemple : [\"paracetamol\",\"ibuprofene\"]) listant les ingrédients actifs du médicament suivant, sans rien ajouter autour : {$medicineName}";

    $payload = [
        'model' => 'gpt-4o',
        'messages' => [
            ['role' => 'user', 'content' => $prompt],
        ],
        'max_tokens' => 300,
    ];

    try {
        $response = $this->httpClient->post('v1/chat/completions', [
            'json' => $payload,
        ]);

        $body = json_decode((string)$response->getBody(), true);

        $content = $body['choices'][0]['message']['content'] ?? '';
        \Log::info('OpenAI analyzeText content: '.$content);

        $ingredients = json_decode($content, true);

        if (!is_array($ingredients)) {
            if (preg_match('/(\[.*\])/', $content, $matches)) {
                $ingredients = json_decode($matches[1], true);
            }
        }

        if (!is_array($ingredients)) {
            $ingredients = $this->extractIngredientsFromText($content);
        }

        return ['ingredients' => $ingredients, 'content' => $content];
    } catch (\Exception $e) {
        \Log::error('OpenAI API error (Text): ' . $e->getMessage());
        return ['ingredients' => []];
    }
}

    /**
     * Envoie un prompt texte libre à OpenAI (gpt-4o), retourne le contenu et l'usage.
     *
     * @param string $prompt
     * @param int $maxTokens
     * @param float $temperature
     * @return array ['content' => ..., 'usage' => ...]
     */
    public function analyzeTextPrompt(string $prompt, int $maxTokens = 100, float $temperature = 0.1): array
    {
        $payload = [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ];

        try {
            $response = $this->httpClient->post('v1/chat/completions', [
                'json' => $payload,
            ]);
            $body = json_decode((string)$response->getBody(), true);
            $content = $body['choices'][0]['message']['content'] ?? '';
            $usage = $body['usage'] ?? [];
            return ['content' => $content, 'usage' => $usage];
        } catch (\Exception $e) {
            \Log::error('OpenAI API error (Prompt): ' . $e->getMessage());
            return ['content' => '', 'usage' => []];
        }
    }

    /**
     * Fallback : extrait les ingrédients d’un texte brut.
     *
     * @param string $text
     * @return array
     */
    protected function extractIngredientsFromText(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $ingredients = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line) continue;

            // Nettoyage (bullet, numéro, etc)
            $line = preg_replace('/^[\-\*\•\–\—\d\.\s]+/', '', $line);
            if (!$line) continue;

            $ingredients[] = ['name' => $line];
        }

        return $ingredients;
    }
}