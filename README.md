1. To run the app locally:

Create .env.local config copying from .env config to set rates API token:

RATES_API_TOKEN=YOUR_TOKEN

````
docker-compose build
docker-compose up -d
docker-compose exec app sh
php app.php input.txt
````

2. To run the tests after step 1:

````
docker-compose exec app vendor/bin/phpunit
````