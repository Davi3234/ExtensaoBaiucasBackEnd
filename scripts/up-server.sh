#!/bin/sh
set -x

php vendor/bin/phpunit Tests --colors

if [ $? -ne 0 ]; then
  echo "Algum teste falhou"
  exit 1
fi

echo "Todos os testes passaram"

php /var/www/html/bin/doctrine orm:schema-tool:update --force
