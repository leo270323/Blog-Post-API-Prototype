# Blog Post API Prototype

## Technologies
- **Language:** PHP 8.x, Laravel 12  
- **Database:** MySQL (configuration in `.env.example`)

## How to Run

1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. `php artisan migrate`
5. Start your PHP, Mysql, web server and run the API

## Running Tests

- Configure your test database in `phpunit.xml`  
  *(This project uses MySQL with a separate database for testing)*  
- Run all tests: php artisan test
- Run a specific test: php `artisan test --filter UserTest` or `php artisan test --filter PostTest`

## Notes

- Remember to attach the header: `Accept: application/json` for every API request
- All tests are located in: tests/Feature.