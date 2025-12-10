# Admin Blog Management API Guide

## 🔐 Authentication Required

All admin endpoints require:
- **Authentication:** Bearer token in header
- **Admin Role:** User must have `role = 'admin'`

---

## 📝 API Endpoints

### Base URL
```
https://backend-w3university.vercel.app/api/admin
```

---

## 1️⃣ Create New Blog

**POST** `/admin/blogs`

### Request Body:
```json
{
  "title": "Understanding React Hooks",
  "title_bn": "রিঅ্যাক্ট হুকস বোঝা",
  "content": "<p>Full content in English...</p>",
  "content_bn": "<p>বাংলায় সম্পূর্ণ কন্টেন্ট...</p>",
  "excerpt": "A brief introduction to React Hooks",
  "excerpt_bn": "রিঅ্যাক্ট হুকসের একটি সংক্ষিপ্ত পরিচিতি",
  "category": "React",
  "category_bn": "রিঅ্যাক্ট",
  "tags": ["react", "hooks", "javascript"],
  "tags_bn": ["রিঅ্যাক্ট", "হুকস", "জাভাস্ক্রিপ্ট"],
  "read_time": "5 min read",
  "read_time_bn": "৫ মিনিট পড়া",
  "image_url": "https://example.com/image.jpg",
  "featured_image": "https://example.com/featured.jpg",
  "status": "published",
  "published_at": "2025-12-10T10:00:00Z"
}
```

### Required Fields:
- `title` (string, max 255)
- `title_bn` (string, max 255)
- `content` (string)
- `content_bn` (string)
- `category` (string, max 100)
- `status` (enum: "draft", "published", "archived")

### Optional Fields:
- `excerpt` (string)
- `excerpt_bn` (string)
- `author` (string, max 255) - Auto-set to logged-in user's name
- `author_bn` (string, max 255) - Auto-set to logged-in user's name
- `category_bn` (string, max 100)
- `tags` (array)
- `tags_bn` (array)
- `read_time` (string, max 50)
- `read_time_bn` (string, max 50)
- `image_url` (string, max 500)
- `featured_image` (string, max 500)
- `published_at` (datetime)

### Response:
```json
{
  "message": "Blog created successfully",
  "blog": {
    "id": 1,
    "title": "Understanding React Hooks",
    "slug": "understanding-react-hooks",
    "author_id": 5,
    "status": "published",
    "views": 0,
    "created_at": "2025-12-10T10:00:00.000000Z",
    ...
  }
}
```

---

## 2️⃣ Get All Blogs (Admin View)

**GET** `/admin/blogs`

### Query Parameters:
- `search` - Search in title and content
- `status` - Filter by status: "published", "draft", "archived"
- `category` - Filter by category
- `author_id` - Filter by author ID
- `sort_by` - Column to sort by (default: "created_at")
- `sort_order` - "asc" or "desc" (default: "desc")
- `per_page` - Items per page (default: 15)
- `page` - Page number

### Example Request:
```
GET /admin/blogs?status=published&category=React&per_page=20&page=1
```

### Response:
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Understanding React Hooks",
      "slug": "understanding-react-hooks",
      ...
    }
  ],
  "total": 50,
  "per_page": 20,
  "last_page": 3
}
```

---

## 3️⃣ Get Single Blog

**GET** `/admin/blogs/{id}`

### Response:
```json
{
  "id": 1,
  "title": "Understanding React Hooks",
  "title_bn": "রিঅ্যাক্ট হুকস বোঝা",
  "content": "<p>Full content...</p>",
  "slug": "understanding-react-hooks",
  "status": "published",
  ...
}
```

---

## 4️⃣ Update Blog

**PUT** `/admin/blogs/{id}`

### Request Body (all fields optional):
```json
{
  "title": "Updated Title",
  "status": "published",
  "category": "JavaScript"
}
```

### Response:
```json
{
  "message": "Blog updated successfully",
  "blog": {
    "id": 1,
    "title": "Updated Title",
    ...
  }
}
```

---

## 5️⃣ Delete Blog

**DELETE** `/admin/blogs/{id}`

### Response:
```json
{
  "message": "Blog deleted successfully"
}
```

---

## 6️⃣ Get Dashboard Stats

**GET** `/admin/blogs/stats`

### Response:
```json
{
  "total_blogs": 100,
  "published_blogs": 80,
  "draft_blogs": 15,
  "total_views": 15000,
  "categories": ["React", "JavaScript", "Python", "Laravel"],
  "recent_blogs": [
    {
      "id": 1,
      "title": "Latest Blog",
      ...
    }
  ]
}
```

---

## 7️⃣ Bulk Delete Blogs

**POST** `/admin/blogs/bulk-delete`

### Request Body:
```json
{
  "ids": [1, 2, 3, 4, 5]
}
```

### Response:
```json
{
  "message": "5 blogs deleted successfully",
  "deleted_count": 5
}
```

---

## 8️⃣ Bulk Update Status

**POST** `/admin/blogs/bulk-update-status`

### Request Body:
```json
{
  "ids": [1, 2, 3],
  "status": "published"
}
```

### Response:
```json
{
  "message": "3 blogs updated successfully",
  "updated_count": 3
}
```

---

## 🎨 Frontend Implementation (Next.js)

### Create Blog Service

Create `lib/adminBlog.js`:

```javascript
import api from './api';

export const adminBlogService = {
  // Get all blogs
  getAllBlogs: async (params = {}) => {
    const { data } = await api.get('/admin/blogs', { params });
    return data;
  },

  // Get single blog
  getBlog: async (id) => {
    const { data } = await api.get(`/admin/blogs/${id}`);
    return data;
  },

  // Create new blog
  createBlog: async (blogData) => {
    const { data } = await api.post('/admin/blogs', blogData);
    return data;
  },

  // Update blog
  updateBlog: async (id, blogData) => {
    const { data } = await api.put(`/admin/blogs/${id}`, blogData);
    return data;
  },

  // Delete blog
  deleteBlog: async (id) => {
    const { data } = await api.delete(`/admin/blogs/${id}`);
    return data;
  },

  // Get stats
  getStats: async () => {
    const { data } = await api.get('/admin/blogs/stats');
    return data;
  },

  // Bulk delete
  bulkDelete: async (ids) => {
    const { data } = await api.post('/admin/blogs/bulk-delete', { ids });
    return data;
  },

  // Bulk update status
  bulkUpdateStatus: async (ids, status) => {
    const { data } = await api.post('/admin/blogs/bulk-update-status', { 
      ids, 
      status 
    });
    return data;
  },
};
```

---

### Example: Create Blog Page

Create `app/admin/blogs/new/page.js`:

```javascript
'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { adminBlogService } from '@/lib/adminBlog';

export default function NewBlogPage() {
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  
  const [formData, setFormData] = useState({
    title: '',
    title_bn: '',
    content: '',
    content_bn: '',
    excerpt: '',
    excerpt_bn: '',
    category: '',
    category_bn: '',
    tags: [],
    tags_bn: [],
    read_time: '',
    read_time_bn: '',
    image_url: '',
    featured_image: '',
    status: 'draft',
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleTagsChange = (e, field) => {
    const tags = e.target.value.split(',').map(tag => tag.trim());
    setFormData(prev => ({
      ...prev,
      [field]: tags
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const result = await adminBlogService.createBlog(formData);
      alert('Blog created successfully!');
      router.push('/admin/blogs');
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create blog');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container mx-auto p-6">
      <h1 className="text-3xl font-bold mb-6">Create New Blog</h1>

      {error && (
        <div className="bg-red-100 text-red-700 p-4 rounded mb-4">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="block font-medium mb-2">Title (English)</label>
            <input
              type="text"
              name="title"
              value={formData.title}
              onChange={handleChange}
              className="w-full border rounded px-4 py-2"
              required
            />
          </div>
          
          <div>
            <label className="block font-medium mb-2">Title (Bengali)</label>
            <input
              type="text"
              name="title_bn"
              value={formData.title_bn}
              onChange={handleChange}
              className="w-full border rounded px-4 py-2"
              required
            />
          </div>
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className="block font-medium mb-2">Category (English)</label>
            <input
              type="text"
              name="category"
              value={formData.category}
              onChange={handleChange}
              className="w-full border rounded px-4 py-2"
              required
            />
          </div>
          
          <div>
            <label className="block font-medium mb-2">Category (Bengali)</label>
            <input
              type="text"
              name="category_bn"
              value={formData.category_bn}
              onChange={handleChange}
              className="w-full border rounded px-4 py-2"
            />
          </div>
        </div>

        <div>
          <label className="block font-medium mb-2">Content (English)</label>
          <textarea
            name="content"
            value={formData.content}
            onChange={handleChange}
            className="w-full border rounded px-4 py-2"
            rows="10"
            required
          />
        </div>

        <div>
          <label className="block font-medium mb-2">Content (Bengali)</label>
          <textarea
            name="content_bn"
            value={formData.content_bn}
            onChange={handleChange}
            className="w-full border rounded px-4 py-2"
            rows="10"
            required
          />
        </div>

        <div>
          <label className="block font-medium mb-2">Tags (English, comma-separated)</label>
          <input
            type="text"
            onChange={(e) => handleTagsChange(e, 'tags')}
            className="w-full border rounded px-4 py-2"
            placeholder="react, javascript, web"
          />
        </div>

        <div>
          <label className="block font-medium mb-2">Status</label>
          <select
            name="status"
            value={formData.status}
            onChange={handleChange}
            className="w-full border rounded px-4 py-2"
            required
          >
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="archived">Archived</option>
          </select>
        </div>

        <div className="flex gap-4">
          <button
            type="submit"
            disabled={loading}
            className="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
          >
            {loading ? 'Creating...' : 'Create Blog'}
          </button>
          
          <button
            type="button"
            onClick={() => router.push('/admin/blogs')}
            className="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>
  );
}
```

---

## ✅ Public Blog Endpoints (Frontend Display)

Blogs published via admin will be visible on:

**GET** `/api/blogs` - Get all published blogs
**GET** `/api/blogs/{slug}` - Get single blog by slug
**GET** `/api/blogs/popular` - Get popular blogs
**GET** `/api/blogs/recent` - Get recent blogs
**GET** `/api/blogs/categories` - Get all categories

These endpoints are **public** and don't require authentication.

---

## 🔒 Security Notes

- Only users with `role = 'admin'` can access admin endpoints
- All admin routes require valid Bearer token
- Author ID is automatically set from authenticated user
- Slugs are auto-generated and ensured to be unique

---

## 📌 Important Database Fields

The backend expects these exact field names:
- `title` / `title_bn`
- `content` / `content_bn`
- `excerpt` / `excerpt_bn`
- `author` / `author_bn`
- `category` / `category_bn`
- `tags` / `tags_bn` (JSON arrays)
- `read_time` / `read_time_bn`
- `image_url` / `featured_image`
- `slug` (auto-generated)
- `status` (draft/published/archived)
- `author_id` (auto-set)
- `published_at`
- `views`

---

## 🚀 Ready to Use!

Your admin blog management system is now fully configured and ready to use. After Vercel deploys, you can create blogs from `/admin/blogs/new` and they will appear on `/blog` page! 🎉
