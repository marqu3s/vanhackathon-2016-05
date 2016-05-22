VanHackathon Axiom Zen Challenges
=================================

Answer to the questions asked
-----------------------------

Q1. What was the hardest part?
R: Playing with socket.io library during the frontend challenge development. I've never used it so had to study it durting the hackathon.

Q2. If you could go back and give yourself advice at the beginning of the project, what would it be?
R: I think that the backend challenge went pretty good, but if I knew how hard it would be to do the
frontend challenge the way I tried to do it, I would advice myself to forget about it and focus just on the backend
to polish it to the maximum.

Q3. If you could change something about this challenge, what would it be? 
R: I would probably team up with a frontend developer. I was affraid of doing it this time not because I don't like to work as a team,
but because I was affraid to didn't have time to finish my part on the project and harm the others on the team.


About the project
-----------------

This project uses a [Yii 2](http://www.yiiframework.com/) Advanced Project Template.

It's hosted at Github: https://github.com/marqu3s/vanhackathon-2016-05

There are 2 tiers:

* api - Code developed for challenge 1 (Mastermind game API)
* frontend - Code developed for challenge 2 (A frontend consuming the API of challenge 1) 

Inside of these 2 folders the most important part of code are inside the subfolders: config, controllers, models, modules and views

The rest of the folders are mostly framework folders. Here is an overview of the project folder:


Directory Structure
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


Setup Instructions
------------------

This projects uses a MySQL database. The dump is included in the root folder - `database_mysql.sql`

After importing the dump, adjust the `components['db']` configuration in `common/config/main.php` accordingly.

Install the composer depencies by running: `composer update` on the root folder.

Set document roots of your web server:

* for api `/path/to/project/api/web/` and using a local URL of your choice, like http://api.mastermind.dev/
* for frontend `/path/to/project/frontend/web/` and using a local URL of your choice, like http://mastermind.dev/

For Apache it could be the following:

```
<VirtualHost *:80>
   ServerName mastermind.dev
   DocumentRoot "/path/to/project/frontend/web/"

   <Directory "/path/to/project/frontend/web/">
       # use mod_rewrite for pretty URL support
       RewriteEngine on
       # If a directory or a file exists, use the request directly
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       # Otherwise forward the request to index.php
       RewriteRule . index.php

       # use index.php as index file
       DirectoryIndex index.php

       # ...other settings...
   </Directory>
</VirtualHost>

<VirtualHost *:80>
   ServerName api.mastermind.dev
   DocumentRoot "/path/to/project/api/web/"

   <Directory "/path/to/project/api/web/">
       # use mod_rewrite for pretty URL support
       RewriteEngine on
       # If a directory or a file exists, use the request directly
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       # Otherwise forward the request to index.php
       RewriteRule . index.php

       # use index.php as index file
       DirectoryIndex index.php

       # ...other settings...
   </Directory>
</VirtualHost>
```

For nginx:

```
server {
   charset utf-8;
   client_max_body_size 128M;

   listen 80; ## listen for ipv4
   #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

   server_name mastermind.dev;
   root        /path/to/project/frontend/web/;
   index       index.php;

   access_log  /path/to/project/log/frontend-access.log;
   error_log   /path/to/project/log/frontend-error.log;

   location / {
       # Redirect everything that isn't a real file to index.php
       try_files $uri $uri/ /index.php$is_args$args;
   }

   # uncomment to avoid processing of calls to non-existing static files by Yii
   #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
   #    try_files $uri =404;
   #}
   #error_page 404 /404.html;

   location ~ \.php$ {
       include fastcgi_params;
       fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       fastcgi_pass   127.0.0.1:9000;
       #fastcgi_pass unix:/var/run/php5-fpm.sock;
       try_files $uri =404;
   }

   location ~ /\.(ht|svn|git) {
       deny all;
   }
}

server {
   charset utf-8;
   client_max_body_size 128M;

   listen 80; ## listen for ipv4
   #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

   server_name api.mastermind.dev;
   root        /path/to/project/api/web/;
   index       index.php;

   access_log  /path/to/project/log/api-access.log;
   error_log   /path/to/project/log/api-error.log;

   location / {
       # Redirect everything that isn't a real file to index.php
       try_files $uri $uri/ /index.php$is_args$args;
   }

   # uncomment to avoid processing of calls to non-existing static files by Yii
   #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
   #    try_files $uri =404;
   #}
   #error_page 404 /404.html;

   location ~ \.php$ {
       include fastcgi_params;
       fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       fastcgi_pass   127.0.0.1:9000;
       #fastcgi_pass unix:/var/run/php5-fpm.sock;
       try_files $uri =404;
   }

   location ~ /\.(ht|svn|git) {
       deny all;
   }
}
```

Adjust the logs location to your needs.

Don't forget to change the hosts file to point the domain to your server.
                
* Windows: c:\Windows\System32\Drivers\etc\hosts
* Linux: /etc/hosts
                
Add the following lines:

* 127.0.0.1     mastermind.dev
* 127.0.0.1     api.mastermind.dev



Challenge 1: API for playing Mastermind Game
--------------------------------------------

The database and the API were built from the begining having multiplayer games in mind.

A documentation is available at Apiary so you can have an overview of its endpoints and supported requests: http://docs.joaomarquesmastermind.apiary.io

A DHC config file is also included on the root folder: `vanhackathon.json`. DHC is a Google Chrome Extension to test REST APIs.
You can install it and import the config file. It will create a project named `VanHackathon` with some API resquests examples used during the development.


Challenge 2: A frontend for playing Mastermind Game with the API from challenge 1
---------------------------------------------------------------------------------

The real challenge here for me (as a developer, not a designer) is to don't stress the server with so many requests.

That's why I will try my best to build a client using websockets instead of firing a lot of ajax requests to the server.

I'm not used to websockets but that's what I'm here for: LEARN NEW STUFF!

I decided to focus on making the websockets work. Maybe it will not look so good but hey, thats a designer job!
I know how to put thinks together with HTML5, CSS3, Javascript, jQuery, Bootstrap because those are the things I know but I' not that good in creating layouts.
I know how to bring one to life!

The result you can see by accessing the address http://mastermind.dev (or whatever you decided to use) on your browser.

UPDATE NEAR SUBMIT HOUR: I did not finished this challenge. I had a hard time with the websockets thing. But I was close.
It was a great experience anyway. I did a lot of resarch on this during the Hackathon and loose a precious time.
But I think the API is in pretty good shape and is very complete.

See you on the next Hackathon!

Cheers!
