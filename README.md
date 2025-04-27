# koins Entry Task
Koins Backend Developer Task

Using the Laravel framework to create a simple application for managing advertising campaigns within specified locations.

## 🚀 Features

- Log in and log out
- Forgot Password
- Recover Password
- Signup
- Resend Auth Code with Expiration
- Update their profile details
- Create and edit(when not in running state) an advertising campaign with valid values.
- View all created advertising campaigns: name, date, status, campaign locations, daily budget, total budget, and banners in a paginated JSON response.
- Create and edit locations
- Create and edit location status
- Payments API (Use a Paystack or FLW.
- Create a webhook that marks the status of paid campaigns as ‘paid’

## 🛠 Requirements

- PHP >= 8.2
- Composer
- MySQL
- Laravel 12.x
- Redis (optional, for caching/queues)
- [DBngin](https://dbngin.com/) or any local database manager (optional)


## ⚙️ Local Setup

Follow these steps to set up the project on your local machine.

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/your-laravel-project.git
cd your-laravel-project
```
2. Install Dependencies<br><br>
Install PHP dependencies via Composer:

```bash
composer install
```
3. Set Up Environment<br><br>
Copy the example environment file:

```bash
cp .env.example .env
```
Generate the application key:

```bash
php artisan key:generate
```
4. Set Environment Variables<br><br>
Open .env and set your configuration. Here are the required variables:

```bash
APP_NAME=APP_NAME
APP_ENV=local
APP_KEY=base64:generated_key_here
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Optional - Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"

# Optional - Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=d
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

PAYSTACK_STAGING_SECRET=your_paystack_secret_key
PAYSTACK_STAGING_PUBLIC=your_paystack_public_keey
PAYSTACK_MODE=your_enviroment

```
5. Set Up the Database<br><br>
Create your database using your local DB manager or CLI, then run:

```bash
php artisan migrate
```
6. Serve the Application<br><br>
You can serve the application using Artisan:

```bash
php artisan serve
```
Or use Laravel Valet for a more seamless local development experience on macOS.

7. Access the App<br><br>
    Local<br>
    Visit http://localhost:8000 (or your Valet domain) in your browser.
    <br>
    Staging<br>
    Visit https://koins-task-891f9a27515a.herokuapp.com in your browser.

