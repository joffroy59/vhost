#!/bin/bash

source script/module.sh

default_bd_port_base=13306
default_bd_port_admin_base=90

setParameter
setConfiguration
showConfiguration

if [ "$configuration_ok" == "yes" ]; then
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

    if [ "$import_site_backup" == "no" -a "$init_helix" == "yes" ]; then
        initHelixStarter $instance_name
    fi

    if [ "$set_permission" == "yes" ]; then
        setPermission $instance_name
    fi

    cd $instance_name
    docker_start
    cd ..

    echo "open : http://localhost:${external_port_joomla}/"
    echo ""
    echo "configuration DB :"
    echo "  hostname          JOOMLA_DB_HOST: joomladb"
    echo "  user                            : root"
    echo "  user password JOOMLA_DB_PASSWORD: example"

    echo "PHP MyAdmin:"
    echo "open : http://localhost:${external_port_db_admin}/"
else
    echo "Stop"
fi

