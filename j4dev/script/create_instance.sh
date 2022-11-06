#!/bin/bash

function createInstance(){
    echo "Create Instance $instance_name"
    if [ -d $instance_name ]; then 
        echo "$instance_name exists"
        exit 1 
    fi
    echo "Create instance $instance_name (joomla version: $joomla_version)"
    cp -prf $template_folder $instance_name
    cp -pf  $instance_name/dockerfile.joomla$joomla_version $instance_name/dockerfile
}

function adaptInstance(){
    echo "Adapt Template"
    sed -i -e "s;%EXTERNAL_PORT_JOOMLA%;$external_port_joomla;g" -e "s;%EXTERNAL_PORT_DB%;$external_port_db;g" -e "s;%EXTERNAL_PORT_DB_ADMIN%;$external_port_db_admin;g" $docker_compose_path
    sed -i -e "s;%INSTANE_NAME%;$instance_name;g" -e "s;%CONTEXT_NAME%;$context_name;g"  $docker_compose_path
    sed -i -e "s;%VOLUMES_UPLOADS_INI%;$volumes_uploads_ini;g" -e "s;%VOLUMES_WWW%;$volumes_www;g" -e "s;%VOLUMES_DB%;$volumes_db;g"  $docker_compose_path
}