## Setting up CORS in the Backend

CORS (Cross-Origin Resource Sharing) is an important security feature that allows you to control which domains are permitted to access resources on your server. If you're deploying the backend to a production server, make sure to configure CORS properly to allow the frontend to communicate with the backend.

### Default Configuration

By default, the backend uses CodeIgniter 4's built-in CORS filter to handle cross-origin requests. Here's a step-by-step guide to configure it.

### 1. Enabling CORS for Specific Routes

In the `app/Config/Routes.php` file, the CORS filter is applied to the `/api` route group:

```php
$routes->group("api", ["filter" => "cors"], function ($routes) {
    // API routes
});
```

This ensures that all routes under `/api` will be handled with CORS enabled.

### 2. Configure CORS Settings

CORS settings can be customized in the `app/Config/Cors.php` file. You can configure which domains (origins), HTTP methods, and headers are allowed. Here's the section you need to focus on:

```php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public $allowedOrigins = ['http://localhost:8080', 'https://your-frontend-domain.com']; // Add frontend domains here
    public $allowedHeaders = ['Content-Type', 'Authorization']; // Headers allowed in the request
    public $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']; // HTTP methods allowed
}
```

#### Explanation:

- **allowedOrigins**: Specify the domains that are allowed to access your API. You can add multiple domains here, such as your local development URL (e.g., `http://localhost:8080`) and the production frontend domain (e.g., `https://your-frontend-domain.com`). Using `['*']` is not recommended because it allows access from any domain.
- **allowedHeaders**: Set the allowed HTTP headers that the frontend can include in requests (e.g., `Authorization` for tokens).

- **allowedMethods**: Define which HTTP methods are permitted in cross-origin requests (e.g., `GET`, `POST`, `PUT`, `DELETE`).

### 3. Handle Preflight OPTIONS Requests

CORS preflight requests are automatically handled by CodeIgniter 4's CORS filter. However, if you need to manually add support for specific routes, add this line inside your route group:

```php
$routes->options('api/(:any)', static function () {}); // Allows preflight OPTIONS requests
```

This is especially important when dealing with custom HTTP methods (like `PUT` or `DELETE`) or sending custom headers (like `Authorization`).

### 4. Handling Cookies for Authentication (Optional)

If your backend uses cookies (e.g., for session-based authentication), make sure to allow credentials to be sent along with requests. You can update your frontend Axios configuration to include credentials in requests:

```javascript
axios.get("https://your-api.com/endpoint", {
  withCredentials: true, // Include cookies with the request
});
```

This will send cookies with the request, which is necessary if you are using session-based authentication. However, if you're using tokens (e.g., JWT), you don’t need to worry about this.

### 5. Testing CORS

After configuring the backend, you should test that CORS is working correctly:

- Use the browser's Developer Tools to check if any CORS-related errors appear in the console when making API requests from your frontend.
- You can also use Postman or similar tools to simulate requests from different origins.

---

### When Deploying to Production

When deploying your backend to production, make sure to update the `allowedOrigins` in the `Cors.php` file to reflect the real domain of your frontend application. This helps ensure that only trusted domains can make requests to your backend.

---
