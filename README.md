# Docplanner technical test (David Bonfill)

## Overview
This project is built using **Laravel** and follows a **TDD (Test-Driven Development)** approach. It leverages **Docker** for local and production environments, using the open-source Docker PHP images from [Server Side Up](https://serversideup.net/open-source/docker-php/).

### Key Features:
- **TDD Development:** PestPHP is used for testing.
- **Quick File Generation:** Common Laravel commands were used to speed up development. Example:
    ```sh
    php artisan make:model -a --api --pest Task
    php artisan make:enum -s TaskStatus
    ```
- **PHPStorm Optimizations:** Configured run settings for streamlined execution of **tests, Larastan, and Laravel Pint**.
- **GitHub Actions CI/CD Pipeline:** Automated checks running **Laravel Pint**, **Larastan**, and **PestPHP tests**. See `.github/workflows/` for details.

---
## Installation & Setup
### Prerequisites
Ensure you have **Docker** and **Docker Compose** installed on your system.

### Docker Considerations (Before Build)
Before building the containers, ensure the following:
- The **USER_ID** and **GROUP_ID** in `docker-compose.yml` match your local user:
  ```yaml
      args:
        USER_ID: 1000 # Your user ID
        GROUP_ID: 1000 # Your group ID
  ```
- The MySQL volume is stored **inside the project directory** under `_db_volume_data`, ensuring database persistence.

### Clone the Repository
```sh
  git clone https://github.com/davidbonfill/docplanner-task.git
  cd docplanner-task
```

### Set Up Environment Variables
Copy the example environment file:
```sh
  cp .env.example .env
```
Adjust the `.env` file according to your needs (especially database credentials).

### Adjust File Owner
```sh
  sudo chown -R $USER .
  sudo find . -type f -exec chmod 664 {} \;
  sudo find . -type d -exec chmod 775 {} \;
```

### Build and Start Docker Containers
```sh
  docker compose up -d --build
```
This will set up the Laravel app and MySQL service.

### Install Dependencies
Before running the application, install the necessary dependencies:
```sh
  docker compose exec docplanner-task composer install
```

### Adjust File Permissions
```sh
  sudo chown -R $USER .
  sudo find . -type f -exec chmod 664 {} \;
  sudo find . -type d -exec chmod 775 {} \;
  sudo find ./vendor/bin/ -type f -exec chmod 755 {} \;
```

### Set Laravel Application Key
```sh
  docker compose exec docplanner-task php artisan key:generate
```

### Run Migrations and Seed Database
```sh
  docker compose exec docplanner-task php artisan migrate --seed
```

### Set Debug Mode for Clearer Exception Messages
After installation, you may want to set `APP_DEBUG=false` in your `.env` file to better visualize controlled exception messages:
```sh
  nano .env
  # Change APP_DEBUG=true to APP_DEBUG=false
```

### Access the Project
- App Running at: [http://localhost:5000](http://localhost:5000)
- API Documentation: [http://localhost:5000/docs](http://localhost:5000/docs) (Generated with **[Scribe](https://scribe.knuckles.wtf/laravel/)**)
- Frontend Task Implementation: [http://localhost:5000/ui](http://localhost:5000/ui) (JavaScript-based frontend consuming the API)

---
## Running Static Analysis & Tests
### Run Laravel Pint (Code Style)
```sh
  docker compose exec docplanner-task vendor/bin/pint --test
```

### Run Larastan (Static Analysis)
```sh
  docker compose exec docplanner-task vendor/bin/phpstan analyse
```

### Run Pest Tests
```sh
  docker compose exec docplanner-task vendor/bin/pest
```

---
## Database Schema
The `schema/` directory contains:
- A **MySQL Workbench** visual schema.
- SQL scripts fulfilling the technical test requirements:
  1. **Database schema with indexes and foreign keys.**
  2. **SQL script to create the schema.**
  3. **Approach to database migrations and versioning.**
  4. **Commented SQL script with explanations.**

---
## CI/CD Pipeline
The project includes a **GitHub Actions** pipeline located in `.github/workflows/`.
This pipeline ensures code quality and runs the following steps automatically:
1. **Laravel Pint:** Code style checking.
2. **Larastan:** Static analysis.
3. **PestPHP Tests:** Running unit and feature tests.

---
## Additional Notes
- To stop the containers:
  ```sh
  docker compose down
  ```
- To rebuild containers (after dependency updates):
  ```sh
  docker compose up -d --build
  ```
- To enter the application container shell:
  ```sh
  docker compose exec docplanner-task bash
  ```

For any issues, check the container logs:
```sh
  docker compose logs -f
```

Enjoy! 

