# **teachplex-backend API Documentation**

**teachplex-backend** is a backend API developed using **CodeIgniter 4 (CI4)** for a training and certification platform.  
It handles user authentication, course enrollment, certificate generation, and more.

> Important note: don't forget to add frontend url into allowedOrigins section in [App\Config\Cors](App/Config/Cors.php). for more information please read [this](setupcors.md)

---

## ⚙️ Overview

This API provides authentication and user management functionalities using **JSON Web Tokens (JWT)** for secure access.

---

## 📦 Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/AxelSeanCP/teachplex-backend.git
cd teachplex-backend
```

### 2. Set Up the Environment

Copy the example environment file and configure it:

```bash
cp env .env
```

Then update `.env` with your settings:

```
app.baseURL = 'http://teachplex-backend.test/'
database.default.hostname = localhost
database.default.database = teachplex
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi

ACCESS_TOKEN_KEY=your_access_token_key
REFRESH_TOKEN_KEY=your_refresh_token_key
```

> ⚠️ Make sure `app.baseURL` matches your development environment (e.g., Laragon/Nginx).

---

### 3. Install Dependencies

Install Composer dependencies:

```bash
composer install
```

---

### 4. Set Up the Database

Make sure your database is created, then run migrations:

```bash
php spark migrate
```

---

### 5. Configure URL Routing (Optional)

If using **Nginx**, make sure your virtual host is configured to point to `public/`.

Example `server` block for Nginx:

```nginx
server {
    listen 80;
    server_name teachplex-backend.test *.teachplex-backend.test;
    root "D:/laragon/www/teachplex-backend/public";

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$is_args$args;
        autoindex on;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass php_upstream;
    }

    charset utf-8;

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    location ~ /\.ht {
        deny all;
    }
}
```

---

### 6. Start the Server

For local development, run:

```bash
php spark serve
```

The server will run at `http://localhost:8080` by default.

---

## 🛡️ Authentication

The API uses JWT for authentication. Make sure to pass your token in the `Authorization` header as:

```
Authorization: Bearer <token>
```

Tokens include:

- **Access Token**
- **Refresh Token**

---

## 📁 Folder Structure Highlights

```
app/
├── Controllers/         → Route controllers (e.g., Auth, Certificates)
├── Models/              → Database models
├── Services/            → Business logic layer
├── Views/               → HTML templates (used for certificate rendering)
├── Filters/             → JWT middleware
├── Helpers/             → Custom utilities
```

---

## API Endpoints

### **1. Users**

#### **Create a New User**

**Endpoint:** `POST /api/users`

**Request Body:**

```json
{
  "name": "Axel",
  "email": "axel@gmail.com",
  "password": "axel1234"
}
```

**Response:**

```json
{
  "status": "success",
  "message": "User created",
  "data": {
    "userId": "user-1234"
  }
}
```

#### **Get All Users**

**Endpoint:** `GET /api/users/`

**Response:**

```json
{
  "status": "success",
  "data": {
    "users": [
      {
        "id": "user-1234",
        "name": "Axel",
        "email": "axel@gmail.com"
      },
      {
        "id": "user-5678",
        "name": "Alex",
        "email": "alex@gmail.com"
      }
    ]
  }
}
```

#### **Get a User**

**Endpoint:** `GET /api/users/:id`

**Response:**

```json
{
  "status": "success",
  "data": {
    "user": {
      "id": "user-1234",
      "name": "Axel",
      "email": "axel@gmail.com"
    }
  }
}
```

---

### **2. Authentications**

#### **Login**

**Endpoint:** `POST /api/authentications`

**Request Body:**

```json
{
  "email": "axel@gmail.com",
  "password": "axel1234"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "accessToken": "access-token",
    "refreshToken": "refresh-token"
  }
}
```

> token will contain userId in _sub_ property

#### **Refresh token**

**Endpoint:** `PUT /api/authentications`

**Request Body:**

```json
{
  "refreshToken": "refresh-token"
}
```

**Response:**

```json
{
  "status": "success",
  "message": "Access token has been refreshed",
  "data": {
    "accessToken": "access-token"
  }
}
```

#### **Logout**

**Endpoint:** `DELETE /api/authentications`

**Request Body:**

```json
{
  "refreshToken": "refresh-token"
}
```

**Response:**

```json
{
  "status": "success",
  "message": "Refresh token deleted"
}
```

---

### **3. Enrollments**

> This endpoint requires an **Access Token** in the request headers.

**Headers:**

```json
{
  "Authorization": "Bearer <your-access-token>"
}
```

#### **Add Enrollment**

**Endpoint:** `POST /api/enrollments`

**Request Body:**

```json
{
  "courseId": "course-12345"
}
// input courseId from wordpress
```

**Response:**

```json
{
  "status": "success",
  "message": "User enrolled"
}
```

#### **Get Enrollments**

> Returns enrollments for the currently authenticated user.

**Endpoint:** `GET /api/enrollments`

**Response (with enrollments):**

```json
{
  "status": "success",
  "data": {
    "enrollments": [
      {
        "id": "enrollment-1234",
        "userId": "user-1234",
        "courseId": "course-1234",
        "isCompleted": 1
      }
    ]
  }
}
```

**Response (if no enrollments):**

```json
{
  "status": "success",
  "data": {
    "enrollments": []
  }
}
```

> Note: This endpoint always returns a `200 OK` status. If the user is not enrolled in any courses, the `enrollments` array will be empty.

#### **Delete Enrollment**

**Endpoint:** `DELETE /api/enrollments`

**Request Body:**

```json
{
  "courseId": "course-1234"
}
```

**Response:**

```json
{
  "status": "success",
  "message": "Enrollments deleted"
}
```

---

### **4. Certificates**

> Manage and verify course completion certificates. Some endpoints **require authentication**.

#### **Upload Certificate Template**

> This endpoint requires an **Access Token** in the request headers.

**Headers:**

```json
{
  "Authorization": "Bearer <your-access-token>"
}
```

**Endpoint:** `POST /api/certificates/templates/upload`

**Form Data:**

- `template`: file (image-based certificate template)

**Response:**

```json
{
  "status": "success",
  "filename": "random-generated-filename.png"
}
```

#### **Create Certificate**

> This endpoint requires an **Access Token** in the request headers.

**Headers:**

```json
{
  "Authorization": "Bearer <your-access-token>"
}
```

**Endpoint:** `POST /api/certificates`

**Request Body:**

```json
{
  "courseId": "course-1234",
  "courseName": "Learn JavaScript"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "certificates": {
      "id": "certificateId"
    },
    "downloadUrl": "download-link"
  }
}
```

#### **Get All Certificates**

> You can also **filter results** using optional query parameters `name` and/or `email`.

**Endpoint:** `GET /api/certificates`

**Optional Query Parameters:**

| Parameter | Type   | Description                             |
| --------- | ------ | --------------------------------------- |
| `name`    | string | Filter certificates by user's full name |
| `email`   | string | Filter certificates by user's email     |

**Example:**
`GET /api/certificates?name=Axel&email=axel@example.com`

**Response:**

```json
{
  "status": "success",
  "data": {
    "certificates": [
      {
        "id": "certificateId",
        "userId": "user-1234",
        "courseId": "course-1234",
        "courseName": "Learn JavaScript",
        "pdfUrl": "/certificates/{certificateId}.pdf",
        "user_name": "Axel"
      }
    ]
  }
}
```

#### **Verify Certificate**

**Endpoint:** `GET /api/certificates/{certificateId}/verify`

**Response:**

```json
{
  "status": "success",
  "data": {
    "certificates": {
      "id": "certificateId",
      "userId": "user-1234",
      "courseId": "course-1234",
      "courseName": "Learn JavaScript",
      "pdfUrl": "/certificates/{certificateId}.pdf",
      "user_name": "Axel"
    }
  }
}
```

#### **Download Certificate**

> download link is obtainable from post and get in /certificates

**Endpoint:** `GET /api/certificates/download/{filename}`

**Behavior:**

- Downloads a generated certificate PDF
- Returns 404 if file does not exist

**Response:**

- PDF file download stream
- Or JSON error:

```json
{
  "status": "fail",
  "message": "Certificate not found"
}
```

### Notes:

- Ensure you provide a valid JWT token for protected routes.
- The API follows RESTful conventions.
- Database migrations must be run before using the API.

For further details, check the [GitHub Repository](https://github.com/AxelSeanCP/teachplex-backend).
