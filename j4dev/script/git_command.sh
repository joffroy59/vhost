#!/bin/bash

function gitInit_main(){
    # git ignore new instance folder
    if [ "$repot_git" == "yes" ]; then
        echo "$instance_name/" >> .gitignore 
        git add .gitignore
        git commit -m "chore($instance_name): create instance"
    fi
}

function gitInit_instance(){
    if [ "$repot_git" == "yes" ]; then
        cd $instance_name
        git init 
        git add .
        git commit -m "chore($instance_name): create instance"
    fi
}