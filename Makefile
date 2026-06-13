.PHONY: fix-perms setup

setup:
	@echo "Veuillez exécuter 'docker compose up -d --build' pour construire les conteneurs."

fix-perms:
	sudo chown -R $(USER):www-data storage bootstrap/cache
	sudo chmod -R 775 storage bootstrap/cache
