PORT=8000
HOST=localhost

COM_COLOR   = \033[0;34m
OBJ_COLOR   = \033[0;36m
OK_COLOR    = \033[0;32m
ERROR_COLOR = \033[0;31m
WARN_COLOR  = \033[0;33m
NO_COLOR    = \033[m

.PHONY: server help install

help: ## Affiche l'aide
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-10s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

server: install ## Lance le serveur
	@echo "Starting server on $(OK_COLOR)http://$(HOST):$(PORT)$(NO_COLOR)"
	@php -S $(HOST):$(PORT) -t public/ -d display_errors=1

install: vendor composer.json ## Installe les dépendances du projet

vendor: composer.json ## Télécharge les dépendances du projet
	composer install

composer.lock: composer.json ## Met à jour les dépendances du projet
	composer update