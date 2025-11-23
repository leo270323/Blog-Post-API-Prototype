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
- Run all tests: `php artisan test`
- Run a specific test: php `artisan test --filter UserTest` or `php artisan test --filter PostTest`

## Notes

- Remember to attach the header: `Accept: application/json` for every API request
- All tests are located in: tests/Feature.
- Added index on column `author_id` in table `posts`
- Added an index for the author_id column in the posts table.
- All API endpoints start with /api (for example: /api/user/1).
- For the Get User List endpoint (`GET /api/users`), you can pass additional query parameters such as: 'limit' (to limit the number of returned results), `order_by` and `sort_type` (to customize sorting), `email` and `name` (to filter users by email or name). Example:
`/api/users?limit=3&page=1&order_by=id&sort_type=desc&email=testmail&name=userName`
- For the Get Post List endpoint (`GET /api/posts`), you can pass parameters such as: `limit` (to limit the number of returned results), `order_by` and `sort_type` (to customize sorting), `title` and `author_id` (to filter posts by title or author). Example:
`/api/posts?limit=20&page=1&order_by=id&sort_type=asc&author_id=2`

## A Few Personal Notes

- Initially, I considered implementing user authentication (using Laravel Sanctum with token-based authentication).
- However, after reviewing the requirements carefully, I realized that for a test assignment or an API prototype, adding authentication would unnecessarily complicate the scope. Another reason is that the test description does not mention any form of authorization or user access control — all APIs are accessible to anyone — so authentication is not required.
- If authentication were added, many additional cases would arise, such as restricting users so they can only interact with (get, update, delete) their own posts, preventing them from deleting other users’ content, etc. This would introduce a large number of scenarios that go beyond the intended scope of a simple test.
- For the post-related APIs, I handled two special cases where a post’s author has been deleted, and I also added tests specifically for this scenario.