# Rename this file to README.md to be the main project README
# Nightingale API - Docker Setup Guide

This project is a Laravel-based API for appointment bookings, user authentication, and scheduling, designed to run easily with Docker.

## Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed on your machine.
- (Optional) [Git](https://git-scm.com/) for cloning the repository.

## Quick Start (Recommended)

1. **Clone the repository:**
   ```bash
   git clone <your-repo-url>
   cd nightingale
   ```

2. **Copy the example environment file:**
   ```bash
   cp .env.example .env
   ```
   > Edit `.env` if you need to change any default settings (optional).

3. **Start the application using Docker Compose:**
   ```bash
   docker-compose up --build -d
   ```
   This will build and start all necessary containers (app, database, nginx, etc.).

4. **Run database migrations and seeders:**
   > The Docker setup is configured to automatically run migrations and seeders on the first startup. If you need to run them manually:
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

5. **Access the API:**
   - The API will be available at: [http://localhost](http://localhost)
   - API documentation (if available) can be found in the `README.md` or via `/` endpoint.

## Useful Docker Commands
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
- **Install Composer dependencies (if needed):**
  ```bash
  docker-compose exec app composer install
  ```

## Running Tests
To run the test suite inside the Docker container:
```bash
docker-compose exec app php artisan test
```

## Troubleshooting
- If you change dependencies or environment variables, you may need to rebuild:
  ```bash
  docker-compose up --build -d
  ```
- For permission issues, you may need to set correct permissions on the `storage` and `bootstrap/cache` directories:
  ```bash
  docker-compose exec app chmod -R 777 storage bootstrap/cache
  ```

## Additional Notes
- All setup, migrations, and seeding are handled by Docker for a seamless experience.
- For custom seeders or additional setup, modify the `database/seeders` directory and re-run migrations/seeds as needed.
- For more details, see the API-specific README files (e.g., `LOGIN_API_README.md`, `REGISTRATION_API_README.md`).

---

**You're ready to go!**

If you encounter any issues, please check the logs or open an issue in the repository.
