curl -sS https://getcomposer.org/installer | /usr/bin/php8.0-cli

/usr/bin/php8.1-cli composer.phar install

/usr/bin/php8.1-cli composer.phar install --no-dev --optimize-autoloader


### Für Ionos Hosting:

/usr/bin/php8.1-cli composer.phar require symfony/apache-pack


### Migration
php bin/console make:migration
php bin/console doctrine:migrations:migrate