## Task Management

### Deployment
- Extract the archive and put it in the folder you want
- Run `cp .env.example .env` file to copy example file to .env
- Then edit your `.env` file with DB credentials and other settings.
- Run composer install command
- Run `php artisan migrate --seed` command.
- Notice: seed is important, because it will create the first admin user for you.
- Run `php artisan key:generate` command.
- Run `php artisan serve` command.
- Visit `http://localhost:8000` in your browser

### Default credentials
- Username: `admin@admin.com`
- Password: `password`
