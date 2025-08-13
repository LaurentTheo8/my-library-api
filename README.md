# My Library API

A REST API for a library management system built with Symfony 7 and API Platform.  
It provides endpoints for managing books, authors, categories, and user authentication with role-based access control.

## Features

- CRUD operations for books, authors, and categories
- User authentication using JWT
- Role-based access control
- CORS configured for local development

## Requirements

- Docker & Docker Compose
- PHP 8.2+ (handled via Docker)
- Composer (optional if using Docker)

## Setup

Clone the repository:

```bash
git clone https://github.com/LaurentTheo8/my-library-api
cd my-librairi-api
``` 

Create a .env.local file to configure your environment variables (see example below):
```bash
###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=your_app_secret_here
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL="postgresql://app:MonSuperMdp@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
POSTGRES_VERSION=16
POSTGRES_DB=app
POSTGRES_USER=app
POSTGRES_PASSWORD=MonSuperMdp
###< doctrine/doctrine-bundle ###

###> nelmio/cors-bundle ###
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
###< nelmio/cors-bundle ###

###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_jwt_passphrase_here
###< lexik/jwt-authentication-bundle ###
```

Docker Setup (see example below):

compose.override.yaml
```bash
services:
###> doctrine/doctrine-bundle ###
  database:
    ports:
      - "5432:5432"
###< doctrine/doctrine-bundle ###
``` 
compose.yaml
```bash

services:
###> doctrine/doctrine-bundle ###
  database:
    image: postgres:${POSTGRES_VERSION:-16}-alpine
    environment:
      POSTGRES_DB: ${POSTGRES_DB:-app}
      # You should definitely change the password in production
      POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:-!ChangeMe!}
      POSTGRES_USER: ${POSTGRES_USER:-app}
    healthcheck:
      test: ["CMD", "pg_isready", "-d", "${POSTGRES_DB:-app}", "-U", "${POSTGRES_USER:-app}"]
      timeout: 5s
      retries: 5
      start_period: 60s
    volumes:
      - database_data:/var/lib/postgresql/data:rw
      # You may use a bind-mounted host directory instead, so that it is harder to accidentally remove the volume and lose all your data!
      # - ./docker/db/data:/var/lib/postgresql/data:rw
###< doctrine/doctrine-bundle ###

volumes:
###> doctrine/doctrine-bundle ###
  database_data:
###< doctrine/doctrine-bundle ###
```

Build and start the containers:

```bash
docker-compose up -d
```

Dependencies:
```bash
composer install
```

JWT Key Setup

Create the folder for JWT keys and generate them:
```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 -passout pass:your_jwt_passphrase_here 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem -passin pass:your_jwt_passphrase_here
```

Development Server

With Symfony CLI, start the Symfony server:
```bash
symfony server:start
```

Database Setup

Create the database and run migrations:
```bash
php bin/console doctrine:migrations:migrate
```

Load Fixtures
```bash
php bin/console doctrine:fixtures:load
```
API Documentation

API Platform provides an interactive Swagger UI:
```bash
http://localhost:8000/api
```

## How to use
Cheat endpoint to make an admin Account
```bash
http://127.0.0.1:8000/api/create-admin
```

Once the account created, go to Login Check->POST /auth

Copy the user token in the response.

You can now click on the button "Authorize" to paste your token and get access to the other endpoint. 

You can register on the front-end nuxt to create an account or login with your admin credentials. 

The new registered account by the front will not have acces to the admin section. 

You can manage it by changing is rôle with the post /api/users endpoint. Rôle authorized : ["ROLE_USER", "ROLE_ADMIN", "ROLE_LIBRAIRIAN"]



