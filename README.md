# Depi'Stage -- Guide d'installation et de mise en route

Ce document explique comment recuperer le projet, le configurer et le faire tourner sur votre machine. Il est destine a toute personne souhaitant tester l'application en local.

---

## 1. Pre-requis

Vous devez avoir les elements suivants installes sur votre machine (Linux, Ubuntu de preference) :

- **PHP 8.1 ou superieur** avec les extensions `pdo`, `pdo_mysql` et `mbstring`
- **Composer** (gestionnaire de dependances PHP)
- **Apache 2** avec le module `mod_rewrite`
- **MySQL** ou **MariaDB**
- **Git**

Si certains composants manquent, voici comment les installer en une seule commande sur Ubuntu/Debian :

```bash
sudo apt update
sudo apt install apache2 php php-pdo php-mysql php-mbstring mysql-server git composer -y
```

---

## 2. Cloner le projet

Recuperez le depot sur votre machine :

```bash
cd /var/www
sudo git clone https://github.com/<utilisateur>/Projet-Web4All.git depistage
```

Donnez les droits necessaires a Apache pour lire les fichiers :

```bash
sudo chown -R www-data:www-data /var/www/depistage
sudo chmod -R 755 /var/www/depistage
```

> Remplacez `<utilisateur>` par le nom du compte GitHub qui heberge le depot.

---

## 3. Installer les dependances PHP

Placez-vous dans le dossier du projet et lancez Composer :

```bash
cd /var/www/depistage
sudo composer install
```

Cela va creer le dossier `vendor/` avec Twig (moteur de templates) et PHPUnit (tests).

---

## 4. Creer et configurer la base de donnees

### 4.1 Creer la base de donnees

Connectez-vous a MySQL :

```bash
sudo mysql -u root
```

Puis executez les commandes suivantes :

```sql
CREATE DATABASE depistage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'depistage_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON depistage.* TO 'depistage_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4.2 Importer le schema SQL

Un fichier `.sql` est fourni avec le projet (dump de la base), importez-le :

```bash
sudo mysql -u depistage_user -p depistage < chemin/vers/le/fichier.sql
```

### 4.3 Renseigner les identifiants de connexion

Editez le fichier `config/database.php` pour y mettre vos informations :

```bash
nano config/database.php
```

Le contenu doit ressembler a ceci :

```php
<?php

return [
    'host'     => '127.0.0.1',
    'dbname'   => 'depistage',
    'username' => 'depistage_user',
    'password' => 'votre_mot_de_passe',
    'charset'  => 'utf8mb4',
];
```

---

## 5. Configurer Apache

### 5.1 Activer les modules necessaires

```bash
sudo a2enmod rewrite
sudo a2enmod expires
```

### 5.2 Creer le VirtualHost principal

Creez un fichier de configuration pour le site :

```bash
sudo nano /etc/apache2/sites-available/depistage.conf
```

Collez le contenu suivant :

```apache
<VirtualHost *:80>
    ServerName depistage.local

    DocumentRoot /var/www/depistage/public

    <Directory /var/www/depistage/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/depistage_error.log
    CustomLog ${APACHE_LOG_DIR}/depistage_access.log combined
</VirtualHost>
```

> Le `AllowOverride All` est indispensable pour que le fichier `.htaccess` du projet fonctionne (URL rewriting).

### 5.3 (Optionnel) VirtualHost pour les fichiers statiques

Si vous souhaitez servir les fichiers statiques (CSS, images) depuis un sous-domaine dedie, un fichier de configuration est fourni dans le projet sous le nom `vhost_statique.conf`. Son contenu :

```apache
<VirtualHost *:80>
    ServerName static.depistage.local
    DocumentRoot /var/www/depistage/public

    <Directory /var/www/depistage/public>
        Options -Indexes +FollowSymLinks
        AllowOverride None
        Require all granted

        # Bloquer l'execution de PHP sur ce vhost
        <FilesMatch "\.php$">
            Require all denied
        </FilesMatch>

        # Cache navigateur pour les performances
        <IfModule mod_expires.c>
            ExpiresActive On
            ExpiresByType image/png "access plus 1 month"
            ExpiresByType image/jpeg "access plus 1 month"
            ExpiresByType text/css "access plus 1 week"
            ExpiresByType application/javascript "access plus 1 week"
        </IfModule>
    </Directory>
</VirtualHost>
```

Pour l'activer :

```bash
sudo cp /var/www/depistage/vhost_statique.conf /etc/apache2/sites-available/depistage-static.conf
sudo a2ensite depistage-static
```

> Ce vhost statique n'est pas obligatoire pour faire fonctionner le projet. Il sert uniquement a ameliorer les performances et la securite en production.

### 5.4 Activer le site et desactiver le site par defaut

```bash
sudo a2ensite depistage.conf
sudo a2dissite 000-default.conf
```

### 5.5 Redemarrer Apache pour appliquer les changements

```bash
sudo systemctl restart apache2
```

---

## 6. Configurer le fichier hosts (acces en local)

Pour acceder au site via `depistage.local` dans votre navigateur, ajoutez une entree dans votre fichier hosts :

```bash
sudo nano /etc/hosts
```

Ajoutez la ligne suivante :

```
127.0.0.1    depistage.local
```

Si vous avez aussi configure le vhost statique, ajoutez aussi :

```
127.0.0.1    static.depistage.local
```

---

## 7. Tester le site

Ouvrez votre navigateur et rendez-vous sur :

```
http://depistage.local
```

Vous devriez voir la page d'accueil de Depi'Stage. Si vous obtenez une erreur 500, verifiez les points suivants :

- Les identifiants en base de donnees dans `config/database.php` sont corrects.
- Le dossier `vendor/` existe bien (sinon relancez `composer install`).
- Le module `mod_rewrite` est bien active (`sudo a2enmod rewrite` puis redemarrez Apache).
- Les droits sur le dossier sont corrects (`sudo chown -R www-data:www-data /var/www/depistage`).
- Consultez les logs Apache pour plus de details : `sudo tail -f /var/log/apache2/depistage_error.log`.

---

## 8. Comptes et roles

L'application gere trois roles d'utilisateurs :

| Role      | Ce qu'il peut faire                                                                                  |
|-----------|------------------------------------------------------------------------------------------------------|
| Etudiant  | Consulter les offres, postuler, gerer sa wishlist, suivre ses candidatures, evaluer une entreprise.  |
| Pilote    | Gerer les etudiants de sa promotion, les entreprises, les offres, consulter les candidatures.        |
| Admin     | Acces complet : gestion des pilotes, etudiants, entreprises, offres, promotions et centres.          |

Les comptes doivent etre crees en base de donnees. Il n'y a pas de page d'inscription publique -- c'est volontaire, les comptes sont crees par un administrateur ou un pilote depuis le panneau d'administration accessible a `/admin`.

---

## 9. Lancer les tests

Les tests unitaires utilisent PHPUnit :

```bash
cd /var/www/depistage
./vendor/bin/phpunit tests/
```

---

## 10. Structure du projet (pour reference)

```
Projet-Web4All/
|-- config/
|   |-- database.php              # Identifiants de la base de donnees
|
|-- public/                       # DocumentRoot Apache (seul dossier accessible)
|   |-- index.php                 # Point d'entree unique (front controller)
|   |-- .htaccess                 # URL rewriting + redirection HTTPS
|   |-- robots.txt
|   |-- sitemap.xml
|   |-- css/                      # Feuilles de style
|   |-- logo/                     # Logo et images
|
|-- src/
|   |-- Controller/               # Logique metier (10 controleurs)
|   |-- Model/                    # Acces aux donnees via PDO (7 modeles)
|
|-- templates/                    # Vues Twig
|   |-- base.html.twig            # Layout principal
|   |-- admin/                    # Vues d'administration
|   |-- auth/                     # Connexion
|   |-- offre/                    # Catalogue et detail des offres
|   |-- entreprise/               # Liste et detail des entreprises
|   |-- etudiant/                 # Candidatures et wishlist
|   |-- pilote/                   # Suivi des candidatures pilote
|   |-- profil/                   # Profil utilisateur
|   |-- errors/                   # Pages 403, 404, 500
|
|-- tests/                        # Tests PHPUnit
|-- composer.json                 # Dependances (Twig, PHPUnit)
|-- vhost_statique.conf           # Exemple de vhost statique
```

---

## Resume des commandes (copier-coller rapide)

Pour ceux qui veulent aller vite, voici toutes les commandes dans l'ordre :

```bash
# Installation des paquets
sudo apt update
sudo apt install apache2 php php-pdo php-mysql php-mbstring mysql-server git composer -y

# Cloner le projet
cd /var/www
sudo git clone https://github.com/<utilisateur>/Projet-Web4All.git depistage
sudo chown -R www-data:www-data /var/www/depistage
sudo chmod -R 755 /var/www/depistage

# Dependances PHP
cd /var/www/depistage
sudo composer install

# Base de donnees
sudo mysql -u root -e "
  CREATE DATABASE depistage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'depistage_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
  GRANT ALL PRIVILEGES ON depistage.* TO 'depistage_user'@'localhost';
  FLUSH PRIVILEGES;
"

# Configurer la connexion
nano config/database.php

# Apache
sudo a2enmod rewrite
sudo a2enmod expires

# Creer le VirtualHost (copier le contenu donne plus haut)
sudo nano /etc/apache2/sites-available/depistage.conf

# Activer le site
sudo a2ensite depistage.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2

# Fichier hosts
echo "127.0.0.1    depistage.local" | sudo tee -a /etc/hosts

# Ouvrir dans le navigateur
# http://depistage.local
```

---

## En cas de probleme

| Symptome                        | Cause probable                                      | Solution                                                        |
|---------------------------------|-----------------------------------------------------|-----------------------------------------------------------------|
| Page blanche ou erreur 500      | Dependances manquantes                              | Verifier que `composer install` a bien tourne                   |
| Erreur de connexion BDD         | Identifiants incorrects dans `config/database.php`  | Verifier host, dbname, username et password                     |
| Page "Not Found" sur les routes | mod_rewrite desactive                               | `sudo a2enmod rewrite` puis `sudo systemctl restart apache2`    |
| Acces interdit (403)            | Droits de fichiers                                  | `sudo chown -R www-data:www-data /var/www/depistage`            |
| Erreur Twig                     | Dossier vendor absent                               | Relancer `composer install`                                     |

---

Projet realise par le Groupe 2 CPIA2 Orléans 2026 du CESI dans le cadre du module Web.