# DatabaseSwiftMailerBundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dextervip/DatabaseSwiftMailerBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dextervip/DatabaseSwiftMailerBundle/?branch=master)
[![Build Status](https://travis-ci.org/paneedesign/DatabaseSwiftMailerBundle.svg?branch=feature%2Fsf4-refactoring)](https://travis-ci.org/paneedesign/DatabaseSwiftMailerBundle)

## Introduction

This bundle add a database driven swiftmailer spool to your Symfony 3 project. It requires Symfony 3.0+ and usage of entities with Doctrine ORM.

### Features

- Auto Retrying: set a maximum number of retries that spool will try to send in case of failure
- Dashboard to list the email spool and perform some actions
- Retry sending an email
- Cancelling an email sending 
- Resending an email

## Installing

### Add composer

Add the dependency to your composer.json

```yml
    "require": {
        ...
        "paneedesign/database-swiftmailer-bundle" : "dev-master"
    }
```

### Add bundle class in kernel

Register the bundle class and its dependencies in your AppKernel.php
```php
    public function registerBundles()
    {
        $bundles = array(
        ...
        new PaneeDesign\DatabaseSwiftMailerBundle\PedDatabaseSwiftMailerBundle(),
        ...
        );
    }
```

### Add routes

If you want to have a spool dashboard, add the following routes.

```yml
ped_database_swift_mailer:
    resource: "@PedDatabaseSwiftMailerBundle/Controller/"
    type:     annotation
    prefix:   /
```

## Configuring

### Update database

Update your database schema to create the necessary entities.

```sh
$ php bin/console doctrine:schema:update --force
```

### Update swiftmailer config

Change your spool type from memory to db in your config.yml

```yml
    spool:     { type: db }
```

### Overriding default templates 

You may want to override the default template to have the look and feel of your application. You can do it following the official Symfony documentation:
https://symfony.com/doc/3.4/templating/overriding.html

## Running

To send emails that are in the database spool, just run the following command: 

```sh
$ php bin/console swiftmailer:spool:send
```

You may add a cron job entry to run it periodically.

You can check the spool status with all emails at http://your_project_url/email-spool


## ToDo List

- Filter emails
- Insert error message once it reaches the maximum of retries
- Last run date
- Count total sent

## License
MIT

