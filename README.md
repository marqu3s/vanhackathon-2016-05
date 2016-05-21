VanHackathon Axiom Zen Challenges
=================================

This project uses a [Yii 2](http://www.yiiframework.com/) Advanced Project Template.

There are 2 tiers:

* api - Code developed for challenge 1 (Mastermind game API)
* frontend - Code developed for challenge 2 (A frontend consuming the API of challenge 1) 

Inside of these 2 folders the most important part of code are inside the subfolders: config, controllers, models, modules and views

The rest of the folders are mostly framework folders. Here is an overview of the project folder:


DIRECTORY STRUCTURE
-------------------

```
api
    config/              contains api configurations
    controllers/         contains api controller classes
    modules/             contains api-specific modelules classes
    runtime/             contains files generated during runtime
    web/                 contains the entry script and Web resources
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both api and frontend
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
environments/            contains environment-based overrides
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
vendor/                  contains dependent 3rd-party packages
```


SETUP INSTRUCTIONS
------------------

This projects uses a MySQL database. The dump is included in the root folder - database_mysql.sql

After importing the dump, adjust the components['db'] configuration in common/config/main-local.php accordingly.
