# Laravel Tasks API

A robust RESTful API built with Laravel for managing tasks, featuring user authentication, task management, caching, and comprehensive test coverage.

![Laravel Version](https://img.shields.io/badge/Laravel-v12.1.1-FF2D20?style=flat-square&logo=laravel)
![PHP Version](https://img.shields.io/badge/PHP-v8.2-777BB4?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)

## Features

- 🔐 User Authentication (Register/Login/Logout)
- ✅ Complete Task Management (CRUD operations)
- 🚀 RESTful API Architecture
- 📝 Task Prioritization and Status Management
- 🔄 Request Caching for Better Performance
- 🛡️ Secure Authentication using Laravel Sanctum
- ⚡ Exception Handling with Custom API Responses
- 📊 Pagination Support
- 🔍 Advanced Filtering Options
- 📅 Due Date Management
- ✨ Task Status Transitions
- 📱 Mobile-Friendly API Design

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Laravel 12.x

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/mohamad-slime/laravel-tasks-api.git
    cd laravel-tasks-api
    ```

2. Install dependencies:

    ```bash
    composer install
    ```

3. Set up environment file:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Configure your database in `.env`:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

5. Run migrations and seeders:

    ```bash
    php artisan migrate --seed
    ```

## API Documentation

### Authentication

- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires authentication)
- `GET /api/me` - Get authenticated user info

### Tasks

All task endpoints require authentication:

- `GET /api/tasks` - List all tasks (with pagination & filters)
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get a specific task
- `PUT /api/tasks/{id}` - Update a task
- `DELETE /api/tasks/{id}` - Delete a task
- `GET /api/tasks/overdue` - Get overdue tasks
- `GET /api/tasks/upcoming` - Get upcoming tasks

### Filtering Tasks

You can filter tasks using query parameters:

```http
GET /api/tasks?status=pending&priority=high&search=project
```

Available filters:
- `status`: pending/in_progress/completed
- `priority`: low/medium/high
- `search`: Search in title and description
- `due_date`: Filter by due date
- `per_page`: Number of items per page (default: 10)

## Request & Response Examples

### Register User

```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Create Task

```http
POST /api/tasks
Content-Type: application/json
Authorization: Bearer {your_token}

{
    "title": "Complete Project",
    "description": "Finish the Laravel API project",
    "priority": "high",
    "status": "pending",
    "due_date": "2025-11-15"
}
```

## Task Properties

- `title` - Task title (required)
- `description` - Task description (required)
- `priority` - Task priority (low/medium/high)
- `status` - Task status (pending/in_progress/completed)
- `due_date` - Task due date (optional)
- `completed_at` - Timestamp when task was completed

## Error Handling

The API uses standard HTTP response codes and returns consistent error responses:

```json
{
    "error": {
        "type": "ValidationException",
        "status": 422,
        "message": "The title field is required."
    }
}
```

## Security

- API authentication via Laravel Sanctum
- Protected routes require authentication
- Rate limiting implementation
- Input validation on all endpoints
- SQL injection protection
- XSS protection
- CORS support

## Testing

Run the test suite:

```bash
php artisan test
```

The project includes:
- Unit Tests (Models & Relationships)
- Feature Tests (API Endpoints)
- Authentication Tests
- Validation Tests

## Cache Configuration

The API implements caching for optimal performance:

```env
CACHE_DRIVER=redis
CACHE_LIFETIME=60
```

## Performance Optimizations

- Response Caching
- Eager Loading Relationships
- Database Indexing
- Query Optimization
- Rate Limiting

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the MIT license.

## Support

For support, please create an issue in the GitHub repository or contact the maintainers.

## Authors

- **Mohamad Salehi** - *Initial work* - [mohamad-slime](https://github.com/mohamad-slime)
