#!/bin/sh
set -x

php /var/www/html/bin/doctrine orm:schema-tool:update --force

php -S 0.0.0.0:80 -t /var/www/html/ public/index.php
