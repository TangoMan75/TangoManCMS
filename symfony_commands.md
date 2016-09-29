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


Bundle
------

php bin/console generate:bundle


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


Run Server
----------

php bin/console server:run


Drop
----

php bin/console doctrine:shema:drop --force


Controllers
-----------

php bin/console generate:controller


Forms
-----

php bin/console generate:form


Git
---

git reset --hard origin/master
git pull origin master
git clone git@github.com:TangoMan75/livredor.git .


Composer
--------

composer update
