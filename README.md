# Service Civique - Website [![Build Status](https://magnum.travis-ci.com/LaNetscouade/service-civique-website.svg?token=MVtmppmZpFGgJurg5C6M&branch=master)](https://magnum.travis-ci.com/LaNetscouade/service-civique-website)

1) Installation
---------------

# Create folder app/sessions with 777 rights

On OSX install ElasticSearch with: `brew install elasticsearch`

```bash
# Install composer depedencies
curl -s http://getcomposer.org/installer | php
php composer.phar install

# Install nodeJS packages
npm install

# Install bower packages
bower install

# build assets
gulp build

# populate elastica indexes
php app/console fos:elastica:populate

# Load communes datas
php app/console doctrine:fixtures:load --append --fixtures src/ServiceCivique/Bundle/AddressingBundle/DataFixtures/ORM

# Import translations
php app/console lexik:translations:import

# Ruby dependencies (capistrano)
gem install bundler
```

2) Tests
--------

Before running test update database schema and clear cache

```bash
php app/console cache:clear --env=test
php app/console doctrine:schema:update --force --env=test
php app/console lexik:translations:import --force --env=test
```

Run tests:

```bash
bin/phpspec run
bin/behat
```

3) Cronjobs
--------

Check that cronjobs are set :

```bash
# Archive old missions (monthly)
0 0 1 * * php /home/www/new.service-civique.gouv.dev.scoua.de/current/app/console sc:archive:missions --env=prod
# Add new mailchimp subscribers (daily)
0 0 * * * php /home/www/new.service-civique.gouv.dev.scoua.de/current/app/console sc:update_mailchimp_subscribers --env=prod
```

4) Import CV
-------------

```bash
# Download all resumes (ZzzZzZZzzzz)
# example in preprod
scp -r -P 2222 lanetpprssh@89.31.147.217:/var/www/test.service-civique.gouv.fr/current/sites/default/files/webform/ /path/to/symfony/web/drupal-cv/

# Launch the command to rename and move resumes
php app/console sc:import:resumes

# Delete imported resumes
rm -rf /path/to/symfony/web/drupal-cv

```