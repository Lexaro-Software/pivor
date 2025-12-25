# Contributing to Pivor

Thank you for your interest in contributing to Pivor!

## Getting Started

1. Fork the repository
2. Clone your fork locally
3. Install dependencies: `composer install && npm install`
4. Copy environment: `cp .env.example .env`
5. Generate key: `php artisan key:generate`
6. Run migrations: `php artisan migrate --seed`
7. Build assets: `npm run build`

## Development

Start the development server:

```bash
composer dev
```

This runs the Laravel server, queue worker, log viewer, and Vite in parallel.

## Running Tests

```bash
php artisan test
```

Or with composer:

```bash
composer test
```

## Code Style

We use Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

## Pull Requests

1. Create a feature branch: `git checkout -b feature/my-feature`
2. Make your changes
3. Run tests: `php artisan test`
4. Format code: `./vendor/bin/pint`
5. Commit with a clear message
6. Push to your fork
7. Open a Pull Request

## Guidelines

- Follow existing code patterns
- Write tests for new features
- Keep commits focused and atomic
- Update documentation if needed

## License

By contributing, you agree that your contributions will be licensed under the AGPL-3.0 license.
