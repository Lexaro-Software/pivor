# Pivor

[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4.svg)](https://php.net)
[![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20.svg)](https://laravel.com)

**Open Source, Self-Hosted CRM for Small Businesses**

Own your customer data. No per-seat pricing, no cloud lock-in. Built by Lexaro Software.

📖 **[Documentation](https://pivor.pages.dev/docs/)** · 🐛 **[Report Issue](https://github.com/Lexaro-Software/pivor/issues)** · 💬 **[Discussions](https://github.com/Lexaro-Software/pivor/discussions)**

<p align="center">
  <img src="docs/screenshots/dashboard.png" alt="Dashboard" width="80%">
</p>

## Quick Start

### Docker (Recommended)

```bash
git clone git@github.com:Lexaro-Software/pivor.git
cd pivor
docker compose up -d
# Access at http://localhost:8080
```

### Local Development

```bash
git clone git@github.com:Lexaro-Software/pivor.git
cd pivor
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

### Default Login

- **Email:** admin@pivor.dev
- **Password:** password

> ⚠️ Change this immediately after first login.

## Updating

### Docker

```bash
git pull origin main
docker compose build
docker compose up -d
```

### Local

```bash
git stash && git pull origin main && git stash pop
composer install && npm install && npm run build
php artisan migrate && php artisan config:cache
```

See the [full update guide](docs/update.md) for troubleshooting.

## Features

- **Clients & Contacts** — Manage companies and people
- **Communications** — Log emails, calls, meetings, and tasks
- **Email Integration** — Two-way sync with Gmail and Outlook
- **REST API** — Token-based API for integrations
- **Roles & Permissions** — Admin, manager, and user roles
- **Import/Export** — CSV import with field mapping
- **Dark Mode** — Easy on the eyes

## Contributing

Contributions welcome! Fork, create a branch, and open a PR.

## License

[AGPL-3.0](LICENSE)
