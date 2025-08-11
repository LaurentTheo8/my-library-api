# Makefile

create-entity:
	php bin/console make:entity --api-resource

fixtures:
	php bin/console doctrine:fixtures:load