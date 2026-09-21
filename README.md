# My blog (PHP)

A learning dynamic blog: posts, categories, comments, registration, and an admin panel. The frontend is the same as in the original project. Run it with Docker (PHP + MySQL).

## Quick start

You need [Docker Desktop](https://www.docker.com/products/docker-desktop/).

```bash
copy .env.example .env
docker compose up --build -d
```

On Linux/macOS use `cp .env.example .env` instead of `copy`.

| Service | URL |
| --- | --- |
| Site | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |

Stop: `docker compose down`  
Reset the database completely: `docker compose down -v`

## Accounts

| Role | Email | Password | Access |
| --- | --- | --- | --- |
| Admin | `admin@myblog.local` | `admin123` | Posts, categories, users, comment moderation |
| User (not admin) | `demo@myblog.local` | `user123` | Sign in on the site, no admin panel |

Sign in: http://localhost:8080/log.php

## What the seeder puts in the database

Besides the two accounts:

- **10 published posts** — carousel, home feed, sidebar categories
- **1 draft** — visible only in the admin panel, not on the home page
- **Comments under posts** — most articles have 2–3 visible reviews; 2 more comments wait for moderation (`status=0`)

Open any post from the feed, for example http://localhost:8080/single.php?post=1 — comments are at the bottom.

## Configuration (.env)

The `.env` file sets **where the frontend runs** and how to connect to the database.

| Variable | Purpose |
| --- | --- |
| `APP_URL` | Base URL used in site links |
| `APP_PORT` | Site port on the host |
| `PHPMYADMIN_PORT` | phpMyAdmin port |
| `MYSQL_HOST_PORT` | MySQL port on the host (always `3306` inside the container) |
| `DB_HOST` | Must be `db` in Docker |
| `DB_NAME` / `DB_USER` / `DB_PASSWORD` | PHP access to MySQL |
| `DB_ROOT_PASSWORD` | Root password for phpMyAdmin |
| `ADMIN_*` / `DEMO_*` | Credentials written by the seeder |

After changing `.env`:

```bash
docker compose up -d
```

## Seeder

File: `app/database/seed.php`.

It creates an admin, a regular user, categories, posts with comments, and placeholder images in `assets/img/posts/`.

On container start it runs automatically if the `users` table is empty.

```bash
docker compose exec app php app/database/seed.php
docker compose exec app php app/database/seed.php --force
```

`--force` clears `users`, `topics`, `posts`, `comments` and seeds the demo data again. Admin/user passwords come from `.env`.

## Structure

```
app/            controllers, database connection, seeder
admin/          admin panel
assets/         CSS, JS, post images
docker/         Dockerfile, Apache, MySQL schema
index.php       home page (same frontend)
```

Table schema: `docker/mysql/schema.sql` (`users`, `topics`, `posts`, `comments`).
