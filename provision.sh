#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
/develop/bin/develop prepare

sudo apt-get update && sudo apt-get install -y php-cli

#install composer
if ! type "composer" > /dev/null; then
  echo "Installing composer.";
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
else
  sudo composer -n self-update
fi

#install mailcatcher.
if ! type "mailcatcher" > /dev/null; then
  sudo gem install activesupport
  sudo gem install mailcatcher
fi

#install sass.
if ! type "sass" > /dev/null; then
  sudo gem install sass
fi

# remove nginx default site.
if [ -f //etc/nginx/sites-enabled/default ]; then
sudo rm /etc/nginx/sites-enabled/default
fi

# Fix ownership of all folders in home dir.
sudo chown -R vagrant:vagrant /home/vagrant


# Symlink configuration files.
php /develop/bin/develop nginx /projects > /tmp/projects.conf
if [ ! -f /etc/nginx/conf.d/projects.conf ]; then
  sudo ln -s /tmp/projects.conf /etc/nginx/conf.d/projects.conf
fi

if [ ! -f /etc/nginx/sites-enabled/console.conf ]; then
  sudo ln -s /develop/config/nginx-console.conf /etc/nginx/sites-enabled/console.conf
fi

if [ ! -f /etc/php5/fpm/pool.d/develop-pool.conf ]; then
  sudo ln -s /develop/config/develop-pool.conf /etc/php5/fpm/pool.d/develop-pool.conf
fi

# Reload php and nginx.
sudo systemctl reload php5-fpm
sudo systemctl status php5-fpm
sudo systemctl reload nginx
sudo systemctl status nginx

# Allow reading logs
if [ ! -f /var/log/fpm-php.develop.log ]; then
  sudo touch /var/log/fpm-php.develop.log
  sudo chown vagrant:vagrant /var/log/fpm-php.develop.log
fi
sudo chmod o+xr /var/log/nginx

ping -c 1 -w 5 192.168.37.1

# Launch mailcatcher.
sudo pkill -kill mailcatcher
sudo mailcatcher --no-quit --smtp-port 25

# Set vim as editor.
sudo update-alternatives --set editor /usr/bin/vim.basic

# Add bashrc.
grep -q -F '. /develop/config/bashrc' ~/.bashrc || echo '. "/develop/config/bashrc"' >> ~/.bashrc