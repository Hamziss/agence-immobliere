# API Gestion d'Agence Immobilière

API RESTful développée avec Laravel 12 pour la gestion d'une agence immobilière. Cette application permet la gestion des biens immobiliers avec un système d'authentification basé sur des rôles (Admin, Agent, Guest).

## Table des matières

-   [Fonctionnalités](#fonctionnalités)
-   [Installation](#installation)
-   [Variables d'environnement](#-variables-denvironnement)
-   [Architecture](#architecture)
-   [Rôles et Permissions](#-rôles-et-permissions)
-   [Documentation API](#-documentation-api)
-   [Exemples de requêtes](#-exemples-de-requêtes)

## Fonctionnalités

-   **Authentification sécurisée** avec Laravel Sanctum
-   **Gestion multi-rôles** : Admin, Agent, Guest
-   **CRUD complet** pour les biens immobiliers
-   **Système de filtrage avancé** (ville, type, prix, statut)
-   **Gestion d'images** avec upload multiple et image principale
-   **Soft Delete** pour les biens supprimés
-   **Publication/dépublication** des annonces
-   **Documentation API** automatique avec Swagger/OpenAPI
-   **Architecture propre** : Controller → Service → Repository
-   **Validation robuste** avec Form Requests
-   **DTOs** pour le transfert de données

## Installation

### 1. Cloner le repository

```bash
git clone https://github.com/Hamziss/agence-immobliere.git
cd agence-immobliere
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Configurer l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### 5. Configurer la base de données

Créer le fichier de base de données SQLite (par défaut) :

```bash
# Créer le répertoire database s'il n'existe pas
mkdir -p database

# Créer le fichier de base de données
touch database/database.sqlite
```

Ou configurer MySQL/PostgreSQL dans le fichier `.env` (voir section [Variables d'environnement](#-variables-denvironnement)).

### 6. Exécuter les migrations

```bash
php artisan migrate
```

### 7. (Optionnel) Peupler la base de données

```bash
php artisan db:seed
```

Cette commande créera :

-   Un utilisateur admin par défaut
-   Quelques utilisateurs agents et guests
-   Des biens immobiliers de démonstration

### 8. Créer le lien symbolique pour le stockage

```bash
php artisan storage:link
```

### 9. Compiler les assets

```bash
npm run build
```

### 10. Générer la documentation Swagger

```bash
php artisan l5-swagger:generate
```

### 11. Démarrer le serveur

```bash
php artisan serve
```

L'application sera accessible sur `http://localhost:8000`

## Variables d'environnement

```env
# Application
APP_NAME="Agence Immobilière API"
APP_ENV=local
APP_KEY=base64:...  # Généré automatiquement
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de données (SQLite par défaut)
DB_CONNECTION=sqlite
# Ou pour MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=agence_immobiliere
# DB_USERNAME=root
# DB_PASSWORD=
```

## Architecture

L'application suit une architecture en couches inspirée du pattern **Repository** et **Service Layer** :

### Flux de données

```
Client Request
    ↓
Routes (api.php)
    ↓
Controller (validation des permissions)
    ↓
Service (logique métier)
    ↓
Repository (accès aux données)
    ↓
Model / Database
```

### Structure détaillée

#### 1. **Controllers** (`app/Http/Controllers`)

-   Point d'entrée des requêtes HTTP
-   Validation via Form Requests
-   Délègue la logique métier aux Services
-   Retourne des Responses formatées (JSON Resources)

#### 2. **Services** (`app/Services`)

-   Contient la **logique métier**
-   Orchestre les opérations complexes
-   Gère les autorisations via Policies
-   Communique avec les Repositories

#### 3. **Repositories** (`app/Repositories`)

-   **Abstraction de l'accès aux données**
-   Méthodes réutilisables pour interagir avec la base de données
-   Encapsule les requêtes Eloquent complexes

#### 4. **DTOs** (Data Transfer Objects - `app/DTOs`)

-   **Utilisation flexible** : utilisés pour transférer des données entre les couches
-   Immutables et type-safe
-   Facilitent la validation et la transformation des données

**Note** : Les DTOs sont utilisés de manière flexible. Certaines opérations simples peuvent utiliser directement les Models Eloquent, tandis que les opérations complexes bénéficient de la structure et validation des DTOs.

#### 6. **Policies** (`app/Policies`)

-   Gèrent les **autorisations granulaires**
-   Déterminent qui peut faire quoi sur chaque ressource

**Exemple** : `PropertyPolicy` définit qui peut créer, voir, modifier ou supprimer un bien.

#### 7. **Form Requests** (`app/Http/Requests`)

-   **Validation centralisée** des requêtes
-   Règles de validation réutilisables
-   Messages d'erreur personnalisés

#### 8. **Resources** (`app/Http/Resources`)

-   **Transformation des données** pour les réponses API
-   Format JSON cohérent
-   Masquage des données sensibles

## 👥 Rôles et Permissions

### Rôles disponibles

| Rôle      | Description                                |
| --------- | ------------------------------------------ |
| **admin** | Accès complet à toutes les fonctionnalités |
| **agent** | Peut gérer ses propres biens immobiliers   |
| **guest** | Accès en lecture seule aux biens publiés   |

### Matrice des permissions

| Action                     | Admin | Agent          | Guest | Non authentifié |
| -------------------------- | ----- | -------------- | ----- | --------------- |
| **Authentification**       |
| S'inscrire                 | ✅    | ✅             | ✅    | ✅              |
| Se connecter               | ✅    | ✅             | ✅    | ✅              |
| Se déconnecter             | ✅    | ✅             | ✅    | ❌              |
| Voir son profil            | ✅    | ✅             | ✅    | ❌              |
| **Biens immobiliers**      |
| Voir liste (publiés)       | ✅    | ✅             | ✅    | ✅              |
| Voir liste (tous)          | ✅    | ❌             | ❌    | ❌              |
| Voir détails (publiés)     | ✅    | ✅             | ✅    | ✅              |
| Voir détails (non publiés) | ✅    | ✅ (ses biens) | ❌    | ❌              |
| Créer un bien              | ✅    | ✅             | ❌    | ❌              |
| Modifier un bien           | ✅    | ✅ (ses biens) | ❌    | ❌              |
| Supprimer un bien          | ✅    | ✅ (ses biens) | ❌    | ❌              |
| Publier/dépublier          | ✅    | ✅ (ses biens) | ❌    | ❌              |
| **Images**                 |
| Uploader des images        | ✅    | ✅ (ses biens) | ❌    | ❌              |
| Supprimer une image        | ✅    | ✅ (ses biens) | ❌    | ❌              |
| Définir image principale   | ✅    | ✅ (ses biens) | ❌    | ❌              |

## 📚 Documentation API

La documentation complète de l'API est disponible via Swagger UI :

```
http://localhost:8000/api/documentation
```

## 📖 Exemples de requêtes

### 1. Inscription (Register)

**Requête** :

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "agent"
  }'
```

**Réponse** (201 Created) :

```json
{
    "message": "Inscription réussie.",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "agent",
        "created_at": "2025-11-12T10:00:00.000000Z"
    },
    "token": "1|abc123xyz456..."
}
```

### 2. Connexion (Login)

**Requête** :

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Réponse** (200 OK) :

```json
{
    "message": "Connexion réussie.",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "agent"
    },
    "token": "2|def789uvw012..."
}
```

### 3. Créer un bien immobilier

**Requête** :

```bash
curl -X POST http://localhost:8000/api/properties \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 2|def789uvw012..." \
  -d '{
    "type": "appartement",
    "rooms": 3,
    "surface": 85.5,
    "price": 250000,
    "city": "Casablanca",
    "district": "Maarif",
    "description": "Bel appartement lumineux avec vue sur mer",
    "status": "disponible",
    "is_published": true
  }'
```

**Réponse** (201 Created) :

```json
{
    "message": "Bien créé avec succès.",
    "property": {
        "id": 1,
        "title": "Appartement à Casablanca - 3 pièces - 85.5 m²",
        "type": "appartement",
        "rooms": 3,
        "surface": "85.50",
        "price": "250000.00",
        "city": "Casablanca",
        "district": "Maarif",
        "description": "Bel appartement lumineux avec vue sur mer",
        "status": "disponible",
        "is_published": true,
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "agent"
        },
        "images": [],
        "primary_image": null,
        "created_at": "2025-11-12T10:15:00.000000Z"
    }
}
```

### 4. Liste filtrée des biens

**Requête** :

```bash
curl -X GET "http://localhost:8000/api/properties?city=Casablanca&type=appartement&price_min=200000&price_max=300000&status=disponible&per_page=10&page=1" \
  -H "Accept: application/json"
```

**Réponse** (200 OK) :

```json
{
    "data": [
        {
            "id": 1,
            "title": "Appartement à Casablanca - 3 pièces - 85.5 m²",
            "type": "appartement",
            "rooms": 3,
            "surface": "85.50",
            "price": "250000.00",
            "city": "Casablanca",
            "district": "Maarif",
            "status": "disponible",
            "is_published": true,
            "primary_image": null,
            "images_count": 0,
            "created_at": "2025-11-12T10:15:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/properties?page=1",
        "last": "http://localhost:8000/api/properties?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

### Paramètres de filtrage disponibles

| Paramètre   | Type   | Description         | Exemple              |
| ----------- | ------ | ------------------- | -------------------- |
| `city`      | string | Filtrer par ville   | `?city=Casablanca`   |
| `type`      | string | Filtrer par type    | `?type=appartement`  |
| `price_min` | float  | Prix minimum        | `?price_min=200000`  |
| `price_max` | float  | Prix maximum        | `?price_max=500000`  |
| `status`    | string | Filtrer par statut  | `?status=disponible` |
| `search`    | string | Recherche textuelle | `?search=vue+mer`    |
| `per_page`  | int    | Résultats par page  | `?per_page=15`       |
| `page`      | int    | Numéro de page      | `?page=2`            |

### Types de biens disponibles

-   `appartement`
-   `villa`
-   `terrain`
-   `bureau`
-   `local_commercial`

### Statuts disponibles

-   `disponible`
-   `vendu`
-   `location`

---

**Développé avec ❤️ par Hamziss**
