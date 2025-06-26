#!/usr/bin/env bash
set -o errexit

# Install the PostgreSQL PHP extension
apt-get update && apt-get install -y libpq-dev
docker-php-ext-install pdo_pgsql pgsql