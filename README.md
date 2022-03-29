# Setup docker environment

- `git clone`
- `yarn install`

Configure the relevant environment by creating the following files:

```
# .env.local
APP_ENV=dev
DATABASE_URL=mysql://013-living-textbook:%database_password%@db:3306/013-living-textbook
HTTP_HOST=localhost
```

Place the following in `.secrets.json`
```
{
  "app": "SomeSecret",
  "database": "dbpass",
  "oidc": ""
}
```

Create a self-signed certificate for nginx:

```
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout docker/nginx/server.key -out docker/nginx/server.cert
```

Build the docker containers with `docker-compose build`.

Bring it all up with `docker-compose up -d`.

Open a console within the php container (assuming the folder is `013-living-textbook`):

- `docker exec -it 013-living-textbook_php_1 /bin/sh`
- `composer install`
- `bin/console doctrine:migrations:migrate` and select `y`

In your normal console, now run:

- `yarn build`

Execute the following queries in the console of the db container (in `docker exec -it 013-living-textbook_db_1 /bin.sh` -> `mysql`)

```sql
use 013-living-textbook;
INSERT INTO user__table (given_name, last_name, full_name, display_name, username, is_oidc, is_admin, roles, password, registered_on, created_at, created_by) VALUES ('Dev', 'Loper', 'Developer', 'Developer', 'developer@localhost.nl', 0, 1, 'a:0:{}', '$argon2id$v=19$m=65536,t=4,p=1$0HoR4yJvi6fb5xFtXXH66w$yXO7fsj/I1X7/jFb0NuNuN/AkRBsiJOLmAnezAFWwCM', NOW(), NOW(), 'developer');
INSERT INTO study_area (owner_user_id, name, created_at, access_type, track_users, open_access, analytics_dashboard_enabled, review_mode_enabled, dotron) VALUES (1, 'Development Area', NOW(), 'public', 0, 0, 0, 0, 0);
```

Open the ltb on `https://localhost:10443/` (and ignore the certificate error). You can login with a local account
used `developer@localhost.nl`:`developer` as credentials. This is a full admin account.

OIDC login will not work, as localhost is not configured (or can be configured as) as valid return URL.

# JS development

You should be able to start the JS development server
with `yarn encore dev-server --hot --https --disableHostCheck=true --cert docker/nginx/server.cert --key docker/nginx/server.key`
for HMR, or just use `yarn watch` when HMR is not required.
