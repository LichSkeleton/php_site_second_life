# My blog (Laravel)

The laboratory application. It keeps the blog from the previous plain-PHP labs: posts belong to a category and an author.

Run it from the project root:

```bash
docker compose up --build -d
```

Site: http://localhost:8082

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@myblog.local` | `admin123` |
| User | `demo@myblog.local` | `user123` |

The database is MySQL database `laravel_blog` (separate from the plain PHP site). Migrations and the seeder run when the `laravel` container starts.

## API

- `GET /api/posts`
- `GET /api/posts/{id}`
- `POST /api/posts`
- `PUT /api/posts/{id}`
- `DELETE /api/posts/{id}`

A page with the request body is at http://localhost:8082/api-help.
