# Blog API Documentation

## Base URL
```
http://localhost:8000/api
```

---

## 📖 Blog Endpoints

### 1. Get All Blogs (Public)
**Endpoint:** `GET /api/blogs`

**Query Parameters:**
- `status` - Filter by status: `published`, `draft`, `archived` (default: `published`)
- `category` - Filter by category name
- `search` - Search in title (English & Bengali)
- `sort_by` - Sort field (default: `published_at`)
- `sort_order` - Sort order: `asc` or `desc` (default: `desc`)
- `per_page` - Items per page (default: `10`)
- `page` - Page number

**Example Request:**
```
GET /api/blogs?category=Web Development&per_page=5&page=1
```

**Success Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Introduction to Laravel",
      "title_bn": "লারাভেল পরিচিতি",
      "excerpt": "Learn the basics of Laravel framework...",
      "excerpt_bn": "লারাভেল ফ্রেমওয়ার্কের মূল বিষয়গুলি...",
      "content": "Laravel is a web application framework...",
      "content_bn": "লারাভেল একটি ওয়েব অ্যাপ্লিকেশন...",
      "author": "John Doe",
      "author_bn": "জন ডো",
      "category": "Web Development",
      "category_bn": "ওয়েব ডেভেলপমেন্ট",
      "tags": ["Laravel", "PHP", "Framework"],
      "tags_bn": ["লারাভেল", "পিএইচপি", "ফ্রেমওয়ার্ক"],
      "read_time": "8 min read",
      "read_time_bn": "৮ মিনিট পড়ুন",
      "image_url": "https://example.com/image.jpg",
      "slug": "introduction-to-laravel",
      "status": "published",
      "views": 1234,
      "published_at": "2025-11-22T10:00:00.000000Z",
      "created_at": "2025-11-20T10:00:00.000000Z",
      "updated_at": "2025-11-22T10:00:00.000000Z"
    }
  ],
  "first_page_url": "http://localhost:8000/api/blogs?page=1",
  "from": 1,
  "last_page": 2,
  "last_page_url": "http://localhost:8000/api/blogs?page=2",
  "next_page_url": "http://localhost:8000/api/blogs?page=2",
  "path": "http://localhost:8000/api/blogs",
  "per_page": 5,
  "prev_page_url": null,
  "to": 5,
  "total": 10
}
```

---

### 2. Get Single Blog (Public)
**Endpoint:** `GET /api/blogs/{slug}`

**Example Request:**
```
GET /api/blogs/introduction-to-laravel
```

**Success Response (200):**
```json
{
  "id": 1,
  "title": "Introduction to Laravel",
  "title_bn": "লারাভেল পরিচিতি",
  "excerpt": "Learn the basics of Laravel framework...",
  "excerpt_bn": "লারাভেল ফ্রেমওয়ার্কের মূল বিষয়গুলি...",
  "content": "Full article content here...",
  "content_bn": "সম্পূর্ণ নিবন্ধ এখানে...",
  "author": "John Doe",
  "author_bn": "জন ডো",
  "category": "Web Development",
  "category_bn": "ওয়েব ডেভেলপমেন্ট",
  "tags": ["Laravel", "PHP", "Framework"],
  "tags_bn": ["লারাভেল", "পিএইচপি", "ফ্রেমওয়ার্ক"],
  "read_time": "8 min read",
  "read_time_bn": "৮ মিনিট পড়ুন",
  "image_url": "https://example.com/image.jpg",
  "slug": "introduction-to-laravel",
  "status": "published",
  "views": 1235,
  "published_at": "2025-11-22T10:00:00.000000Z",
  "created_at": "2025-11-20T10:00:00.000000Z",
  "updated_at": "2025-11-27T01:30:00.000000Z"
}
```

**Note:** This endpoint automatically increments the `views` count.

---

### 3. Get Blog Categories (Public)
**Endpoint:** `GET /api/blogs/categories`

**Success Response (200):**
```json
[
  {
    "category": "Web Development",
    "category_bn": "ওয়েব ডেভেলপমেন্ট"
  },
  {
    "category": "Frontend Development",
    "category_bn": "ফ্রন্টএন্ড ডেভেলপমেন্ট"
  },
  {
    "category": "Database",
    "category_bn": "ডাটাবেস"
  }
]
```

---

### 4. Get Popular Blogs (Public)
**Endpoint:** `GET /api/blogs/popular`

**Query Parameters:**
- `limit` - Number of blogs to return (default: `5`)

**Example Request:**
```
GET /api/blogs/popular?limit=3
```

**Success Response (200):**
```json
[
  {
    "id": 2,
    "title": "Getting Started with React",
    "title_bn": "রিঅ্যাক্ট দিয়ে শুরু করা",
    "views": 2156,
    ...
  },
  {
    "id": 5,
    "title": "Modern CSS Techniques",
    "title_bn": "আধুনিক CSS কৌশল",
    "views": 1823,
    ...
  }
]
```

---

### 5. Get Recent Blogs (Public)
**Endpoint:** `GET /api/blogs/recent`

**Query Parameters:**
- `limit` - Number of blogs to return (default: `5`)

**Example Request:**
```
GET /api/blogs/recent?limit=3
```

**Success Response (200):**
```json
[
  {
    "id": 5,
    "title": "Modern CSS Techniques",
    "published_at": "2025-11-26T10:00:00.000000Z",
    ...
  },
  {
    "id": 4,
    "title": "Understanding REST APIs",
    "published_at": "2025-11-25T10:00:00.000000Z",
    ...
  }
]
```

---

### 6. Create Blog (Protected - Requires Authentication)
**Endpoint:** `POST /api/blogs`

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "title": "My New Blog Post",
  "title_bn": "আমার নতুন ব্লগ পোস্ট",
  "excerpt": "This is a short description",
  "excerpt_bn": "এটি একটি সংক্ষিপ্ত বিবরণ",
  "content": "Full blog content goes here...",
  "content_bn": "সম্পূর্ণ ব্লগ বিষয়বস্তু এখানে যায়...",
  "author": "Jane Doe",
  "author_bn": "জেন ডো",
  "category": "Tutorial",
  "category_bn": "টিউটোরিয়াল",
  "tags": ["Laravel", "Backend"],
  "tags_bn": ["লারাভেল", "ব্যাকএন্ড"],
  "read_time": "5 min read",
  "read_time_bn": "৫ মিনিট পড়ুন",
  "image_url": "https://example.com/my-image.jpg",
  "slug": "my-new-blog-post",
  "status": "published"
}
```

**Validation Rules:**
- `title` - required, string, max:255
- `title_bn` - required, string, max:255
- `excerpt` - required, string
- `excerpt_bn` - required, string
- `content` - required, string
- `content_bn` - required, string
- `author` - required, string, max:255
- `author_bn` - required, string, max:255
- `category` - required, string, max:255
- `category_bn` - required, string, max:255
- `tags` - nullable, array
- `tags_bn` - nullable, array
- `read_time` - nullable, string, max:50
- `read_time_bn` - nullable, string, max:50
- `image_url` - nullable, url
- `slug` - nullable, string, unique (auto-generated if not provided)
- `status` - nullable, enum: `draft`, `published`, `archived`
- `published_at` - nullable, date (auto-set if status is published)

**Success Response (201):**
```json
{
  "message": "Blog created successfully",
  "blog": {
    "id": 7,
    "title": "My New Blog Post",
    "slug": "my-new-blog-post",
    "status": "published",
    ...
  }
}
```

---

### 7. Update Blog (Protected - Requires Authentication)
**Endpoint:** `PUT /api/blogs/{slug}`

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: application/json
```

**Request Body:** (all fields are optional - send only what you want to update)
```json
{
  "title": "Updated Blog Title",
  "title_bn": "আপডেট করা ব্লগ শিরোনাম",
  "status": "published"
}
```

**Success Response (200):**
```json
{
  "message": "Blog updated successfully",
  "blog": {
    "id": 7,
    "title": "Updated Blog Title",
    ...
  }
}
```

---

### 8. Delete Blog (Protected - Requires Authentication)
**Endpoint:** `DELETE /api/blogs/{slug}`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Success Response (200):**
```json
{
  "message": "Blog deleted successfully"
}
```

---

## 🔍 Usage Examples

### Fetching Blogs in Next.js

```javascript
// Get all published blogs
const fetchBlogs = async (page = 1, perPage = 10) => {
  const response = await fetch(
    `http://localhost:8000/api/blogs?page=${page}&per_page=${perPage}`
  );
  const data = await response.json();
  return data;
};

// Get blogs by category
const fetchBlogsByCategory = async (category) => {
  const response = await fetch(
    `http://localhost:8000/api/blogs?category=${encodeURIComponent(category)}`
  );
  const data = await response.json();
  return data;
};

// Get single blog
const fetchBlog = async (slug) => {
  const response = await fetch(`http://localhost:8000/api/blogs/${slug}`);
  const data = await response.json();
  return data;
};

// Get popular blogs
const fetchPopularBlogs = async (limit = 5) => {
  const response = await fetch(
    `http://localhost:8000/api/blogs/popular?limit=${limit}`
  );
  const data = await response.json();
  return data;
};

// Create blog (authenticated)
const createBlog = async (blogData, token) => {
  const response = await fetch('http://localhost:8000/api/blogs', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`,
    },
    body: JSON.stringify(blogData),
  });
  const data = await response.json();
  return data;
};
```

---

## 📊 Database Schema

```sql
CREATE TABLE `blogs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_bn` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `excerpt_bn` text NOT NULL,
  `content` longtext NOT NULL,
  `content_bn` longtext NOT NULL,
  `author` varchar(255) NOT NULL,
  `author_bn` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `category_bn` varchar(255) NOT NULL,
  `tags` json DEFAULT NULL,
  `tags_bn` json DEFAULT NULL,
  `read_time` varchar(255) DEFAULT NULL,
  `read_time_bn` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `views` bigint unsigned DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogs_slug_unique` (`slug`),
  KEY `blogs_slug_index` (`slug`),
  KEY `blogs_status_index` (`status`),
  KEY `blogs_category_index` (`category`),
  KEY `blogs_published_at_index` (`published_at`)
);
```

---

## 🎯 Features

1. **Bilingual Support** - Full English and Bengali content
2. **SEO-Friendly** - Unique slugs for URLs
3. **Auto-Generated Slugs** - Automatically created from title
4. **View Tracking** - Automatic view count increment
5. **Status Management** - Draft, Published, Archived
6. **Categorization** - Category filtering and listing
7. **Tagging System** - JSON-based tags in both languages
8. **Pagination** - Built-in pagination support
9. **Search** - Search across English and Bengali titles
10. **Sorting** - Flexible sorting options
11. **Popular & Recent** - Dedicated endpoints for trending content
12. **Protected Routes** - Authentication required for create/update/delete

---

## 🔐 Authentication

Blog creation, updating, and deletion require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your_access_token}
```

Get the token from `/api/login` or `/api/register` endpoints.

---

## 📝 Sample Data

The database has been seeded with 6 sample blog posts (5 published, 1 draft) to help you test the API.
