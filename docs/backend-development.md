The backend of the LTB is based on the [Symfony PHP Framework](https://symfony.com/). See the `composer.json` file to see which version is currently in use.

Documentation regarding Symfony and its used components can be found at https://symfony.com/doc/current/index.html. For certain packages dedicated documentation exists: just check the Github page in case you need it.

We recommend [Symfony: The Fast Track](https://symfony.com/book) as a resource if you want to learn the basics of Symfony within reasonable time. It shows what Symfony is capable of and touches most of the concept used by the LTB.

## Install dependencies

Make sure to install the PHP dependencies first by running the following command in the project root folder:

```
composer install
```

## Run

The easiest way to run the LTB is to use the docker development image. It will contain all PHP dependencies by default.
