# Laravel + Filament Starter Project

A minimal starter template built with Laravel and Filament Admin Panel, featuring basic user authentication.

## Features

-   Laravel 10.x
-   Filament Admin Panel
-   Basic User Authentication
-   SQLite Database (for quick setup)
-   Dark/Light Theme Support

## Requirements

-   PHP 8.1 or higher
-   Composer
-   Node.js & NPM
-   SQLite (or your preferred database)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/RikiSanjayaa/filament-app.git
cd filament-app
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install NPM dependencies:

```bash
npm install
```

4. Create environment file:

```bash
cp .env.example .env
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Create SQLite database:

```bash
touch database/database.sqlite
```

7. Update .env file with SQLite configuration:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

8. Run migrations:

```bash
php artisan migrate
```

9. Create an admin user:

```bash
php artisan make:filament-user
```

## Running the Application

1. Start the Laravel development server:

```bash
php artisan serve
```

2. Start the Vite development server:

```bash
npm run dev
```

3. Access the application:

-   Main site: [http://localhost:8000](http://localhost:8000)
-   Admin panel: [http://localhost:8000/admin](http://localhost:8000/admin)

## Features Included

-   User Authentication
    -   Login
    -   Registration
    -   Password Reset
-   Filament Admin Panel
    -   User Management
    -   Dark/Light Theme Toggle
