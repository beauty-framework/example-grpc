# 👤 Example gRPC for Beauty Framework

This is an example of an gRPC microservice on Beauty Framework. This example implements authorization, as well as saving logs by Job upon user login/register/validation completion. This microservice implements a simple authorization mechanism.

Since the framework is still raw, repositories with raw queries are used instead of the ORM module, and a custom command is used instead of migrations. However, all missing modules will be added in the future.

More docs about gRPC package in this repo [beauty-framework/grpc](https://github.com/beauty-framework/grpc)

---

## 📦 Installation

```bash
git clone https://github.com/beauty-framework/example-grpc beauty-example-grpc
```

```bash
cd beauty-example-grpc
```

```bash
cp .env.example .env
```

```bash
make up # or make prod
```

```bash
make beauty migrate
```

---

## 📘 gRPC API

Generated from: `proto/auth.proto`

### 🔐 Authentication Service

#### rpc `Login(LoginRequest) returns (LoginResponse)`

**Description**: Authenticate a user and return access token.

* **Request:**

```json
{
  "email": "user@example.com",
  "password": "secret"
}
```

* **Response:**

```json
{
  "token": "...",
  "name": "Kirill",
  "email": "admin@admin.com"
}
```

* **Auth:** ❌ None

---

#### rpc `Register(RegisterRequest) returns (RegisterResponse)`

**Description**: Register a new user and return access token.

* **Request:**

```json
{
  "name": "Kirill",
  "email": "user@example.com",
  "password": "secret"
}
```

* **Response:**

```json
{
  "token": "...",
  "name": "Kirill",
  "email": "user@example.com"
}
```

* **Auth:** ❌ None

---

#### rpc `Validate (ValidateRequest) returns (ValidateReply)`

**Description**: Validate access token and return user data.

* **Request:**

```json
{
  "token": "..."
}
```

* **Response:**

```json
{
  "valid": true,
  "name": "Kirill",
  "email": "user@example.com"
}
```

* **Auth:** ❌ None

---

#### rpc `Logout (ValidateRequest) returns (LogoutReply)`

**Description**: Logout user.

* **Request:**

```json
{
  "token": "..."
}
```

* **Response:**

```json
{
  "success": true
}
```

* **Auth:** ❌ None

---

## 🧠 CLI Commands

| Command              | Description                |
|----------------------|----------------------------|
| generate\:controller | Generate controller        |
| generate\:command    | Generate a new CLI command |
| generate\:middleware | Generate a new middleware  |
| generate\:request    | Generate a new request     |
| generate\:event      | Create a new event         |
| generate\:listener   | Create a new listener      |
| generate\:job        | Create a new job           |
| migrate              | Run a migrations           |

---

## 🐳 Docker Setup (default)

Beauty is designed to run **natively inside Docker**. By default, all services are containerized:

| Service | Image               | Notes                          |
|---------|---------------------|--------------------------------|
| app     | php:8.4-alpine + RR | RoadRunner + CLI build targets |
| db      | postgres:16         | PostgreSQL 16                  |
| redis   | redis\:alpine       | Redis 7                        |

---

## 🛠 Makefile Commands

| Category          | Command                                | Description                                     |
|-------------------|----------------------------------------|-------------------------------------------------|
| Start             | `make up`                              | Start the DEV environment                       |
|                   | `make prod`                            | Start the PROD environment                      |
| Stop              | `make stop`                            | Stop all containers                             |
|                   | `make down`                            | Remove all containers and volumes               |
|                   | `make restart`                         | Restart all containers                          |
|                   | `make restart-container CONTAINER=...` | Restart a specific container                    |
|                   | `make stop-container CONTAINER=...`    | Stop a specific container                       |
| PHP               | `make php <cmd>`                       | Run php command inside the app container        |
|                   | `make beauty <cmd>`                    | Run beauty CLI command inside the app container |
| Tests             | `make test`                            | Run PHPUnit tests                               |
| Composer          | `make composer <cmd>`                  | Run composer command inside the app container   |
| Shell             | `make bash`                            | Open bash shell inside the app container        |
| Logs              | `make logs <container>`                | View logs of specific container                 |
| Database          | `make psql`                            | Access PostgreSQL CLI                           |
| Cache             | `make redis`                           | Access Redis CLI                                |
| Generate protobuf | `make grpcgen proto/auth.proto`        | Generate protobuf from proto file               |

---

## 🔗 LICENSE
MIT
