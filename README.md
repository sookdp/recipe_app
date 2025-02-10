# Recipes App

---
C'est une plateforme d'idées de recettes de cuisine. Les utilisateurs peuvent consulter les recettes, les ajouter à leur liste de favoris et les commenter.

---

## 1. Clonage du dépôt

```bash
git clone https://github.com/sookdp/recipe_app.git
cd recipes-app
```

## 2. Installation des dépendances

```bash
composer install
```

## 3. Configuration de la base de données

Remplacer les valeurs des variables d'environnement dans le fichier `.env` par les votres.

```bash
DATABASE_URL="mysql://root:root123@127.0.0.1:3306/recipesdb?serverVersion=8.0.32&charset=utf8mb4"
```

### 4. Modification du lien de l'API
Remplacer le lien de l'API dans le fichier `src/Controller/RecipeController.php` par le votre.
Il est recommandé d'utiliser le lien de l'API de l'application `recipes_api` et de le lancer en local.

```bash
$response = $this->httpClient->request('GET', 'http://127.0.0.1:8002/api/recipes');
```

## 5. Démarage du serveur

```bash
symfony serve
```
