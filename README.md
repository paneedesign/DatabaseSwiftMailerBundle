<h1 align="center">DatabaseSwiftMailerBundle</h1>
<h2>A database spool bundle for SwiftMailer and Symfony 4+</h2>
<p>
  <img alt="Version" src="https://img.shields.io/badge/version-3.0.0-blue.svg?cacheSeconds=2592000" />
  <a href="#" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg" />
  </a>
</p>

### 🏠 [Homepage](https://github.com/paneedesign/DatabaseSwiftMailerBundle)

## Introduction

This bundle add a database driven swiftmailer spool to your Symfony 4 project. It requires Symfony 4.2+ and usage of entities with Doctrine ORM.

### Features

- Auto Retrying: set a maximum number of retries that spool will try to send in case of failure
- Dashboard to list the email spool and perform some actions
- Retry sending an email
- Cancelling an email sending 
- Resending an email

## Install

```sh
composer require paneedesign/database-swiftmailer-bundle
```

## Configure

Register bundle into `bundles.php` file

```php
return [
    ...
    PaneeDesign\DatabaseSwiftMailerBundle\PedDatabaseSwiftMailerBundle::class => ['all' => true],
];
```

Add to `.env`

```dotenv
###> paneedesign/database-swift-mailer ###
SMD_MAX_RETRIES=10
SMD_DELETE_SENT_MESSAGES=false
SMD_AUTO_FLUSH=true
SMD_ENTITY_MANAGER=doctrine.default_entity_manager
###< paneedesign/database-swift-mailer ###
```

Create `config/packages/ped_database_swift_mailer.yaml`

```yaml
ped_database_swift_mailer:
    max_retries: "%env(int:SMD_MAX_RETRIES)%"
    delete_sent_messages: "%env(bool:SMD_DELETE_SENT_MESSAGES)%"
    auto_flush: "%env(bool:SMD_AUTO_FLUSH)%"
    entity_manager: "%env(SMD_ENTITY_MANAGER)%"
```

Create `config/routes/ped_database_swift_mailer.yaml`

```yaml
ped_database_swift_mailer:
    resource: PaneeDesign\DatabaseSwiftMailerBundle\Controller\EmailController
    type:     annotation
    prefix:   /email-spool
```

Update your database schema to create the necessary entities.

```sh
$ bin/console doctrine:schema:update --force
```

Change your spool type from `memory` to `db` in your `config/packages/swiftmailer.yaml`

```yaml
swiftmailer:
    spool: { type: db }
```


## Usage

To send emails that are in the database spool, just run the following command:

```sh
bin/console swiftmailer:spool:send
```

You may add a cron job entry to run it periodically.

You can check the spool status with all emails at http://your_project_url/email-spool

## Bonus

### Overriding default templates 

You may want to override the default template to have the look and feel of your application. You can do it following the official Symfony documentation:
https://symfony.com/doc/current/bundles/override.html

## ToDo List

- Filter emails
- Insert error message once it reaches the maximum of retries
- Last run date
- Count total sent

## Authors

👤 **Fabiano Roberto <fabiano.roberto@ped.technology>**

* Twitter: [@dr_thief](https://twitter.com/dr_thief)
* Github: [@fabianoroberto](https://github.com/fabianoroberto)

👤 **Luigi Cardamone <luigi.cardamone@ped.technology>**

* Twitter: [@CardamoneLuigi](https://twitter.com/CardamoneLuigi)
* Github: [@LuigiCardamone](https://github.com/LuigiCardamone)

## 🤝 Contributing

Contributions, issues and feature requests are welcome!<br />Feel free to check [issues page](https://github.com/paneedesign/DatabaseSwiftMailerBundle/issues).

## Show your support

Give a ⭐️ if this project helped you!

***
_This README was generated with ❤️ by [readme-md-generator](https://github.com/kefranabg/readme-md-generator)_
