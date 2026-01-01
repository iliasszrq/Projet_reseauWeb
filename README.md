# 🎓 GestioSeances - Gestion des Demandes de Changement/Annulation de Séances

> Application web de workflow pour la gestion des modifications d'emploi du temps universitaire

---

## 📋 Table des Matières

- [Présentation du Projet](#-présentation-du-projet)
- [Problématique](#-problématique)
- [Fonctionnalités Principales](#-fonctionnalités-principales)
- [Architecture Technique](#-architecture-technique)
- [Structure du Projet](#-structure-du-projet)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Équipe de Développement](#-équipe-de-développement)
- [Workflow de Validation](#-workflow-de-validation)
- [Sécurité](#-sécurité)
- [Livrables](#-livrables)

---

## 🎯 Présentation du Projet

**GestioSeances** est une application web développée en PHP/MySQL permettant de centraliser, suivre et valider les demandes de changement ou d'annulation de séances de cours dans un établissement universitaire.

L'application implémente un workflow hiérarchique impliquant trois acteurs :
- 👨‍🏫 **Professeur** : Initiateur des demandes
- 👩‍💼 **Assistante Administrative** : Première validation (faisabilité technique)
- 👔 **Directeur** : Approbation finale

---

## ❓ Problématique

La gestion actuelle des modifications d'emploi du temps par email ou papier engendre :

| Problème | Impact |
|----------|--------|
| Perte de traçabilité | Impossible de suivre l'historique des demandes |
| Délais incertains | Pas de visibilité sur le temps de traitement |
| Conflits non détectés | Doubles réservations de salles |
| Communication inefficace | Étudiants non informés des changements |

**Notre solution** : Une plateforme centralisée avec workflow automatisé et notifications en temps réel.

---

## ✨ Fonctionnalités Principales

### Pour le Professeur
- ✅ Création de demandes (changement ou annulation)
- ✅ Upload de pièces justificatives
- ✅ Suivi en temps réel du statut des demandes
- ✅ Notifications par email et in-app
- ✅ Gestion du profil et mot de passe

### Pour l'Assistante Administrative
- ✅ File d'attente des demandes avec indicateurs d'urgence
- ✅ Vérification automatique des conflits de planning
- ✅ Validation/Rejet avec commentaires
- ✅ Vue calendrier des emplois du temps
- ✅ Statistiques de traitement

### Pour le Directeur
- ✅ Approbation finale des demandes pré-validées
- ✅ Gestion des utilisateurs (CRUD)
- ✅ Paramétrage de l'application
- ✅ Tableau de bord exécutif avec graphiques
- ✅ Génération de rapports PDF/Excel

---

## 🏗 Architecture Technique

### Stack Technologique

| Couche | Technologies |
|--------|-------------|
| **Frontend** | HTML5, CSS3, JavaScript, Bootstrap 5 |
| **Backend** | PHP 8+ (Architecture MVC) |
| **Base de données** | MySQL 8.0 |
| **Sécurité** | Sessions PHP, bcrypt, tokens CSRF |
| **Emails** | PHPMailer |

### Architecture MVC

```
┌─────────────────────────────────────────────────────────┐
│                      NAVIGATEUR                         │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    INDEX.PHP                            │
│                   (Front Controller)                    │
└─────────────────────────┬───────────────────────────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
┌─────────────┐   ┌─────────────┐   ┌─────────────┐
│  CONTROLLER │   │    MODEL    │   │    VIEW     │
│             │◄──│             │──►│             │
│ - Auth      │   │ - User      │   │ - Templates │
│ - Demande   │   │ - Demande   │   │ - Layouts   │
│ - Planning  │   │ - Seance    │   │ - Partials  │
│ - Admin     │   │ - Notif     │   │             │
└─────────────┘   └─────────────┘   └─────────────┘
                          │
                          ▼
              ┌───────────────────────┐
              │     BASE DE DONNÉES   │
              │        MySQL          │
              └───────────────────────┘
```

---

## 📁 Structure du Projet

```
GestioSeances/
│
├── 📁 app/
│   ├── 📁 controllers/          # Contrôleurs MVC
│   │   ├── AuthController.php
│   │   ├── DemandeController.php
│   │   ├── PlanningController.php
│   │   ├── NotificationController.php
│   │   ├── AdminController.php
│   │   └── StatistiqueController.php
│   │
│   ├── 📁 models/               # Modèles (accès BDD)
│   │   ├── User.php
│   │   ├── Demande.php
│   │   ├── Seance.php
│   │   ├── Salle.php
│   │   ├── Matiere.php
│   │   ├── Notification.php
│   │   ├── Validation.php
│   │   └── PieceJointe.php
│   │
│   ├── 📁 views/                # Vues (templates)
│   │   ├── 📁 layouts/
│   │   │   ├── main.php
│   │   │   └── auth.php
│   │   ├── 📁 auth/
│   │   │   ├── login.php
│   │   │   ├── forgot-password.php
│   │   │   └── reset-password.php
│   │   ├── 📁 professeur/
│   │   │   ├── dashboard.php
│   │   │   ├── demandes/
│   │   │   └── profil.php
│   │   ├── 📁 assistante/
│   │   │   ├── dashboard.php
│   │   │   ├── file-attente.php
│   │   │   └── planning.php
│   │   ├── 📁 directeur/
│   │   │   ├── dashboard.php
│   │   │   ├── utilisateurs/
│   │   │   └── parametres.php
│   │   └── 📁 partials/
│   │       ├── header.php
│   │       ├── sidebar.php
│   │       ├── footer.php
│   │       └── notifications.php
│   │
│   └── 📁 core/                 # Classes utilitaires
│       ├── Database.php
│       ├── Router.php
│       ├── Session.php
│       ├── Validator.php
│       ├── Mailer.php
│       └── Security.php
│
├── 📁 public/                   # Fichiers accessibles publiquement
│   ├── index.php               # Point d'entrée unique
│   ├── 📁 css/
│   │   ├── style.css
│   │   └── dashboard.css
│   ├── 📁 js/
│   │   ├── app.js
│   │   ├── calendar.js
│   │   └── notifications.js
│   └── 📁 images/
│       └── logo.png
│
├── 📁 storage/                  # Fichiers uploadés (hors webroot)
│   ├── 📁 uploads/
│   └── 📁 logs/
│
├── 📁 database/
│   ├── schema.sql              # Structure de la BDD
│   ├── seed.sql                # Données de test
│   └── migrations/
│
├── 📁 docs/                     # Documentation
│   ├── 📁 uml/
│   │   ├── use-case.png
│   │   ├── class-diagram.png
│   │   └── sequence-diagrams/
│   ├── 📁 maquettes/
│   ├── guide-utilisateur.pdf
│   └── documentation-technique.pdf
│
├── 📁 tests/                    # Tests
│   ├── scenarios-fonctionnels.md
│   └── rapport-securite.md
│
├── .htaccess
├── config.php
├── composer.json
└── README.md
```

---

## ⚙️ Installation

### Prérequis

- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Serveur Apache avec mod_rewrite
- Composer (gestionnaire de dépendances PHP)

### Étapes d'Installation

```bash
# 1. Cloner le repository
git clone https://github.com/votre-equipe/gestio-seances.git
cd gestio-seances

# 2. Installer les dépendances
composer install

# 3. Créer la base de données
mysql -u root -p < database/schema.sql

# 4. Insérer les données de test
mysql -u root -p gestio_seances < database/seed.sql

# 5. Configurer l'application
cp config.example.php config.php
# Éditer config.php avec vos paramètres

# 6. Configurer les permissions
chmod 755 storage/uploads
chmod 755 storage/logs
```

---

## 🔧 Configuration

Éditer le fichier `config.php` :

```php
<?php
return [
    // Base de données
    'db' => [
        'host' => 'localhost',
        'name' => 'gestio_seances',
        'user' => 'root',
        'pass' => 'votre_mot_de_passe'
    ],
    
    // Email (SMTP)
    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'user' => 'votre-email@gmail.com',
        'pass' => 'mot_de_passe_application'
    ],
    
    // Application
    'app' => [
        'name' => 'GestioSeances',
        'url' => 'http://localhost/gestio-seances',
        'debug' => true
    ]
];
```

---

## 👥 Équipe de Développement

| Membre | Rôle | Modules Assignés |
|--------|------|------------------|
| **Développeur 1** | Lead Backend | Authentification, Sécurité, Core |
| **Développeur 2** | Backend | Demandes, Validations, Notifications |
| **Développeur 3** | Frontend + Backend | Interfaces, Planning, Calendrier |
| **Développeur 4** | Backend + Documentation | Administration, Stats, BDD, Docs |

---

## 🔄 Workflow de Validation

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  PROFESSEUR  │     │  ASSISTANTE  │     │  DIRECTEUR   │     │   RÉSULTAT   │
└──────┬───────┘     └──────┬───────┘     └──────┬───────┘     └──────┬───────┘
       │                    │                    │                    │
       │ Crée demande       │                    │                    │
       ├───────────────────►│                    │                    │
       │                    │                    │                    │
       │                    │ Vérifie conflits   │                    │
       │                    │ Valide/Rejette     │                    │
       │                    ├───────────────────►│                    │
       │                    │                    │                    │
       │                    │                    │ Approuve/Rejette   │
       │                    │                    ├───────────────────►│
       │                    │                    │                    │
       │◄────────────────────────────────────────────────────────────┤
       │              Notification du résultat                       │
       │                                                              │
```

### Statuts Possibles

| Statut | Description | Couleur |
|--------|-------------|---------|
| `brouillon` | Demande en cours de rédaction | ⚪ Gris |
| `en_attente` | Soumise, en attente de l'assistante | 🟡 Jaune |
| `validee_assistante` | Validée, en attente du directeur | 🔵 Bleu |
| `approuvee` | Approuvée par le directeur | 🟢 Vert |
| `rejetee` | Rejetée (assistante ou directeur) | 🔴 Rouge |
| `annulee` | Annulée par le professeur | ⚫ Noir |

---

## 🔒 Sécurité

### Mesures Implémentées

| Menace | Protection |
|--------|------------|
| **Mots de passe** | Hashage bcrypt (coût 12) |
| **Brute Force** | Blocage après 5 tentatives (15 min) |
| **XSS** | Échappement `htmlspecialchars()` + CSP |
| **CSRF** | Tokens uniques par formulaire |
| **SQL Injection** | Requêtes préparées PDO exclusivement |
| **Upload malveillant** | Vérification MIME, limite 5 Mo, renommage |
| **Session Hijacking** | Régénération ID, vérification IP/UA |

---

## 📦 Livrables

- [x] Code source PHP (architecture MVC)
- [x] Script SQL (schema + données de test)
- [ ] Documentation technique
- [ ] Diagrammes UML
- [ ] Guide utilisateur
- [ ] Maquettes des interfaces
- [ ] Rapport de tests

---

## 📅 Planning Prévisionnel

| Tâches |
|--------|
|Setup projet, BDD, authentification |
|Module demandes, upload fichiers |
|Module planning, calendrier |
|Module admin, notifications |
|Statistiques, rapports |
|Tests, documentation, déploiement |

---

## 📝 Conventions de Code

- **Nommage** : camelCase pour variables/fonctions, PascalCase pour classes
- **Indentation** : 4 espaces
- **Commentaires** : PHPDoc pour toutes les fonctions publiques
- **Commits** : Messages clairs et descriptifs en français

---

## 📄 Licence

Projet académique - EIDIA 2025

---

