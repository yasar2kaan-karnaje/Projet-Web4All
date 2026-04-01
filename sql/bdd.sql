SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `administrateurs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `candidatures` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `cv_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lettre_motivation` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('en_attente','acceptee','refusee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `date_candidature` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `centres` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `entreprises` (
  `id` int NOT NULL,
  `nom` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secteur` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localisation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `taille` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel_contact` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `etudiants` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `promotion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `evaluations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `entreprise_id` int NOT NULL,
  `note` int NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `date_evaluation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

CREATE TABLE `offres` (
  `id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `competences` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remuneration` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duree` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lieu` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entreprise_id` int DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pilotes` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `is_recruteur` tinyint(1) NOT NULL DEFAULT '0',
  `entreprise_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pilote_promotions` (
  `id` int NOT NULL,
  `pilote_id` int NOT NULL,
  `promotion_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ref_promotions` (
  `id` int NOT NULL,
  `centre_id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int NOT NULL,
  `centre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `entreprise_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `administrateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_candidature` (`user_id`,`offre_id`),
  ADD KEY `offre_id` (`offre_id`);

ALTER TABLE `centres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

ALTER TABLE `entreprises`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `entreprise_id` (`entreprise_id`);

ALTER TABLE `offres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offres_ibfk_1` (`entreprise_id`);

ALTER TABLE `pilotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `entreprise_id` (`entreprise_id`);

ALTER TABLE `pilote_promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pilote_promo` (`pilote_id`,`promotion_id`),
  ADD KEY `promotion_id` (`promotion_id`);

ALTER TABLE `ref_promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_centre_promo` (`centre_id`,`nom`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_entreprise` (`entreprise_id`),
  ADD KEY `fk_users_role` (`role_id`),
  ADD KEY `fk_users_updated_by` (`updated_by`);

ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`offre_id`),
  ADD KEY `offre_id` (`offre_id`);

ALTER TABLE `administrateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `candidatures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `centres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `entreprises`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `etudiants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `evaluations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `offres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `pilotes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `pilote_promotions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `ref_promotions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `administrateurs`
  ADD CONSTRAINT `administrateurs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE;

ALTER TABLE `etudiants`
  ADD CONSTRAINT `etudiants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE;

ALTER TABLE `offres`
  ADD CONSTRAINT `offres_ibfk_1` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE CASCADE;

ALTER TABLE `pilotes`
  ADD CONSTRAINT `pilotes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pilotes_ibfk_2` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE SET NULL;

ALTER TABLE `pilote_promotions`
  ADD CONSTRAINT `pilote_promotions_ibfk_1` FOREIGN KEY (`pilote_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pilote_promotions_ibfk_2` FOREIGN KEY (`promotion_id`) REFERENCES `ref_promotions` (`id`) ON DELETE CASCADE;

ALTER TABLE `ref_promotions`
  ADD CONSTRAINT `ref_promotions_ibfk_1` FOREIGN KEY (`centre_id`) REFERENCES `centres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ref_promotions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_entreprise` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `fk_users_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- PEUPLEMENT

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Nettoyage de toutes les tables existantes (Méthode DELETE pour éviter l'erreur #1701)
DELETE FROM wishlist;
ALTER TABLE wishlist AUTO_INCREMENT = 1;

DELETE FROM candidatures;
ALTER TABLE candidatures AUTO_INCREMENT = 1;

DELETE FROM evaluations;
ALTER TABLE evaluations AUTO_INCREMENT = 1;

DELETE FROM pilote_promotions;
ALTER TABLE pilote_promotions AUTO_INCREMENT = 1;

DELETE FROM pilotes;
ALTER TABLE pilotes AUTO_INCREMENT = 1;

DELETE FROM etudiants;
ALTER TABLE etudiants AUTO_INCREMENT = 1;

DELETE FROM administrateurs;
ALTER TABLE administrateurs AUTO_INCREMENT = 1;

DELETE FROM offres;
ALTER TABLE offres AUTO_INCREMENT = 1;

DELETE FROM entreprises;
ALTER TABLE entreprises AUTO_INCREMENT = 1;

DELETE FROM users;
ALTER TABLE users AUTO_INCREMENT = 1;

DELETE FROM ref_promotions;
ALTER TABLE ref_promotions AUTO_INCREMENT = 1;

DELETE FROM centres;
ALTER TABLE centres AUTO_INCREMENT = 1;

DELETE FROM roles;
ALTER TABLE roles AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- 2. Création des Rôles
INSERT INTO roles (id, nom, label) VALUES
(1, 'admin', 'Administrateur'),
(2, 'pilote', 'Pilote'),
(3, 'etudiant', 'Étudiant');

-- 3. Création des Centres
INSERT INTO centres (id, nom) VALUES
(1, 'Paris'), (2, 'Lyon'), (3, 'Bordeaux');

-- 4. Création des Entreprises (15 entreprises avec taille, email et tel)
INSERT INTO entreprises (id, nom, secteur, localisation, description, taille, email_contact, tel_contact) VALUES
(1, 'TechCorp', 'Informatique', 'Paris', 'ESN leader du marché de la tech.', 'Grande Entreprise', 'contact@techcorp.fr', '01 02 03 04 05'),
(2, 'DataForge', 'Data Science', 'Lyon', 'Start-up spécialisée en Intelligence Artificielle.', 'TPE', 'hello@dataforge.fr', '04 12 23 34 45'),
(3, 'WebMakerZ', 'Web', 'Bordeaux', 'Agence web de création de sites vitrines.', 'PME', 'agence@webmakerz.com', '05 22 33 44 55'),
(4, 'SecurIT', 'Cybersécurité', 'Paris', 'Expert en sécurité des systèmes d information.', 'ETI', 'contact@securit.fr', '01 44 55 66 77'),
(5, 'CloudSys', 'Cloud', 'Lyon', 'Hébergement et architecture Cloud.', 'PME', 'support@cloudsys.fr', '04 88 99 00 11'),
(6, 'GreenEnergy', 'Énergie', 'Nantes', 'Solutions informatiques pour la transition écologique.', 'Grande Entreprise', 'rh@greenenergy.com', '02 11 22 33 44'),
(7, 'HealthTech', 'Santé', 'Lille', 'Développement de logiciels pour le milieu hospitalier.', 'ETI', 'contact@healthtech.fr', '03 55 66 77 88'),
(8, 'FinTech Hub', 'Finance', 'Paris', 'Start-up innovante dans la gestion de patrimoine.', 'TPE', 'hello@fintechhub.fr', '01 99 88 77 66'),
(9, 'AeroDev', 'Aéronautique', 'Toulouse', 'Conception de logiciels embarqués pour l aviation.', 'Grande Entreprise', 'recrutement@aerodev.com', '05 10 20 30 40'),
(10, 'AutoDrive', 'Automobile', 'Strasbourg', 'Recherche et développement sur les véhicules autonomes.', 'ETI', 'contact@autodrive.fr', '03 90 80 70 60'),
(11, 'AgriData', 'Agriculture', 'Rennes', 'Analyse de données pour l agriculture connectée.', 'PME', 'info@agridata.fr', '02 40 50 60 70'),
(12, 'LogistiX', 'Logistique', 'Marseille', 'Optimisation des chaînes d approvisionnement par l IA.', 'ETI', 'contact@logistix.com', '04 70 80 90 00'),
(13, 'EduSmart', 'Éducation', 'Bordeaux', 'Plateforme de e-learning adaptative.', 'TPE', 'hello@edusmart.fr', '05 15 25 35 45'),
(14, 'RetailSoft', 'E-commerce', 'Paris', 'Solutions ERP pour les grandes enseignes de distribution.', 'Grande Entreprise', 'rh@retailsoft.com', '01 50 60 70 80'),
(15, 'GameStudio', 'Jeux Vidéo', 'Montpellier', 'Studio indépendant de développement de jeux.', 'PME', 'jobs@gamestudio.fr', '04 20 30 40 50');

-- 5. Création des Offres (15 offres : 3 par entreprise pour les 5 premières)
INSERT INTO offres (id, titre, description, competences, remuneration, duree, lieu, entreprise_id) VALUES
(1, 'Dev Fullstack', 'Mission complète web', 'PHP, JS, React', '1200€/mois', '6 mois', 'Paris', 1),
(2, 'Dev Backend PHP', 'Création API', 'PHP, Laravel', '1000€/mois', '4 mois', 'Paris', 1),
(3, 'Admin Sys', 'Gestion de serveurs Linux', 'Linux, Bash', '800€/mois', '6 mois', 'Paris', 1),
(4, 'Data Analyst', 'Analyse de données clients', 'Python, SQL', '1100€/mois', '6 mois', 'Lyon', 2),
(5, 'Data Engineer', 'Création de pipelines', 'Python, Spark', '1300€/mois', '6 mois', 'Lyon', 2),
(6, 'Machine Learning', 'Modélisation IA', 'TensorFlow', '1400€/mois', '6 mois', 'Lyon', 2),
(7, 'Intégrateur Web', 'Intégration HTML/CSS', 'HTML, CSS', '650€/mois', '2 mois', 'Bordeaux', 3),
(8, 'Designer UI/UX', 'Maquettage Figma', 'Figma, AdobeXD', '700€/mois', '3 mois', 'Bordeaux', 3),
(9, 'Chef de Projet Web', 'Gestion méthode Agile', 'Scrum, Trello', '900€/mois', '4 mois', 'Bordeaux', 3),
(10, 'Pentester', 'Tests intrusifs', 'Kali, Metasploit', '1500€/mois', '6 mois', 'Paris', 4),
(11, 'Analyste SOC', 'Analyse de logs', 'Splunk, Réseau', '1200€/mois', '5 mois', 'Paris', 4),
(12, 'Consultant Sécu', 'Audit ISO 27001', 'ISO27001', '1300€/mois', '6 mois', 'Paris', 4),
(13, 'Ingénieur Cloud', 'Déploiement AWS', 'AWS, Terraform', '1400€/mois', '6 mois', 'Lyon', 5),
(14, 'DevOps', 'CI/CD et Docker', 'Docker, GitLab CI', '1350€/mois', '6 mois', 'Lyon', 5),
(15, 'Architecte AWS', 'Design d architecture', 'AWS', '1500€/mois', '6 mois', 'Lyon', 5);

-- 6. Création des Utilisateurs

-- -> Administrateur (1)
INSERT INTO users (id, nom, prenom, email, password, role_id, centre) VALUES
(1, 'ADMIN', 'Super', 'super.admin@test.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 1, NULL);
INSERT INTO administrateurs (user_id) VALUES (1);

-- -> Pilotes de l'école (2, 3, 4) + Recruteur d'entreprise (5)
INSERT INTO users (id, nom, prenom, email, password, role_id, centre, entreprise_id) VALUES
(2, 'MARTIN', 'Paul', 'paul.martin@cesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 2, 'Paris', NULL),
(3, 'BERNARD', 'Lucie', 'lucie.bernard@cesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 2, 'Lyon', NULL),
(4, 'THOMAS', 'Marc', 'marc.thomas@cesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 2, 'Bordeaux', NULL),
(5, 'ROUSSEAU', 'Jacques', 'jacques.rousseau@techcorp.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 2, 'Paris', 1);

-- Association dans la table pilotes (ID 5 est recruteur pour entreprise 1)
INSERT INTO pilotes (id, user_id, is_recruteur, entreprise_id) VALUES 
(1, 2, 0, NULL), 
(2, 3, 0, NULL), 
(3, 4, 0, NULL),
(4, 5, 1, 1);

-- -> Création de 3 promotions, gérées par nos 3 pilotes scolaires (Le recruteur n'y est pas)
INSERT INTO ref_promotions (id, centre_id, nom) VALUES
(1, 1, 'A2 Info Paris'), (2, 2, 'A2 Info Lyon'), (3, 3, 'A2 Info Bordeaux');
INSERT INTO pilote_promotions (pilote_id, promotion_id) VALUES
(2, 1), (3, 2), (4, 3);

-- -> Étudiants (12 - 4 par promotion/pilote)
INSERT INTO users (id, nom, prenom, email, password, role_id, centre) VALUES
(11, 'DURAND', 'Jean', 'jean.durand@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Paris'),
(12, 'LEROY', 'Marie', 'marie.leroy@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Paris'),
(13, 'MOREAU', 'Pierre', 'pierre.moreau@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Paris'),
(14, 'SIMON', 'Sophie', 'sophie.simon@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Paris'),

(15, 'LAURENT', 'Lucas', 'lucas.laurent@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Lyon'),
(16, 'LEFEBVRE', 'Julie', 'julie.lefebvre@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Lyon'),
(17, 'MICHEL', 'Hugo', 'hugo.michel@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Lyon'),
(18, 'GARCIA', 'Chloe', 'chloe.garcia@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Lyon'),

(19, 'DAVID', 'Leo', 'leo.david@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Bordeaux'),
(20, 'BERTRAND', 'Emma', 'emma.bertrand@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Bordeaux'),
(21, 'ROUX', 'Paul', 'paul.roux@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Bordeaux'),
(22, 'VINCENT', 'Alice', 'alice.vincent@viacesi.fr', '$2y$10$MqSusOQjdFYvRVx14PZud.GevvxLOgWspUAHdsi3FGPNX00b4PMfG', 3, 'Bordeaux');

INSERT INTO etudiants (user_id, promotion) VALUES
(11, 'A2 Info Paris'), (12, 'A2 Info Paris'), (13, 'A2 Info Paris'), (14, 'A2 Info Paris'),
(15, 'A2 Info Lyon'), (16, 'A2 Info Lyon'), (17, 'A2 Info Lyon'), (18, 'A2 Info Lyon'),
(19, 'A2 Info Bordeaux'), (20, 'A2 Info Bordeaux'), (21, 'A2 Info Bordeaux'), (22, 'A2 Info Bordeaux');

-- 7. Création des Candidatures (2 par étudiant)
INSERT INTO candidatures (user_id, offre_id, statut) VALUES
(11, 1, 'en_attente'), (11, 2, 'en_attente'),
(12, 3, 'en_attente'), (12, 4, 'en_attente'),
(13, 5, 'en_attente'), (13, 6, 'en_attente'),
(14, 7, 'en_attente'), (14, 8, 'en_attente'),
(15, 9, 'en_attente'), (15, 10, 'en_attente'),
(16, 11, 'en_attente'), (16, 12, 'en_attente'),
(17, 13, 'en_attente'), (17, 14, 'en_attente'),
(18, 15, 'en_attente'), (18, 1, 'en_attente'),
(19, 2, 'en_attente'), (19, 3, 'en_attente'),
(20, 4, 'en_attente'), (20, 5, 'en_attente'),
(21, 6, 'en_attente'), (21, 7, 'en_attente'),
(22, 8, 'en_attente'), (22, 9, 'en_attente');

-- 8. Création de la Wishlist (1 par étudiant)
INSERT INTO wishlist (user_id, offre_id) VALUES
(11, 3), (12, 5), (13, 7), (14, 9),
(15, 11), (16, 13), (17, 15), (18, 2),
(19, 4), (20, 6), (21, 8), (22, 10);

-- 9. Création des Évaluations (Pilotes et Admin sur au moins 3 entreprises)
INSERT INTO evaluations (user_id, entreprise_id, note, commentaire) VALUES
(1, 1, 5, 'Excellente entreprise, très bonne intégration des étudiants et suivi rigoureux.'),
(2, 1, 4, 'Bon environnement de travail, les missions confiées aux étudiants sont très formatrices.'),
(3, 2, 5, 'Super start-up, très dynamique sur les sujets Data et Intelligence Artificielle.'),
(4, 2, 3, 'Bonne ambiance générale mais la charge de travail peut être importante par moments.'),
(1, 3, 4, 'Agence sérieuse, un cadre parfait pour un premier stage en développement web.'),
(2, 3, 4, 'Équipe accueillante et de très bons retours de la part des étudiants placés.');