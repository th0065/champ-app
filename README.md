# Champ App

Application Laravel (champ-app) — backend + Livewire front-end minimal.

## Description

Petit projet e‑commerce basé sur Laravel avec Livewire, Fortify pour l'authentification, Vite pour les assets et Pest pour les tests.

## Technologies

- PHP / Laravel
- Livewire
- Laravel Fortify
- Vite
- Pest (tests)

## Prérequis

- PHP (recommandé 8.1+)
- Composer
- Node.js & npm
- MySQL (ou autre SGBD configuré)
- XAMPP (Windows) ou tout serveur web pointant vers le dossier `public`

## Installation (locale)

1. Cloner le dépôt:

```
git clone <repo-url> champ-app
cd champ-app
```

2. Installer les dépendances PHP:

```
composer install
```

3. Installer les dépendances JavaScript et compiler les assets en développement:

```
npm install
npm run dev
```

4. Dupliquer l'exemple d'environnement et générer la clé d'application:

```
copy .env.example .env
php artisan key:generate
```

5. Configurer la base de données dans le fichier `.env` (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

6. Lancer les migrations et les seeders:

```
php artisan migrate --seed
```

## Lancer l'application

- Avec le serveur intégré (développement):

```
php artisan serve
```

- Ou configurer XAMPP/Apache pour pointer vers le dossier `public` du projet.

## Tests

Lancer la suite de tests:

```
php artisan test
```

Ou (si vous utilisez Pest directement):

```
./vendor/bin/pest
```

## Commandes utiles

- `php artisan migrate` — exécuter les migrations
- `php artisan db:seed` — exécuter les seeders
- `php artisan tinker` — console interactive
- `npm run build` — compiler les assets pour production

## Déploiement (notes rapides)

- Compiler les assets: `npm run build`
- Mettre à jour les dépendances Composer en production: `composer install --no-dev --optimize-autoloader`
- Exécuter les migrations: `php artisan migrate --force`
- Configurer la variable d'environnement `APP_ENV=production` et `APP_DEBUG=false`.

## Contribution

1. Créer une branche feature.
2. Ouvrir une pull request avec description claire.

## Licence

Licence à définir — ajouter un fichier LICENSE si nécessaire.

---

Fichier créé automatiquement. Pour modifier le contenu ou ajouter des sections (API, endpoints, diagrammes), dites-moi ce que vous voulez inclure.
