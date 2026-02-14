UID := $(shell id -u)
GID := $(shell id -g)

COMPOSE := cd backend && UID=$(UID) GID=$(GID) docker compose

up:
	$(COMPOSE) up --build -d

down:
	cd backend && docker compose down

restart:
	cd backend && docker compose down
	$(COMPOSE) up --build -d

build:
	$(COMPOSE) build

logs:
	cd backend && docker compose logs -f

bash:
	cd backend && docker compose exec app bash

horizon:
	cd backend && docker compose exec app php artisan horizon

composer-install:
	cd backend && docker compose exec app composer install

key:
	cd backend && docker compose exec app php artisan key:generate

migrate:
	cd backend && docker compose exec app php artisan migrate

migrate-fresh:
	cd backend && docker compose exec app php artisan migrate:fresh --seed

seed:
	cd backend && docker compose exec app php artisan db:seed

clear:
	cd backend && docker compose exec app php artisan optimize:clear

tinker:
	cd backend && docker compose exec app php artisan tinker

setup:
	cd backend && docker compose exec app composer install
	cd backend && docker compose exec app php artisan key:generate
	cd backend && docker compose exec app php artisan migrate --force --seed
