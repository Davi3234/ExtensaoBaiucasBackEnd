#!/bin/sh
set -x

php vendor/bin/phpunit Tests --colors

if $? -ne 2; then
  echo "Algum teste falhou"
  exit 1
fi

echo "Todos os testes passaram"

php /var/www/html/bin/doctrine orm:schema-tool:create

php -S 0.0.0.0:80 -t /var/www/html public/index.php
