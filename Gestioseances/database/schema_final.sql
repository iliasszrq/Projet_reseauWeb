-- ============================================
-- GestioSeances - Script COMPLET FINAL
-- Fusion Dev 1 + Dev 2 + Dev 4
-- Mot de passe : password123
-- ============================================

DROP DATABASE IF EXISTS gestioseances;

CREATE DATABASE gestioseances
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE gestioseances;

-- ============================================
-- TABLE: users
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('professeur', 'assistante', 'directeur') NOT NULL DEFAULT 'professeur',
    telephone VARCHAR(20) NULL,
    departement VARCHAR(100) NULL,
    bureau VARCHAR(50) NULL,
    actif TINYINT(1) DEFAULT 1,
    tentatives_connexion INT DEFAULT 0,
    date_blocage DATETIME NULL,
    token_reset VARCHAR(255) NULL,
    token_expiration DATETIME NULL,
    derniere_connexion DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLE: matieres
-- ============================================
CREATE TABLE matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(255) NOT NULL,
    description TEXT NULL,
    niveau VARCHAR(10) NULL,
    semestre VARCHAR(5) NULL,
    nb_heures INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLE: salles
-- ============================================
CREATE TABLE salles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    batiment VARCHAR(50) NULL,
    capacite INT DEFAULT 30,
    equipements TEXT NULL,
    disponible TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLE: seances
-- ============================================
CREATE TABLE seances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professeur_id INT NOT NULL,
    matiere_id INT NOT NULL,
    salle_id INT NOT NULL,
    jour ENUM('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi') NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    groupe VARCHAR(50) NULL,
    type_seance VARCHAR(50) DEFAULT 'cours',
    semestre ENUM('S1', 'S2') DEFAULT 'S1',
    annee_universitaire VARCHAR(9) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (professeur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE,
    FOREIGN KEY (salle_id) REFERENCES salles(id) ON DELETE CASCADE,
    
    INDEX idx_seances_professeur (professeur_id),
    INDEX idx_seances_jour (jour)
) ENGINE=InnoDB;

-- ============================================
-- TABLE: demandes
-- ============================================
CREATE TABLE demandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professeur_id INT NOT NULL,
    seance_id INT NOT NULL,
    type ENUM('changement', 'annulation') NOT NULL,
    statut ENUM('brouillon', 'en_attente', 'validee_assistante', 'approuvee', 'rejetee', 'annulee') 
           DEFAULT 'brouillon',
    motif TEXT NOT NULL,
    nouvelle_date DATE NULL,
    nouvelle_heure_debut TIME NULL,
    nouvelle_heure_fin TIME NULL,
    nouvelle_salle_id INT NULL,
    commentaire_professeur TEXT NULL,
    urgente TINYINT(1) DEFAULT 0,
    date_soumission DATETIME NULL,
    date_validation_assistante DATETIME NULL,
    date_decision_directeur DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (professeur_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seance_id) REFERENCES seances(id) ON DELETE CASCADE,
    FOREIGN KEY (nouvelle_salle_id) REFERENCES salles(id) ON DELETE SET NULL,
    
    INDEX idx_demandes_statut (statut),
    INDEX idx_demandes_professeur (professeur_id),
    INDEX idx_demandes_date (created_at)
) ENGINE=InnoDB;

-- ============================================
-- TABLE: validations
-- ============================================
CREATE TABLE validations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    user_id INT NOT NULL,
    action ENUM('soumise', 'validee', 'rejetee', 'approuvee', 'annulee', 'modifiee') NOT NULL,
    commentaire TEXT NULL,
    ancien_statut VARCHAR(50) NULL,
    nouveau_statut VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_validations_demande (demande_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE: pieces_jointes
-- ============================================
CREATE TABLE pieces_jointes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    nom_original VARCHAR(255) NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    type_mime VARCHAR(100) NOT NULL,
    taille INT NOT NULL,
    chemin VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLE: notifications
-- ============================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    demande_id INT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger') DEFAULT 'info',
    lue TINYINT(1) DEFAULT 0,
    date_lecture DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (demande_id) REFERENCES demandes(id) ON DELETE SET NULL,
    
    INDEX idx_notifications_user (user_id, lue)
) ENGINE=InnoDB;

-- ============================================
-- DONNÉES DE TEST
-- Mot de passe : password123
-- ============================================

INSERT INTO users (nom, prenom, email, password, role, telephone, departement, actif) VALUES
('Alami', 'Hassan', 'hassan.alami@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'directeur', '0661234567', 'Direction', 1),
('Bennani', 'Fatima', 'fatima.bennani@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'assistante', '0662345678', 'Administration', 1),
('Mansouri', 'Samira', 'samira.mansouri@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'assistante', '0663456789', 'Administration', 1),
('Tazi', 'Mohammed', 'mohammed.tazi@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'professeur', '0664567890', 'Informatique', 1),
('El Amrani', 'Nadia', 'nadia.elamrani@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'professeur', '0665678901', 'Informatique', 1),
('Ziani', 'Karim', 'karim.ziani@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'professeur', '0666789012', 'Informatique', 1),
('Idrissi', 'Leila', 'leila.idrissi@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'professeur', '0667890123', 'Informatique', 1),
('Benjelloun', 'Ahmed', 'ahmed.benjelloun@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'professeur', '0668901234', 'Mathématiques', 1),
('Chakir', 'Sofia', 'sofia.chakir@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'professeur', '0669012345', 'Informatique', 1),
('Berrada', 'Youssef', 'youssef.berrada@eidia.ma', '$2y$10$iqdCSUN.EH3.n/hfycZOu.UDIL7ZBv0HMxi1ioMEG039qidkah3mW', 'professeur', '0660123456', 'Informatique', 0);

INSERT INTO salles (nom, batiment, capacite, equipements, disponible) VALUES
('A101', 'Bâtiment A', 40, 'Projecteur, Tableau blanc, Climatisation', 1),
('A102', 'Bâtiment A', 35, 'Projecteur, Tableau blanc', 1),
('A201', 'Bâtiment A', 50, 'Projecteur, Tableau blanc, Système audio', 1),
('B101', 'Bâtiment B', 30, 'Ordinateurs (20), Projecteur', 1),
('B102', 'Bâtiment B', 30, 'Ordinateurs (20), Projecteur', 1),
('B201', 'Bâtiment B', 45, 'Projecteur, Tableau interactif', 1),
('C101', 'Bâtiment C', 60, 'Amphithéâtre, Projecteur, Micro', 1),
('C102', 'Bâtiment C', 25, 'Salle de TP, Équipements électroniques', 0);

INSERT INTO matieres (code, nom, description, niveau, semestre, nb_heures) VALUES
('INF101', 'Programmation Web', 'Développement web avec PHP/MySQL', 'L3', 'S5', 42),
('INF102', 'Base de données avancées', 'Conception et administration de BDD', 'L3', 'S5', 42),
('INF103', 'Génie Logiciel', 'Méthodologies et bonnes pratiques', 'L3', 'S5', 36),
('INF104', 'Réseaux informatiques', 'Fondamentaux des réseaux', 'L3', 'S5', 36),
('INF105', 'Intelligence Artificielle', 'Introduction à l\'IA', 'M1', 'S1', 48),
('INF106', 'Sécurité informatique', 'Cybersécurité et protection', 'M1', 'S1', 42),
('MGT101', 'Gestion de projet', 'Management de projets IT', 'L3', 'S6', 36),
('MGT102', 'Management des SI', 'Systèmes d\'information', 'M1', 'S2', 42);

INSERT INTO seances (matiere_id, professeur_id, salle_id, jour, heure_debut, heure_fin, type_seance, groupe, annee_universitaire) VALUES
(1, 4, 1, 'lundi', '08:30:00', '10:30:00', 'cours', 'Groupe A', '2024-2025'),
(1, 4, 1, 'mercredi', '08:30:00', '10:30:00', 'td', 'Groupe A', '2024-2025'),
(7, 4, 6, 'jeudi', '10:45:00', '12:45:00', 'cours', 'L3-A', '2024-2025'),
(1, 4, 1, 'vendredi', '08:30:00', '10:30:00', 'cours', 'Groupe A', '2024-2025'),
(2, 5, 4, 'lundi', '10:45:00', '12:45:00', 'tp', 'Groupe B', '2024-2025'),
(2, 5, 4, 'mercredi', '08:30:00', '10:30:00', 'tp', 'Groupe A', '2024-2025'),
(3, 6, 3, 'lundi', '14:00:00', '16:00:00', 'cours', 'Groupe A', '2024-2025'),
(3, 6, 1, 'mardi', '14:00:00', '16:00:00', 'td', 'Groupe B', '2024-2025'),
(8, 6, 3, 'jeudi', '14:00:00', '16:00:00', 'cours', 'M1-C', '2024-2025'),
(4, 7, 2, 'mardi', '14:00:00', '17:00:00', 'tp', 'Groupe C', '2024-2025'),
(4, 7, 2, 'jeudi', '14:00:00', '17:00:00', 'tp', 'Groupe B', '2024-2025'),
(5, 8, 7, 'lundi', '08:30:00', '10:30:00', 'cours', 'M1-A', '2024-2025'),
(5, 8, 7, 'mercredi', '08:30:00', '11:30:00', 'cours', 'M1-A', '2024-2025'),
(6, 9, 3, 'lundi', '14:00:00', '16:00:00', 'cours', 'M1-B', '2024-2025');

INSERT INTO demandes (professeur_id, seance_id, type, motif, nouvelle_date, nouvelle_heure_debut, nouvelle_heure_fin, nouvelle_salle_id, statut, urgente, date_soumission, date_validation_assistante, date_decision_directeur, created_at) VALUES
(4, 1, 'changement', 'Conférence internationale à laquelle je dois participer', '2025-01-10', '14:00:00', '16:00:00', 2, 'approuvee', 0, '2025-01-02 09:15:00', '2025-01-02 14:30:00', '2025-01-02 16:45:00', '2025-01-02 09:15:00'),
(5, 5, 'annulation', 'Formation obligatoire organisée par l\'établissement', NULL, NULL, NULL, NULL, 'approuvee', 0, '2025-01-03 10:30:00', '2025-01-03 11:00:00', '2025-01-03 14:20:00', '2025-01-03 10:30:00'),
(6, 7, 'changement', 'Chevauchement avec une réunion pédagogique', '2025-01-09', '10:45:00', '12:45:00', 3, 'validee_assistante', 1, '2025-01-05 14:20:00', '2025-01-05 15:30:00', NULL, '2025-01-05 14:20:00'),
(7, 10, 'changement', 'Déplacement professionnel prévu', '2025-01-10', '08:30:00', '11:30:00', 2, 'validee_assistante', 0, '2025-01-06 08:45:00', '2025-01-06 09:30:00', NULL, '2025-01-06 08:45:00'),
(8, 12, 'changement', 'Problème de santé - certificat médical joint', '2025-01-14', '14:00:00', '16:00:00', 3, 'en_attente', 1, '2025-01-06 16:30:00', NULL, NULL, '2025-01-06 16:30:00'),
(9, 14, 'annulation', 'Grève des transports - impossibilité de me déplacer', NULL, NULL, NULL, NULL, 'en_attente', 1, '2025-01-07 07:00:00', NULL, NULL, '2025-01-07 07:00:00'),
(4, 4, 'changement', 'Conflit avec une autre séance dans mon emploi du temps', '2025-01-21', '14:00:00', '16:00:00', 1, 'en_attente', 0, '2025-01-07 09:30:00', NULL, NULL, '2025-01-07 09:30:00'),
(5, 6, 'changement', 'Préférence personnelle', '2025-01-15', '08:30:00', '10:30:00', 4, 'rejetee', 0, '2025-01-04 11:00:00', '2025-01-04 14:00:00', NULL, '2025-01-04 11:00:00'),
(6, 8, 'annulation', 'Absence d\'étudiants prévue', NULL, NULL, NULL, NULL, 'rejetee', 0, '2025-01-05 15:45:00', '2025-01-05 16:00:00', '2025-01-05 17:30:00', '2025-01-05 15:45:00'),
(7, 11, 'changement', 'En cours de rédaction...', '2025-01-22', '10:00:00', '12:00:00', 2, 'brouillon', 0, NULL, NULL, NULL, '2025-01-07 10:15:00');

INSERT INTO validations (demande_id, user_id, action, commentaire, ancien_statut, nouveau_statut, created_at) VALUES
(1, 4, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-02 09:15:00'),
(1, 2, 'validee', 'Salle A102 disponible. Aucun conflit détecté.', 'en_attente', 'validee_assistante', '2025-01-02 14:30:00'),
(1, 1, 'approuvee', 'Demande justifiée. Approuvée.', 'validee_assistante', 'approuvee', '2025-01-02 16:45:00'),
(2, 5, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-03 10:30:00'),
(2, 2, 'validee', 'Annulation validée. Formation institutionnelle prioritaire.', 'en_attente', 'validee_assistante', '2025-01-03 11:00:00'),
(2, 1, 'approuvee', 'Approuvé.', 'validee_assistante', 'approuvee', '2025-01-03 14:20:00'),
(3, 6, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-05 14:20:00'),
(3, 2, 'validee', 'Salle disponible. Pas de conflit avec d\'autres cours.', 'en_attente', 'validee_assistante', '2025-01-05 15:30:00'),
(4, 7, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-06 08:45:00'),
(4, 2, 'validee', 'Nouvelle date et salle confirmées.', 'en_attente', 'validee_assistante', '2025-01-06 09:30:00'),
(5, 8, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-06 16:30:00'),
(6, 9, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-07 07:00:00'),
(7, 4, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-07 09:30:00'),
(8, 5, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-04 11:00:00'),
(8, 2, 'rejetee', 'Motif insuffisant. Les préférences personnelles ne constituent pas un motif valable.', 'en_attente', 'rejetee', '2025-01-04 14:00:00'),
(9, 6, 'soumise', NULL, 'brouillon', 'en_attente', '2025-01-05 15:45:00'),
(9, 2, 'validee', 'Validation technique effectuée.', 'en_attente', 'validee_assistante', '2025-01-05 16:00:00'),
(9, 1, 'rejetee', 'Motif non recevable. L\'absence d\'étudiants doit être gérée autrement.', 'validee_assistante', 'rejetee', '2025-01-05 17:30:00');

INSERT INTO pieces_jointes (demande_id, nom_original, nom_fichier, type_mime, taille, chemin, created_at) VALUES
(1, 'invitation_conference.pdf', 'inv_conf_20250102091500.pdf', 'application/pdf', 245678, 'storage/uploads/1/inv_conf_20250102091500.pdf', '2025-01-02 09:15:00'),
(2, 'convocation_formation.pdf', 'conv_form_20250103103000.pdf', 'application/pdf', 189234, 'storage/uploads/2/conv_form_20250103103000.pdf', '2025-01-03 10:30:00'),
(3, 'planning_reunion.pdf', 'plan_reu_20250105142000.pdf', 'application/pdf', 123456, 'storage/uploads/3/plan_reu_20250105142000.pdf', '2025-01-05 14:20:00'),
(5, 'certificat_medical.pdf', 'cert_med_20250106163000.pdf', 'application/pdf', 98765, 'storage/uploads/5/cert_med_20250106163000.pdf', '2025-01-06 16:30:00'),
(5, 'ordonnance.jpg', 'ordo_20250106163100.jpg', 'image/jpeg', 456789, 'storage/uploads/5/ordo_20250106163100.jpg', '2025-01-06 16:31:00'),
(6, 'avis_greve.pdf', 'avis_greve_20250107070000.pdf', 'application/pdf', 156789, 'storage/uploads/6/avis_greve_20250107070000.pdf', '2025-01-07 07:00:00');

INSERT INTO notifications (user_id, demande_id, titre, message, type, lue, created_at) VALUES
(4, 1, 'Demande approuvée', 'Votre demande de changement pour le cours de Programmation Web a été approuvée.', 'success', 1, '2025-01-02 16:45:00'),
(5, 2, 'Demande approuvée', 'Votre demande d\'annulation a été approuvée.', 'success', 1, '2025-01-03 14:20:00'),
(5, 8, 'Demande rejetée', 'Votre demande de changement a été rejetée. Motif insuffisant.', 'danger', 1, '2025-01-04 14:00:00'),
(6, 3, 'Demande validée', 'Votre demande a été validée par l\'assistante et est en attente d\'approbation du directeur.', 'info', 0, '2025-01-05 15:30:00'),
(6, 9, 'Demande rejetée', 'Votre demande d\'annulation a été rejetée par le directeur.', 'danger', 1, '2025-01-05 17:30:00'),
(7, 4, 'Demande validée', 'Votre demande a été validée par l\'assistante. En attente d\'approbation finale.', 'info', 0, '2025-01-06 09:30:00'),
(8, 5, 'Demande enregistrée', 'Votre demande a bien été enregistrée et est en cours de traitement.', 'info', 0, '2025-01-06 16:30:00'),
(9, 6, 'Demande enregistrée', 'Votre demande d\'annulation a été enregistrée.', 'info', 0, '2025-01-07 07:00:00'),
(2, 5, 'Nouvelle demande urgente', 'Le Prof. Ahmed Benjelloun a soumis une demande urgente de changement.', 'warning', 0, '2025-01-06 16:30:00'),
(2, 6, 'Nouvelle demande urgente', 'Le Prof. Sofia Chakir a soumis une demande urgente d\'annulation.', 'warning', 0, '2025-01-07 07:00:00'),
(2, 7, 'Nouvelle demande', 'Le Prof. Mohammed Tazi a soumis une nouvelle demande.', 'info', 0, '2025-01-07 09:30:00'),
(1, 3, 'Demande à approuver', 'Une demande validée par l\'assistante nécessite votre approbation.', 'warning', 0, '2025-01-05 15:31:00'),
(1, 4, 'Demande à approuver', 'Une nouvelle demande validée par l\'assistante nécessite votre approbation.', 'warning', 0, '2025-01-06 09:31:00');

-- =====================================================
-- COMPTES DE CONNEXION
-- Mot de passe pour TOUS : password123
-- =====================================================
-- Directeur     : hassan.alami@eidia.ma
-- Assistante    : fatima.bennani@eidia.ma
-- Assistante 2  : samira.mansouri@eidia.ma
-- Professeur    : mohammed.tazi@eidia.ma
-- Professeur    : nadia.elamrani@eidia.ma
-- Professeur    : karim.ziani@eidia.ma
-- Professeur    : leila.idrissi@eidia.ma
-- Professeur    : ahmed.benjelloun@eidia.ma
-- Professeur    : sofia.chakir@eidia.ma
-- =====================================================

SELECT 'Base de données installée avec succès !' AS message;
