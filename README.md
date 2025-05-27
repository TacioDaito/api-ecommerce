# API Ecommerce

This is a simple RESTful API example for an e-commerce platform built with Laravel. It provides endpoints for managing users, roles, products, and orders. The API uses OAuth2 authentication with PKCE (Proof Key for Code Exchange) via Laravel Passport, ensuring secure access for web and mobile clients.

---

## Features

- **User Authentication**: Secure login and registration using OAuth2 with PKCE.
- **Role-Based Access Control**: Assign roles (e.g., admin, user) to users for fine-grained permissions.
- **Product Management**: CRUD operations for products, accessible only by admin users.
- **Order Management**: Users can create and view their orders; admins can access all orders.
- **Rate Limiting**: Protects API endpoints from abuse.
- **Custom Policies**: Authorization policies for sensitive actions.
- **Error Handling**: Consistent JSON error responses for API consumers.

---

## Authentication

This API uses **OAuth2 with PKCE** for authentication, implemented via [Laravel Passport](https://laravel.com/docs/10.x/passport). PKCE enhances security for public clients (like SPAs and mobile apps) by mitigating authorization code interception attacks.

### How PKCE Works

1. The client generates a code verifier and challenge.
2. The client initiates the OAuth2 flow, sending the challenge.
3. The server issues an authorization code.
4. The client exchanges the code for an access token, sending the original verifier.
5. The server validates the verifier before issuing tokens.

---

## API Endpoints

| Method | Endpoint            | Description                        | Auth Required | Role         |
|--------|---------------------|------------------------------------|---------------|--------------|
| GET    | `/api/user`         | Get authenticated user info        | Yes           | Any          |
| GET    | `/api/orders`       | List orders                        | Yes           | User/Admin   |
| POST   | `/api/orders`       | Create new order                   | Yes           | User/Admin   |
| GET    | `/api/products`     | List products                      | Yes           | Admin only   |
| POST   | `/api/products`     | Create product                     | Yes           | Admin only   |
| ...    | ...                 | ...                                | ...           | ...          |

> **Note:** Product routes are protected by a policy allowing only admin users.

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- MySQL or PostgreSQL
- [Node.js](https://nodejs.org/) (for frontend assets, if needed)

### Installation

1. **Clone the repository:**
   ```sh
   git clone https://github.com/yourusername/api-ecommerce.git
   cd api-ecommerce
   ```

2. **Install dependencies:**
   ```sh
   composer install
   ```

3. **Copy and configure environment:**
   ```sh
   cp .env.example .env
   # Edit .env with your DB and Passport settings
   ```

4. **Generate application key:**
   ```sh
   php artisan key:generate
   ```

5. **Run migrations and seeders:**
   ```sh
   php artisan migrate --seed
   ```

6. **Install Passport and generate keys:**

TacioDaito: Can a has and belongToMany relationship exist?

GitHub Copilot: Yes, in Laravel, a "has and belongs to many" relationship is called a **many-to-many** relationship. This is set up using the `belongsToMany` method on both related models.

In your code, the `Role` model correctly defines:

```php
public function users()
{
    return $this->belongsToMany(User::class);
}
```

But in your `User` model, you should also use `belongsToMany` for the `roles` relationship, not `has`:

```php
public function roles()
{
    return $this->belongsToMany(Role::class);
}
```

The `has` method is not valid for many-to-many relationships. Use `belongsToMany` on both sides for a proper many-to-many relationship.

TacioDaito: What one-to-many relationships can exist in this?

GitHub Copilot: In your provided models, the only **one-to-many** relationship is:

### User → Order

```php
public function orders()
{
    return $this->hasMany(Order::class);
}
```

- **One User has many Orders**
- **Each Order belongs to one User** (assuming the `Order` model has a `user_id` foreign key)

---

The `roles()` relationship in `User` and the `users()` relationship in `Role` are intended to be **many-to-many** (should use `belongsToMany`, not `has`).  
No other one-to-many relationships are defined in these models.

TacioDaito: What one-to-many relationships can exist between role and user?

GitHub Copilot: Based on your current models, **no one-to-many relationship exists between `Role` and `User`**.

- The `Role` model uses `belongsToMany(User::class)`, indicating a many-to-many relationship.
- The `User` model should also use `belongsToMany(Role::class)` (currently, it incorrectly uses `has(Role::class)`).

**If you want a one-to-many relationship** (e.g., one role has many users, each user has one role), you would:

1. Add a `role_id` foreign key to the `users` table.
2. In `User`:
    ```php
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    ```
3. In `Role`:
    ```php
    public function users()
    {
        return $this->hasMany(User::class);
    }
    ```

**Currently, your setup is for many-to-many, not one-to-many.**  
So, as written, there is **no one-to-many relationship between `Role` and `User`**.

TacioDaito: User with role 'admin' can't access products routes

GitHub Copilot: The issue is likely due to how the `can:onlyAllowAdmin` middleware and policy are set up. Here’s what you need to check and fix:

---

### 1. Policy Registration

Make sure your `ProductPolicy` is registered in `AuthServiceProvider`:

```php
// ...existing code...
protected $policies = [
    \App\Models\Product::class => \App\Policies\ProductPolicy::class,
];
// ...existing code...
```

---

### 2. Gate Definition

The `can` middleware expects a [Gate](https://laravel.com/docs/10.x/authorization#gates) or policy method.  
You need to define a gate in `AuthServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;

public function boot()
{
    $this->registerPolicies();

    Gate::define('onlyAllowAdmin', function ($user) {
        // Adjust this logic to match your role implementation
        return $user->role && $user->role->name === 'admin';
    });
}
```

---

### 3. User Model Relationship

Your `User` model should have a `role()` relationship (not `roles()` and not `belongsToMany`).  
Update your `User` model:

```php
public function role()
{
    return $this->belongsTo(Role::class);
}
```

---

### 4. Usage

Now, the `can:onlyAllowAdmin` middleware will check if the authenticated user’s role is `admin`.

---

#### **Summary of Fixes**
- Register the policy in `AuthServiceProvider`.
- Define the `onlyAllowAdmin` gate.
- Ensure the `User` model has a `role()` relationship.
- Make sure your users have a `role_id` set in the database.

---

Let me know if you need help with any of these steps!

TacioDaito: How can I get the status code of the exception?

GitHub Copilot: You can get the status code from an exception in Laravel by calling the `getStatusCode()` method, which is available on HTTP exceptions like `HttpException`, `NotFoundHttpException`, and `AccessDeniedHttpException`.

**Example:**

```php
$exceptions->render(function (AccessDeniedHttpException $error) {
    return response()->json([
        'success' => false,
        'error' => $error->getMessage(),
        'status' => $error->getStatusCode(), // <-- status code here
    ], $error->getStatusCode());
});
```

For exceptions that do not have `getStatusCode()`, you may need to set a default or check the exception type before accessing the method.

TacioDaito: ?? and :? in php

GitHub Copilot: In PHP:

### `??` (Null Coalescing Operator)
- Returns the left operand if it exists and is not `null`; otherwise, returns the right operand.
- Commonly used for default values.

```php
$value = $array['key'] ?? 'default';
// Equivalent to: isset($array['key']) ? $array['key'] : 'default'
```

---

### `:?`  
- **There is no `:?` operator in PHP.**
- You might be thinking of the ternary operator: `condition ? value_if_true : value_if_false`.

```php
$result = $a > $b ? 'A is greater' : 'B is greater or equal';
```

---

**Summary:**  
- `??` is the null coalescing operator.  
- `:?` does not exist in PHP; use `? :` for ternary expressions.

TacioDaito: Write a professional README for github, talking about this API that uses Authentication with PKCE

GitHub Copilot: # API Ecommerce

This is a RESTful API for an e-commerce platform built with Laravel. It provides endpoints for managing users, roles, products, and orders. The API uses OAuth2 authentication with PKCE (Proof Key for Code Exchange) via Laravel Passport, ensuring secure access for web and mobile clients.

---

## Features

- **User Authentication**: Secure login and registration using OAuth2 with PKCE.
- **Role-Based Access Control**: Assign roles (e.g., admin, user) to users for fine-grained permissions.
- **Product Management**: CRUD operations for products, accessible only by admin users.
- **Order Management**: Users can create and view their orders; admins can access all orders.
- **Rate Limiting**: Protects API endpoints from abuse.
- **Custom Policies**: Authorization policies for sensitive actions.
- **Error Handling**: Consistent JSON error responses for API consumers.

---

## Authentication

This API uses **OAuth2 with PKCE** for authentication, implemented via [Laravel Passport](https://laravel.com/docs/10.x/passport). PKCE enhances security for public clients (like SPAs and mobile apps) by mitigating authorization code interception attacks.

### How PKCE Works

1. The client generates a code verifier and challenge.
2. The client initiates the OAuth2 flow, sending the challenge.
3. The server issues an authorization code.
4. The client exchanges the code for an access token, sending the original verifier.
5. The server validates the verifier before issuing tokens.

---

## API Endpoints

| Method | Endpoint            | Description                        | Auth Required | Role         |
|--------|---------------------|------------------------------------|---------------|--------------|
| GET    | `/api/user`         | Get authenticated user info        | Yes           | Any          |
| GET    | `/api/orders`       | List orders                        | Yes           | User/Admin   |
| POST   | `/api/orders`       | Create new order                   | Yes           | User/Admin   |
| GET    | `/api/products`     | List products                      | Yes           | Admin only   |
| POST   | `/api/products`     | Create product                     | Yes           | Admin only   |
| ...    | ...                 | ...                                | ...           | ...          |

> **Note:** Product routes are protected by a policy allowing only admin users.

---

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- MySQL or PostgreSQL
- [Node.js](https://nodejs.org/) (for frontend assets, if needed)

### Installation

1. **Clone the repository:**
   ```sh
   git clone https://github.com/yourusername/api-ecommerce.git
   cd api-ecommerce
   ```

2. **Install dependencies:**
   ```sh
   composer install
   ```

3. **Copy and configure environment:**
   ```sh
   cp .env.example .env
   # Edit .env with your DB and Passport settings
   ```

4. **Generate application key:**
   ```sh
   php artisan key:generate
   ```

5. **Run migrations and seeders:**
   ```sh
   php artisan migrate --seed
   ```

6. **Install Passport and generate keys:**
    ```sh
    php artisan passport:install
    php artisan passport:keys
    ```


#### Installation

```sh
git clone https://github.com/yourusername/api-ecommerce.git
cd api-ecommerce

composer install

cp .env.example .env
# Edit .env with your DB and Passport settings

php artisan key:generate

php artisan migrate --seed

php artisan passport:install
```

---

### License

This project is open-source.

---

### Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

### Contact

For questions or support, please open an issue on GitHub.
