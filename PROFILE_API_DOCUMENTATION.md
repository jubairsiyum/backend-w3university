# User Profile API Documentation

Complete API documentation for user profile management, favorites, activity tracking, and performance analytics.

## Base URL
```
https://your-domain.com/api
```

## Authentication
All profile endpoints require authentication using Laravel Sanctum tokens. Include the token in the Authorization header:
```
Authorization: Bearer {your-token}
```

---

## 📋 Profile Endpoints

### 1. Get Complete Profile
Get authenticated user's complete profile including all relationships and stats.

**Endpoint:** `GET /api/profile`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "last_login_at": "2024-12-10T10:30:00.000000Z"
    },
    "profile": {
      "id": 1,
      "user_id": 1,
      "username": "johndoe",
      "phone": "+1234567890",
      "bio": "Full-stack developer passionate about web technologies",
      "avatar": "avatars/user1.jpg",
      "github_url": "https://github.com/johndoe",
      "linkedin_url": "https://linkedin.com/in/johndoe",
      "twitter_url": "https://twitter.com/johndoe",
      "portfolio_url": "https://johndoe.com",
      "location": "New York, USA",
      "timezone": "America/New_York",
      "date_of_birth": "1995-06-15",
      "skill_level": "advanced",
      "programming_languages": ["JavaScript", "Python", "PHP"],
      "interests": ["Web Development", "Machine Learning"],
      "daily_goal_minutes": 60,
      "email_notifications": true,
      "is_public": true
    },
    "performance": {
      "total_courses_completed": 12,
      "total_lessons_completed": 145,
      "total_exercises_completed": 89,
      "total_quizzes_completed": 34,
      "total_certificates_earned": 5,
      "total_hours_learned": 87.5,
      "current_streak": 15,
      "longest_streak": 30,
      "total_points": 4250,
      "experience_level": 8,
      "badges": ["Fast Learner", "Quiz Master", "30 Day Streak"]
    },
    "favorites": [],
    "recent_activities": [],
    "today_activity": null,
    "stats": {
      "total_favorites": 0,
      "active_days_last_30": 25,
      "total_active_minutes_last_30": 1450
    }
  }
}
```

---

### 2. Update Basic Information
Update user's name and email.

**Endpoint:** `PUT /api/profile/basic-info`

**Request Body:**
```json
{
  "name": "John Smith",
  "email": "john.smith@example.com"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Basic information updated successfully",
  "data": {
    "id": 1,
    "name": "John Smith",
    "email": "john.smith@example.com",
    "role": "user",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

**Validation Errors (422):**
```json
{
  "success": false,
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 3. Update Profile Details
Update extended profile information.

**Endpoint:** `PUT /api/profile/details`

**Request Body:**
```json
{
  "username": "john_doe_dev",
  "phone": "+1987654321",
  "bio": "Senior Full-stack Developer | Open Source Contributor",
  "github_url": "https://github.com/johndoe",
  "linkedin_url": "https://linkedin.com/in/johndoe",
  "twitter_url": "https://twitter.com/johndoe",
  "portfolio_url": "https://johndoe.dev",
  "location": "San Francisco, CA",
  "timezone": "America/Los_Angeles",
  "date_of_birth": "1995-06-15",
  "skill_level": "expert",
  "programming_languages": ["JavaScript", "TypeScript", "Python", "Go"],
  "interests": ["Web Development", "DevOps", "Cloud Architecture"],
  "daily_goal_minutes": 90,
  "email_notifications": true,
  "is_public": true
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "username": "john_doe_dev",
    "phone": "+1987654321",
    "bio": "Senior Full-stack Developer | Open Source Contributor",
    "skill_level": "expert",
    "programming_languages": ["JavaScript", "TypeScript", "Python", "Go"],
    "interests": ["Web Development", "DevOps", "Cloud Architecture"],
    "daily_goal_minutes": 90
  }
}
```

**Validation Rules:**
- `username`: unique, max 255 characters
- `phone`: max 20 characters
- `bio`: max 1000 characters
- `skill_level`: enum (beginner, intermediate, advanced, expert)
- `programming_languages`: array
- `interests`: array
- `daily_goal_minutes`: integer, 0-1440
- URLs must be valid
- `email_notifications`, `is_public`: boolean

---

### 4. Upload Avatar
Upload profile avatar image.

**Endpoint:** `POST /api/profile/avatar`

**Request (multipart/form-data):**
```
avatar: [file] (JPEG, PNG, JPG, GIF, max 2MB)
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Avatar uploaded successfully",
  "data": {
    "avatar_url": "/storage/avatars/xyz123.jpg",
    "avatar_path": "avatars/xyz123.jpg"
  }
}
```

**Validation Errors (422):**
```json
{
  "success": false,
  "errors": {
    "avatar": ["The avatar must be an image.", "The avatar must not be greater than 2048 kilobytes."]
  }
}
```

---

### 5. Change Password
Change user's password.

**Endpoint:** `POST /api/profile/change-password`

**Request Body:**
```json
{
  "current_password": "oldPassword123",
  "new_password": "newPassword456",
  "new_password_confirmation": "newPassword456"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

**Error - Incorrect Current Password (401):**
```json
{
  "success": false,
  "message": "Current password is incorrect"
}
```

**Validation Rules:**
- `current_password`: required
- `new_password`: min 8 characters, must be confirmed

---

## ⭐ Favorites Endpoints

### 6. Get Favorites
Get all user's favorites, optionally filtered by type.

**Endpoint:** `GET /api/profile/favorites?type=course`

**Query Parameters:**
- `type` (optional): course, tutorial, blog, tool, resource

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "type": "course",
      "title": "Advanced React Patterns",
      "description": "Master advanced React concepts",
      "url": "https://example.com/courses/react",
      "category": "Frontend",
      "tags": ["React", "JavaScript", "Hooks"],
      "order": 1,
      "created_at": "2024-12-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "user_id": 1,
      "type": "tutorial",
      "title": "Docker Compose Guide",
      "description": "Complete Docker Compose tutorial",
      "url": "https://example.com/tutorials/docker",
      "category": "DevOps",
      "tags": ["Docker", "DevOps"],
      "order": 2,
      "created_at": "2024-12-02T00:00:00.000000Z"
    }
  ]
}
```

---

### 7. Add Favorite
Add new favorite item.

**Endpoint:** `POST /api/profile/favorites`

**Request Body:**
```json
{
  "type": "course",
  "title": "Node.js Masterclass",
  "description": "Complete Node.js course from beginner to advanced",
  "url": "https://example.com/courses/nodejs",
  "category": "Backend",
  "tags": ["Node.js", "JavaScript", "Express"],
  "order": 3
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Favorite added successfully",
  "data": {
    "id": 3,
    "user_id": 1,
    "type": "course",
    "title": "Node.js Masterclass",
    "description": "Complete Node.js course from beginner to advanced",
    "url": "https://example.com/courses/nodejs",
    "category": "Backend",
    "tags": ["Node.js", "JavaScript", "Express"],
    "order": 3,
    "created_at": "2024-12-10T00:00:00.000000Z"
  }
}
```

**Validation Rules:**
- `type`: required, enum (course, tutorial, blog, tool, resource)
- `title`: required, max 255 characters
- `url`: must be valid URL
- `tags`: array

---

### 8. Update Favorite
Update existing favorite.

**Endpoint:** `PUT /api/profile/favorites/{id}`

**Request Body:**
```json
{
  "title": "Advanced Node.js Masterclass",
  "description": "Updated description",
  "order": 1
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Favorite updated successfully",
  "data": {
    "id": 3,
    "title": "Advanced Node.js Masterclass",
    "description": "Updated description",
    "order": 1
  }
}
```

---

### 9. Delete Favorite
Delete a favorite item.

**Endpoint:** `DELETE /api/profile/favorites/{id}`

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Favorite deleted successfully"
}
```

---

## 📊 Activity Tracking Endpoints

### 10. Track Activity
Record daily activity (creates or updates today's activity).

**Endpoint:** `POST /api/profile/activity`

**Request Body:**
```json
{
  "minutes_active": 45,
  "lessons_completed": 3,
  "exercises_completed": 5,
  "quizzes_completed": 2,
  "blogs_read": 1,
  "comments_posted": 2,
  "code_snippets_created": 4
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Activity tracked successfully",
  "data": {
    "id": 25,
    "user_id": 1,
    "activity_date": "2024-12-10",
    "minutes_active": 45,
    "lessons_completed": 3,
    "exercises_completed": 5,
    "quizzes_completed": 2,
    "blogs_read": 1,
    "comments_posted": 2,
    "code_snippets_created": 4,
    "streak_days": 15,
    "created_at": "2024-12-10T10:00:00.000000Z"
  }
}
```

**Note:** This also automatically updates performance stats and streak calculations.

---

### 11. Get Activity History
Get activity history for specified number of days.

**Endpoint:** `GET /api/profile/activity/history?days=30`

**Query Parameters:**
- `days` (optional, default: 30): Number of days to retrieve

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "activities": [
      {
        "id": 25,
        "user_id": 1,
        "activity_date": "2024-12-10",
        "minutes_active": 45,
        "lessons_completed": 3,
        "exercises_completed": 5,
        "quizzes_completed": 2,
        "blogs_read": 1,
        "comments_posted": 2,
        "code_snippets_created": 4,
        "streak_days": 15
      },
      {
        "id": 24,
        "user_id": 1,
        "activity_date": "2024-12-09",
        "minutes_active": 60,
        "lessons_completed": 4,
        "exercises_completed": 6,
        "quizzes_completed": 1,
        "blogs_read": 2,
        "comments_posted": 3,
        "code_snippets_created": 5,
        "streak_days": 14
      }
    ],
    "summary": {
      "total_minutes": 1450,
      "total_lessons": 78,
      "total_exercises": 112,
      "total_quizzes": 34,
      "active_days": 25,
      "average_daily_minutes": 58
    }
  }
}
```

---

## 🏆 Performance Endpoints

### 12. Get Performance Stats
Get comprehensive performance statistics.

**Endpoint:** `GET /api/profile/performance`

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "total_courses_completed": 12,
    "total_lessons_completed": 145,
    "total_exercises_completed": 89,
    "total_quizzes_completed": 34,
    "average_quiz_score": 87.5,
    "total_certificates_earned": 5,
    "total_hours_learned": 87.5,
    "current_streak": 15,
    "longest_streak": 30,
    "total_points": 4250,
    "experience_level": 8,
    "badges": ["Fast Learner", "Quiz Master", "30 Day Streak"],
    "skills_completed": ["JavaScript Basics", "React Fundamentals", "Node.js"],
    "last_active_date": "2024-12-10",
    "joined_date": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

### 13. Award Badge
Award a badge to the user (typically called by system events).

**Endpoint:** `POST /api/profile/badge`

**Request Body:**
```json
{
  "badge": "Week Warrior"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Badge awarded successfully",
  "data": {
    "badges": ["Fast Learner", "Quiz Master", "30 Day Streak", "Week Warrior"]
  }
}
```

---

## 👥 Public Profile

### 14. View Public Profile
View another user's public profile (no authentication required for public profiles).

**Endpoint:** `GET /api/profiles/{userId}`

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 2,
      "name": "Jane Smith",
      "created_at": "2024-02-01T00:00:00.000000Z"
    },
    "profile": {
      "username": "janesmith",
      "bio": "Frontend developer and UI/UX enthusiast",
      "avatar": "avatars/user2.jpg",
      "location": "London, UK",
      "skill_level": "advanced",
      "programming_languages": ["JavaScript", "TypeScript", "CSS"],
      "github_url": "https://github.com/janesmith",
      "linkedin_url": "https://linkedin.com/in/janesmith",
      "twitter_url": "https://twitter.com/janesmith",
      "portfolio_url": "https://janesmith.dev"
    },
    "performance": {
      "total_courses_completed": 8,
      "total_certificates_earned": 3,
      "current_streak": 7,
      "experience_level": 6,
      "badges": ["Quick Start", "Consistent Learner"]
    }
  }
}
```

**Error - Private Profile (403):**
```json
{
  "success": false,
  "message": "This profile is private"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)
```json
{
  "message": "Resource not found"
}
```

---

## Database Schema

### user_profiles Table
- `id`: Primary key
- `user_id`: Foreign key to users table (cascade delete)
- `username`: Unique username
- `phone`: Phone number
- `bio`: User biography
- `avatar`: Avatar file path
- Social URLs: `github_url`, `linkedin_url`, `twitter_url`, `portfolio_url`
- `location`: User location
- `timezone`: User timezone
- `date_of_birth`: Date of birth
- `skill_level`: Enum (beginner, intermediate, advanced, expert)
- `programming_languages`: JSON array
- `interests`: JSON array
- `daily_goal_minutes`: Daily learning goal
- `email_notifications`: Boolean
- `is_public`: Boolean (profile visibility)

### user_favorites Table
- `id`: Primary key
- `user_id`: Foreign key (cascade delete)
- `type`: Enum (course, tutorial, blog, tool, resource)
- `title`: Favorite title
- `description`: Description
- `url`: Resource URL
- `category`: Category
- `tags`: JSON array
- `order`: Display order

### user_activities Table
- `id`: Primary key
- `user_id`: Foreign key (cascade delete)
- `activity_date`: Date (unique with user_id)
- `minutes_active`: Minutes active
- Completion counters: `lessons_completed`, `exercises_completed`, `quizzes_completed`
- Engagement: `blogs_read`, `comments_posted`, `code_snippets_created`
- `streak_days`: Current streak

### user_performance Table
- `id`: Primary key
- `user_id`: Foreign key (cascade delete)
- Totals: `total_courses_completed`, `total_lessons_completed`, etc.
- `average_quiz_score`: Average quiz score
- `total_hours_learned`: Total hours learned
- Streaks: `current_streak`, `longest_streak`
- `total_points`: Total points earned
- `experience_level`: Calculated level
- `badges`: JSON array
- `skills_completed`: JSON array
- `last_active_date`: Last activity date
- `joined_date`: Account creation date

---

## Usage Examples

### Frontend Integration (Next.js)

```javascript
// Get user profile
const getProfile = async () => {
  const token = localStorage.getItem('token');
  const response = await fetch('/api/profile', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
};

// Update profile
const updateProfile = async (data) => {
  const token = localStorage.getItem('token');
  const response = await fetch('/api/profile/details', {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  });
  return await response.json();
};

// Track daily activity
const trackActivity = async (activityData) => {
  const token = localStorage.getItem('token');
  const response = await fetch('/api/profile/activity', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(activityData)
  });
  return await response.json();
};

// Upload avatar
const uploadAvatar = async (file) => {
  const token = localStorage.getItem('token');
  const formData = new FormData();
  formData.append('avatar', file);
  
  const response = await fetch('/api/profile/avatar', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`
    },
    body: formData
  });
  return await response.json();
};
```

---

## Notes

1. **Authentication**: All endpoints (except public profile) require Sanctum token authentication
2. **Automatic Updates**: Activity tracking automatically updates performance stats and streaks
3. **Cascading Deletes**: Deleting a user cascades to all related profile data
4. **File Storage**: Avatars stored in `storage/app/public/avatars` directory
5. **Validation**: All endpoints include comprehensive validation
6. **Rate Limiting**: Consider implementing rate limiting for API endpoints
7. **Caching**: Performance data can be cached for better performance

---

## Migration & Setup

1. Run migrations:
```bash
php artisan migrate
```

2. Configure storage link:
```bash
php artisan storage:link
```

3. Set up CORS in `config/cors.php` for frontend access

4. Configure `.env` file with database and file storage settings

