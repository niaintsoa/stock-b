# Biloki - Gestion de Stock et API

Ce projet est une application complète de gestion de stock, construite avec **Laravel 11**, **Filament PHP** pour l'interface d'administration, et **API Platform** pour l'exposition des données via une API REST riche.

## 🚀 Prérequis

- Docker et Docker Compose installés sur votre machine.
- Git.

## 🛠️ Installation et Mise en Marche

Suivez ces étapes pour installer et démarrer le projet localement :

### 1. Cloner le dépôt et préparer l'environnement

```bash
git clone <votre-url-de-repo> test-biloki
cd test-biloki

# Copier le fichier d'environnement
cp .env.example .env
```

### 2. Démarrer les conteneurs Docker (Laravel Sail)

Ce projet utilise Laravel Sail pour l'environnement de développement (PHP, MySQL, Mailpit/Mailhog).

```bash
# Installez les dépendances Composer via un conteneur temporaire
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# Démarrez les conteneurs en arrière-plan
./vendor/bin/sail up -d
```

### 3. Configurer l'application

Une fois les conteneurs démarrés, exécutez les commandes suivantes dans le conteneur `app` :

```bash
# Générer la clé d'application
./vendor/bin/sail artisan key:generate

# Exécuter les migrations et populer la base de données avec des données de test
./vendor/bin/sail artisan migrate --seed
```

> **Note :** La commande `--seed` crée un compte Administrateur par défaut.
> **Email :** `admin@example.com`
> **Mot de passe :** `password`

### 4. Accéder à l'application

- **Panel d'Administration (Filament) :** [http://localhost/admin](http://localhost/admin)
- **Documentation de l'API (Swagger UI) :** [http://localhost/api/docs](http://localhost/api/docs)
- **Boîte de réception des emails (Mailhog/Mailpit) :** [http://localhost:8025](http://localhost:8025)

---

## 🔒 Utilisation de l'API

L'API est entièrement sécurisée via **Laravel Sanctum** (Bearer Tokens).

### Obtenir un Token (Login)

Pour interagir avec l'API, vous devez d'abord obtenir un token d'authentification.

**Requête (POST) :** `http://localhost/api/login`

**Corps (JSON) :**
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Réponse :**
```json
{
    "message": "Connexion réussie.",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@example.com",
        "profile_type": "App\\Domain\\Entity\\Admin"
    },
    "token": "1|votre_token_secret_ici..."
}
```

### Tester l'API avec Swagger UI

1. Allez sur [http://localhost/api/docs](http://localhost/api/docs).
2. Cliquez sur le bouton **Authorize** 🔒 en haut à droite.
3. Collez votre token généré précédemment et validez.
4. Vous pouvez maintenant tester toutes les routes (Products, Customers, StockMovements) directement depuis l'interface !

### Fonctionnalités de l'API (API Platform)

L'API offre nativement des fonctionnalités avancées :
- **Recherches (Partial Search) :** ex. `GET /api/products?name=pneu`
- **Filtres Exacts :** ex. `GET /api/products?status=active`
- **Tris (Ordering) :** ex. `GET /api/products?sort[created_at]=desc`
- **Filtres de dates :** ex. `GET /api/stock_movements?created_at[after]=2024-01-01`

---

## 🏗️ Architecture du Code

- **Entités Métier (`app/Domain/Entity`) :** Contient la logique métier pure (Modèles, relations polymorphiques, règles de gestion de stock FIFO). API Platform et Filament s'appuient tous les deux sur ces entités.
- **Ressources Administrateur (`app/Infrastructure/Filament/Resources`) :** Interfaces graphiques pour gérer les entités.
- **API (`routes/api.php` & `config/api-platform.php`) :** L'API repose sur le standard JSON:API / JSON-LD, avec des routes d'authentification personnalisées dans `AuthController`.
