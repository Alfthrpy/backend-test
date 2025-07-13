# Employee Management API

This is a REST API built with Laravel 12 for managing employees and divisions. It features token-based authentication, CRUD operations for employees, and filtering capabilities.

## Tech Stack

-   **Framework**: Laravel 12
-   **PHP**: ^8.2
-   **Authentication**: Laravel Sanctum (Token-based)
-   **Database**: MySQL
-   **File Handling**: Intervention/Image for image processing

## Project Setup

1.  **Clone the repository**
    ```bash
    git clone <your-repository-url>
    cd <your-project-directory>
    ```

2.  **Install PHP dependencies**
    ```bash
    composer install
    ```

3.  **Setup Environment**
    -   Copy the `.env.example` file to `.env`.
    -   Generate an application key:
        ```bash
        php artisan key:generate
        ```
    -   Configure your database connection variables in the `.env` file (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4.  **Run Database Migrations & Seeders**
    -   This will create all necessary tables and populate the `divisions` and `users` tables with sample data.
    ```bash
    php artisan migrate --seed
    ```

5.  **Create Storage Symlink**
    -   This makes uploaded files (like employee images) publicly accessible.
    ```bash
    php artisan storage:link
    ```

6.  **Run the application**
    ```bash
    php artisan serve
    ```

---

## API Endpoints

All endpoints are prefixed with `/api`.

### **Authentication**

#### **Login**
-   **Method**: `POST`
-   **Endpoint**: `/login`
-   **Description**: Authenticates a user and returns a Sanctum token.
-   **Body**:
    ```json
    {
        "username": "your_username",
        "password": "your_password"
    }
    ```
-   **Success Response (200)**:
    ```json
    {
        "success": "success",
        "message": "Login berhasil",
        "data": {
            "token": "1|xxxxxxxxxxxxxxxx",
            "admin": {
                "id": "user-uuid",
                "name": "Test User",
                "username": "Test User",
                "phone": "+1234567890",
                "email": "user@example.com"
            }
        }
    }
    ```
-   **Error Response (401)**:
    ```json
    {
        "status": "error",
        "message": "Username atau Password salah."
    }
    ```

---

### **Protected Routes**

All the following routes require an `Authorization` header with a bearer token.

-   **Header**: `Authorization: Bearer <your-sanctum-token>`
-   **Header**: `Accept: application/json`

#### **Logout**
-   **Method**: `POST`
-   **Endpoint**: `/logout`
-   **Description**: Revokes the current user's access token.
-   **Success Response (200)**:
    ```json
    {
        "status": "success",
        "message": "Logout berhasil"
    }
    ```

### **Divisions**

#### **Get All Divisions**
-   **Method**: `GET`
-   **Endpoint**: `/divisions`
-   **Description**: Retrieves a paginated list of all divisions.
-   **Query Parameters (Optional)**:
    -   `name` (string): Filter divisions by name (partial match).
    -   `page` (integer): The page number for pagination.
-   **Success Response (200)**: Returns a `DivisionResource` with a paginated list of divisions.

### **Employees**

#### **Get All Employees**
-   **Method**: `GET`
-   **Endpoint**: `/employees`
-   **Description**: Retrieves a paginated list of employees.
-   **Query Parameters (Optional)**:
    -   `name` (string): Filter employees by name (partial match).
    -   `division_id` (string): Filter employees by the division's UUID.
    -   `page` (integer): The page number for pagination.
-   **Success Response (200)**: Returns an `EmployeeResource` with a paginated list of employees.

#### **Create Employee**
-   **Method**: `POST`
-   **Endpoint**: `/employees`
-   **Description**: Creates a new employee record. Requires `form-data` due to file upload.
-   **Request Body (`form-data`)**:
    -   `image` (file): Image of the employee (`jpeg,png,jpg,gif,svg`, max 2MB). **Required**.
    -   `name` (string): Employee's name. **Required**.
    -   `phone` (string): Employee's phone number. **Required|Unique**.
    -   `division` (string): The UUID of the division. **Required**.
    -   `position` (string): Employee's job position. **Required**.
-   **Success Response (200)**:
    ```json
    {
        "status": "success",
        "message": "Karyawan berhasil ditambahkan"
    }
    ```
-   **Error Response (422)**: If validation fails.

#### **Update Employee**
-   **Method**: `POST` (using `form-data`)
-   **Endpoint**: `/employees/{id}` (where `{id}` is the employee's UUID)
-   **Description**: Updates an existing employee's record. Must send as `form-data` and include a `_method` field.
-   **Request Body (`form-data`)**:
    -   `_method` (string): Must be set to `PUT`. **Required**.
    -   `image` (file): New image for the employee. **Optional**.
    -   `name` (string): Employee's name. **Required**.
    -   `phone` (string): Employee's phone number. **Required|Unique** (except for the current employee).
    -   `division` (string): The UUID of the division. **Required**.
    -   `position` (string): Employee's job position. **Required**.
-   **Success Response (200)**:
    ```json
    {
        "status": "success",
        "message": "Karyawan berhasil diperbarui"
    }
    ```
-   **Error Response (422)**: If validation fails.

#### **Delete Employee**
-   **Method**: `DELETE`
-   **Endpoint**: `/employees/{id}` (where `{id}` is the employee's UUID)
-   **Description**: Deletes an employee record and their associated image from storage.
-   **Success Response (200)**:
    ```json
    {
        "status": "success",
        "message": "Karyawan berhasil dihapus"
    }
    ```
-   **Error Response (404)**: If the employee is not found.

### TEST SQL
#### **Nilai RT**
- **Method** : `GET`
- **Endpoint** : `\nilaiRT` 
- **Description** : Get nilai with materi_uji_id 7, excluding those in pelajaran_khusus
-   **Success Response (200)**:
    ```json
    [
    {
        "nama": "Ahmad Fadlan",
        "nisn": "3097012709",
        "nilaiRT": {
            "artistic": "2",
            "conventional": "2",
            "enterprising": "4",
            "investigative": "2",
            "realistic": "4",
            "social": "2"
        }
    },
    ]
    ```


#### **Nilai ST**
- **Method** : `GET`
- **Endpoint** : `\nilaiST` 
- **Description** : Get nilai with materi_uji_id 4 with sum
-   **Success Response (200)**:
    ```json
    [
    {
        "nama": "Muhammad Sanusi",
        "nisn": "0094494403",
        "listNilai": {
            "figural": 142.86,
            "kuantitatif": 89.01,
            "penalaran": 200,
            "verbal": 208.35
        },
        "total": 640.22
    },
    ]
    ```
