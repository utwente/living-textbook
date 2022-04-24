You will need to have a local installation of [Node LTS](https://nodejs.org/en/) to be able to build the frontend assets. See
the [frontend development docs](frontend-development.md) for more information.

## Install dependencies

Make sure to install the Node dependencies first.

```
yarn install
```

## Builds

We offer a couple of build methods:

- Development build: `yarn dev`
- Automatic watch (requires refresh on changes): `yarn watch`
- Hot module reloading (HMR): `yarn encore dev-server --host localhost --hot --https --cert docker/nginx/server.cert --key docker/nginx/server.key`
- Production build: `yarn build`

All these will work with the docker development image.
