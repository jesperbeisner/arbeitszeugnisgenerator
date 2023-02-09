.PHONY: help
help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

test: ## Runs CS-Fixer, PHPUnit and PHPStan
	docker-compose exec php composer test

csfixer: ## Run CS-Fixer
	docker-compose exec php composer csfixer

phpunit: ## Run PHPUnit
	docker-compose exec php composer phpunit

phpstan: ## Run PHPStan
	docker-compose exec php composer phpstan
