run:
	symfony server:stop;
	symfony server:start;

stop:
	symfony server:stop;

clear:
	php bin/console cache:clear;

schema_v:
	php bin/console doctrine:schema:validate;

diff:
	php bin/console doctrine:migrations:diff;

mig:
	php bin/console doctrine:migrations:migrate;
prev:
	php bin/console doctrine:migrations:migrate prev;
mig_p:
	php bin/console doctrine:migrations:migrate prev

watch:
	npm run watch;

t:
	php bin/console translation:download;
	php bin/console app:translation-version:update;

r_debug:
	php bin/console debug:router

dump_routes:
	php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json

run_fixture:
	php bin/console  sylius:fixtures:load vote_analitycs_attributes

stan:
	vendor/bin/phpstan analyse

style:
	./vendor/bin/php-cs-fixer fix src

assets:
	php bin/console assets:install

consume:
	php bin/console messenger:consume -vv

jwt:
	php bin/console lexik:jwt:generate-keypair

to_postman:
	bin/console api:openapi:export --output=swagger_docs.json
