# Support Ticketing System Project

A Support Ticketing System built with Laravel and Filament Admin Panel, featuring basic user authentication.

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
git clone https://github.com/RikiSanjayaa/support-ticket.git
cd support-ticket
```

\*note: enable these in your php.ini file:
extension=curl
extension=fileinfo
extension=intl
extension=ldap
extension=mbstring
extension=exif
extension=openssl
extension=pdo_sqlite
extension=sqlite3
extension=zip

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

## Docker Deployment (Production)

This project includes Docker configuration for production deployment on a home server or any Linux machine.

### Prerequisites

-   Docker and Docker Compose installed on your server
-   A Linux server (Ubuntu 20.04 LTS or newer recommended)

### Quick Start

1. Clone the repository on your server:

```bash
git clone https://github.com/RikiSanjayaa/support-ticket.git
cd support-ticket
```

2. Create production environment file:

```bash
cp .env.production .env
```

3. Configure environment variables in `.env`:

```bash
nano .env
```

**Important variables to set:**

```env
APP_KEY=base64:your_generated_key_here          # Generate: php artisan key:generate
APP_URL=http://your-server-ip-or-domain         # Your server address
DB_PASSWORD=your_strong_password                # Set a strong password
SEED_DATABASE=true                              # Set to true for first run only
```

4. Build and start the containers:

```bash
docker-compose build
docker-compose up -d
```

5. Monitor the startup:

```bash
docker-compose logs -f app
```

Wait until you see "Application ready!" message.

### Accessing Your Application

-   Main site: `http://your-server-ip:8000`
-   Admin panel: `http://your-server-ip:8000/admin`

### Common Docker Commands

```bash
# View logs
docker-compose logs -f app

# Stop containers
docker-compose down

# Restart containers
docker-compose restart app

# Run Laravel commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear

# Access container shell
docker-compose exec app sh
```

### Database

The application uses **PostgreSQL** in production (configured in `docker-compose.yaml`).

-   Database data is persisted in a named volume (`postgres_data`)
-   Data survives container restarts
-   Backup the volume: `docker volume inspect support-ticket_postgres_data`

### Troubleshooting

**Port already in use:**
Edit `docker-compose.yaml` and change the port mapping:

```yaml
ports:
    - "8001:80" # Change 8000 to any available port
```

**Container crashes:**

```bash
docker-compose logs app | tail -50
```

**Database connection errors:**

```bash
docker-compose logs postgres
```

**Want to reseed database:**

```bash
SEED_DATABASE=true docker-compose up -d
# Then after seeding completes, change back to false
```

## Features Included

-   User Authentication
    -   Login
    -   Registration
    -   Password Reset
-   Filament Admin Panel
    -   User Management
    -   Dark/Light Theme Toggle
