#!/bin/bash

template_folder="ASGLM_template"
helix_template_folder="archive/helixultimate_quickstart_j3_v2.0.9"


function is_healthy() {
    service="$1"
    container_id="$(docker-compose ps -q "$service")"
    health_status="$(docker inspect -f "{{.State.Health.Status}}" "$container_id")"

    if [ "$health_status" = "healthy" ]; then
        return 0
    else
        return 1
    fi
}
