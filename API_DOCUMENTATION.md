# API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication Endpoints

### 1. Register User
**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Success Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-11-27T10:00:00.000000Z",
    "updated_at": "2025-11-27T10:00:00.000000Z"
  },
  "access_token": "1|xxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

**Validation Errors (422):**
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 2. Login User
**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-11-27T10:00:00.000000Z",
    "updated_at": "2025-11-27T10:00:00.000000Z"
  },
  "access_token": "2|xxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

**Error Response (422):**
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### 3. Get Authenticated User
**Endpoint:** `GET /api/user`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Success Response (200):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "email_verified_at": null,
  "created_at": "2025-11-27T10:00:00.000000Z",
  "updated_at": "2025-11-27T10:00:00.000000Z"
}
```

**Error Response (401):**
```json
{
  "message": "Unauthenticated."
}
```

---

### 4. Logout User
**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Success Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

## Frontend Integration (Next.js)

### Using Fetch API

```javascript
// Register
const register = async (name, email, password, password_confirmation) => {
  const response = await fetch('http://localhost:8000/api/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ name, email, password, password_confirmation }),
  });
  
  const data = await response.json();
  
  if (response.ok) {
    // Store token in localStorage or state management
    localStorage.setItem('token', data.access_token);
    return data;
  } else {
    throw new Error(data.message);
  }
};

// Login
const login = async (email, password) => {
  const response = await fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ email, password }),
  });
  
  const data = await response.json();
  
  if (response.ok) {
    localStorage.setItem('token', data.access_token);
    return data;
  } else {
    throw new Error(data.message);
  }
};

// Get User
const getUser = async () => {
  const token = localStorage.getItem('token');
  
  const response = await fetch('http://localhost:8000/api/user', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`,
    },
  });
  
  return await response.json();
};

// Logout
const logout = async () => {
  const token = localStorage.getItem('token');
  
  const response = await fetch('http://localhost:8000/api/logout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`,
    },
  });
  
  if (response.ok) {
    localStorage.removeItem('token');
  }
};
```

### Using Axios

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Register
export const register = async (name, email, password, password_confirmation) => {
  const { data } = await api.post('/register', { name, email, password, password_confirmation });
  localStorage.setItem('token', data.access_token);
  return data;
};

// Login
export const login = async (email, password) => {
  const { data } = await api.post('/login', { email, password });
  localStorage.setItem('token', data.access_token);
  return data;
};

// Get User
export const getUser = async () => {
  const { data } = await api.get('/user');
  return data;
};

// Logout
export const logout = async () => {
  await api.post('/logout');
  localStorage.removeItem('token');
};
```

---

## CORS Configuration

The API is configured to accept requests from:
- `http://localhost:3000` (Next.js default)

If your frontend runs on a different port, update the `FRONTEND_URL` in `.env`:

```env
FRONTEND_URL=http://localhost:3001
```

---

## Running the Laravel Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

---

## Database

- **Connection:** MySQL
- **Database:** ekusheyCodingBackend
- **Tables:** users, personal_access_tokens, sessions, cache, jobs

Make sure your MySQL server is running and the database exists before running migrations.
