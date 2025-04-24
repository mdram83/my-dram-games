#My Dram Games
Repository for multiplayer board games application.

##Installation
When setting up repository locally make sure to complete below steps:
1. Define APP_URL. By default it is wsl.localhost for local development. If you want to use different name, make sure to update /.env and /.nginx/conf.d/nginx.conf (rewrite part).
2. Create selfsigned ssl cert 'nginx-dev-selfsigned.crt' and key 'nginx-dev-selfsigned.key' for wsl.localhost domain (or adjusted in point 1) and put them in /.nginx/certs folder.
2.1. Run command:
openssl req -nodes -x509 -days 3650 -newkey rsa:2048 -addext "subjectAltName=DNS:wsl.localhost" -keyout /home/michal/projects/mydramgames/.nginx/certs/nginx-dev-selfsigned.key -out /home/michal/projects/mydramgames/.nginx/certs/nginx-dev-selfsigned.crt;
2.2. Install certificate on your local machine
3. Build docker image (docker compose).
4. Within mydramgames-app container run php artisan key:generate to generate .env file with APP_KEY and adjust remaining settings (see .env.example).
5. Create auth.json file in same folder that composer.json. Add following content:
{
   "github-oauth": {
      "github.com": "REPLACE_WITH_PROPER_GITHUB_TOKEN"
   }
}
6. Within mydramgames-app container run npm and composer update to install required dependencies.
