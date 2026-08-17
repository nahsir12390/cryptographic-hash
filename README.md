# Cryptographic Hash Function Visualizer and Collision Explainer

A responsive educational web app built with Laravel 13, Tailwind CSS, and Vite. The project demonstrates how hash functions work, how a single-character change triggers a large avalanche effect, and why collision resistance matters in modern cryptography.

## Features

- Generate MD5, SHA-1, SHA-256, and a toy demo hash
- Compare two inputs to visualize the avalanche effect
- Explain collision resistance and weak legacy algorithms
- Highlight the difference between broken, deprecated, and secure algorithms
- Clean dark UI with responsive layout for mobile and desktop

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

## Project Purpose

This project is intended for learning and demonstration. It helps students and beginners understand the practical meaning of hashing, the avalanche effect, and collision resistance in a more visual and user-friendly way.

## License

This project is for educational purposes and is available under the MIT License.
