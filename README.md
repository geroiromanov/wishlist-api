## Setup instruction

1. Install dependencies:
```
composer install
```

2. Copy .env file
```
cp .env.example .env
```

3. Generate APP key
```
php artisan key:generate
```

4. Install PHP extensions
```
sudo apt install php-sqlite3 php-mbstring
sudo service apache2 restart
```

5. Run migratioms with seeders
```
php artisan migrate --seed
```


6. Serve the APP
```
php artisan serve
```


* Run tests (optional)
```
php artisan test
```

* Generate swagger DOC
```
php artisan l5-swagger:generate
```

