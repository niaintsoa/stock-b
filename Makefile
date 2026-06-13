.PHONY: setup fix-perms up down restart build shell artisan migrate seed logs tinker

# --- Variables ---
APP_CONTAINER=app

# --- Commandes Docker ---
up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

build:
	docker compose up -d --build

logs:
	docker compose logs -f

shell:
	docker compose exec $(APP_CONTAINER) bash

# --- Commandes Laravel ---
setup:
	@echo "Lancement des conteneurs..."
	make build
	@echo "Application des permissions..."
	make fix-perms
	@echo "Le projet est prêt !"

fix-perms:
	sudo chown -R $(USER):www-data storage bootstrap/cache
	sudo chmod -R 775 storage bootstrap/cache

artisan:
	docker compose exec $(APP_CONTAINER) php artisan $(CMD)

migrate:
	docker compose exec $(APP_CONTAINER) php artisan migrate

seed:
	docker compose exec $(APP_CONTAINER) php artisan db:seed

tinker:
	docker compose exec $(APP_CONTAINER) php artisan tinker

composer-install:
	docker compose exec $(APP_CONTAINER) composer install
