<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(string $apiKey = null)
    {
        $this->httpClient = HttpClient::create();
        $this->apiKey = $apiKey ?? $_ENV['OPENAI_API_KEY'] ?? '';
    }

    /**
     * Check content for vulgarity and return a moderation score
     * 
     * @param string $content The content to check
     * @return array An array containing the score and reasons if any
     */
    public function moderateContent(string $content): array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/moderations', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'input' => $content,
                ],
            ]);
            dump($response);
            dump($content);
            $data = $response->toArray();
            
            if (!isset($data['results']) || empty($data['results'])) {
                return [
                    'score' => 0,
                    'reasons' => ['Error: No moderation results returned'],
                    'approved' => true
                ];
            }

            $result = $data['results'][0];
            $flagged = $result['flagged'];
            
            // Get the highest category score
            $categories = $result['categories'];
            $scores = $result['category_scores'];
            
            $highestScore = 0;
            $reasons = [];
            
            foreach ($scores as $category => $score) {
                if ($categories[$category] && $score > $highestScore) {
                    $highestScore = $score;
                }
                
                if ($categories[$category]) {
                    $reasons[] = ucfirst(str_replace('-', ' ', $category));
                }
            }
            
            // Determine if content should be approved based on score threshold
            $approved = $highestScore < 0.7;
            
            return [
                'score' => $highestScore,
                'reasons' => $reasons,
                'approved' => $approved
            ];
        } catch (\Exception $e) {
            return [
                'score' => 0,
                'reasons' => ['Error: ' . $e->getMessage()],
                'approved' => false
            ];
        }
    }
}