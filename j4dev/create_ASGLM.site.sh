#!/bin/bash

source script/module.sh

default_bd_port_base=13306
default_bd_port_admin_base=90

setParameter
setConfiguration
showConfiguration

createInstance
adaptInstance

gitInit_main
gitInit_instance

cd $instance_name
docker_up
docker_up_wait
docker_stop
cd ..

if [ "$import_site_backup" == "yes" ]; then
    importSiteBackup $instance_name
fi

if [ "$set_permission" == "yes" ]; then
    setPermission $instance_name
fi
