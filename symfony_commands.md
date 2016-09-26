Symfony Commands
================

Create database
---------------

php bin/console doctrine:database:create
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --dump-sql -v
php bin/console doctrine:schema:update --force

Clear cache
-----------

php bin/console cache:clear
php bin/console cache:clear -e=prod

Install fixture bundle
----------------------

composer require --dev doctrine/doctrine-fixtures-bundle

Load fixtures
-------------

php bin/console doctrine:fixtures:load
php bin/console doctrine:fixtures:load --append

Entities
--------

php bin/console doctrine:generate:entity
php bin/console doctrine:generate:entities

Controllers
-----------

php bin/console generate:controller

Generating crud controller and views based on entity
----------------------------------------------------

php bin/console generate:doctrine:crud

