# Book Collection

A simple project to show a collection of books entries.

## Available features
User are allowed to do the following:
- Show a table of available books
- Add a book to the table
- Delete a book from the table
- Edit the book's author
- Sort the book by title or author
- Search for a book by title or author
- Export the following as CSV or XML file
    - A list with Title and Author
    - A list with only Titles
    - A list with only Authors

> There is no sign-in required.

## Requirements
- [Docker](https://docs.docker.com/install)
- [Docker Compose](https://docs.docker.com/compose/install)

## Local development setup with Docker
1. Clone the repository.
1. Start the containers by running `docker-compose up -d` in the project root.
1. Install the composer packages by running `docker-compose exec laravel composer install`.
1. Create the database by running `docker-compose exec laravel php artisan migrate`,
1. View the application on `http://localhost` (If there is a "Permission denied" error, run `docker-compose exec laravel chown -R www-data storage`).

> Note that the changes you make to local files will be automatically reflected in the container. 

## Persistent database
If you want to make sure that the data in the database persists even if the database container is deleted, add a file named `docker-compose.override.yml` in the project root with the following contents.
```
version: "3.7"

services:
  mysql:
    volumes:
    - mysql:/var/lib/mysql

volumes:
  mysql:
```
Then run the following.
```
docker-compose stop \
  && docker-compose rm -f mysql \
  && docker-compose up -d
``` 

## Testing
Run the following command to execute tests:
```
docker-compose exec laravel ./vendor/bin/phpunit --coverage-html ./coverage-report
```
The resulting coverage report can be viewed at `./coverage-report/index.html`.
