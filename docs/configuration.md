# Configuration

Pivor is configured through environment variables in the `.env` file.

## Application Settings

```env
APP_NAME=Pivor
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name shown in UI | Pivor |
| `APP_ENV` | Environment (local, production) | production |
| `APP_DEBUG` | Show detailed errors | false |
| `APP_URL` | Full URL to your installation | http://localhost |

## Database

Pivor supports SQLite, MySQL, and PostgreSQL.

### SQLite (Default)

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pivor
DB_USERNAME=pivor
DB_PASSWORD=secret
```

### PostgreSQL

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pivor
DB_USERNAME=pivor
DB_PASSWORD=secret
```

## Session & Cache

For production, consider using Redis:

```env
SESSION_DRIVER=redis
CACHE_STORE=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## Mail

Configure email for notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=pivor@your-domain.com
MAIL_FROM_NAME="Pivor CRM"
```

---

[Back to Documentation](README.md)
