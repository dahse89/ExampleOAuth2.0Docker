Example OAuth 2.0 to Docker server using MySQL database and Redis for access_token and authorization_codes
==========================

Setup
______

1. You will need a local php enviroment because the client is running on you local and use OAuth to authenticate to docker server.
2. run composer install for src/php/php_oauth_server/www/composer.json
3. run composer install for php_oauth_client/www/vendor/composer.json
4. check docker-composer.yml for conflicting prots
5. start mysql docker and run src/php/php_oauth_server/www/migartion/oauth2.sql
6. build src/nginx/Dockerfile and adjust the name in docker-composer#web.image

