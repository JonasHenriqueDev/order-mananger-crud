#!/bin/bash
set -e

sleep 5

if [ -d "/var/www/vendor" ]; then
    echo "Verificando permissões do vendor..."
    find /var/www/vendor -not -user www-data -exec echo "Permissões incorretas detectadas" \; -quit
fi

exec "$@"
