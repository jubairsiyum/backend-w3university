# Next.js Frontend Setup Guide

## 🔧 Backend API Configuration

**Backend URL:** `https://backend-w3university.vercel.app/api`  
**Frontend URL:** `https://ekusheycoding.vercel.app`

---

## 📦 Step 1: Install Dependencies

```bash
npm install axios
# or
yarn add axios
```

---

## 🔑 Step 2: Create Environment Variables

Create a `.env.local` file in your Next.js project root:

```env
# API Configuration
NEXT_PUBLIC_API_URL=https://backend-w3university.vercel.app/api

# For local development, use:
# NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

---

## 🛠️ Step 3: Create API Service

Create `lib/api.js` or `utils/api.js`:

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add token to all requests
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Handle response errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
```

---

## 🔐 Step 4: Create Auth Service

Create `lib/auth.js`:

```javascript
import api from './api';

export const authService = {
  // Register new user
  register: async (name, email, password, password_confirmation) => {
    const { data } = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation,
    });
    
    // Store token and user
    localStorage.setItem('auth_token', data.access_token);
    localStorage.setItem('user', JSON.stringify(data.user));
    
    return data;
  },

  // Login user
  login: async (email, password) => {
    const { data } = await api.post('/login', {
      email,
      password,
    });
    
    // Store token and user
    localStorage.setItem('auth_token', data.access_token);
    localStorage.setItem('user', JSON.stringify(data.user));
    
    return data;
  },

  // Logout user
  logout: async () => {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      // Clear local storage
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
    }
  },

  // Get current user
  getCurrentUser: () => {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
  },

  // Check if user is authenticated
  isAuthenticated: () => {
    return !!localStorage.getItem('auth_token');
  },
};
```

---

## 👤 Step 5: Create Profile Service

Create `lib/profile.js`:

```javascript
import api from './api';

export const profileService = {
  // Get user profile
  getProfile: async () => {
    const { data } = await api.get('/profile');
    return data;
  },

  // Update basic info
  updateBasicInfo: async (profileData) => {
    const { data } = await api.put('/profile/basic-info', profileData);
    return data;
  },

  // Update details
  updateDetails: async (details) => {
    const { data } = await api.put('/profile/details', details);
    return data;
  },

  // Upload avatar
  uploadAvatar: async (file) => {
    const formData = new FormData();
    formData.append('avatar', file);
    
    const { data } = await api.post('/profile/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return data;
  },

  // Change password
  changePassword: async (currentPassword, newPassword, newPasswordConfirmation) => {
    const { data } = await api.post('/profile/change-password', {
      current_password: currentPassword,
      new_password: newPassword,
      new_password_confirmation: newPasswordConfirmation,
    });
    return data;
  },

  // Get favorites
  getFavorites: async () => {
    const { data } = await api.get('/profile/favorites');
    return data;
  },

  // Add favorite
  addFavorite: async (favoriteData) => {
    const { data } = await api.post('/profile/favorites', favoriteData);
    return data;
  },

  // Delete favorite
  deleteFavorite: async (id) => {
    const { data } = await api.delete(`/profile/favorites/${id}`);
    return data;
  },

  // Track activity
  trackActivity: async (activityData) => {
    const { data } = await api.post('/profile/activity', activityData);
    return data;
  },

  // Get activity history
  getActivityHistory: async (startDate, endDate) => {
    const { data } = await api.get('/profile/activity/history', {
      params: { start_date: startDate, end_date: endDate },
    });
    return data;
  },

  // Get performance
  getPerformance: async () => {
    const { data } = await api.get('/profile/performance');
    return data;
  },
};
```

---

## 📝 Step 6: Usage Example - Login Page

Create `app/login/page.js`:

```javascript
'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { authService } from '@/lib/auth';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await authService.login(email, password);
      router.push('/profile');
    } catch (err) {
      setError(err.response?.data?.message || 'Login failed');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center">
      <form onSubmit={handleLogin} className="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h1 className="text-2xl font-bold mb-6">Login</h1>
        
        {error && (
          <div className="bg-red-100 text-red-700 p-3 rounded mb-4">
            {error}
          </div>
        )}
        
        <div className="mb-4">
          <label className="block text-gray-700 mb-2">Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="w-full px-4 py-2 border rounded-lg"
            required
          />
        </div>
        
        <div className="mb-6">
          <label className="block text-gray-700 mb-2">Password</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className="w-full px-4 py-2 border rounded-lg"
            required
          />
        </div>
        
        <button
          type="submit"
          disabled={loading}
          className="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
        >
          {loading ? 'Logging in...' : 'Login'}
        </button>
      </form>
    </div>
  );
}
```

---

## 👤 Step 7: Usage Example - Profile Page

Create `app/profile/page.js`:

```javascript
'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { authService } from '@/lib/auth';
import { profileService } from '@/lib/profile';

export default function ProfilePage() {
  const router = useRouter();
  const [profile, setProfile] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!authService.isAuthenticated()) {
      router.push('/login');
      return;
    }

    fetchProfile();
  }, []);

  const fetchProfile = async () => {
    try {
      const data = await profileService.getProfile();
      setProfile(data.data);
    } catch (error) {
      console.error('Error fetching profile:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = async () => {
    await authService.logout();
    router.push('/login');
  };

  if (loading) {
    return <div className="min-h-screen flex items-center justify-center">Loading...</div>;
  }

  return (
    <div className="min-h-screen p-8">
      <div className="max-w-4xl mx-auto">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-3xl font-bold">Profile</h1>
          <button
            onClick={handleLogout}
            className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
          >
            Logout
          </button>
        </div>

        {profile && (
          <div className="bg-white p-6 rounded-lg shadow-md">
            <h2 className="text-xl font-semibold mb-4">User Information</h2>
            <p><strong>Name:</strong> {profile.user.name}</p>
            <p><strong>Email:</strong> {profile.user.email}</p>
            <p><strong>Username:</strong> {profile.profile.username || 'Not set'}</p>
            <p><strong>Bio:</strong> {profile.profile.bio || 'Not set'}</p>
          </div>
        )}
      </div>
    </div>
  );
}
```

---

## 🔒 Step 8: Protected Route Component (Optional)

Create `components/ProtectedRoute.js`:

```javascript
'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { authService } from '@/lib/auth';

export default function ProtectedRoute({ children }) {
  const router = useRouter();

  useEffect(() => {
    if (!authService.isAuthenticated()) {
      router.push('/login');
    }
  }, [router]);

  if (!authService.isAuthenticated()) {
    return null;
  }

  return children;
}
```

---

## ✅ API Endpoints Available

### Public Endpoints:
- `POST /register` - Register new user
- `POST /login` - Login user
- `GET /blogs` - Get all blogs
- `GET /blogs/{slug}` - Get single blog
- `GET /profiles/{userId}` - Get public profile

### Protected Endpoints (requires Bearer token):
- `GET /profile` - Get user profile
- `PUT /profile/basic-info` - Update basic info
- `PUT /profile/details` - Update details
- `POST /profile/avatar` - Upload avatar
- `POST /profile/change-password` - Change password
- `GET /profile/favorites` - Get favorites
- `POST /profile/favorites` - Add favorite
- `POST /logout` - Logout user

---

## 🚀 Testing Your Setup

1. **Start your Next.js app:**
   ```bash
   npm run dev
   ```

2. **Test login:**
   - Go to `/login`
   - Enter credentials
   - Check if token is stored in localStorage
   - Verify redirect to `/profile`

3. **Check API calls:**
   - Open browser DevTools → Network tab
   - Make sure requests have `Authorization: Bearer {token}` header

---

## 🐛 Troubleshooting

### CORS Errors:
- Make sure backend CORS is configured for `https://ekusheycoding.vercel.app`
- Check `config/cors.php` on backend

### 401 Unauthorized:
- Check if token is being sent in Authorization header
- Verify token is stored in localStorage
- Make sure you're logged in

### Token Not Working:
- Clear localStorage and login again
- Check backend logs for errors

---

## 📌 Important Notes

- Tokens are stored in localStorage (consider using httpOnly cookies for production)
- Always use HTTPS in production
- Backend expects `Bearer {token}` format in Authorization header
- Token is hashed on backend using SHA-256
