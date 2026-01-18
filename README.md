# 🎓 GestioSeances - Gestion des Demandes de Changement/Annulation de Séances

> Application web de workflow pour la gestion des modifications d'emploi du temps universitaire + Infrastructure DNS & Serveur Web

---

## 👨‍💻 Réalisé par

| | | | |
|:---:|:---:|:---:|:---:|
| **Iliass Zarquan** | **Jaafar Ouazzani Chahdi** | **Aymane Drissi Bourhanbour** | **Aya Sefri** |

**Encadré par : Pr. Amamou Ahmed**

*Université Euromed de Fès - EIDIA - Filière Cybersécurité*

*Année universitaire 2025-2026*

---

## 📋 Table des Matières

- [Présentation du Projet](#-présentation-du-projet)
- [Problématique](#-problématique)
- [Fonctionnalités Principales](#-fonctionnalités-principales)
- [Architecture Technique](#-architecture-technique)
- [Infrastructure DNS & Web](#-infrastructure-dns--web)
- [Structure du Projet](#-structure-du-projet)
- [Installation](#-installation)
- [Déploiement sur Serveur](#-déploiement-sur-serveur)
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

Le projet inclut également le déploiement d'une **infrastructure complète** avec serveur DNS (BIND9) et serveur web sécurisé (Apache2 + SSL).

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
| **Backend** | PHP 8.x (Architecture MVC) |
| **Base de données** | MySQL 8.x |
| **Serveur Web** | Apache2 + mod_ssl + mod_rewrite |
| **Serveur DNS** | BIND9 |
| **Sécurité** | SSL/TLS, bcrypt, tokens CSRF, PDO |
| **Virtualisation** | VirtualBox (Ubuntu 25.10) |

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

## 🌐 Infrastructure DNS & Web

### Architecture Réseau

```
┌─────────────────────────────────────────────────────────────┐
│                  Réseau 192.168.0.0/24                      │
│                                                             │
│   ┌─────────────────────────────────────────────────────┐   │
│   │           Serveur Ubuntu 25.10                      │   │
│   │              192.168.0.116                          │   │
│   │                                                     │   │
│   │   ┌─────────┐  ┌─────────┐  ┌─────────┐            │   │
│   │   │  BIND9  │  │ Apache2 │  │  MySQL  │            │   │
│   │   │  (DNS)  │  │  (Web)  │  │  (BDD)  │            │   │
│   │   └─────────┘  └─────────┘  └─────────┘            │   │
│   │                                                     │   │
│   └─────────────────────────────────────────────────────┘   │
│                           ▲                                  │
│              DNS + HTTPS  │                                  │
│                           │                                  │
│   ┌───────────┐      ┌───────────┐                          │
│   │ VM Client │      │ Mac (Hôte)│                          │
│   └───────────┘      └───────────┘                          │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Configuration DNS (BIND9)

| Domaine | Type | Valeur |
|---------|------|--------|
| gestio.local | A | 192.168.0.116 |
| www.gestio.local | A | 192.168.0.116 |
| ns1.gestio.local | A | 192.168.0.116 |
| db.gestio.local | A | 192.168.0.116 |

### Configuration Web (Apache2)

- **VirtualHost HTTP** : Redirection vers HTTPS
- **VirtualHost HTTPS** : Port 443 avec SSL/TLS
- **Certificat** : Auto-signé (365 jours)
- **DocumentRoot** : `/var/www/gestio.local/public`

---

## 📁 Structure du Projet

```
GestioSeances/
│
├── 📁 app/
│   ├── 📁 controllers/          # Contrôleurs MVC
│   │   ├── AuthController.php
│   │   ├── DemandeController.php
│   │   ├── AdminController.php
│   │   ├── NotificationController.php
│   │   └── StatsController.php
│   │
│   ├── 📁 models/               # Modèles (accès BDD)
│   │   ├── User.php
│   │   ├── Demande.php
│   │   ├── Notification.php
│   │   ├── Validation.php
│   │   └── PieceJointe.php
│   │
│   ├── 📁 views/                # Vues (templates)
│   │   ├── 📁 layouts/
│   │   │   └── main.php
│   │   ├── 📁 auth/
│   │   │   ├── login.php
│   │   │   ├── forgot-password.php
│   │   │   └── reset-password.php
│   │   ├── 📁 demandes/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── show.php
│   │   │   └── edit.php
│   │   ├── 📁 admin/
│   │   │   ├── users.php
│   │   │   └── settings.php
│   │   └── 📁 stats/
│   │       └── index.php
│   │
│   └── 📁 core/                 # Classes utilitaires
│       ├── Database.php
│       ├── Router.php
│       ├── Session.php
│       └── Security.php
│
├── 📁 public/                   # Fichiers accessibles publiquement
│   ├── index.php               # Point d'entrée unique
│   ├── .htaccess               # Règles de réécriture
│   ├── 📁 css/
│   └── 📁 js/
│
├── 📁 storage/                  # Fichiers uploadés
│   ├── 📁 uploads/
│   └── 📁 logs/
│
├── 📁 database/
│   └── schema_final.sql        # Structure de la BDD
│
├── config.php                  # Configuration
└── README.md
```

---

## ⚙️ Installation

### Prérequis

- PHP 8.0 ou supérieur
- MySQL 8.0 ou supérieur
- Serveur Apache avec mod_rewrite et mod_ssl
- Ubuntu 25.10 (ou distribution similaire)

### Installation Locale (XAMPP/WAMP)

```bash
# 1. Cloner le projet dans htdocs
git clone https://github.com/votre-equipe/gestio-seances.git
cd gestio-seances

# 2. Créer la base de données
mysql -u root -p < database/schema_final.sql

# 3. Configurer l'application
cp config.example.php config.php
# Éditer config.php avec vos paramètres

# 4. Configurer les permissions
chmod 755 storage/uploads
chmod 755 storage/logs
```

---

## 🚀 Déploiement sur Serveur

### 1. Installation des paquets

```bash
# Mise à jour du système
sudo apt update && sudo apt upgrade -y

# Installation LAMP
sudo apt install -y apache2 php php-mysql mysql-server

# Installation BIND9
sudo apt install -y bind9 bind9utils dnsutils

# Activation des modules Apache
sudo a2enmod rewrite ssl headers
```

### 2. Configuration DNS (BIND9)

**Fichier `/etc/bind/named.conf.options` :**
```
options {
    directory "/var/cache/bind";
    listen-on { 127.0.0.1; 192.168.0.116; };
    allow-query { localhost; 192.168.0.0/24; };
    forwarders { 8.8.8.8; 8.8.4.4; };
    forward only;
};
```

**Fichier `/etc/bind/zones/db.gestio.local` :**
```
$TTL 86400
@ IN SOA ns1.gestio.local. admin.gestio.local. (
        2025011401 3600 1800 604800 86400 )
@ IN NS  ns1.gestio.local.
ns1 IN A 192.168.0.116
www IN A 192.168.0.116
```

### 3. Configuration SSL

```bash
# Création du certificat auto-signé
sudo mkdir -p /etc/apache2/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/apache2/ssl/gestio.local.key \
    -out /etc/apache2/ssl/gestio.local.crt \
    -subj "/C=MA/ST=Fes/O=EIDIA/CN=www.gestio.local"
```

### 4. VirtualHost Apache

**Fichier `/etc/apache2/sites-available/gestio.local-ssl.conf` :**
```apache
<VirtualHost *:443>
    ServerName www.gestio.local
    ServerAlias gestio.local
    DocumentRoot /var/www/gestio.local/public

    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/gestio.local.crt
    SSLCertificateKeyFile /etc/apache2/ssl/gestio.local.key

    <Directory /var/www/gestio.local/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gestio_error.log
    CustomLog ${APACHE_LOG_DIR}/gestio_access.log combined
</VirtualHost>
```

### 5. Déploiement de l'application

```bash
# Copier les fichiers
sudo mkdir -p /var/www/gestio.local
sudo cp -r /chemin/vers/GestioSeances/* /var/www/gestio.local/

# Permissions
sudo chown -R www-data:www-data /var/www/gestio.local
sudo chmod -R 755 /var/www/gestio.local
sudo chmod -R 775 /var/www/gestio.local/storage

# Activer le site
sudo a2ensite gestio.local-ssl.conf
sudo systemctl restart apache2
```

### 6. Configuration Base de Données

```bash
# Connexion MySQL
sudo mysql -u root

# Création de la base et de l'utilisateur
CREATE DATABASE gestioseances CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'gestio_admin'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON gestioseances.* TO 'gestio_admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import du schéma
mysql -u gestio_admin -p gestioseances < /var/www/gestio.local/database/schema_final.sql
```

### 7. Test du déploiement

```bash
# Test DNS
dig @192.168.0.116 www.gestio.local +short
# Résultat attendu: 192.168.0.116

# Test Web
curl -k https://www.gestio.local
```

---

## 🔧 Configuration

Éditer le fichier `config.php` :

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestioseances');
define('DB_USER', 'gestio_admin');
define('DB_PASS', 'password123');

define('APP_NAME', 'GestioSeances');
define('APP_URL', 'https://www.gestio.local');

// Rôles
define('ROLE_PROFESSEUR', 'professeur');
define('ROLE_ASSISTANTE', 'assistante');
define('ROLE_DIRECTEUR', 'directeur');

// Statuts des demandes
define('STATUT_EN_ATTENTE', 'en_attente');
define('STATUT_VALIDEE_ASSISTANTE', 'validee_assistante');
define('STATUT_APPROUVEE', 'approuvee');
define('STATUT_REJETEE', 'rejetee');
```

---

## 👥 Équipe de Développement

| Membre | Rôle | Modules Assignés |
|--------|------|------------------|
| **Iliass Zarquan** | Lead Backend & Infrastructure | Authentification, Sécurité, Core, DNS, Serveur Web |
| **Jaafar Ouazzani Chahdi** | Backend Developer | Demandes, Validations, Notifications, Workflow |
| **Aymane Drissi Bourhanbour** | Full Stack Developer | Interfaces utilisateur, Planning, Calendrier, Frontend |
| **Aya Sefri** | Backend & Documentation | Administration, Statistiques, Base de données, Documentation |

### Encadrant
**Pr. Amamou Ahmed** - Université Euromed de Fès - EIDIA - Module Administration Système et Réseau

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
| **Communication** | HTTPS obligatoire (SSL/TLS) |
| **DNS** | allow-transfer none, restriction des requêtes |

---

## 📦 Livrables

- [x] Code source PHP (architecture MVC)
- [x] Script SQL (schema + données de test)
- [x] Infrastructure DNS (BIND9)
- [x] Serveur Web sécurisé (Apache2 + SSL)
- [x] Configuration VirtualHost
- [x] Rapport de projet (LaTeX)
- [x] Présentation (Beamer)
- [x] README complet

---

## 🧪 Tests

### Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Professeur | prof@uemf.ac.ma | password123 |
| Assistante | assistante@uemf.ac.ma | password123 |
| Directeur | directeur@uemf.ac.ma | password123 |

### URLs de test

- Application : `https://www.gestio.local`
- Test DNS : `dig @192.168.0.116 www.gestio.local`

---

## 📝 Conventions de Code

- **Nommage** : camelCase pour variables/fonctions, PascalCase pour classes
- **Indentation** : 4 espaces
- **Commentaires** : PHPDoc pour toutes les fonctions publiques
- **Commits** : Messages clairs et descriptifs en français

---

## 📄 Licence

Projet académique - Université Euromed de Fès - EIDIA - Cybersécurité

**Année universitaire 2025-2026**

---

<div align="center">

**🎓 GestioSeances**

*Application développée dans le cadre du module Administration Système et Réseau*

**Université Euromed de Fès - EIDIA - Cybersécurité**

Janvier 2026

</div>
