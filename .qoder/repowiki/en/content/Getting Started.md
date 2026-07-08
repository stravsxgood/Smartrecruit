# Getting Started

<cite>
**Referenced Files in This Document**
- [composer.json](file://composer.json)
- [package.json](file://package.json)
- [.env.example](file://.env.example)
- [config/app.php](file://config/app.php)
- [config/database.php](file://config/database.php)
- [config/fortify.php](file://config/fortify.php)
- [bootstrap/app.php](file://bootstrap/app.php)
- [vite.config.ts](file://vite.config.ts)
- [resources/js/app.ts](file://resources/js/app.ts)
- [routes/web.php](file://routes/web.php)
- [database/migrations/0001_01_01_000000_create_users_table.php](file://database/migrations/0001_01_01_000000_create_users_table.php)
- [database/migrations/2026_06_24_164756_add_role_and_metadata_to_users_table.php](file://database/migrations/2026_06_24_164756_add_role_and_metadata_to_users_table.php)
- [database/seeders/DatabaseSeeder.php](file://database/seeders/DatabaseSeeder.php)
</cite>

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#prerequisites)
3. [Installation](#installation)
4. [Environment Configuration](#environment-configuration)
5. [Database Setup](#database-setup)
6. [Asset Compilation](#asset-compilation)
7. [Development Server](#development-server)
8. [Verification](#verification)
9. [Default Admin Credentials](#default-admin-credentials)
10. [Troubleshooting](#troubleshooting)
11. [Conclusion](#conclusion)

## Introduction
This guide helps you install and run SmartRecruit ATS locally. It covers prerequisites, cloning the repository, environment setup, database migration, asset compilation, starting the development server, verifying the installation, and resolving common issues. The application uses Laravel for the backend and Vue 3 with Inertia for the frontend, configured via Vite.

## Prerequisites
Before installing SmartRecruit ATS, ensure your environment meets the following requirements:

- PHP runtime
  - Version: 8.3 or higher
  - Required extensions: pdo_sqlite (and optionally pdo_mysql, pdo_pgsql depending on chosen database)
- Node.js
  - Version: 18+ recommended (the project specifies modern tooling; ensure compatibility)
- Database
  - SQLite (default): included with PDO; no separate service required
  - MySQL/MariaDB: optional; requires a running service and PDO MySQL enabled
  - PostgreSQL: optional; requires a running service and PDO PostgreSQL enabled
- Composer
  - Latest stable version compatible with PHP 8.3+
- Operating System
  - Windows, macOS, or Linux

These requirements are derived from:
- PHP version constraint in [composer.json](file://composer.json#L12)
- Laravel framework requirement in [composer.json](file://composer.json#L16)
- Database connection defaults and options in [config/database.php:20-115](file://config/database.php#L20-L115)
- Frontend toolchain in [package.json:15-60](file://package.json#L15-L60)

**Section sources**
- [composer.json:11-19](file://composer.json#L11-L19)
- [composer.json:108-119](file://composer.json#L108-L119)
- [config/database.php:20-115](file://config/database.php#L20-L115)
- [package.json:15-60](file://package.json#L15-L60)

## Installation
Follow these steps to install SmartRecruit ATS from repository cloning to initial setup:

1. Clone the repository to your local machine using Git.
2. Navigate into the project directory.
3. Install PHP dependencies using Composer:
   - Run: composer install
4. Prepare the environment file:
   - Copy the example environment file: cp .env.example .env
   - Generate the application key: php artisan key:generate
5. Install frontend dependencies using npm:
   - Run: npm install
6. Build assets for the first time:
   - Run: npm run build

These steps are automated by the Composer script setup:
- [composer.json:45-57](file://composer.json#L45-L57)

**Section sources**
- [composer.json:45-57](file://composer.json#L45-L57)

## Environment Configuration
Configure your environment by editing the .env file. The default configuration uses SQLite and database-backed sessions and cache. Key areas to review:

- Application settings
  - APP_ENV, APP_DEBUG, APP_URL, APP_NAME
  - Locales: APP_LOCALE, APP_FALLBACK_LOCALE, APP_FAKER_LOCALE
  - Encryption: APP_KEY (generated automatically)
- Database selection
  - DB_CONNECTION: sqlite (default), mysql, mariadb, pgsql, sqlsrv
  - For SQLite: DB_DATABASE points to the SQLite file path
  - For MySQL/MariaDB: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
  - For PostgreSQL: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Sessions and cache
  - SESSION_DRIVER: database (default)
  - CACHE_STORE: database (default)
- Queue and broadcasting
  - QUEUE_CONNECTION: database (default)
  - BROADCAST_CONNECTION: log (default)
- Mailer
  - MAIL_MAILER: log (default)
- Vite frontend
  - VITE_APP_NAME: passed to the frontend

Important defaults and options are defined in:
- [config/app.php:16-126](file://config/app.php#L16-L126)
- [config/database.php:20-115](file://config/database.php#L20-L115)
- [.env.example:1-66](file://.env.example#L1-L66)

**Section sources**
- [.env.example:1-66](file://.env.example#L1-L66)
- [config/app.php:16-126](file://config/app.php#L16-L126)
- [config/database.php:20-115](file://config/database.php#L20-L115)

## Database Setup
By default, the application uses SQLite with database-backed sessions and cache. To prepare the database:

1. Run migrations to create tables:
   - Command: php artisan migrate
2. Optionally seed the database with initial data:
   - Command: php artisan db:seed
   - The default seeder creates a sample user record.

Migration files define the schema:
- Users table creation and related tables: [database/migrations/0001_01_01_000000_create_users_table.php:12-38](file://database/migrations/0001_01_01_000000_create_users_table.php#L12-L38)
- Additional columns for roles and metadata: [database/migrations/2026_06_24_164756_add_role_and_metadata_to_users_table.php:12-17](file://database/migrations/2026_06_24_164756_add_role_and_metadata_to_users_table.php#L12-L17)
- Seeding logic: [database/seeders/DatabaseSeeder.php:16-24](file://database/seeders/DatabaseSeeder.php#L16-L24)

Notes:
- SQLite is enabled by default via DB_CONNECTION=sqlite in the environment file.
- Session and cache stores are configured to use the database by default.

**Section sources**
- [database/migrations/0001_01_01_000000_create_users_table.php:12-38](file://database/migrations/0001_01_01_000000_create_users_table.php#L12-L38)
- [database/migrations/2026_06_24_164756_add_role_and_metadata_to_users_table.php:12-17](file://database/migrations/2026_06_24_164756_add_role_and_metadata_to_users_table.php#L12-L17)
- [database/seeders/DatabaseSeeder.php:16-24](file://database/seeders/DatabaseSeeder.php#L16-L24)
- [.env.example:23-30](file://.env.example#L23-L30)
- [config/database.php:20-115](file://config/database.php#L20-L115)

## Asset Compilation
Build frontend assets using Vite:

- Production build:
  - npm run build
- Development watch mode:
  - npm run dev

The Vite configuration integrates Laravel, Inertia, Vue, Tailwind CSS, and Wayfinder:
- [vite.config.ts:9-34](file://vite.config.ts#L9-L34)
- Frontend entrypoint:
  - [resources/js/app.ts:1-34](file://resources/js/app.ts#L1-L34)

**Section sources**
- [package.json:5-14](file://package.json#L5-L14)
- [vite.config.ts:9-34](file://vite.config.ts#L9-L34)
- [resources/js/app.ts:1-34](file://resources/js/app.ts#L1-L34)

## Development Server
Start the development stack using the provided Composer scripts:

- Development script (runs Laravel server, queue listener, and Vite in parallel):
  - composer dev

This script runs:
- php artisan serve
- php artisan queue:listen with tries=1
- npm run dev

Refer to the Composer script definition:
- [composer.json:54-57](file://composer.json#L54-L57)

**Section sources**
- [composer.json:54-57](file://composer.json#L54-L57)

## Verification
After completing installation, verify the setup:

1. Visit the home route:
   - URL: http://localhost (or your APP_URL)
   - Expected: A welcome page rendered by Inertia
   - Route definition: [routes/web.php:9-16](file://routes/web.php#L9-L16)

2. Access the dashboard (requires authentication):
   - URL: /dashboard
   - Requires login; protected by auth middleware
   - Route definition: [routes/web.php:18-22](file://routes/web.php#L18-L22)

3. Confirm frontend rendering:
   - The Vue app initializes via Inertia and applies layouts
   - Entry point: [resources/js/app.ts:10-27](file://resources/js/app.ts#L10-L27)

4. Validate backend configuration:
   - Application name and URL from environment:
     - [config/app.php:16-55](file://config/app.php#L16-L55)
   - Database connection defaults:
     - [config/database.php:20-115](file://config/database.php#L20-L115)

**Section sources**
- [routes/web.php:9-22](file://routes/web.php#L9-L22)
- [resources/js/app.ts:10-27](file://resources/js/app.ts#L10-L27)
- [config/app.php:16-55](file://config/app.php#L16-L55)
- [config/database.php:20-115](file://config/database.php#L20-L115)

## Default Admin Credentials
The default seeder creates a sample user account. Use these credentials to log in:

- Email: test@example.com
- Password: not explicitly set in the seeder; you must set a password for this user before logging in

To create an initial admin user:

1. Seed the database:
   - php artisan db:seed
   - Reference: [database/seeders/DatabaseSeeder.php:16-24](file://database/seeders/DatabaseSeeder.php#L16-L24)

2. Set a password for the seeded user via the registration or password reset flow, or create a new user with administrative role.

Note: The seeder inserts a user with name and email; password must be provided.

**Section sources**
- [database/seeders/DatabaseSeeder.php:16-24](file://database/seeders/DatabaseSeeder.php#L16-L24)

## Troubleshooting
Common installation issues and resolutions:

- PHP version mismatch
  - Symptom: Composer errors indicating unsupported PHP version
  - Resolution: Upgrade to PHP 8.3+ as required by the project

- Missing database extension
  - Symptom: PDO-related errors when connecting to MySQL/MariaDB/PostgreSQL
  - Resolution: Enable the appropriate PDO extension (pdo_mysql, pdo_pgsql) in php.ini

- SQLite file permissions
  - Symptom: Cannot write to database file or migrations fail
  - Resolution: Ensure the database path exists and is writable by the web server

- Node/npm compatibility
  - Symptom: npm install fails or Vite build errors
  - Resolution: Use a supported Node.js version and clear node_modules if necessary

- Environment variables not applied
  - Symptom: Incorrect database host/port or missing APP_KEY
  - Resolution: Confirm .env values and regenerate APP_KEY if needed

- Authentication redirects
  - Symptom: Redirect loops after login
  - Resolution: Review Fortify configuration and redirect path
    - [config/fortify.php](file://config/fortify.php#L76)

- Middleware and routing
  - Symptom: 404 or unexpected layouts
  - Resolution: Confirm routes and middleware groups
    - [routes/web.php:18-29](file://routes/web.php#L18-L29)
    - [bootstrap/app.php:17-25](file://bootstrap/app.php#L17-L25)

**Section sources**
- [composer.json:12](file://composer.json#L12)
- [config/database.php:20-115](file://config/database.php#L20-L115)
- [config/fortify.php:76](file://config/fortify.php#L76)
- [routes/web.php:18-29](file://routes/web.php#L18-L29)
- [bootstrap/app.php:17-25](file://bootstrap/app.php#L17-L25)

## Conclusion
You now have SmartRecruit ATS installed and running locally. Use the provided Composer and npm scripts to streamline setup and development. For production, adjust environment variables, switch to a persistent database, and configure HTTPS and queues appropriately.