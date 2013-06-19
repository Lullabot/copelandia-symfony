# Copelandia Symfony Client

This project implements a Symfony 2 client that interacts with Copelandia.

See it working at http://copesf.lulladev.com.

## Installation

1. Clone this project.
1. [Install composer](http://getcomposer.org/doc/00-intro.md#globally).
1. Install all the dependencies by doing:

``` bash
cd code
composer install
```

1. Copy code/app/config/parameters.yml.default into code/app/config/parameters.yml
1. Create a virtual host that points to code/web.
1. Open http://yourlocalsite/app_dev.php. You should see a list of recipes which
   by default are pulled from http://copelandia.lulladev.com.
1. Optionally, install https://github.com/Lullabot/copelandia locally and set
   the backend_host parameter accordingly.
