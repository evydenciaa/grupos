#!/usr/bin/env bash
set -euo pipefail
SOURCE="$(cd "$(dirname "$0")" && pwd)"
TARGET="${1:?Informe um diretório novo para Laravel}"
if [ -e "$TARGET" ]; then echo "Destino já existe; abortando."; exit 1; fi
composer create-project laravel/laravel "$TARGET" '^13.0' --no-interaction
cd "$TARGET"
composer require 'filament/filament:^5.0' --no-interaction
php artisan filament:install --panels --no-interaction
cp -R "$SOURCE/overlay/." .
cp "$SOURCE/AGENTS.md" .
cp -R "$SOURCE/docs" "$SOURCE/.agents" .
php artisan optimize:clear
echo 'Configure .env, execute migrate e make:filament-user. Instale Boost somente em desenvolvimento.'
