<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RecipesController extends AbstractController{
    private $client;

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/recipes', name: 'app_recipes')]
    public function index(): Response
    {
        // Appel de l'API Symfony
        $response = $this->httpClient->request('GET', 'http://127.0.0.1:8002/api/recipes');

        $recipesData = $response->toArray();

        // Décodage du JSON
        $recipes = $recipesData['member'] ?? [];

        // Vérifie le contenu brut de la réponse
        // dump($recipes);

        // Préparer les ingrédients et étapes sous forme de chaînes
        foreach ($recipes as &$recipe) {
            $recipe['ingredients'] = implode(', ', $recipe['ingredients']);
            $recipe['steps'] = implode(' ', $recipe['steps']);
        }

        // Passage des données à Twig
        return $this->render('recipes/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
}
