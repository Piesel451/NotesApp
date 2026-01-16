# NotesApp

A simple backend API written in PHP, using [Phroute](https://github.com/mrjgreen/phroute) for routing. Designed for easy extension and further development.

---

## Project Structure

```
NotesApp/
├── config/         # Application configuration (bootstrap, DB connection)
├── public/         # Public server directory (index.php, assets, .htaccess)
├── src/            # Application code (PSR-4, namespace App)
│   ├── Config/         # Configuration classes (Database)
│   ├── Controller/     # Controllers (e.g., RegisterController)
│   ├── Entity/         # Entities (e.g., User)
│   ├── Http/           # Request/Response handling (Request, Response)
│   └── Repository/     # Database access logic (UserRepository)
├── storage/        # Data, logs, cache (future use)
├── vendor/         # Composer dependencies
├── .env            # Environment variables (DB, etc.)
└── composer.json   # Project definition and autoload
```

---

## Database Structure

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

## Requirements

- PHP >= 8.1  
- Composer  
- MySQL / MariaDB (for user management)

---

## Installation

1. **Install dependencies:**
    ```bash
    composer install
    ```

2. **Configure database connection in `.env`:**
    ```
    DB_HOST=127.0.0.1
    DB_NAME=notes-app
    DB_USER=root
    DB_PASS=
    ```

3. **Set your web server root to the `public/` directory.**  
   For Apache, the `.htaccess` file is used.

4. **Run the project locally:**
    ```bash
    php -S localhost:8000 -t public
    ```

---

## API

| Method | Endpoint         | Description                |
|--------|------------------|----------------------------|
| POST   | `/register`      | Register a new user        |

---

## Technologies Used

- PHP 8.1+
- [Phroute](https://github.com/mrjgreen/phroute) (routing)
- Composer (dependency management)
- MySQL / MariaDB

---

## Credits

- [Phroute](https://github.com/mrjgreen/phroute) for routing

---

## Contributing

Pull requests and suggestions are welcome! Feel free to open an issue or submit a PR.

---
