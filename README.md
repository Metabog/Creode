# Creode

This is a short technical test. Upon navigating to /index.php or just /, the script will poll Facebook to retrieve all posts from Creode's Facebook page. It will save them all in a database hosted on AWS RDS (PostgreSQL), via an upsert operation (updating existing posts and adding new ones). 

The app uses a couple of technologies. You need Composer to retrieve the required PHP libraries. Once you have composer
run in the root folder:

composer install 

This will set up PSR-4 autoloading and install the vendor libraries. The Creode class is autoloaded and resides in its own namespace in src.

I used Twig templates for the (simple) frontend, so no PHP is actually used on the page. Config.json contains database credentials
and Facebook API credentials. 

Either install the app on a server or use:

php -S localhost:8000 

To host it locally on port 8000.


