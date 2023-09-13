1. To setup the project locally:
````
docker build -t rates_task_image .
docker-compose up -d
docker-compose exec php-cli sh
composer install
````

2. Define rate_api_key parameter in config/services.yaml as api.exchangeratesapi.io requests auth now.