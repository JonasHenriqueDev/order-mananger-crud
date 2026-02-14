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
