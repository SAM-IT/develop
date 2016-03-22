# develop
Homestead Alternative

# Installation
1. Make sure you have installed composer. And that `~/.composer/vendor/bin` is in your path.
2. Go into a terminal and type `composer global require sam-it/develop`
3. Configure DNSMasq, on Ubuntu:
````
echo "address=/dev/192.168.37.2" > /etc/NetworkManager/dnsmasq.d/dev-tld"
echo "local=/dev/" >> /etc/NetworkManager/dnsmasq.d/dev-tld"
````
4. Reload dnsmasq.
````
sudo systemctl stop NetworkManager
sudo pkill dnsmasq
sudo systemctl start NetworkManager
````

5. Make sure all your projects are in `/mnt/data/projects`, or edit the VagrantFile.
6. Start the VM by doing `develop up`

On boot each of your project will be assigned a domain name based on the name of the folder it is in, `projects/testproject` will be available at `http://testproject.dev`.

The system will try to find out the webroot directory of your project using several heuristics:
1. If a file `manifest.json` exists it will use the `root` key.
2. If a file named `index.php` exists in project root it will use that.
3. If a file named `application/index.php` exists, the root will be set to `application` (yii1).
4. If a file named `public/index.php` exists the root will be set to `public`.
5. If none of those are found it will recursively iterate directories and find all `index.php` files.
After that it sorts them based on abscence or presence of the word `public` in the path. It then selects the folder for the first `index.php`.

Besides having all your projects available this repository will add a `http://console.dev` domain that offers some commonly used tools.
1. PHPMyAdmin (https://www.phpmyadmin.net/)
2. MailCatcher (http://mailcatcher.me/)
3. Pimp-my-log (http://pimpmylog.com/)
4. Beanstalk console (https://github.com/ptrofimov/beanstalk_console)


