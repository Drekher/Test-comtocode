# Test ComtoCode

Test pour les développeurs symfony (4 h)

## Fonctionnalités

Le présent outil a pour objectif de permettre aux utilisateurs connectés d'accéder à un formulaire de contact. 


### Technologies

Symfony 4.2
```
FOS User Bundle 
```
Twig 
```
Doctrine DBAL

### Installation

Installer les dépendances via
composer install


Modifier le fichier. env et la partie DATABASE_URL (avec les informations de connexion à la base de données).  
php bin/console doctrine:database:create

Configurer la database grace aux entités crées.
php bin/console make:migration
php bin/console doctrine:migrations:migrate



