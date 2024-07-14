#My Dram Games
Repository for multiplayer board games application.

##Installation
When setting up repository locally make sure to complete below steps:
1. Define APP_URL. By default it is wsl.localhost for local development. If you want to use different name, make sure to update /.env and /.nginx/conf.d/nginx.conf (rewrite part).
2. Create selfsigned ssl cert 'nginx-dev-selfsigned.crt' and key 'nginx-dev-selfsigned.key' for wsl.localhost domain (or adjusted in point 1) and put them in /.nginx/certs folder.
3. Build docker image (docker compose).
4. Within mydramgames-app container run php artisan key:generate to generate .env file with APP_KEY and adjust remaining settings (see .env.example).
5. Within mydramgames-app container run npm and composer update to install required dependencies.
