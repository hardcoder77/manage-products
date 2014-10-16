#!/bin/sh
sudo apt-get -y update
sudo apt-get -y dist-upgrade
sudo apt-get -y install apache2
sudo a2enmod rewrite
sudo apt-get -y install libapache2-mod-php5
currectuser=`whoami`
sudo adduser $currectuser www-data
sudo chown -R www-data:www-data /var/www
sudo chmod -R g+rw /var/www
sudo apt-get -y install mysql-server
sudo apt-get -y install php5-mysql
sudo apt-get -y install git
cd
git clone https://github.com/hardcoder77/manage-products.git
cd manage-products
cp -R data /var/www/html
cp -R resources /var/www/html
cp -R services /var/www/html
cp .htaccess /var/www/html
cp config.php /var/www/html
cp Server.php /var/www/html
sudo /etc/init.d/apache2 restart

