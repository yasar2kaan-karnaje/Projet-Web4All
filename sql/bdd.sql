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

-- 4. Création des Entreprises (5 entreprises)
INSERT INTO entreprises (id, nom, secteur, localisation, description) VALUES
(1, 'TechCorp', 'Informatique', 'Paris', 'ESN leader du marché de la tech.'),
(2, 'DataForge', 'Data Science', 'Lyon', 'Start-up spécialisée en Intelligence Artificielle.'),
(3, 'WebMakerZ', 'Web', 'Bordeaux', 'Agence web de création de sites vitrines.'),
(4, 'SecurIT', 'Cybersécurité', 'Paris', 'Expert en sécurité des systèmes d information.'),
(5, 'CloudSys', 'Cloud', 'Lyon', 'Hébergement et architecture Cloud.');

-- 5. Création des Offres (15 offres : 3 par entreprise)
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

-- 6. Création des Utilisateurs avec le nouveau mot de passe

-- -> Administrateur (1)
INSERT INTO users (id, nom, prenom, email, password, role_id) VALUES
(1, 'Admin', 'Super', 'admin@test.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 1);
INSERT INTO administrateurs (user_id) VALUES (1);

-- -> Pilotes (3)
INSERT INTO users (id, nom, prenom, email, password, role_id) VALUES
(2, 'Martin', 'Paul', 'pilote1@cesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 2),
(3, 'Bernard', 'Lucie', 'pilote2@cesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 2),
(4, 'Thomas', 'Marc', 'pilote3@cesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 2);
INSERT INTO pilotes (id, user_id, is_recruteur) VALUES (1, 2, 0), (2, 3, 0), (3, 4, 0);

-- -> Création de 3 promotions, gérées par nos 3 pilotes
INSERT INTO ref_promotions (id, centre_id, nom) VALUES
(1, 1, 'A2 Info Paris'), (2, 2, 'A2 Info Lyon'), (3, 3, 'A2 Info Bordeaux');
INSERT INTO pilote_promotions (pilote_id, promotion_id) VALUES
(2, 1), (3, 2), (4, 3);

-- -> Étudiants (12 - 4 par promotion/pilote)
INSERT INTO users (id, nom, prenom, email, password, role_id) VALUES
(11, 'Etu1', 'Jean', 'etu1@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(12, 'Etu2', 'Marie', 'etu2@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(13, 'Etu3', 'Pierre', 'etu3@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(14, 'Etu4', 'Sophie', 'etu4@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),

(15, 'Etu5', 'Lucas', 'etu5@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(16, 'Etu6', 'Julie', 'etu6@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(17, 'Etu7', 'Hugo', 'etu7@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(18, 'Etu8', 'Chloe', 'etu8@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),

(19, 'Etu9', 'Leo', 'etu9@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(20, 'Etu10', 'Emma', 'etu10@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(21, 'Etu11', 'Paul', 'etu11@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3),
(22, 'Etu12', 'Alice', 'etu12@viacesi.fr', '$2y$10$jYP7l0rCpcz5dnpx6ksoKeprETEc2DE4YjEshanfroVqNm46R3xe6', 3);

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
