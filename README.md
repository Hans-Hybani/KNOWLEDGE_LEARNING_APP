# KNOWLEDGE LEARNING APP

## Description
**KNOWLEDGE LEARNING APP** est une application Symfony conçue pour la gestion de l'apprentissage et du partage de connaissances. Ce projet permet aux utilisateurs de créer, gérer et suivre des cours et du contenu éducatif.

## Prérequis
Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- **PHP 8.0 ou plus récent**
- **Composer**
- **Symfony CLI**
- **MySQL** ou un autre système de gestion de base de données supporté par Doctrine

## Installation

### 1. Cloner le dépôt
Clonez le projet depuis GitHub en utilisant la commande suivante :

```bash
git clone https://github.com/Hans-Hybani/KNOWLEDGE_LEARNING_APP
cd KNOWLEDGE_LEARNING_APP

2. Installer les dépendances
Une fois dans le répertoire du projet, utilisez Composer pour installer toutes les dépendances :
composer install

3. Configurer les variables d'environnement
Créez un fichier .env en copiant le contenu du fichier .env.example :
Modifiez le fichier .env pour configurer les paramètres de connexion à la base de données. Assurez-vous que les informations sont correctes, par exemple :
DATABASE_URL="mysql://user:password@127.0.0.1:3306/knowledge_learning_app?serverVersion=5.7"

4. Créer la base de données
Créez la base de données en exécutant la commande suivante :

symfony console doctrine:database:create

5. Exécuter les migrations
Appliquez les migrations pour créer les tables nécessaires dans la base de données :
symfony console make:migration
symfony console doctrine:migrations:migrate

6. Charger les fixtures
Chargez les données de test (fixtures) dans la base de données en répondant yes à l'invite de confirmation :
symfony console doctrine:fixtures:load

7. Lancer le serveur Symfony
Démarrez le serveur Symfony pour accéder à l'application :
symfony server:start

Voici une synthèse avec toutes les commandes regroupées !!!!

git clone https://github.com/Hans-Hybani/KNOWLEDGE_LEARNING_APP
cd KNOWLEDGE_LEARNING_APP
composer install
cp .env.example .env
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
symfony server:start

