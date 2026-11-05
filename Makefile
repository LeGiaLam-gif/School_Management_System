# Lenh tat cho nhom. Chay trong container `app` (can `docker compose up -d`).
APP = docker compose exec app

.PHONY: up down logs shell migrate fresh test lint lint-fix analyse ci

up:        ## Khoi dong toan bo dich vu
	docker compose up -d --build

down:      ## Dung dich vu (giu du lieu)
	docker compose down

logs:      ## Xem log ung dung
	docker compose logs -f app queue scheduler

shell:     ## Mo shell trong container app
	$(APP) bash

migrate:   ## Chay migration
	$(APP) php artisan migrate

fresh:     ## Xoa va dung lai CSDL kem du lieu mau
	$(APP) php artisan migrate:fresh --seed

test:      ## Chay toan bo test
	$(APP) php artisan test

lint:      ## Kiem tra style (Pint)
	$(APP) vendor/bin/pint --test

lint-fix:  ## Tu sua style
	$(APP) vendor/bin/pint

analyse:   ## Phan tich tinh (Larastan)
	$(APP) vendor/bin/phpstan analyse --memory-limit=1G

ci: lint analyse test   ## Giong pipeline CI
