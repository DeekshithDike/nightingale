# Nightingale API - Dockerized Laravel Setup

Nightingale is a Laravel-based API for appointment bookings, user authentication, and scheduling. This guide will help you get started quickly using Docker.

---

## 🚀 Quick Start

### 1. Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (required)
- [Git](https://git-scm.com/) (recommended for cloning)

### 2. Clone the Repository
```bash
git clone <your-repo-url>
cd nightingale
```

### 3. Environment Setup
Copy the example environment file and adjust as needed:
```bash
cp .env.example .env
```
Edit `.env` to customize settings (optional).

### 4. Start the Application
Build and start all containers (app, database, nginx, etc.):
```bash
docker-compose up --build -d
```

### 5. Database Migrations & Seeders
Migrations and seeders run automatically on first startup. To run manually:
```bash
docker-compose exec app php artisan migrate --seed
```

### 6. Access the API
- API base URL: [http://localhost](http://localhost)
- API documentation: See this README or the `/` endpoint.

---

## 🛠️ Common Docker Commands

- **Stop containers:**
  ```bash
  docker-compose down
  ```
- **View logs:**
  ```bash
  docker-compose logs -f
  ```
- **Run artisan commands:**
  ```bash
  docker-compose exec app php artisan <command>
  ```
- **Install Composer dependencies:**
  ```bash
  docker-compose exec app composer install
  ```

## 🧪 Running Tests
Run the test suite inside the container:
```bash
docker-compose exec app php artisan test
```

## 🐛 Troubleshooting

- **Rebuild after dependency/env changes:**
  ```bash
  docker-compose up --build -d
  ```
- **Fix permissions (if needed):**
  ```bash
  docker-compose exec app chmod -R 777 storage bootstrap/cache
  ```

## 🌱 Database Seeders

The following seeders are included and run by default:
- `DatabaseSeeder.php` (main entry point)
- `AppointmentBookingSeeder.php` (appointment bookings)
- `AvailableSlotSeeder.php` (available slots)

To run a specific seeder manually:
```bash
docker-compose exec app php artisan db:seed --class=AppointmentBookingSeeder
```
You can add or customize seeders in `database/seeders`.

## 📄 Additional Notes
- All setup, migrations, and seeding are handled by Docker for a seamless experience.
- For customizations, see the relevant sections in this README.

---

**You're ready to go!**

If you encounter any issues, check the logs or open an issue in the repository.
