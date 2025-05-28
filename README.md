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

This API uses **OAuth2 with PKCE** for authentication, implemented via [Laravel Passport](https://laravel.com/docs/12.x/passport#code-grant-pkce). PKCE enhances security for public clients (like SPAs and mobile apps) by mitigating authorization code interception attacks.

### How PKCE Works

1. The client generates a code verifier, a code challenge and a state.
2. The client initiates the OAuth2 flow, sending all the data as query parameters to the _/oauth/authorize/_ route.
3. The server responds with a login view.
4. The client logs in with their credentials.
5. The server responds with a consent view.
6. The client allows or denies access to the API.
8. The server issues an authorization code if allowed.
9. The client exchanges the code for an access token, sending the original verifier with the authorization code as query parameters.
10. The server validates the verifier before issuing tokens through URL query.

>![OAuth2.0 Flow](https://github.com/user-attachments/assets/ba9a185a-7706-4872-bc5c-36dfe5f4eb69)
>
>The code verifier should be a random string of between 43 and 128 characters containing letters, numbers, and "-", ".", "_", "~" characters, as defined in the RFC 7636 specification.
>
>The code challenge should be a Base64 encoded string with URL and filename-safe characters. The trailing '=' characters should be removed and no line breaks, whitespace, or other additional characters should be present.

```sh
<?php
$state = Str::random(40)
$codeVerifier = Str::random(128)
$encoded = base64_encode(hash('sha256', $codeVerifier, true));
$codeChallenge = strtr(rtrim($encoded, '='), '+/', '-_');
```

- Query params:
    - client_id: [your-client-id]
    - redirect_uri: [your-callback-uri]
    - response_type: code
    - scope:
    - state: [state]
    - code_challenge: [code-challenge]
    - code_challenge_method: S256

---

## API Endpoints

| Method | Endpoint            | Description                        | Auth Required | Role         |
|--------|---------------------|------------------------------------|---------------|--------------|
| GET    | `/api/user`         | Get authenticated user info        | Yes           | Any          |
| GET    | `/api/orders`       | List all user orders               | Yes           | User/Admin   |
| POST   | `/api/orders`       | Create new order                   | Yes           | User/Admin   |
| GET    | `/api/orders/{id}`  | Show specific order                | Yes           | User/Admin   |
| PUT/PATCH | `/api/orders/{id}` | Update specific order            | Yes           | User/Admin   |
| DELETE | `/api/orders/{id}`  | Delete specific order              | Yes           | User/Admin   |
| GET    | `/api/products`     | List products                      | Yes           | Admin only   |
| POST   | `/api/products`     | Create product                     | Yes           | Admin only   |
| GET    | `/api/products/{id}` | Show specific product             | Yes           | Admin only   |
| PUT/PATCH | `/api/products/{id}` | Update specific product        | Yes           | Admin only   |
| DELETE | `/api/products/{id}`  | Delete specific product          | Yes           | Admin only   |

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
    
---

### License

This project is open-source.

---

### Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

### Contact

For questions or support, please open an issue on GitHub.
