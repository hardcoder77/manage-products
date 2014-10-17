#!/bin/sh
export DEBIAN_FRONTEND=noninteractive
sudo apt-get -y update
sudo DEBIAN_FRONTEND=noninteractive apt-get -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" dist-upgrade
sudo apt-get -y install apache2
sudo a2enmod rewrite
sudo apt-get -y install libapache2-mod-php5
currectuser=`whoami`
sudo adduser ${currectuser} www-data
sudo chown -R www-data:www-data /var/www
sudo chmod -R g+rw /var/www
export DEBIAN_FRONTEND=noninteractive
sudo apt-get -q -y install mysql-server
sudo apt-get -y install php5-mysql
sudo apt-get -y install git
cd
git clone https://github.com/hardcoder77/manage-products.git
cd manage-products
sudo cp -R data /var/www/html
sudo cp -R resources /var/www/html
sudo cp -R services /var/www/html
sudo cp .htaccess /var/www/html
sudo cp config.php /var/www/html
sudo cp Server.php /var/www/html
sudo cp apache/rewrite.conf /etc/apache2/mods-enabled
sudo cp apache/000-default.conf /etc/apache2/sites-enabled/
echo `ls`
echo `pwd`
cp sql/wings_2014-10-16.sql ~
mysql -uroot -e "create database wings"
mysql -u root wings < sql/wings_2014-10-16.sql
cd ..
rm -rf manage-products
sudo /etc/init.d/apache2 restart


