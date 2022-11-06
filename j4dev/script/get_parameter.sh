#!/bin/bash

function setParameter(){
    read -p "Enter the name of the instance [ASGLM_INSTANCE]: " instance_name
    instance_name=${instance_name:-ASGLM_INSTANCE}
    docker_compose_path="$instance_name/docker-compose.yml"

    read -p "Enter the externla port for Joomla [80]: " external_port_joomla
    external_port_joomla=${external_port_joomla:-80}

    read -p "Creer un repository git [no|yes] [no]: " repot_git
    repot_git=${repot_git:-no}

    read -p "Import ASGLM site [no|yes] [yes]: " import_site_backup
    import_site_backup=${import_site_backup:-yes}

    if [ "$import_site_backup" == "no" ]; then
        read -p "Init Helix Starter site [no|yes] [yes]: " init_helix
        init_helix=${init_helix:-yes}
    fi

    read -p "Set permission [no|yes] [no]: " set_permission
    set_permission=${set_permission:-no}
}


function setConfiguration(){
    external_port_db_default=$(expr $default_bd_port_base + $(expr $external_port_joomla - 80))
    external_port_db=${external_port_db:-${external_port_db_default}}

    external_port_db_admin_default=$(expr $default_bd_port_admin_base + $(expr $external_port_joomla - 80))
    external_port_db_admin=${external_port_db_admin:-${external_port_db_admin_default}}

    context_name="$(pwd)/$instance_name/"
    volumes_uploads_ini="${context_name}uploads.ini"
    volumes_www="${context_name}www"
    volumes_db="${context_name}db"    
}

function showConfiguration(){
    echo -e "Configuration:"
    echo -e "  External Joomla Port   : $external_port_joomla"
    echo -e "  External Db Port       : $external_port_db"
    echo -e "  External Db Admin Port : $external_port_db_admin"
    echo -e "  Contaxt name           : $context_name"
    echo -e "  Volumes upload.ini     : $volumes_uploads_ini"
    echo -e "  Volumes www            : $volumes_www"
    echo -e "  Volumes db             : $volumes_db"
    echo -e "  Use git                : $repot_git"
    echo -e "  Set Folder permission  : $set_permission"
    echo -e "  Import site backup     : $import_site_backup"
    echo -e "  Init Helix Starter     : $init_helix"
    echo -e ""

    read -p "Configuration ok [yes|no] [yes]:" configuration_ok
    configuration_ok=${configuration_ok:-yes}
    echo "$configuration_ok"
}

function createInstance(){
    if [ -d $instance_name ]; then 
        echo "$instance_name exists"
        exit 1 
    fi
    echo "Create instance $instance_name"
    cp -prf $template_folder $instance_name
}

function adaptInstance(){
    echo "Adapt Template"
    sed -i -e "s;%EXTERNAL_PORT_JOOMLA%;$external_port_joomla;g" -e "s;%EXTERNAL_PORT_DB%;$external_port_db;g" -e "s;%EXTERNAL_PORT_DB_ADMIN%;$external_port_db_admin;g" $docker_compose_path
    sed -i -e "s;%INSTANE_NAME%;$instance_name;g" -e "s;%CONTEXT_NAME%;$context_name;g"  $docker_compose_path
    sed -i -e "s;%VOLUMES_UPLOADS_INI%;$volumes_uploads_ini;g" -e "s;%VOLUMES_WWW%;$volumes_www;g" -e "s;%VOLUMES_DB%;$volumes_db;g"  $docker_compose_path
}