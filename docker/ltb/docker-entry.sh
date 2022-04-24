#! /bin/bash -e

# Ensure to use the correct working directory
cd /usr/src/app

# Detect public mirror directory, and update its contents
if [[ -d public_mirror ]]; then
  rm -rf public_mirror/*
  cp -r public/* public_mirror/
fi

# Wait for database
if [[ -z "${DATABASE_CHECK}" ]]; then
  sleep 10
else
  /opt/wait-for "${DATABASE_CHECK}"
fi

# Automatically install the dependencies on startup for the development environment
if [[ "${APP_ENV}" == "dev" ]]; then
    composer install
fi

# Migrate database
su -s /bin/bash -c "php bin/console doctrine:migrations:migrate --no-interaction --query-time --allow-no-migration -vv" www-data
su -s /bin/bash -c "php bin/console doctrine:migrations:migrate --no-interaction --query-time --allow-no-migration -vv" www-data

# When in prod, make sure the environment is dumped
if [[ "${APP_ENV}" != "dev" ]]; then
  su -s /bin/bash -c "composer symfony:dump-env" www-data
fi

# Start supervisor
exec supervisord -c /etc/supervisor/supervisord.conf
