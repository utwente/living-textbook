# Docker instructions

We offer two docker-compose configurations: one for local development and one for production. Both images are built on
the same base images which contains all required dependencies to run the whole application.

We recommend the usage of a Linux based OS or MacOS when using docker due to volume binding limitations with Windows,
but it is also possible to use the Windows Subsystem for Linux (WSL) if you clone the source on the WSL disk you are
using with Docker.

## Storage

The images are configured such that all state storage is permanently stored in a docker volume. When switching from the
development to the production configuration (and vice-versa) the data in those volumes is being kept.

## Setup

You will need to have a current `docker` engine installed, combined with the `docker-compose` script.

The LTB enforces the use of HTTPS. Therefor, you will need to have a valid key and certificate for your domain which you
can place in `docker/nginx/server.key` and `docker/nginx/server.cert`.

As alternative, you can generate a self-signed certificate by running the following command in the project root folder.
You can just hit enter for all the fields it asks you to fill.

```
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout docker/nginx/server.key -out docker/nginx/server.cert
```

Next, prepare the `.env` files in the docker folder. Simply copy the example `.dist` files which already contain some
sensible
defaults to files. Remove the `.dist` suffix from the copies and make sure their variables are filled. Make sure to
configure the
following environment variables depending on your environment.

#### DB env

| Env                   | Description                           |
|-----------------------|---------------------------------------|
| `MYSQL_ROOT_PASSWORD` | The root password of the database     |
| `MYSQL_PASSWORD`      | The password the LTB application uses | 

#### LTB env (prod only)

| Env         | Description                                                                                 |
|-------------|---------------------------------------------------------------------------------------------|
| `HTTP_HOST` | Set this to the host you will be serving the application from, such as `ltb.itc.utwente.nl` |

#### Secrets

You will need to configure the secrets accordingly. Make a copy of `.secrets.json.dist` called `.secrets.json` and fill
in the
values accordingly.

```json
{
  "app": "<random generated long value used to secure the session and others>",
  "database": "<the MYSQL_PASSWORD password as configured in db.env>",
  "oidc": "OIDC client secret, if configured"
}
```

### Switching environments

When you switch environments (development to production, or vice-versa) you should make sure to pass the `---build`
argument the first time you build the docker image. This ensures that the image for the environment will be built
correctly!

## Development

The development image is a base image which does not contain the code and dependencies, making it suitable for local
development. It will mount your local installation folder directly into the containers, so any changes made in the
sources will directly be reflected in the container, without having to build again.

> Note: When using Windows as OS, make sure to clone the sources on the WSL disk you're also using docker with. If you
> do not do this, the volume bind will be slow and most probably unworkable!

You will need to have a local installation of [Node LTS](https://nodejs.org/en/) to be able to build the frontend
assets. See
the [frontend development docs](frontend-development.md) for more information.

> Note: OIDC login will not work, as localhost is not configured (or can be configured as) as valid return URL.

### Environment

You can further tweak the environment by creating a `.env.local` environment file. See
the [environment description](environment.md) to learn more.

### Development start-up

1. Make a copy of `docker-compose.dev.yml` called `docker-compose.yml`
2. Make sure the environment is configured accordingly
3. Start the containers by running the command `docker-compose up` in the project root folder and wait until it is up
   and running (you will see `NOTICE: ready to handle connections`)
4. Start a new console if you want to use other commands on the containers, like checking the logs from the docker run.
   Stopping the command in the original console will shut down the containers.
5. Setup the frontend assets: choose a method from the [frontend development docs](frontend-development.md)
6. Visit the ltb on `https://localhost:10443/` in your browser (and ignore the certificate error).

> Note: Before you can login, you must create a local account and a study area. Check [the instructions](#first-account)
> at the bottom of this document.

### Common development methods

If you need to run a console command you can do so with `docker-compose exec`, for example to clear the cache:

```
docker-compose exec ltb bin/console cache:clear
```

or to update the vendors:

```
docker-compose exec ltb composer update
```

## Production

The production image contains a full production build of the LTB (so it includes all code and dependencies required to
run the application).

### Environment

You can further tweak the environment by updating the `docker/ltb_prod.env` environment file. See
the [environment description](environment.md) to learn more.

### Production how-to

1. Make a copy of `docker-compose.prod.yml` called `docker-compose.yml`
2. Make sure the environment is configured accordingly
3. Start with `docker-compose up -d`
4. Visit the ltb in your browser by going to the host configured in `ltb_prod.env`.

> Note: Before you can login, you must create a local account and a study area. Check [the instructions](#first-account)
> at the bottom of this document.

### Common commands

If you need to run a console command you can do so with `docker-compose exec`, for example to clear the cache:

```
docker-compose exec ltb bin/console cache:clear
```

## First account

In order to authenticate yourself within the LTB, you must have created a local account combined with an initial study
area. You can do so by running the following in a new console in the project root folder. The default values between
square brackets will be used if you hit enter without filling anything in.

```
docker-compose exec ltb bin/console ltb:add:account --with-area
```

Note that for both the ltb container must be up and running!
