#!/bin/bash

HELIX_STARTER_FOLDER="archive"

function menu_from_array2(){
    PS3="Select version: "
    select itemSelected; do
        # Check the selected menu itemSelected number
        if [ 1 -le "$REPLY" ] && [ "$REPLY" -le $# ]; then
            break;
        else
            echo "Wrong selection: Select any number from 1-$#"
        fi
    done
}

function initHelixStarter(){
    INSTANCE_FOLDER=""
    if [ $? -ne 1 ]; then
        INSTANCE_FOLDER="$1"
    else 
        echo "No Folder for instance set"
        exit 1
    fi

    echo "Init Helix Ultimate Starter site"

    echo "Existing Version:"
    ls -lhd $HELIX_STARTER_FOLDER/*/
    echo "Get Last version"
    if [ -z "$(ls -Ad $HELIX_STARTER_FOLDER/*/)" ]; then
        echo "No backup in $HELIX_STARTER_FOLDER"
        exit 1
    fi
    LAST_VERSION=$(ls -1td $HELIX_STARTER_FOLDER/*/ | head -1)
    echo "$LAST_VERSION"
    
    echo "Choose version:"
    menu_from_array2 $(ls -1d $HELIX_STARTER_FOLDER/*/)

    echo "Version selected: $itemSelected"

    echo "Remove $INSTANCE_FOLDER/www/*"
    sudo rm -rf $INSTANCE_FOLDER/www/*
    
    echo "Inix Helix Ultimate Starter: $itemSelected in $INSTANCE_FOLDER/www/"
    sudo cp -rfp $itemSelected/*  $INSTANCE_FOLDER/www/ 
    sudo chmod -R a+w $INSTANCE_FOLDER/www/

}