# Support Ticket System

A modern support ticketing system built with Laravel and Filament Admin Panel, running on Docker containers.

## Features

-   Laravel 11.x with Filament Admin Panel
-   PostgreSQL Database
-   Redis Caching
-   Nginx Web Server
-   User Authentication & Authorization
-   Docker Containerization for easy deployment
-   Dark/Light Theme Support

## Requirements

-   Docker & Docker Compose
-   No PHP, Node.js, or database installation needed (all included in containers)

## Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/RikiSanjayaa/support-ticket.git
cd support-ticket
```

Clone the environment example as `.env`:

```bash
cp .env.example .env
```

Configure environment variables in `.env`:

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

### 2. Start Containers

```bash
docker compose -f compose.dev.yaml up -d
```

### 3. Run Setup Commands

```bash
# Wait for services to start
sleep 5

# Run migrations
docker compose -f compose.dev.yaml exec php-fpm php artisan migrate --force

# Seed database with test data
docker compose -f compose.dev.yaml exec php-fpm php artisan db:seed --force

# Clear caches
docker compose -f compose.dev.yaml exec php-fpm php artisan cache:clear config:clear
```

### 4. Access the Application

-   **URL**: http://localhost:8000
-   **Admin Panel**: http://localhost:8000/admin

That's it! The application is now running.

## Automated Setup (Optional)

use the setup script:

```bash
bash scripts/setup.sh dev
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
