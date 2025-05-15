# 🏥 SanareNovo - Application Web de Gestion de Clinique (Symfony)

## 📖 Description du Projet

SanareNovo est une application web développée avec **Symfony** permettant la gestion complète d’un établissement médical.  
Ce projet a été réalisé dans le cadre du cours **PIDEV** à **Esprit School of Engineering**. L’objectif est de désengorger les hôpitaux tunisiens en fournissant une plateforme numérique moderne pour la gestion :

- des utilisateurs,
- des services,
- des consultations,
- des équipements,
- des blogs,
- et des recrutements.

Ce backend est interconnecté avec une application JavaFX via une **base de données partagée**.

### Fonctionnalités principales :

- 🧑‍⚕️ Gestion multi-utilisateurs (Patient, Médecin, Administrateur, RH, Technicien, Coordinateur)
- 📅 Prise de rendez-vous et suivi des consultations
- 📁 Dossiers médicaux des patients
- 🏥 Gestion des services, salles et équipements
- 📰 Blog médical avec commentaires
- 💼 Gestion des recrutements et des candidatures
- 🔐 Sécurité, authentification, rôles et autorisations

---

## 📑 Table des Matières

- [📖 Description du Projet](#-description-du-projet)
- [🧰 Technologies Utilisées](#-technologies-utilisées)
- [📁 Structure du Répertoire](#-structure-du-répertoire)
- [📦 Installation](#-installation)
- [⚙️ Utilisation](#️-utilisation)
- [🔗 Connexion à la Base de Données](#-connexion-à-la-base-de-données)
- [👥 Contribution](#-contribution)
- [🛡️ Licence](#️-licence)
- [🙏 Remerciements](#-remerciements)
- [🏷️ Topics GitHub](#-topics-github)

---

## 🧰 Technologies Utilisées

- **Langage :** PHP 8.2+
- **Framework :** Symfony 6.x
- **ORM :** Doctrine
- **Base de données :** MySQL (partagée avec l'application JavaFX)
- **Authentification :** Symfony Security + reCAPTCHA v3
- **Emailing :** Symfony Mailer (Gmail ou Mailtrap)
- **Notifications temps réel :** Pusher
- **API météo :** OpenWeatherMap
- **SMS & Téléphonie :** Twilio
- **Frontend :** Twig + Bootstrap 5
- **IDE :** VS Code

---

## 📁 Structure du Répertoire

/config
/src
├── Controller
├── Entity
├── Form
├── Repository
├── Service
/public
/templates
/tests

---

## 📦 Installation

1. Cloner le projet :
   ```bash
   git clone https://github.com/aymenZargouni/PI-SanareNovo-Symfony.git
   cd PI-SanareNovo-Symfony

2. Installer les dépendances :
  ```bash
  composer install
  npm install
  npm run build
  ```
3. Configurer la base de données :
   Modifier les informations de connexion
```bash
DATABASE_URL="mysql://user:password@127.0.0.1:3306/sanarenovo"
```
4. Créer la base de données et les tables :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
5. Lancer le serveur :

```bash
symfony server:start
```

## ⚙️ Utilisation

Accès à l’espace admin pour la gestion des entités (utilisateurs, services, salles…)

Accès patient pour consulter son dossier médical ou prendre un rendez-vous

Accès médecin pour gérer les consultations

Interface RH pour les recrutements

Blog public et commentaires

Emails automatiques lors des actions clés (inscription, consultation…)

## 🔗 Connexion à la Base de Données
Le projet Symfony est connecté à une base de données MySQL partagée avec l’application JavaFX.
Il est important que les deux projets soient synchronisés au niveau du schéma de base de données et des identifiants.

## 👥 Contribution
Membres de l’équipe :
Aymen Zargouni – Gestion des utilisateurs

Hichem Mhadhbi – Gestion des blogs & commentaires

Mahdy Chaouechi – Gestion des services & salles

Youssef Tarhouni – Gestion des consultations

Sabrine Zaddem – Gestion des équipements & réclamations

Takoua Hichri – Gestion des recrutements

N’hésitez pas à ouvrir une issue ou une pull request pour contribuer.

## 🛡️ Licence
Ce projet est distribué sous la licence MIT.
Consultez le fichier LICENSE pour plus d’informations.

## 🙏 Remerciements
Ce projet a été réalisé sous la supervision de Karray Gargouri
à Esprit School of Engineering, dans le cadre du module PIDEV 3A.
Merci à toute l’équipe pédagogique pour leur accompagnement.

## 🏷️ Topics GitHub
symfony gestion-clinique api-platform doctrine twig pidev esprit-school-of-engineering backend-app php mysql
