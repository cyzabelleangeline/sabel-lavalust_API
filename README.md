# Lava / Stock — LavaLust API

Full stack product inventory exercise: a React client talks to the LavaLust API, which stores users and products in MySQL. The browser never connects to MySQL. Product list and CRUD endpoints require a valid bearer access token.

## Local setup

Requirements: PHP 8+, MySQL (Aiven is supported), Node.js 20+, and npm.

1. Copy `.env.example` to `.env` and set `DB_DRIVER=mysql`, `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`, and `DB_CHARSET=utf8mb4`. For Aiven, keep `DB_SSL_CA=database/aiven-ca.pem` (or point it at the CA file Aiven provides). Generate unique `JWT_SECRET` and `REFRESH_TOKEN_KEY` values; do not commit `.env`.
2. Apply the tables with the LavaLust migration command:

   ```sh
   php lava migration status
   php lava migration run
   ```

   The expected tables are `migrations`, `users`, `refresh_tokens`, and `products`. Migration `005_add_user_names` upgrades a database created before `firstname` and `lastname` were included. Register a user at `/api/register` from the React screen; passwords are hashed.
3. Start the API:

   ```sh
   php lava serve --port=3000
   ```

4. In the React repository, copy `.env.example` to `.env`, leave `VITE_API_URL` blank for local development, run `npm install`, then `npm run dev`. Vite proxies `/api` to `http://127.0.0.1:3000`.

## API routes

| Method | Path | Access |
| --- | --- | --- |
| POST | `/api/register` | Public |
| POST | `/api/login` | Public |
| POST | `/api/logout` | Bearer token |
| GET | `/api/products` | Bearer token |
| GET | `/api/products/{id}` | Bearer token |
| POST | `/api/products` | Bearer token |
| PUT | `/api/products/{id}` | Bearer token |
| PATCH | `/api/products/{id}` | Bearer token |
| DELETE | `/api/products/{id}` | Bearer token |

The API uses LavaLust's API library for JSON responses and token validation. Product rows use `product_name`, `description`, `price`, `quantity`, and `created_at` (TIMESTAMP).

## Migration commands

```sh
php lava migration status
php lava migration create-migration create_categories_table
php lava migration run
php lava migration rollback
php lava migration rollback-all
php lava migration refresh
```

`rollback-all` and `refresh` remove application tables and data. Use them only with a disposable development database. HTTP migration routes are available to follow the migration class lab sheet; keep the API private while using the destructive routes.

## Deployment checklist

- Deploy this repository to Render using its Dockerfile and configure the database and token environment variables in the Render service settings.
- Set `DB_SSL_CA` to the bundled Aiven CA path (or an uploaded CA path), and verify the Render service can connect to the Aiven database.
- Deploy the React repository as a static site. Set `VITE_API_URL` to the deployed API origin before its build, and set the API's `allow_origin` in `app/config/api.php` to the exact frontend origin.
- Keep all database credentials and token secrets in host environment settings. The current Render API and frontend URLs must be filled into the submission after deployment; they are not embedded in the source.

The API repository also builds the same React client into `public/` for a convenient combined preview. The separate React repository remains deployable as the exercise's frontend submission.
