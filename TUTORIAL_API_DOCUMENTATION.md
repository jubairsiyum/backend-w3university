# Tutorial API Documentation

## Base URL
```
https://backend-w3university.vercel.app/api
```

## Public Endpoints

### Get All Tutorials

```http
GET /api/tutorials
```

**Query Parameters:**
- `language_id` (optional): Filter tutorials by programming language (e.g., 'javascript', 'python', 'html')
- `is_published` (optional): Filter by published status (true/false)
- `search` (optional): Search tutorials by title
- `per_page` (optional): Number of items per page (default: returns all)

**Example Request:**
```bash
curl -X GET "https://backend-w3university.vercel.app/api/tutorials?language_id=javascript"
```

**Example Response:**
```json
[
  {
    "id": 1,
    "language_id": "javascript",
    "title": "Introduction to JavaScript",
    "content": "JavaScript is a lightweight, interpreted programming language...",
    "code_example": "console.log('Hello, World!');",
    "order": 1,
    "is_published": true,
    "created_at": "2025-12-10T08:00:00.000000Z",
    "updated_at": "2025-12-10T08:00:00.000000Z"
  }
]
```

### Get Single Tutorial

```http
GET /api/tutorials/{id}
```

**Example Request:**
```bash
curl -X GET "https://backend-w3university.vercel.app/api/tutorials/1"
```

**Example Response:**
```json
{
  "id": 1,
  "language_id": "javascript",
  "title": "Introduction to JavaScript",
  "content": "JavaScript is a lightweight, interpreted programming language...",
  "code_example": "console.log('Hello, World!');",
  "order": 1,
  "is_published": true,
  "created_at": "2025-12-10T08:00:00.000000Z",
  "updated_at": "2025-12-10T08:00:00.000000Z"
}
```

### Get Available Languages

```http
GET /api/tutorials/languages
```

**Example Request:**
```bash
curl -X GET "https://backend-w3university.vercel.app/api/tutorials/languages"
```

**Example Response:**
```json
[
  "javascript",
  "python",
  "html",
  "css",
  "react",
  "nodejs"
]
```

## Protected Endpoints (Requires Authentication)

### Create Tutorial

```http
POST /api/tutorials
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "language_id": "javascript",
  "title": "Variables and Data Types",
  "content": "Learn about var, let, const and different data types in JavaScript.",
  "code_example": "let name = 'John';\nconst age = 25;",
  "order": 2,
  "is_published": true
}
```

**Example Request:**
```bash
curl -X POST "https://backend-w3university.vercel.app/api/tutorials" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "language_id": "javascript",
    "title": "Variables and Data Types",
    "content": "Learn about var, let, const and different data types.",
    "code_example": "let name = '\''John'\'';\nconst age = 25;",
    "order": 2,
    "is_published": true
  }'
```

**Example Response:**
```json
{
  "message": "Tutorial created successfully",
  "tutorial": {
    "id": 2,
    "language_id": "javascript",
    "title": "Variables and Data Types",
    "content": "Learn about var, let, const and different data types.",
    "code_example": "let name = 'John';\nconst age = 25;",
    "order": 2,
    "is_published": true,
    "created_at": "2025-12-10T08:30:00.000000Z",
    "updated_at": "2025-12-10T08:30:00.000000Z"
  }
}
```

### Update Tutorial

```http
PUT /api/tutorials/{id}
Authorization: Bearer {token}
```

**Request Body:** (All fields are optional)
```json
{
  "title": "Updated Title",
  "content": "Updated content...",
  "code_example": "// Updated code",
  "order": 5,
  "is_published": false
}
```

**Example Request:**
```bash
curl -X PUT "https://backend-w3university.vercel.app/api/tutorials/2" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Advanced Variables",
    "order": 3
  }'
```

**Example Response:**
```json
{
  "message": "Tutorial updated successfully",
  "tutorial": {
    "id": 2,
    "language_id": "javascript",
    "title": "Advanced Variables",
    "content": "Learn about var, let, const and different data types.",
    "code_example": "let name = 'John';\nconst age = 25;",
    "order": 3,
    "is_published": true,
    "created_at": "2025-12-10T08:30:00.000000Z",
    "updated_at": "2025-12-10T09:00:00.000000Z"
  }
}
```

### Delete Tutorial

```http
DELETE /api/tutorials/{id}
Authorization: Bearer {token}
```

**Example Request:**
```bash
curl -X DELETE "https://backend-w3university.vercel.app/api/tutorials/2" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "message": "Tutorial deleted successfully"
}
```

### Bulk Delete Tutorials

```http
POST /api/tutorials/bulk-delete
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "ids": [1, 2, 3]
}
```

**Example Response:**
```json
{
  "message": "Tutorials deleted successfully"
}
```

### Bulk Update Published Status

```http
POST /api/tutorials/bulk-update-status
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "ids": [1, 2, 3],
  "is_published": false
}
```

**Example Response:**
```json
{
  "message": "Tutorial status updated successfully"
}
```

## Admin Endpoints (Requires Admin Role)

All admin endpoints use the `/api/admin/tutorials` prefix and require both authentication and admin role.

### Admin - Get All Tutorials (Including Unpublished)

```http
GET /api/admin/tutorials
Authorization: Bearer {admin_token}
```

**Query Parameters:** Same as public endpoint, but shows unpublished by default.

### Admin - Create/Update/Delete Tutorials

Same endpoints as protected routes but accessible via `/api/admin/tutorials` prefix.

## Data Structure

### Tutorial Object

```typescript
interface Tutorial {
  id: number;
  language_id: string;          // e.g., 'javascript', 'python'
  title: string;                // Tutorial title
  content: string;              // Tutorial content/description
  code_example: string | null;  // Code example (optional)
  order: number;                // Display order (lower = first)
  is_published: boolean;        // Publish status
  created_at: string;           // ISO 8601 datetime
  updated_at: string;           // ISO 8601 datetime
}
```

## Frontend Integration (Next.js)

### Create API Helper

```typescript
// lib/tutorialApi.ts
const API_BASE_URL = 'https://backend-w3university.vercel.app/api';

export const tutorialAPI = {
  // Get all tutorials for a language
  async getByLanguage(languageId: string) {
    const response = await fetch(`${API_BASE_URL}/tutorials?language_id=${languageId}`);
    if (!response.ok) throw new Error('Failed to fetch tutorials');
    return response.json();
  },

  // Get single tutorial
  async getById(id: number) {
    const response = await fetch(`${API_BASE_URL}/tutorials/${id}`);
    if (!response.ok) throw new Error('Failed to fetch tutorial');
    return response.json();
  },

  // Admin: Create tutorial
  async create(data: any, token: string) {
    const response = await fetch(`${API_BASE_URL}/tutorials`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });
    if (!response.ok) throw new Error('Failed to create tutorial');
    return response.json();
  },

  // Admin: Update tutorial
  async update(id: number, data: any, token: string) {
    const response = await fetch(`${API_BASE_URL}/tutorials/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });
    if (!response.ok) throw new Error('Failed to update tutorial');
    return response.json();
  },

  // Admin: Delete tutorial
  async delete(id: number, token: string) {
    const response = await fetch(`${API_BASE_URL}/tutorials/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    if (!response.ok) throw new Error('Failed to delete tutorial');
    return response.json();
  },
};
```

### Usage in Components

```tsx
'use client';

import { useEffect, useState } from 'react';
import { tutorialAPI } from '@/lib/tutorialApi';

export default function TutorialPage({ languageId }: { languageId: string }) {
  const [tutorials, setTutorials] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadTutorials() {
      try {
        const data = await tutorialAPI.getByLanguage(languageId);
        setTutorials(data);
      } catch (error) {
        console.error('Error loading tutorials:', error);
      } finally {
        setLoading(false);
      }
    }

    loadTutorials();
  }, [languageId]);

  if (loading) return <div>Loading...</div>;

  return (
    <div>
      {tutorials.map((tutorial) => (
        <div key={tutorial.id}>
          <h3>{tutorial.title}</h3>
          <p>{tutorial.content}</p>
          {tutorial.code_example && (
            <pre><code>{tutorial.code_example}</code></pre>
          )}
        </div>
      ))}
    </div>
  );
}
```

## Error Responses

All endpoints return appropriate HTTP status codes:

- `200 OK` - Successful GET request
- `201 Created` - Successful POST request
- `401 Unauthorized` - Missing or invalid authentication
- `403 Forbidden` - User doesn't have required permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation errors

**Error Response Format:**
```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## Testing

You can test the API using the included test file or tools like Postman/Insomnia:

```bash
# Test getting tutorials
curl https://backend-w3university.vercel.app/api/tutorials

# Test with language filter
curl "https://backend-w3university.vercel.app/api/tutorials?language_id=javascript"
```
