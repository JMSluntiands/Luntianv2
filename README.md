# BLIUNQ

Laravel 12 + React (TypeScript) + Tailwind CSS full-stack setup.

## Stack

- **Backend:** Laravel 12 (PHP)
- **Frontend:** React 19, TypeScript, Tailwind CSS 4
- **Build:** Vite 7

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- (Optional) SQLite for default DB

## Setup

```bash
# Install PHP dependencies
composer install

# Install frontend dependencies
npm install

# Copy environment and generate key (if not done)
cp .env.example .env
php artisan key:generate

# Build frontend (production)
npm run build
```

## Development

**Option 1 – All-in-one (recommended)**

```bash
composer run dev
```

Starts Laravel server, queue worker, logs, and Vite dev server. App: **http://localhost:8000**

**Option 2 – Separate terminals**

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (hot reload)
npm run dev
```

Then open **http://localhost:8000**

## Project structure

- `app/` – Laravel backend (controllers, models, etc.)
- `resources/js/` – React + TypeScript
  - `app.tsx` – Entry, mounts React
  - `Root.tsx` – Root React component
  - `bootstrap.js` – Axios etc.
- `resources/css/app.css` – Tailwind
- `resources/views/app.blade.php` – HTML shell for the React app
- `routes/web.php` – Routes (e.g. `/` → React app)

## Commands

| Command | Description |
|--------|-------------|
| `composer run dev` | Start full dev stack |
| `npm run dev` | Vite dev server only |
| `npm run build` | Production frontend build |
| `php artisan serve` | Laravel dev server only |
| `php artisan migrate` | Run DB migrations |

## Existing PDF uploads (LBS jobs)

After securing file uploads, all new plan/docs/checker files for LBS jobs are stored under:

- `storage/app/lbs-documents/{folderName}/{fileName}`
  - `folderName` is normally the job’s reference number or client reference (same naming used before under `public/document/{folderName}`).

If you already have existing PDF files under the old path:

- `public/document/{folderName}/{fileName}.pdf`

you can keep the same folder names and move them manually into:

- `storage/app/lbs-documents/{folderName}/{fileName}.pdf`

Only files listed in the `jobs.upload_files`, `jobs.upload_project_files`, or `staff_uploaded_files.files_json` columns will be downloadable through the app.
