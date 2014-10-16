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
debconf-set-selections <<< 'mysql-server mysql-server/root_password password '
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password '
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
echo `ls`
echo `pwd`
mysql -u root wings < sql/wings.sql
cd ..
rm -rf manage-products
sudo /etc/init.d/apache2 restart


