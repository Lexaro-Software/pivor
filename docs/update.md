# Updating Pivor

This guide covers how to update your Pivor installation to the latest version.

## Docker Installation

For Docker deployments, pull the latest changes and rebuild the container:

```bash
# Navigate to your Pivor directory
cd pivor

# Pull the latest changes
git pull origin main

# Rebuild and restart the container
docker compose build
docker compose up -d
```

Your data is preserved in Docker volumes (`pivor_data` and `pivor_storage`), so updates are safe.

## Local Installation

For local development or manual installations:

```bash
# Navigate to your Pivor directory
cd pivor

# Stash any local changes (optional)
git stash

# Pull the latest changes
git pull origin main

# Restore local changes if needed
git stash pop

# Update PHP dependencies
composer install

# Update frontend dependencies and rebuild
npm install
npm run build

# Run any new migrations
php artisan migrate

# Clear and refresh caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### "Local changes would be overwritten"

If you see this error when running `git pull`:

```
error: Your local changes to the following files would be overwritten by merge
```

This means some tracked files were modified locally. You have two options:

**Option 1: Preserve your changes**
```bash
git stash
git pull origin main
git stash pop
```

**Option 2: Discard local changes**
```bash
git fetch --all
git reset --hard origin/main
```

> **Warning:** Option 2 will discard all local modifications to tracked files.

### After updating

If you encounter issues after updating:

1. Clear all caches:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. Regenerate caches:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. Check for new environment variables in `.env.example` and add them to your `.env` file.

---

[Back to Documentation](README.md)
