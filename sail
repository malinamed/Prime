#!/usr/bin/env bash
# Laravel Sail wrapper script

# Ensure that Docker is running
docker info > /dev/null 2>&1

if [ $? -ne 0 ]; then
    echo "Docker is not running." >&2
    exit 1
fi

UNAMEOUT="$(uname -s)"

WHITE='\033[1;37m'
NC='\033[0m'

# Source .env to pick up any environment overrides
if [ -n "$APP_ENV" ] && [ -f ./.env.$APP_ENV ]; then
  source ./.env.$APP_ENV;
elif [ -f ./.env ]; then
  source ./.env;
fi

export APP_PORT=${APP_PORT:-80}
export APP_SERVICE=${APP_SERVICE:-"app"}
export DB_PORT=${DB_PORT:-5432}
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

function sail_is_not_running {
    echo "${WHITE}Sail is not running.${NC}" >&2
    echo "" >&2
    echo "${WHITE}You may start Sail using the following command:${NC} './vendor/bin/sail up' or './sail up'" >&2
    exit 1
}

if [ $# -gt 0 ]; then
    if docker compose ps "$APP_SERVICE" 2> /dev/null | grep -q 'running\|Up'; then
        exec docker compose exec \
            -u sail \
            "$APP_SERVICE" \
            "$@"
    else
        sail_is_not_running
    fi
fi
