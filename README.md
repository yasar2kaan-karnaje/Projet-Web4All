# Dépi'Stage - Plateforme de Recherche de Stages

Dépi'Stage est une application web conçue pour les étudiants (particulièrement du CESI) afin de faciliter leur recherche de stage, et pour l'équipe pédagogique afin d'administrer les offres, entreprises, étudiants et candidatures. 

L'application est construite en PHP 8.1+ avec une architecture MVC "maison" (sans grand framework type Symfony, mais utilisant Composer pour l'autoloading) et Twig pour les templates.

---

## 🛠️ Prérequis Système

Pour faire tourner le projet en local sur votre machine, vous devez avoir installé :

1. **PHP >= 8.1** (avec l'extension pdo_mysql activée).
2. **Composer** (Gestionnaire de dépendances PHP).
3. **MySQL** ou **MariaDB** (Base de données).

---

## 🚀 Installation & Lancement Rapide

### 1. Cloner ou Extraire les fichiers
Si vous avez reçu ce projet sous forme de dossier zip ou de répertoire, placez-le dans le dossier de votre choix (par ex. sous Windows dans un dossier de dev ou sous WSL : `/var/www/ajob4u.fr`).

Ouvrez un terminal (ou invite de commandes/PowerShell) à la racine du projet.

### 2. Installer les dépendances (Vendor)
Le projet utilise Twig pour l'affichage géré par Composer. Exécutez la commande suivante à la racine du projet :

```bash
composer install
```
*(Cela va créer le dossier `vendor/` avec toutes les bibliothèques requises).*

### 3. Configurer la base de données (MySQL)

**A. Importation du Schéma**
Vous devez créer la base de données et les tables nécessaires. 
Grâce à un client SQL (comme phpMyAdmin, DBeaver, ou en ligne de commande), importez le fichier de structure principal :
1. Fichier principal : `sql/schema.sql`. (Ce script va créer de lui-même la base de données `depistage` ainsi que les données et utilisateurs par défaut).
2. Si votre application a besoin de données récentes, exécutez également les scripts de migration présents dans le dossier `sql/` (par exemple : `alter_entreprises.sql`, et les fichiers `migration_xxx.sql`).

**B. Configuration des accès**
Ouvrez le fichier local dédié à la connexion SQL situé ici : `config/database.php`.
Vérifiez que les informations de connexion (hôte, nom d'utilisateur, et mot de passe DB) correspondent bien à votre environnement MySQL local :

```php
// config/database.php
return [
    'host' => '127.0.0.1', // ou 'localhost'
    'dbname' => 'depistage',
    'username' => 'root', // Votre utilisateur MySQL
    'password' => 'VOTRE_MOT_DE_PASSE_SQL', // Renseignez ici votre mdp MySQL (ex: JUL678jul678@)
    'charset' => 'utf8mb4',
];
```

### 4. Démarrer le serveur local PHP
Pour tester rapidement sans configurer un serveur Apache/Nginx (comme XAMPP ou WAMP), vous pouvez utiliser le serveur interne de PHP.

1. Déplacez-vous dans le dossier `public` :
   ```bash
   cd public
   ```
2. Lancez le serveur local :
   ```bash
   php -S localhost:8000
   ```

*L'application est maintenant disponible !*

---

## 🌐 Utilisation

Ouvrez votre navigateur et rendez-vous sur : [http://localhost:8000](http://localhost:8000)

### 👥 Comptes de démonstration pré-configurés

La base de données (`sql/schema.sql`) inclut des jeux de test pour essayer tous les rôles immédiatement :

*   **Administrateur** (Accès complet à la gestion CRUD du site)
    *   **Email:** `admin@depistage.eu`
    *   **Mot de passe:** `admin123`

*   **Pilote / Tuteur** (Gestion des promotions, consultations des élèves)
    *   **Email:** `m.dupont@cesi.fr`
    *   **Mot de passe:** `pilote123`

*   **Étudiant** (Recherche d'offres, postuler à des stages, Wishlist)
    *   **Email:** `a.dupont@viacesi.fr`
    *   **Mot de passe:** `etudiant123`

---

## 📂 Architecture simplifiée

*   **`config/`** : Fichiers de configuration (connexion BDD).
*   **`public/`** : Fichiers accessibles publiquement. Point d'entrée du site (`index.php`) et ressources CSS, Images...
*   **`src/`** : Logique métier en PHP orienté objet.
    *   **`Controller/`** : Les contrôleurs interceptent l'URL, préparent les données et demandent l'affichage.
    *   **`Model/`** : Les modèles centralisent toutes les requêtes directes à la base de données.
    *   `Router.php`, `Database.php` : Composants du cœur du Framework MVC "Maison".
*   **`templates/`** : Les fichiers d'affichage structurés (Vues) écrit en Twig (`.html.twig`).
*   **`sql/`** : Scripts de création et migration de la base de données.
