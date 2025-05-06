<h3 style="text-align: center; color: yellow;">UNDER DEVELOPMENT</h3>

# Payrollee

Payrollee is a foundational web application designed for payroll management. It serves primarily as instructional material for demonstrating core web development concepts using PHP (Laravel) and related technologies.

Developed by [memoowi](https://instagram.com/me_moowi).

Stack :
* Laravel v12
* Livewire
* Volt
* Alpine JS
* TailwindCSS v4

## Prerequisites

Before you begin, ensure you have the following installed on your system:

* Git
* PHP (compatible version with the project's `composer.json`)
* Composer
* Node.js and npm (or yarn)
* A database server (e.g., MySQL, PostgreSQL, SQLite)

## Important Notes

If you're using `ArchLinux` or any other distro/OS that uses cutting edge version of `php` and `libxml2` like myself, you might encounter an error on `nunomaduro/termwind` package. To resolve this but not permanent, cuz it's not official release.. duh. Follow these steps after `composer install` :
1. Open `HTMLRenderer.php` in vendor
2. In the `parse` function, comment out the `$html` variable and replace it like so :
   ```bash
   # $html = '<?xml encoding="UTF-8">'.trim($html);
    $html = '<!DOCTYPE html><html><body>' . $html . '</body></html>';
   ```
3. Then to continue run `php artisan package:discover --ansi`

## Getting Started

Follow these steps to set up the project locally:

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/memoowi/payroll-website.git
    ```

2.  **Navigate into the project directory:**
    ```bash
    cd payroll-website
    ```

3.  **Install dependencies:**
    Install both PHP (Composer) and JavaScript (npm) dependencies.
    ```bash
    composer install
    npm install
    # or if you use yarn:
    # yarn install
    ```

4.  **Create the environment configuration file:**
    Copy the example environment file.
    ```bash
    cp .env.example .env
    ```

5.  **Generate the application key:**
    This is crucial for securing session data and other encrypted elements.
    ```bash
    php artisan key:generate
    ```

6.  **Configure your database:**
    Open the `.env` file you created in step 4 and update the `DB_*` variables (e.g., `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) to match your local database setup. Ensure the specified database exists.

7.  **Run database migrations and make the initial data needed:**
    * This will create the necessary tables in your database. 
    * Also If you wish to customize the default administrator credentials or initial company settings, edit the `database/seeders/DatabaseSeeder.php` file before proceeding.
    ```bash
    php artisan migrate --seed
    ```

8. **Seed the database with Dummy Data (optional):**
    * If you wish to add or generate pre-made data, that can be found in `database/seeders/**Seeder.php` file.
    * Run the seeder to populate the database by calling them seeder filenames.
    ```bash
    php artisan db:seed --class=FileNameSeeder
    ```
    * or if you prefer to populate the database with all of the pre-made data, just open the `database/seeders/DatabaseSeeder.php` file. Then uncomment the call method under the run function. After that you can refresh the migration.
    ```bash
    php artisan migrate:fresh --seed
    ```

9.  **Serve the application:**
    Start the local development server.
    ```bash
    composer run dev
    ```
    You should now be able to access the application in your web browser, typically at `http://127.0.0.1:8000`.

## Usage

After completing the installation, access the application via the URL provided by the `composer run dev` command. You can log in using the administrator credentials created during the database seeding process (either the defaults or the ones you customized).