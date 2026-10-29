#!/bin/sh
set -eu

cd /var/www/html

# Preparar o armazenamento persistente
mkdir -p /data
touch /data/taskflow.sqlite

# Gerar a chave da aplicação, caso não exista
if [ ! -f /data/.app_key ]; then
    php -r 'echo "base64:" . base64_encode(random_bytes(32));' \
        > /data/.app_key

    chmod 600 /data/.app_key
fi

# Carregar a chave de criptografia
export APP_KEY="$(cat /data/.app_key)"

# Executar as migrações
php artisan migrate --force

# Iniciar o Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
