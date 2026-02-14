UID := $(shell id -u)
GID := $(shell id -g)

COMPOSE := cd backend && UID=$(UID) GID=$(GID) docker compose

up:
	$(COMPOSE) up --build

down:
	cd backend && docker compose down

restart:
	cd backend && docker compose down
	$(COMPOSE) up --build

build:
	$(COMPOSE) build

logs:
	cd backend && docker compose logs -f

bash:
	cd backend && docker compose exec app bash

horizon:
	cd backend && docker compose exec app php artisan horizon

setup:
	$(COMPOSE) up -d
	cd backend && docker compose exec app rm -rf /var/www/vendor
	cd backend && docker compose exec app composer install
	cd backend && docker compose exec app php artisan key:generate
	cd backend && docker compose exec app php artisan migrate

fix-permissions:
	@echo "🔧 Corrigindo permissões..."
	cd backend && docker compose exec -u root app chown -R www-data:www-data /var/www/vendor
	cd backend && docker compose exec -u root app chown -R www-data:www-data /var/www/storage
	cd backend && docker compose exec -u root app chown -R www-data:www-data /var/www/bootstrap/cache

composer-install:
	cd backend && docker compose exec app composer install

composer-update:
	cd backend && docker compose exec app composer update

clean:
	cd backend && docker compose down -v
	rm -rf backend/vendor

help:
	@echo "Comandos disponíveis:"
	@echo "  make up                - Sobe os containers"
	@echo "  make down              - Para os containers"
	@echo "  make restart           - Reinicia os containers"
	@echo "  make build             - Reconstrói as imagens"
	@echo "  make setup             - Configura o projeto do zero"
	@echo "  make fix-permissions   - Corrige permissões do vendor e storage"
	@echo "  make composer-install  - Instala dependências do Composer"
	@echo "  make composer-update   - Atualiza dependências do Composer"
	@echo "  make clean             - Remove tudo e limpa volumes"
	@echo "  make logs              - Mostra logs dos containers"
	@echo "  make bash              - Acessa o bash do container app"
	@echo "  make horizon           - Executa o Horizon"
