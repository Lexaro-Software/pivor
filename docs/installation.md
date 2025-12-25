# Installation

## Requirements

- PHP 8.2+
- Composer 2+
- Node.js 18+
- SQLite, MySQL, or PostgreSQL

## Docker (Recommended)

The fastest way to get Pivor running:

```bash
# Clone the repository
git clone git@github.com:Lexaro-Software/pivor.git
cd pivor

# Copy environment file
cp .env.example .env

# Start containers
docker compose up -d

# Run migrations and seed data
docker compose exec app php artisan migrate --seed

# Access Pivor at http://localhost:8080
```

## Local Development

For development or manual installation:

```bash
# Clone the repository
git clone git@github.com:Lexaro-Software/pivor.git
cd pivor

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create database and seed
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the server
php artisan serve
```

Access Pivor at http://localhost:8000

## Default Login

After installation, login with:

- **Email:** admin@pivor.dev
- **Password:** password

> **Important:** Change these credentials immediately after first login.

## Next Steps

- [Configuration](configuration.md) - Customize your installation
- [Clients Module](modules/clients.md) - Start managing clients

---

[Back to Documentation](README.md)
