# A login-page written in PHP.

## Opinions

- Standards are [PHP-FIG](http://http://www.php-fig.org/).
- [Composer](http://http://getcomposer.org/) does the autoloading.
-   Classes have suffixes (`Model`|`View`|`Controller`) even though they are namespaced.
    I think it's clearer when you often have both View and Model-classes with the same name.

## How to get it running

Since I'm using Composers autoloading, you need to run a few commands before this code will run.

If you have Composer installed globally, run:

    cd app/
    composer install

Then you're good to go. If it's not installed globally:

    # Install composer locally
    cd /path/to/this/project/root/app
    curl -s http://getcomposer.org/installer | php

    # Run the local install from within the app/-directory
    php composer.phar install

Both of these commands do the same thing - generate a `vendor`-directory with an autoloader
and eventual dependencies.

## Deployment

Now I'm using this [script](https://gist.github.com/6581757) to deploy via SSH.

I've also set this in `.htaccess` on the host.

    AddDefaultCharset utf-8
