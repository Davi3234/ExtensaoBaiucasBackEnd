#!/bin/sh
set -x

#VERIFICAR SEMPRE SE A CODIFICAÇÃO DO ARQUIVO ESTÁ LF

php /var/www/html/bin/doctrine orm:schema-tool:create

php -S 0.0.0.0:80 -t /var/www/html
