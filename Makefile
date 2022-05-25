php-fpm-build:
	docker build -t php-fpm:latest ./infra/k8s/php-fpm/build

p1-app-deploy:
	./infra/k8s/php-fpm/deploy-app.sh p1

p1-app-db-liquibase-local:
	apps/p1/database/liquibase/liquibase update \
		--defaults-file=apps/p1/database/migration/liquibase.properties

check-delete:
	if [ -n "${DELETE}" ]; then \
    	return 0; \
    else \
        echo "If you really want to delete resource, provide DELETE variable to make call," \
        "e.g. make delete-resource DELETE=1"; \
        return 1; \
    fi;

php-fpm-install-local:
	kubectl apply -k infra/k8s/php-fpm/kustomize/overlays/local

php-fpm-delete-local: check-delete
	kubectl delete -k infra/k8s/php-fpm/kustomize/overlays/local

mysql-install-local:
	kubectl apply -k infra/k8s/my-sql/kustomize/overlays/local

mysql-delete-local: check-delete
	kubectl delete -k infra/k8s/my-sql/kustomize/overlays/local
