<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RecipedetailsController extends AbstractController
{
    private $client;

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    #[Route('/recipedetails/{id}', name: 'app_recipedetails')]
    public function index(int $id): Response
    {
        // Appel de l'API Symfony
        $response = $this->httpClient->request('GET', "http://127.0.0.1:8001/api/recipes/$id");

        // Vérification si la requête a réussi
        if ($response->getStatusCode() !== 200) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        // Décodage du JSON en tableau PHP
        $recipe = $response->toArray();

        // Vérifie si la recette contient bien les ingrédients et les étapes
        $recipe['ingredients'] = isset($recipe['ingredients']) ? implode(', ', $recipe['ingredients']) : 'Aucun ingrédient';

        // Si les étapes sont déjà un tableau, on les garde telles quelles
        if (isset($recipe['steps']) && is_array($recipe['steps'])) {
            $steps = $recipe['steps'];
        } else {
            // Sépare les étapes basées sur une majuscule après un espace
            $pattern = '/(?<=\b[A-Za-z])\s+(?=[A-Z])/';
            $steps = isset($recipe['steps']) ? preg_split($pattern, $recipe['steps']) : ['Aucune étape'];
        }

        $comments = [];
        if (!empty($recipe['comments'])) {
            foreach ($recipe['comments'] as $commentUrl) {
                $commentResponse = $this->httpClient->request('GET', "http://127.0.0.1:8001$commentUrl");
                if ($commentResponse->getStatusCode() === 200) {
                    $comments[] = $commentResponse->toArray();
                }
            }
        }

        // Passage des données à Twig
        return $this->render('recipedetails/index.html.twig', [
            'recipe' => $recipe,
            'steps' => $steps, // On passe les étapes correctement
            'comments' => $comments,
        ]);
    }
}
