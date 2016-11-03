# What is this?

This a blog impementation built on top of the Symfony2 framework.
It's still a work in progress.

# Why did you do this?

For fun and to learn.

# Using the app

## Clone the app from this repository

    git clone https://github.com/mylk/blog-symfony.git

Enter the application directory:

    cd blog-symfony

## Directories and Permissions setup

Execute the following commands in order to create two Symfony required directories and give the appropriate permissions:

    mkdir app/cache
    mkdir app/logs

    chmod 775 app/cache
    chmod 775 app/logs

## Database setup:

The app configuration assumes you already have a MySQL instance running.
It also assumes that you have a user having username and password set to "root" and "toor" respectively.
If this is not your case, you have to modify them from the following configuration file:

    app/config/parameters.yml

Change the following parameters' values to fit your needs:

    database_user
    database_password

The app also assumes that you don't already have a database named "blog".
If this is not your case modify the following parameter in the aforementioned configuration file:

    database_name

## Database schema creation:

Let Doctrine do the job for you:

    app/console doctrine:schema:create
    app/console doctrine:schema:update --force

## Database initialization

    app/console doctrine:fixtures:load -n

Remember, the application assumes that your database setup has a user "root" with password "toor".

## Run the application

The following command assumes that you have PHP >= 5.4.0 which provides a built-in web server:

    app/console server:run

The app now runs on localhost:8000, you can visit this address to start using it.

Alternatively, if you have ```docker``` and ```docker-compose``` installed, you can run the application in a container:

    docker-compose build
    docker-compose up

## Mailer setup

If you want to use the mailer functionality, to inform users and article composer about comments,
edit the following in app/config/parameters.yml and add your corresponding Gmail account info:

    mailer_user
    mailer_password

# App administration interface

You can use the administration interface visiting localhost:8000/admin

The following administration account is already set up:

    Username: admin
    Password: adminpass

## Testing

Testing the application requires database dumps have been imported.
For instructions, check the sections "Database schema creation" and "Database initialization".

To run the provided tests:

    bin/phpunit -c app/