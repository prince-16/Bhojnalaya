# Bhojnalaya

Bhojnalaya is a restaurant POS and management application built with Laravel and Vue 3. The project currently includes a Petpooja-inspired table screen and order-taking UI, and it is structured so the Laravel backend can later power tables, menu items, orders, and billing flows.

## Tech Stack

- PHP 8.3+
- Laravel 13
- Vue 3
- Vite 8
- Tailwind CSS 4
- PHPUnit 12

## Current Features

- Restaurant table screen inspired by Petpooja
- Table-wise order entry flow
- Category sidebar for menu browsing
- Item grid with add-to-cart interactions
- Cart panel with quantity controls and billing actions
- Laravel + Vue single entry setup through Vite

## Prerequisites

Install these before running the project:

- PHP 8.3 or higher
- Composer
- Node.js and npm
- A database supported by Laravel such as MySQL

## Getting Started

### 1. Clone the repository

```bash
git clone <your-repository-url>
cd Bhojnalaya
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Create the environment file

```bash
cp .env.example .env
```

If you are on Windows PowerShell and `cp` behaves differently, you can use:

```powershell
Copy-Item .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Configure your database

Open `.env` and update the database settings.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bhojnalaya
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Start the development servers

Run these in separate terminals:

```bash
php artisan serve
npm run dev
```

Then open the Laravel local development URL shown by `php artisan serve`.

## Alternative Development Command

This project also includes a Composer development script that starts multiple services together:

```bash
composer run dev
```

This can start:

- Laravel development server
- queue listener
- Laravel logs stream
- Vite dev server

Use this only if you want the combined Laravel workflow from the Composer script.

## Build for Production

```bash
npm run build
```

## Run Tests

```bash
composer run test
```

Or directly:

```bash
php artisan test
```

## API Documentation

Swagger UI is available for testing the existing REST API.

- Start the Laravel server with `php artisan serve`
- Open `http://127.0.0.1:8000/api/documentation`
- If you change API annotations, regenerate docs with `php artisan l5-swagger:generate`

## Recommended First-Run Flow

If someone pulls this repository for the first time, this is the safe default process:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
npm run dev
```

## Project Structure

- `app/` Laravel application code
- `config/` framework and application configuration
- `database/` migrations, factories, and seeders
- `public/` public entry point and built assets
- `resources/js/` Vue frontend code
- `resources/css/` frontend styles
- `resources/views/` Blade views
- `routes/` web and console routes
- `tests/` unit and feature tests

## Notes for Developers

- The frontend is currently mounted from `resources/js/app.js`
- The current main Vue UI is rendered from `resources/js/components/App.vue`
- Production frontend assets are generated with Vite into `public/build/`
- If setup changes, update this README so new contributors can still onboard quickly

## Troubleshooting

### App key error

If Laravel reports a missing application key:

```bash
php artisan key:generate
```

### Database connection error

Check that your `.env` database credentials are correct and that your database server is running.

### Tables are missing

Run:

```bash
php artisan migrate
```

### Frontend changes are not showing

Make sure the Vite development server is running:

```bash
npm run dev
```

### Dependencies are not installed correctly

Try:

```bash
composer install
npm install
```

## Future README Improvements

As the project grows, you should keep this file updated with:

- feature list changes
- screenshots
- API setup details
- role-based login instructions
- deployment steps
- seed data instructions

## License

This project is open-sourced under the MIT license.
