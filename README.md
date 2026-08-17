# Cryptographic Hash Function Visualizer and Collision Explainer

A responsive educational web app built with Laravel 13, Tailwind CSS, and Vite. The project demonstrates how hash functions work, how a single-character change triggers a large avalanche effect, and why collision resistance matters in modern cryptography.

## Features

- Generate MD5, SHA-1, SHA-256, and a toy demo hash
- Compare two inputs to visualize the avalanche effect
- Explain collision resistance and weak legacy algorithms
- Highlight the difference between broken, deprecated, and secure algorithms
- Clean responsive UI for mobile and desktop

## Tech Stack

- PHP 8.3
- Laravel 13
- Tailwind CSS 4
- Vite
- JavaScript

## Project Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Then visit:

```text
http://127.0.0.1:8000
```

## Deploy on Render

This project is prepared for Render using a Blueprint file and a startup script:

- [render.yaml](render.yaml)
- [render-build.sh](render-build.sh)
- [scripts/render-start.sh](scripts/render-start.sh)
- [Dockerfile](Dockerfile)

### Quick Deploy Steps

1. Push this project to GitHub.
2. In Render, click New + and choose Blueprint.
3. Select the repository and deploy.
4. After deployment, open the generated service URL and test:
	- Homepage loads
	- Hash generation works
	- Avalanche comparison works
	- Collision demo works

### Render Runtime Behavior

- Build command installs PHP and Node dependencies, then builds Vite assets.
- Start command runs migrations and Laravel caches, then starts the web server on the Render port.
- SQLite is used as the database and stored at `/var/data/database.sqlite`.
- A Render disk is mounted at `/var/data` so SQLite data remains available across deploys and restarts.

### Important Notes

- Replace APP_URL in [render.yaml](render.yaml) with your actual Render service URL after the first deploy.
- This setup avoids paid database plans by using SQLite.
- If Render disk options are unavailable on your account/region, fallback to ephemeral SQLite by changing `DB_DATABASE` to `/tmp/database.sqlite` (data will reset when the service restarts).
- `SESSION_DRIVER` and `CACHE_STORE` are set to `file` for simpler hosting and fewer external dependencies.
- Free plans may spin down when idle; first request after idle can be slower.

### Docker Container Option on Render

If you prefer container deployment:

1. In Render, create a new Web Service from your repository.
2. Choose Docker as the environment (Render will use [Dockerfile](Dockerfile)).
3. Keep the same environment variables from [render.yaml](render.yaml), especially:
	- `APP_ENV=production`
	- `APP_DEBUG=false`
	- `APP_KEY` (generate in Render)
	- `DB_CONNECTION=sqlite`
	- `DB_DATABASE=/var/data/database.sqlite`
4. Mount a disk to `/var/data` for SQLite persistence.

## Project Purpose

This project is intended for learning and demonstration. It helps students and beginners understand the practical meaning of hashing, the avalanche effect, and collision resistance in a more visual and user-friendly way.

## License

This project is for educational purposes and is available under the MIT License.
