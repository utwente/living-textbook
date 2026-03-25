/** @type {import('stylelint').Config} */
export default {
  extends: ['@drenso/stylelint-config'],
  ignoreFiles: [
    'public/build/**/*',
    'public/bundles/**/*',
    'public/ckeditor/**/*',
    'public/ckeditor-plugins/**/*',
    'public/email/email.css',
    'vendor/**/*',
  ],
};
