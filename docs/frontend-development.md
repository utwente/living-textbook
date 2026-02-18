You will need to have a local installation of [Node](https://nodejs.org/en/, see the `package.json` `engines` definition for the required version)
to be able to build the frontend assets.

## Install dependencies

Make sure to install the Node dependencies first by running the following command in the project root folder.

```
npm install
```

## Builds

We offer a couple of build methods. You may need to run the back end at least once if you get a compile error.

- Development build: `npm run dev`
- Automatic watch (requires refresh on changes): `npm run watch`
- Hot module reloading (HMR): `npm run dev-server:encore -- --host localhost --hot --https --cert docker/nginx/server.cert --key docker/nginx/server.key`
- Production build: `npm run build`

All these will work with the docker development image. If you are not sure use `npm run dev` for development or `npm run build` for production.
