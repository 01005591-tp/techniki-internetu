check-delete:
	if [ -n "${DELETE}" ]; then \
    	return 0; \
    else \
        echo "If you really want to delete resource, provide DELETE variable to make call," \
        "e.g. make delete-resource DELETE=1"; \
        return 1; \
    fi;

mysql-install-local:
	 kubectl apply -k infra/k8s/my-sql/kustomize/overlays/local

mysql-delete-local: check-delete
	kubectl delete -k infra/k8s/my-sql/kustomize/overlays/local
