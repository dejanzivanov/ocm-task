# OCM-Task

A Laravel 8 & Vue 2.7.1 project for OCM Task management.

Hosted at: https://ocm.dejanzivanov.com

## Prerequisites

- **PHP**: ^7.4
- **Composer**: 2.5.7  
- **Node.js**: 18.20.8 
- **npm**: 10.8.2  
- **MySQL**: ≥5.7
- **Git**
- **NewsAPI Key** - It can be obtained here:  [News API](https://newsapi.org/)

## Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/dejanzivanov/ocm-task.git
   cd ocm-task
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   In `.env`, set your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ocm_database
   DB_USERNAME=ocm_username
   DB_PASSWORD=PasswordThatYouLike
   ADMIN_USERNAME=admin_username
   ADMIN_EMAIL=admin_login@email.com
   ADMIN_PASSWORD=administrator_password!
   NEWS_API=API_KEY_TAKEN_FROM_NEWSAPI
   ```

4. **Install Node.js dependencies & build assets**
   ```bash
   npm install
   npm run dev       # development build
   # or
   npm run build     # production build
   ```

5. **Set folder permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

   7. **Run database seeding for administrator account**
   ```bash
   php artisan db:seed
   ```

8. **Serve the application**
   ```bash
   php artisan serve
   ```
   Visit http://127.0.0.1:8000 in your browser.

## Usage After Login

1. Login using the **ADMIN_USERNAME** and **ADMIN_PASSWORD** set in your `.env`.  
2. After login, navigate to `/home`. You’ll see a screen like this:

   ![API Input Screen](https://raw.githubusercontent.com/dejanzivanov/ocm-task/refs/heads/main/instructions/api_input.png)

3. Enter your NewsAPI key and click **Import API Key**.  
4. Once the key is successfully imported, click **Create News** and afterwards **Visit News Page** to view the latest news.


## License

This project is open-source under the MIT License.
