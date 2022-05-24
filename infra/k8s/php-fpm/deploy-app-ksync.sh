#!/usr/bin/env bash

base_dir=${PWD}
app_name="$1"
pod_name=$(kubectl -n php-fpm get pods -l app=php-fpm --template '{{range .items}}{{.metadata.name}}{{end}}')
echo "Deploying app ${app_name} to a pod ${pod_name} using ksync"
ksync create -n php-fpm \
  "--name=${app_name}" \
  "--pod=${pod_name}" \
  "${base_dir}/apps/${app_name}/src" \
  "/srv/app/public/src" \
  -c php-fpm-php-fpm
