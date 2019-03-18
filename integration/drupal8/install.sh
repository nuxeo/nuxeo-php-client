#!/usr/bin/env bash

sudo rm -rf \
  vendor/ \
  web/{core,profiles,sites}/ \
  web/{themes,*.gitignore,.es*,.css*,*.php,*.txt,*.config,*.htaccess} \
  .editor* .git*

php ../../composer.phar install

#php vendor/bin/drush site:install \
#  --db-url=sqlite://sites/default/files/.ht.sqlite \
#  --site-name="Nuxeo Drupal Integration" \
#  --account-name=admin \
#  --account-pass=admin \
#  --site-mail=void@null.com \
#  --yes \
#  minimal
#
#php vendor/bin/drush en nuxeo
#php vendor/bin/drush cr
