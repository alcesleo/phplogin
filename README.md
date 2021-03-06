# A login-page written in PHP.

## Opinions

- Standards are [PHP-FIG](http://http://www.php-fig.org/), and are checked using [PHP_CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer/).
- [Composer](http://http://getcomposer.org/) does the autoloading.
-   Classes have suffixes (`Model`|`View`|`Controller`) even though they are namespaced.
    It is a little redundant, but I think it's worth it to know what role a file has without
    looking at the folder/namespace. This works well with tabs in the editor.

## Structure

![](http://yuml.me/d132d910/)

## How to get it running

Since I'm using Composers autoloading, you need to run a few commands before this code will run.

If you have Composer installed globally, run:

    cd app/
    composer install

Then you're good to go. If it's not installed globally:

    # Install composer locally
    cd app/
    curl -s http://getcomposer.org/installer | php

    # Run the local install from within the app/-directory
    php composer.phar install

Both of these commands do the same thing - generate a `vendor`-directory with an autoloader
and eventual dependencies.

## Deployment

Now I'm using this [script](https://gist.github.com/6581757) to deploy via SSH.

I've also set this in `.htaccess` on the host.

    AddDefaultCharset utf-8

## Testing

To run the tests:

    # Make sure codeception is installed
    composer install --dev

    # codecept is either php codecept.phar or vendor/bin/codecept
    # if you want to use it globally like the commands are written:
    # add it to your path.

    # generate files
    codecept build

    # run the tests
    codecept run

## Todo

- Move the tests, should they really be uploaded to the server?
- Temp password expiration, same as cookie.
- Split controllers into UC:s
- Register users
- Protect against evil tutors
- Username 5+letters
- Password require capitals etc
- Error logging
