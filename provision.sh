#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
echo "Updating apt-cache";
sudo apt-get update > /dev/null
echo "Installing packages...";
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password secret'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password secret'
sudo apt-get -y install nginx php5-fpm php5-cli mysql-server git
sudo apt-get -y dist-upgrade
echo "Installing composer.";
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
# Fix ownership of all folders in home dir.
sudo chown -R vagrant:vagrant /home/vagrant
#sudo mv /tmp/develop /develop
#sudo git --git-dir /develop pull