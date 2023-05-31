# corppooling-back

### Le covoiturage professionnel !

Api : <a href="https://corppooling.colvrg.com/api" target="_blank">corppooling.colvrg.com/api</a>

Documentation : <a href="https://corppooling.colvrg.com/docs" target="_blank">corppooling.colvrg.com/docs</a>

### Technologies

- Symfony 6
- Api platform

## Installation & Déploiement

### Prérequis

- PHP 8.1

### Installation

1. Cloner le projet
```bash
git clone https://github.com/Corppooling/corppooling-back.git
```

2. Créer le fichier d'environnement et y insérer vos valeurs
```bash
cd corppooling-back
cp .env.example .env
```

3. Installer les dépendances
```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### - Développement

```bash
php bin/console server:start
```

Pour les fixtures :

```bash
php bin/console doctrine:fixtures:load
```

#### - Production

Le virtual host de apache ou nginx doit pointer sur le dossier public du repository

#### A noter

L'ensemble des commandes sont disponibles en tapant `php bin/console`