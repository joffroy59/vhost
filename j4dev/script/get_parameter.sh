#!/bin/bash

function setParameter() {
    read -p "Enter the name of the instance [ASGLM_INSTANCE]: " instance_name
    instance_name=${instance_name:-ASGLM_INSTANCE}
    docker_compose_path="$instance_name/docker-compose.yml"

    read -p "Enter the externla port for Joomla [80]: " external_port_joomla
    external_port_joomla=${external_port_joomla:-80}

    read -p "Creer un repository git [no|yes] [yes]: " repot_git
    repot_git=${repot_git:-yes}

    read -p "Import ASGLM site [no|yes] [no]: " import_site_backup
    import_site_backup=${import_site_backup:-no}

    if [ "$import_site_backup" == "yes" -o "$init_helix" == "yes" ]; then
        read -p "Version de joomal de base joomla(3/4) [4]: " joomla_version
        joomla_version=${joomla_version:-4}
    fi

    if [ "$import_site_backup" == "no" ]; then
        read -p "Init Helix Starter site [no|yes] [no]: " init_helix
        init_helix=${init_helix:-no}
        if [ "$init_helix" == "no" ]; then
            read -p "Init Joomla 5 Starter site [no|yes] [yes]: " ininit_joomla5it_helix
            init_joomla5=${init_joomla5:-yes}
            joomla_version=5
        fi
    fi

    read -p "Set permission [no|yes] [yes]: " set_permission
    set_permission=${set_permission:-yes}

    if [ "$init_helix" == "yes" ]; then
        setJoomla5Base
    else
        if [ "$init_joomla5" == "yes" ]; then
            setHelixStarterBase
        fi
    fi

    joomla_base=$itemSelected
    echo -e "  Joomla base         : $joomla_base"
}

function setConfiguration() {
    external_port_db_default=$(expr $default_bd_port_base + $(expr $external_port_joomla - 80))
    external_port_db=${external_port_db:-${external_port_db_default}}

    external_port_db_admin_default=$(expr $default_bd_port_admin_base + $(expr $external_port_joomla - 80))
    external_port_db_admin=${external_port_db_admin:-${external_port_db_admin_default}}

    context_name="$(pwd)/$instance_name/"
    volumes_uploads_ini="${context_name}uploads.ini"
    volumes_www="${context_name}www"
    volumes_db="${context_name}db"

    joomla5_base = "TODO"

}

function showConfiguration() {
    echo -e "Configuration:"
    echo -e "  Joomla version         : joomla$joomla_version"
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
    echo -e "  Init Joomla 5          : $init_joomla5"
    echo -e "  joomla_base            : $joomla_base"
    echo -e ""

    read -p "Configuration ok [yes|no] [yes]:" configuration_ok
    configuration_ok=${configuration_ok:-yes}
    echo "$configuration_ok"
}
