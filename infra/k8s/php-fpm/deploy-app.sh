#!/usr/bin/env bash

app_name="$1"
pod_name=$(kubectl -n php-fpm get pods -l app=php-fpm --template '{{range .items}}{{.metadata.name}}{{end}}')
echo "Deploying app ${app_name} to a pod ${pod_name}"
kubectl cp "./apps/${app_name}/src/" "php-fpm/${pod_name}:/srv/app/public" -c php-fpm-php-fpm
