# Development Setup

This repository contains the local development environment for the application.
The **actual application code** and its **dedicated README** are located inside the `app` directory.

---

## 📁 Project Structure

```
/
├── app/                  # Main application code + its own README
└── localEnvironment/     # Docker-based local development setup
    └── docker-compose.yml
```

---

## 🐳 Local Development Environment (Docker)

The `localEnvironment` directory contains the `docker-compose.yml` file used to run the application inside a PHP 8.4 CLI container.

This environment allows you to open an **interactive shell** and manually execute or test the `console.php` script.

---

## ▶️ Starting an Interactive Shell

From inside the `localEnvironment` folder, run:

```bash
docker compose run --rm php bash
```

This will open a shell *inside* the PHP container.

---

## ▶️ Executing the `console.php` Script

Once inside the container:

```bash
php console.php --action {action} --file {file}
```

or run it directly from outside without entering the shell:

```bash
docker compose run --rm php php console.php --action {action} --file {file}
```

---

## ℹ️ Notes

* The container is configured for development only.
* Application logic should **not** be placed in the `localEnvironment` folder—only in `app/`.

