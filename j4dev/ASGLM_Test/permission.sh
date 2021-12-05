cat <<- EOD
WLUSER=joffroy
PROD_FILE="LICENSE.txt htaccess.txt web.config.txt README.txt configuration.php index.php robots.txt.dist"
PROD_DIR="administrator cache components includes installation layouts media plugins templates api cli images language libraries modules tmp"
cd www
find \$(echo \$PROD_DIR) -type -d -exec sudo chmod g+s {} \;
sudo chmod -R g+w  \$(echo \$PROD_DIR)
sudo chown -R www-data:${WLUSER} \$(echo \$PROD_DIR)
sudo chown -R www-data:${WLUSER} \$(echo \$PROD_FILE)
EOD