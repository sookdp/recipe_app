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
    public function index(Request $request, int $id): Response
    {
        $userId = $this->getUser() ? $this->getUser()->getId() : null;
        $userEmail = $this->getUser() ? $this->getUser()->getEmail() : null;

        // Appel de l'API Symfony pour récupérer la recette
        $response = $this->httpClient->request('GET', "http://127.0.0.1:8001/api/recipes/$id");

        if ($response->getStatusCode() !== 200) {
            throw $this->createNotFoundException('Recette non trouvée');
        }

        $recipe = $response->toArray();

        // Récupération des données du formulaire
        if ($request->isMethod('POST')) {
            $content = $request->request->get('content');
            $rating = intval($request->request->get('rating')); // Récupérer la note

            // Créer les données pour le commentaire
            $commentData = [
                '@context' => '/context',
                'content' => $content,
                'rating' => $rating,
                'user' => $userId,
                'recipe' => '/api/recipes/' . $id, // Lien vers la recette
            ];

            $commentJson = json_encode($commentData);
            // dump($commentJson); // Vérifier le contenu du commentaire (JSON)

            // Requête POST pour ajouter le commentaire
            $commentResponse = $this->httpClient->request('POST', "http://127.0.0.1:8001/api/comments", [
                'headers' => [
                    'Content-Type' => 'application/ld+json',
                ],
                'body' => $commentJson,
            ]);

            // Si le commentaire est ajouté avec succès
            if ($commentResponse->getStatusCode() === 201) {
                // Redirige vers la même page pour afficher le commentaire ajouté
                return $this->redirectToRoute('app_recipedetails', ['id' => $id]);
            }

            // Si la requête de commentaire échoue
            $this->addFlash('error', 'Erreur lors de l\'ajout du commentaire.');
        }

        // Traitement des ingrédients et des étapes de la recette
        $recipe['ingredients'] = isset($recipe['ingredients']) ? implode(', ', $recipe['ingredients']) : 'Aucun ingrédient';

        $steps = isset($recipe['steps']) && is_array($recipe['steps']) ? $recipe['steps'] : ['Aucune étape'];

        // Récupérer les commentaires de l'API
        $comments = [];
        if (!empty($recipe['comments'])) {
            foreach ($recipe['comments'] as $commentUrl) {
                $commentResponse = $this->httpClient->request('GET', "http://127.0.0.1:8001$commentUrl");
                if ($commentResponse->getStatusCode() === 200) {
                    $comments[] = $commentResponse->toArray();
                }
            }
        }

        // Passage des données à Twig pour afficher la recette et les commentaires
        return $this->render('recipedetails/index.html.twig', [
            'recipe' => $recipe,
            'steps' => $steps,
            'comments' => $comments,
            'userId' => $userId,
            'userEmail' => $userEmail,
        ]);
    }
}
