up:
	docker compose up -d
down:
	docker compose down
install:
	docker exec app composer install
	docker exec app npm install
	docker exec app npm run dev
db-create:
	docker exec app php bin/console doctrine:database:create
migrate:
	docker exec app php bin/console doctrine:migrations:migrate
serve:
	docker exec app php -S 0.0.0.0:8000 -t public/
