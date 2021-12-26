#!/bin/bash

source script/common.sh

template_folder="ASGLM_template"

default_bd_port_base=13306
default_bd_port_admin_base=90

# parametrage 
read -p "Enter the name of the instance [ASGLM_INSTANCE]: " isntance_name
isntance_name=${isntance_name:-ASGLM_INSTANCE}
docker_compose_path="$isntance_name/docker-compose.yml"

read -p "Enter the externla port for Joomla [80]: " external_port_joomla
external_port_joomla=${external_port_joomla:-80}

read -p "Creer un repository git [no|yes] [no]: " repot_git
repot_git=${repot_git:-no}

external_port_db_default=$(expr $default_bd_port_base + $(expr $external_port_joomla - 80))
external_port_db=${external_port_db:-${external_port_db_default}}

external_port_db_admin_default=$(expr $default_bd_port_admin_base + $(expr $external_port_joomla - 80))
external_port_db_admin=${external_port_db_admin:-${external_port_db_admin_default}}

context_name="$(pwd)/$isntance_name/"
volumes_uploads_ini="${context_name}uploads.ini"
volumes_www="${context_name}www"
volumes_db="${context_name}db"

if [ -d $isntance_name ]; then 
    echo "$isntance_name exists"
    exit 1 
fi

echo "Create instance $isntance_name"
cp -prf $template_folder $isntance_name
echo -e "adapt instance with conf:"
echo -e "\tExternal Joomla Port: $external_port_joomla"
echo -e "\tExternal Db Port: $external_port_db"
echo -e "\tExternal Db Admin Port: $external_port_db_admin"
echo -e "\tContaxt name: $context_name"
echo -e "\tVolumes upload.ini: $volumes_uploads_ini}"
echo -e "\tVolumes www: $volumes_www"
echo -e "\tVolumes db: $volumes_db"
echo -e "repo git: $repot_git"
echo -e "\tVolumes db: $volumes_db"

echo -e "\t"
sed -i -e "s;%EXTERNAL_PORT_JOOMLA%;$external_port_joomla;g" -e "s;%EXTERNAL_PORT_DB%;$external_port_db;g" -e "s;%EXTERNAL_PORT_DB_ADMIN%;$external_port_db_admin;g" $docker_compose_path
sed -i -e "s;%INSTANE_NAME%;$isntance_name;g" -e "s;%CONTEXT_NAME%;$context_name;g"  $docker_compose_path
sed -i -e "s;%VOLUMES_UPLOADS_INI%;$volumes_uploads_ini;g" -e "s;%VOLUMES_WWW%;$volumes_www;g" -e "s;%VOLUMES_DB%;$volumes_db;g"  $docker_compose_path

# git ignore new instance folder
echo "$isntance_name/" >> .gitignore 
git_cmd add .gitignore
git_cmd commit -m "chore($isntance_name): create instance"

cd $isntance_name
git_cmd init 
git_cmd add .
git_cmd commit -m "chore($isntance_name): create instance"

docker-compose up -d 
if [ $? -ne 0 ]; then
    echo "Error creating docker container"
    exit 1
fi

echo "waiting for Joomla is fully init ..."
while ! is_healthy joomla; do sleep 1; done

# echo "stop joomla ..."
docker-compose stop 

# sh permission.sh

# sh prepare_import_akeebah.sh
