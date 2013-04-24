Locale Component
================

Locale provides fallback code to handle cases when the ``intl`` extension is
missing.

Loading the fallback classes for example using the ClassLoader component only
requires adding the following lines to your autoloader:

    // intl
    if (!function_exists('intl_get_error_code')) {
        require __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

        $loader->registerPrefixFallback(__DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs');
    }

If you are using Composer for autoloading, then you can even simplify it by
removing the ``$loader->registerPrefixFallback`` line.

Resources
---------

You can run the unit tests with the following command:

    $ cd path/to/Symfony/Component/XXX/
    $ composer.phar install --dev
    $ phpunit

If your PHP have the ``intl`` extension enabled but the intl extension ICU data
version mismatch the one shipped with the component, you can build the data for
it and use the ``USE_INTL_ICU_DATA_VERSION`` environment variable.

    php Resources/data/build-data.php
    export USE_INTL_ICU_DATA_VERSION=true
    phpunit

This way the tests will use the ICU data files with the same version of your
``intl`` extension.

Read the file ``Resources/data/UPDATE.txt`` for more info about building or
updating the ICU data files.
