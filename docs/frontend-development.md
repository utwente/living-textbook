You will need to have a local installation of [Node](https://nodejs.org/en/, see the `package.json` `engines` definition for the required version)
to be able to build the frontend assets.

## Install dependencies

Make sure to install the Node dependencies first by running the following command in the project root folder.

```
yarn install
```

## Builds

We offer a couple of build methods. You may need to run the back end at least once if you get a compile error.

- Development build: `yarn dev`
- Automatic watch (requires refresh on changes): `yarn watch`
- Hot module reloading (HMR): `yarn encore dev-server --host localhost --hot --https --cert docker/nginx/server.cert --key docker/nginx/server.key`
- Production build: `yarn build`

All these will work with the docker development image. If you are not sure use `yarn dev` for development or `yarn build` for production.
