#!/bin/bash

BACKUP_FOLDER=backuSiteASGLM

function menu_from_array(){
    PS3="Select backup: "
    select slectedBackup; do
        # Check the selected menu slectedBackup number
        if [ 1 -le "$REPLY" ] && [ "$REPLY" -le $# ]; then
            break;
        else
            echo "Wrong selection: Select any number from 1-$#"
        fi
    done
}


function importSiteBackup(){
    INSTANCE_FOLDER=""
    if [ $? -ne 1 ]; then
        INSTANCE_FOLDER="$1"
    else 
        echo "No Folder for instance set"
        exit 1
    fi

    echo "Import site"

    echo "Existing Backup:"
    ls -lh $BACKUP_FOLDER/
    echo "Get Last backup"
    if [ -z "$(ls -A $BACKUP_FOLDER/)" ]; then
        echo "No backup in $BACKUP_FOLDER"
        exit 1
    fi
    LAST_BACKUP=$(ls -1t $BACKUP_FOLDER/ | head -1)
    echo "$LAST_BACKUP"
    
    echo "Choose backup:"
    menu_from_array $(ls -1 $BACKUP_FOLDER/)

    echo "Backup selected: $slectedBackup"

    echo "Remove $INSTANCE_FOLDER/www/*"
    #rm -rf $INSTANCE_FOLDER/www/*
    
    echo "Copy kickstarter in $INSTANCE_FOLDER/www/"
    # cp -p akeebah/kickstart-core-7.1.0/kickstart.php  $INSTANCE_FOLDER/www/ 

    echo "Copy Backup jpa: $BACKUP_FOLDER/$slectedBackup in $INSTANCE_FOLDER/www/"
}