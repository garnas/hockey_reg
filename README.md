curl -sS https://getcomposer.org/installer | /usr/bin/php8.0-cli

/usr/bin/php8.1-cli composer.phar install

/usr/bin/php8.1-cli composer.phar install --no-dev --optimize-autoloader


### Für Ionos Hosting:

/usr/bin/php8.1-cli composer.phar require symfony/apache-pack


### Migration
curl -sS https://getcomposer.org/installer | /usr/bin/php8.1-cli
/usr/bin/php8.1-cli composer.phar install --no-dev --optimize-autoloader
/usr/bin/php8.1-cli composer.phar dump-env prod
/usr/bin/php8.1-cli bin/console make:migration
/usr/bin/php8.1-cli bin/console doctrine:migrations:migrate