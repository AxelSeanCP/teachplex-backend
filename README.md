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

### **3. Courses**

#### **Add Course**

This endpoint requires an authenticated `Admin` user to add new courses.

**Authentication:**
This endpoint requires an **Access Token** in the request headers.

**Headers:**

```json
{
  "Authorization": "Bearer <your-access-token>"
}
```

**Endpoint:** `POST /api/courses`

**Request Type: `multipart/form-data`**

**Request Body:**

```json
{
  "title": "course-title",
  "description": "course-description",
  "duration": "12 hours",
  "level": "beginner",
  "long_description": "long-course-description", //optional
  "topics": ["course-topics-1", "course-topics-2"], //optional
  "thumbnail": "course-thumbnail.png" //optional
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "courseId": "course-id"
  }
}
```

#### **Get Courses**

**Endpoint:** `GET /api/courses`

**Response:**

```json
{
  "status": "success",
  "data": {
    "courses": [
      {
        "id": "course-1234",
        "title": "user-1234",
        "description": "course-description",
        "duration": "12 hours",
        "level": "beginner",
        "thumbnail": "course-thumbnail.png"
      }
    ]
  }
}
```

#### **Get Course Detail**

**Endpoint:** `GET /api/courses/:courseId`

**Response:**

```json
{
  "status": "success",
  "data": {
    "course": {
      "id": "course-id",
      "title": "course-title",
      "description": "course-description",
      "duration": "12 hours",
      "level": "beginner",
      "thumbnail": "course-thumbnail.png",
      "long_description": "course-long-description",
      "topics": ["course-topics-1", "course-topics-2"],
      "lessons": [
        {
          "id": "lesson-id",
          "title": "lesson-title"
        }
      ]
    }
  }
}
```

#### **Update Course**

This endpoint requires an authenticated `Admin` user to update courses.

**Authentication:**
This endpoint requires an **Access Token** in the request headers.

**Endpoint:** `PUT /api/courses/:courseId`

**Request Type: `multipart/form-data`**

**Request Body:**

```json
{
  "title": "course-title",
  "description": "course-description",
  "duration": "15 hours",
  "level": "intermediatte",
  "long_description": "long-course-description", //optional
  "topics": ["course-topics-1", "course-topics-2", "course-topics-3"], //optional
  "thumbnail": "course-thumbnail.png" //optional
}
```

**Response:**

```json
{
  "status": "success",
  "message": "Course updated successfully"
}
```

#### **Delete Course**

This endpoint requires an authenticated `Admin` user to delete courses.

**Authentication:**
This endpoint requires an **Access Token** in the request headers.

**Endpoint:** `DELETE /api/courses/:courseId`

**Response:**

```json
{
  "status": "success",
  "message": "Course deleted"
}
```

---

### **4. Lessons**

> This resource requires an **Access Token** in the request headers.

**Headers:**

```json
{
  "Authorization": "Bearer <your-access-token>"
}
```

#### **Add Lesson**

This endpoint requires an authenticated `Admin` user to add lesson.

**Endpoint:** `POST /api/courses/:courseId/lessons`

**Request Body:**

```json
{
  "title": "lesson-title",
  "content": "lesson-content"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "lessonId": "lesson-id"
  }
}
```

#### **Get Lesson Detail**

> viewing lesson detail will update lesson progress if enrolled to the course

**Endpoint:** `GET /api/courses/:courseId/lessons/:lessonId`

**Response:**

```json
{
  "status": "success",
  "data": {
    "lesson": {
      "id": "lesson-id",
      "title": "lesson-title",
      "slug": "lesson-slug",
      "content": "lesson-content",
      "lesson_order": "1"
    }
  }
}
```

#### **Update Lesson**

This endpoint requires an authenticated `Admin` user to update lesson.

**Endpoint:** `PUT /api/courses/:courseId/lessons/:lessonId`

**Request Body:**

```json
{
  "title": "lesson-title",
  "content": "lesson-content"
}
```

**Response:**

```json
{
  "status": "success",
  "message": "Lesson updated successfully"
}
```

#### **Delete Lesson**

This endpoint requires an authenticated `Admin` user to delete lesson.

**Endpoint:** `DELETE /api/courses/:courseId/lessons`

**Response:**

```json
{
  "status": "success",
  "message": "Lesson deleted successfully"
}
```

### **5. Course & Lesson Progress**

> This resource requires an **Access Token** in the request headers.

**Headers:**

```json
{
  "Authorization": "Bearer <your-access-token>"
}
```

#### **Get Overall Course Progress**

**Endpoint:** `GET /api/courses/:courseId/progress`

**Response:**

```json
{
  "status": "success",
  "data": {
    "progress": {
      "course_id": "course-id",
      "course_title": "course-title",
      "is_enrolled": true,
      "overall_course_completed": true,
      "overall_course_completed_at": "2025-05-23 10:15:23",
      "total_lessons": 1,
      "completed_lessons_count": 1,
      "progress_percentage": 100,
      "lessons": [
        {
          "id": "lesson-id",
          "title": "lesson-title",
          "slug": "lesson-slug",
          "lesson_order": 2,
          "is_completed": true,
          "completed_at": "2025-05-23 10:11:59"
        }
      ]
    }
  }
}
```

#### **Finish Course**

> finish lessons before finishing the course by viewing the lessons

**Endpoint:** `/api/courses/:courseId/finish`

**Response:**

```json
{
  "status": "success",
  "message": "Course completed"
}
```

---

### **3. Enrollments**

> This resource requires an **Access Token** in the request headers.

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
        "courseId": "course-1234"
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

#### **Get All User Enrollments**

> This endpoint requires an authenticated `Admin` user to get all user enrollments.

**Endpoint:** `/api/enrollments/all`

**Response:**

```json
{
  "status": "success",
  "data": {
    "enrollments": [
      {
        "id": "enrollment-id",
        "user_id": "user-id",
        "course_id": "course-id",
        "created_at": "2025-05-26 09:45:23",
        "updated_at": "2025-05-26 09:45:23",
        "user_name": "john",
        "course_title": "Belajar Laravel"
      },
      {
        "id": "enrollment-id",
        "user_id": "user-id",
        "course_id": "course-id",
        "created_at": "2025-05-23 13:09:00",
        "updated_at": "2025-05-23 13:09:00",
        "user_name": "jane",
        "course_title": "Belajar Laravel"
      }
    ]
  }
}
```

#### **Delete Enrollment**

> This endpoint requires an authenticated `Admin` user to delete enrollment.

**Endpoint:** `DELETE /api/enrollments/:enrollmentId`

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

> This endpoint requires an authenticated `Admin` user to upload template.

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

> note: make sure the course is finished before generating certificate

**Endpoint:** `POST /api/certificates`

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
  "data": {
    "certificateId": "certificate-id",
    "downloadUrl": "download-link"
  }
}
```

#### **Get All Certificates**

> This endpoint requires an authenticated `Admin` user to get all certificates.

> This endpoint requires an **Access Token** in the request headers.

**Headers:**

```json
{
  "Authorization": "Bearer <your-access-token>"
}
```

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
    "certificate": {
      "id": "certificate-id",
      "user_id": "user-id",
      "course_id": "course-id",
      "pdf_url": "http://teachplex-backend.test/api/certificates/download/certificate-id.pdf",
      "created_at": "2025-05-26 09:48:20",
      "updated_at": "2025-05-26 09:48:20",
      "user_name": "john"
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
