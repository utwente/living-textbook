The following environment variables are interesting to be configured.

| Env                    | Description                                                                                                                                          |
|------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------|
| `MAILER_DSN`           | If you want to be able to email, configure this according to the [mailer documentation](https://symfony.com/doc/current/mailer.html#transport-setup) |
| `EXCEPTION_MAILER_URL` | Same as above. When set, exception mails will be sent. Not required to be used, and not recommended to use when Sentry is configured.                |                                                                                 |
| `EXCEPTION_RECEIVER`   | The target of the exception mails                                                                                                                    |
| `EXCEPTION_SENDER`     | The exception mail sender                                                                                                                            |
| `SENTRY_DSN`           | Configure the for PHP Sentry error tracking                                                                                                          |
| `SENTRY_JS_DSN`        | Configure this for JS Sentry error tracking                                                                                                          | 
| `OIDC_WELL_KNOWN`      | Well-known endpoint for OIDC authentication                                                                                                          |
| `OIDC_CLIENT_ID`       | OIDC client ID                                                                                                                                       |
| `HTTP_HOST`            | The HTTP host you're going to use. `localhost:10443` for development                                                                                 |
| `MAIL_FROM`            | Where email sent normally is coming from                                                                                                             |
