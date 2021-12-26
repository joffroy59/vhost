#!/bin/bash

if [ $# -eq 1 ]; then
    cd $1
fi
exit 1 

WLUSER=joffroy
PROD_FILE="LICENSE.txt htaccess.txt web.config.txt README.txt configuration.php index.php robots.txt.dist"
PROD_DIR="administrator cache components includes installation layouts media plugins templates api cli images language libraries modules tmp"
cd www
find "$PROD_DIR" -type -d -exec sudo chmod g+s {} \;
sudo chmod -R g+w $PROD_DIR
sudo chown -R www-data:${WLUSER} $PROD_DIR
sudo chown -R www-data:${WLUSER} $PROD_FILE
