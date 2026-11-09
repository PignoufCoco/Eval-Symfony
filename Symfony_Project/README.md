# Installation pour l’évaluation

Prérequis : PHP 8.4 ou supérieur, Composer et une base PostgreSQL compatible avec
la configuration du projet. Exécuter les commandes depuis `Symfony_Project`.

1. Installer les dépendances, y compris celles de développement :

   ```bash
   composer install
   ```

2. Créer un fichier `.env.local` et y définir `DATABASE_URL` avec les identifiants
   de votre base locale (voir le format dans `.env`). Ne pas committer ce fichier.

3. Préparer une base dédiée à la démonstration :

   ```bash
   php bin/console doctrine:database:create --if-not-exists
   php bin/console doctrine:migrations:migrate --no-interaction
   php bin/console doctrine:fixtures:load
   ```

   **Attention : `doctrine:fixtures:load` efface les données existantes avant de
   charger le jeu de démonstration.** Vérifier la base ciblée avant de confirmer.
   Les fixtures sont disponibles en environnement `dev` et `test`.

4. Démarrer le serveur avec `symfony serve`, puis ouvrir l’adresse affichée.

## Compte de démonstration

Sur `/login` : **prof@example.com** / **DemoSymfony2026!**.
Ce compte possède le rôle `ROLE_BENEVOLE` requis pour accéder à la gestion.
Ces identifiants publics sont réservés à une installation locale de démonstration.

Le jeu contient un utilisateur, deux bénévoles, trois artistes fictifs, trois
lieux fictifs, trois techniques et six œuvres, avec des associations multiples
et un exemple d’œuvre recouverte. Les dates de destruction sont renseignées car
elles sont obligatoires dans le modèle actuel. Aucune photo factice n’est créée :
vous pouvez ajouter vos propres images depuis la gestion des photos.

Les fixtures permettent de recréer les données chez le professeur ; elles ne
publient pas le site sur Internet.
