#!/bin/bash

BASEDIR=$(pwd)

red=`tput setaf 1`
green=`tput setaf 2`
reset=`tput sgr0`


sudo apt install nginx -y

dpkg -s mongodb-org 2>/dev/null >/dev/null 
if [ $? -eq 0 ]; then
    echo "Mongo  is installed!"
else
    sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 9DA31620334BD75D9DCB49F368818C72E52529D4

    echo "deb [ arch=amd64 ] https://repo.mongodb.org/apt/ubuntu bionic/mongodb-org/4.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-4.0.list

    sudo apt-get update

    sudo apt-get install -y mongodb-org

    echo "mongodb-org hold" | sudo dpkg --set-selections
    echo "mongodb-org-server hold" | sudo dpkg --set-selections
    echo "mongodb-org-shell hold" | sudo dpkg --set-selections
    echo "mongodb-org-mongos hold" | sudo dpkg --set-selections
    echo "mongodb-org-tools hold" | sudo dpkg --set-selections
fi


sudo service mongod start

sudo apt install php7.2-fpm php7.2-xml php7.2-curl php7.2-mbstring php7.2-gd php7.2-zip -y

sudo apt purge apache2* -y

sudo apt-get install php7.2-dev -y
sudo apt-get install libcurl4-openssl-dev pkg-config libssl-dev libsslcommon2-dev -y
sudo apt-get install php-pear -y
sudo pecl install mongodb -y

sudo sh -c "echo 'extension=mongodb.so' > /etc/php/7.2/cli/conf.d/20-mongodb.ini"
sudo sh -c "echo 'extension=mongodb.so' > /etc/php/7.2/fpm/conf.d/20-mongodb.ini"


sudo service php7.2-fpm restart

sudo apt install curl php7.2-cli unzip -y
curl -sS https://getcomposer.org/installer -o composer-setup.php


HASH=$(curl -L https://composer.github.io/installer.sig)
HASH="$HASH"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

sudo php composer-setup.php --install-dir=/usr/bin --filename=composer

sudo rm composer-setup.php


sudo chmod -R 777 data/

sudo chmod -R 777 public/tami/

sudo chown -R "$USER" "/home/$USER/.composer"

composer update


read -p "setup host server nginx domain (yes/no)?[${green}yes${reset}|${red}no${reset}]: " CONT
if [ "$CONT" = "yes" ]; then
    read -p "Enter server name (e.g mydomain.com): " DOMAIN
    if [ "$DOMAIN" ]; then
        if grep -q "$DOMAIN" "/etc/hosts"; then
            echo "$DOMAIN exists in /etc/hosts file"
        else
            sudo sh -c "echo '127.0.0.1 $DOMAIN' >> /etc/hosts"
        fi
        
            if [ -f "/etc/nginx/sites-available/$DOMAIN" ]
            then
                    echo "/etc/nginx/sites-available/$DOMAIN existed"
            else
                sudo touch "/etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo 'server{'  >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  listen 80;'  >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  server_name' $DOMAIN';'  >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  root '$BASEDIR'/public;'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  error_log' $BASEDIR'/logs/error.log;'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  index index.php;'    >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  location / {'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '      try_files \$uri \$uri/ /index.php?\$is_args\$args;'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  }'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  location ~ \.php$ {'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '      include snippets/fastcgi-php.conf;'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '      fastcgi_pass 127.0.0.1:9000;'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '  }'   >> /etc/nginx/sites-available/$DOMAIN"
                    sudo sh -c "echo '}'  >> /etc/nginx/sites-available/$DOMAIN"

                sudo ln -s "/etc/nginx/sites-available/$DOMAIN"  "/etc/nginx/sites-enabled/"


                sudo apt install crudini -y
                
                sudo crudini --set /etc/php/7.2/fpm/pool.d/www.conf www listen 127.0.0.1:9000
                
                sudo service nginx start
                sudo service nginx reload
                sudo service php7.2-fpm restart
            fi
           
        
    else
        echo "${red}Server is empty${reset}, server nginx not config host";
    fi

    cp "$BASEDIR/vendor/tami/system/src/system/Database/bin/doctrine.mongo.local.php.dist" "$BASEDIR/config/php/autoload/doctrine.mongo.local.php" 
else
  echo "no config nginx";


    read -p "Copy config database mongodb (yes/no)?[${green}yes${reset}|${red}no${reset}]: " CONT
    if [ "$CONT" = "yes" ]; then

        cp "$BASEDIR/vendor/tami/system/src/system/Database/bin/doctrine.mongo.local.php.dist" "$BASEDIR/config/php/autoload/doctrine.mongo.local.php" 

    fi
fi

echo "Done."