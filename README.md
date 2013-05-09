# Copelandia Symfony Client

This project implements a Symfony 2 client that interacts with Copelandia.

## Installation

1. Clone this project.
1. [Install composer](http://getcomposer.org/doc/00-intro.md#globally).
1. Install all the dependencies by doing:

``` bash
cd code
composer install
```

1. Create a virtual host that points to code/web.
1. Open app_dev.php/cofig at your web browser to set up a database.

## Sending emails

A the moment emails are sent through Gmail using Swiftmailer library. You need to
set mailer_user and mailer_password at code/app/config/parameters.yml so emails
can be sent.
