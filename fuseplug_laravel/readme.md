## Laravel version of fuseplug

## to install
### requirements: 
* a modern mac 
* homebrew installed
* php 7 and composer installed
* mysql installed
### instructions
* clone the fuseplug environment
* cd to the project dir and run `composer install`
* install and set up rabbitmq.  Here's what I used: https://www.rabbitmq.com/install-homebrew.html
* do yourself a favor and install the rabbitmq admin interface.  You can query rabbitmq for whatever you need with the command line but this is a nice web ui for seeing what's happening.  More info here: https://www.rabbitmq.com/management.html
* in rabbitmq, create a queue called fuseplug, or whatever you want.  Also set up a user in rabbitmq if you want, and record this information in the .env file, in the section that has RABBITMQ-prefixed variables
* run composer install
* create a database called fuseplug_laravel in mysql.  if you use a different name update the DB-prefixed variables in the .env file
* start the webserver
* the app's root url is a html page (the only one for this app) with links to JSON-based api documentation, system status and more.

