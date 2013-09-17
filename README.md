# A login-page written in PHP.

## Opinions

- Standards are [PHP-FIG](http://http://www.php-fig.org/).
- [Composer](http://http://getcomposer.org/) does the autoloading.
- MVC-classes have suffixes (`Model`|`View`|`Controller`) even though they are namespaced, because
    I want to know what the tabs in my editor goes to

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

## Bugs / Todo

- Login by cookies is not working
- Use temporary passwords on the client
- Register users
- Protect against evil tutors
