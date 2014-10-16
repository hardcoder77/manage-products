#!/bin/sh
sudo apt-get -y update
sudo apt-get -y dist-upgrade
sudo apt-get -y install apache2
sudo a2enmod rewrite
sudo apt-get -y install libapache2-mod-php5
sudo /etc/init.d/apache2 restart
sudo adduser ubuntu www-data
sudo chown -R www-data:www-data /var/www
sudo chmod -R g+rw /var/www
sudo apt-get -y install mysql-server
sudo apt-get -y install php5-mysql




