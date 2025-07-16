# Task Management System (softExpertTask)

A simple task management system built with Laravel that supports role-based access control for Managers and Users.

---

## Features

- Authentication for seeded system actors
- Role-based permissions (Manager / User)
- Task creation, assignment, and status updates
- Task dependencies (A task can't be completed until all its dependencies are completed)
- Filter tasks by status, due date range, and assignee

---

## Requirements

- PHP >= 8.2
- Composer
- Laravel >= 11
- MySQL or compatible database

---

## Setup Instructions

### 1. Clone the repository
    git clone https://github.com/Mohamed855/softExpertTask.git
### 2. Open project directory
    cd softExpertTask
### 3. Install Dependencies
    composer install
### 4. Configure Environment
    cp .env.example .env
Edit the `.env` file and configure your database credentials (create a database named `task_management` for example).
### 5. Run Migrations and Seeders
    php artisan key:generate
### 6. Run Migrations and Seeders
    php artisan migrate --seed
### 7. Start the Server
    php artisan serve

---

## Seeded Users

| Role    | Email             | Password |
|---------|-------------------|----------|
| Manager | manager@test.com  | manager  |
| User    | user@test.com     | user     |

---

## API Endpoints

### Public (no auth required)

| Method | Endpoint     | Description     |
|--------|--------------|-----------------|
| POST   | /api/login   | Login           |

### Authenticated Users (User or Manager)

| Method | Endpoint                                | Description                         |
|--------|-----------------------------------------|-------------------------------------|
| GET    | /api/profile                            | View profile                        |
| ANY    | /api/logout                             | Logout                              |
| GET    | /api/tasks                              | List tasks                          |
| POST   | /api/tasks                              | Create a task (Manager only)        |
| GET    | /api/tasks/{id}                         | View task details                   |
| PUT    | /api/tasks/{id}                         | Update task                         |
| DELETE | /api/tasks/{id}                         | Delete task (Manager only)          |
| POST   | /api/tasks/{id}/assign-dependencies     | Assign dependencies to a task       |
| POST   | /api/tasks/{id}/update-status           | Update task status                  |

### Lists

| Method | Endpoint             | Description                                |
|--------|----------------------|--------------------------------------------|
| GET    | /api/users           | List users with role 'user'                |
| GET    | /api/managers        | List users with role 'manager'             |
| GET    | /api/tasks-list      | List all tasks (for dependency selection)  |
| GET    | /api/task-statuses   | List available task statuses               |

---

## Roles and Permissions

### User Role

- Can login, view profile, and logout.
- Can view only their own tasks.
- Can filter and view task details.
- Can update the status of assigned tasks only if all dependencies are completed.
- Can view task statuses list.

### Manager Role

- Can login, view profile, and logout.
- Can view all tasks, filter them, and view task details.
- Can create new tasks and assign them to users.
- Can update task details and reassign users.
- Can delete tasks.
- Can assign task dependencies.
- Can update task statuses (only after dependencies are completed).
- Can view users, managers, tasks, and statuses lists.
