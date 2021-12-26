#!/bin/bash

function docker_up(){
    docker-compose up --build -d  
    if [ $? -ne 0 ]; then
        echo "Error creating docker container"
        exit 1
    fi
}

function docker_up_wait(){
    echo "waiting for Joomla is fully init ..."
    while ! is_healthy joomla; do sleep 1; done
}

function docker_stop(){
    echo "stop joomla ..."
    docker-compose stop 
}

function docker_start(){
    echo "start joomla ..."
    docker-compose start 
}