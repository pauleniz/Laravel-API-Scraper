.PHONY: help

SAIL := ./vendor/bin/sail

help: ## Display all available commands.
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

start: ## Start containers.
	$(SAIL) up -d

stop: ## Stop containers.
	$(SAIL) stop

migrate: ## Migrate database migrations.
	$(SAIL) artisan migrate

migrate-seed: ## Migrate database migrations and seed dummy data.
	$(SAIL) artisan migrate --seed

migrate-fresh-seed: ## Refresh database, migrate database migrations and seed dummy data.
	$(SAIL) artisan migrate:fresh --seed

test: ## Setup testing database.
	$(SAIL) artisan test

clear: ## Clear application and configuration cache.
	$(SAIL) artisan optimize:clear

optimize: ## Cache config and routes.
	$(SAIL) artisan optimize && $(SAIL) artisan route:trans:cache

queue: ## Run the queue.
	./vendor/bin/sail artisan queue:work
