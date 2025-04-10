# Anime API Project Documentation

## Table of Contents

-   [Overview](#overview)
-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Usage](#usage)
    -   [Data Import](#data-import)
    -   [API Endpoints](#api-endpoints) 
-   [Error Handling](#error-handling)
-   [Rate Limiting](#rate-limiting)
-   [Database Structure](#database-structure)
-   [File Structure](#project-file-structure-map)

## Overview

This project is a Laravel-based API that fetches and manages anime data from the Jikan API. It provides endpoints to access anime information in both Polish and English languages, with proper slug handling and data validation.

### Key Features

-   Fetches top 100 anime from Jikan API
-   Stores data in MySQL with language-specific slugs
-   RESTful API endpoints with proper error handling
-   Rate limiting for Jikan API requests
-   Comprehensive search and filtering capabilities
-   Language support (PL/EN)

## Requirements

-   PHP 8.1 or higher
-   MySQL 5.7 or higher
-   Composer
-   Laravel 10.x
-   Node.js & NPM (for frontend assets)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/fxcjahid/laravel-jikan-api
cd laravel-jikan-api
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
npm run build
```

4. Create environment file:

```bash
cp .env.example .env
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Configure your database in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anime_api
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations:

```bash
php artisan migrate
```

### Rate Limiting Configuration

You can adjust the Jikan API rate limiting in `app/Services/JikanApiService.php`:

```php
private const REQUEST_DELAY = 1; // seconds between requests
```

## Usage

### Data Import

1. Run the initial data import:

```bash
php artisan anime:import
```

Options:

-   `--force`: Force import even if recently run

```bash
php artisan anime:import --force
```

The import command will:

-   Fetch top 100 anime from Jikan API
-   Create slugs for both languages
-   Store data in the database
-   Handle rate limiting automatically

### API Endpoints

#### Get Anime by Slug

```
GET http://127.0.0.1:8000/api/anime/{slug}?lang={lang}
```

Parameters:

-   `slug`: Anime slug in the appropriate language
-   `lang`: Language code (pl/en)

Example requests:

```bash
# English version
curl "http://127.0.0.1:8000/api/anime/fullmetal-alchemist?lang=en"

# Polish version
curl "http://127.0.0.1:8000/api/anime/fullmetal-alchemist?lang=pl"
```

Success Response (200):

```json
{
    "data": {
        "id": 1,
        "mal_id": 5114,
        "title": "Fullmetal Alchemist: Brotherhood",
        "slug": "fullmetal-alchemist",
        "synopsis": "...",
        "type": "TV",
        "episodes": 64,
        "status": "Finished Airing",
        "score": 9.15,
        "rank": 1,
        "popularity": 3,
        "aired": {
            "from": "2009-04-05",
            "to": "2010-07-04"
        }
    }
}
```

Example test scenarios:

```bash
# Valid English slug
GET http://127.0.0.1:8000/api/anime/fullmetal-alchemist?lang=en

# Invalid language code
GET http://127.0.0.1:8000/api/anime/fullmetal-alchemist?lang=de

# Non-existent slug
GET http://127.0.0.1:8000/api/anime/non-existent-anime?lang=en
```

## Error Handling

The API uses proper HTTP status codes:

-   200: Successful request
-   404: Anime not found or wrong language
-   422: Invalid parameters
-   429: Too many requests (rate limit)
-   500: Server error

Error Response Format:

```json
{
    "message": "Error message",
    "errors": {
        "field": ["Error details"]
    }
}
```

## Rate Limiting

The application implements two types of rate limiting:

1. Jikan API rate limiting (1 request per second)
2. API endpoint rate limiting (60 requests per minute per IP)

## Database Structure

### Animes Table

```sql
CREATE TABLE animes (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    mal_id int unsigned NOT NULL,
    titles json NOT NULL,
    slugs json NOT NULL,
    synopsis text,
    type varchar(255),
    episodes int,
    score decimal(3,2),
    rank int,
    popularity int,
    status varchar(255),
    aired_from date,
    aired_to date,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY animes_mal_id_unique (mal_id)
)
```

### Project File Structure Map

```

anime-api/

├── app/

│   ├── Console/

│   │   └── Commands/

│   │       └── ImportAnimeData.php         # Anime import command

│   │

│   ├── Exceptions/

│   │   ├── Handler.php                     # Global exception handler

│   │   └── JikanApiException.php           # Custom API exception

│   │

│   ├── Http/

│   │   ├── Controllers/

│   │   │   ├── AnimeController.php         # Web controller for views

│   │   │   └── Api/

│   │   │       └── AnimeController.php     # API controller

│   │   │

│   │   ├── Requests/

│   │   │   └── ShowAnimeRequest.php        # API request validation

│   │   │

│   │   └── Resources/

│   │       └── AnimeResource.php           # API resource transformer

│   │

│   ├── Models/

│   │   └── Anime.php                       # Anime model

│   │

│   └── Services/

│       └── JikanApiService.php             # Jikan API service

│

├── database/

│   └── migrations/

│       └── [timestamp]_create_animes_table.php  # Database migration

│

├── resources/

│   └── views/

│       └── animes/

│           ├── index.blade.php             # List view

│           └── show.blade.php              # Detail view

│

├── routes/

│   ├── api.php                             # API routes

│   └── web.php                             # Web routes

│

├── tests/

│   ├── Feature/

│   │   ├── AnimeApiTest.php               # API endpoint tests

│   │   └── ImportAnimeCommandTest.php      # Import command tests

│   │

│   └── Unit/

│       └── JikanApiServiceTest.php         # Service unit tests

│

├── .env.example                            # Environment example file

├── README.md                               # Project documentation

└── composer.json                           # PHP dependencies

```

## Key Directory Explanations

### App Directory

-   **Commands**: Contains the anime import command
-   **Exceptions**: Custom exception handling
-   **Http/Controllers**: Web and API controllers
-   **Http/Requests**: Request validation classes
-   **Http/Resources**: API resource transformers
-   **Services**: External service integrations

### Database Directory

-   **Migrations**: Database structure definitions

### Resources Directory

-   **Views**: Blade templates for web interface

### Routes Directory

-   **api.php**: API endpoint definitions
-   **web.php**: Web route definitions

### Tests Directory

-   **Feature**: Integration tests
-   **Unit**: Unit tests for individual components

## File Purposes

### Core Files

-   `ImportAnimeData.php`: Handles fetching and storing anime data
-   `JikanApiService.php`: Manages communication with external API
-   `AnimeController.php`: Handles web and API requests
-   `Anime.php`: Defines database model and relationships

### View Files

-   `index.blade.php`: Lists all anime entries
-   `show.blade.php`: Shows detailed anime information

### Configuration Files

-   `.env.example`: Template for environment configuration
-   `README.md`: Project documentation
