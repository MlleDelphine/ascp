#!/bin/bash
cp -rf /vagrant/???* /var/www/html
cp -rf /vagrant/.??* /var/www/html

sudo apt-get install php5-curl
composer install