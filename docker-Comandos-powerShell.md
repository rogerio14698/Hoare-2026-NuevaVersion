docker run --rm horae-php:8.1.25 php -v
docker run --rm -it -v ${PWD}:/var/www/html -p 9000:9000 horae-php:8.1.25