// Example Next.js Auth Context
// Save this as: app/context/AuthContext.js or similar

'use client';

import { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';

const AuthContext = createContext({});

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

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        const { data } = await api.get('/user');
        setUser(data);
      } catch (error) {
        localStorage.removeItem('token');
      }
    }
    setLoading(false);
  };

  const register = async (name, email, password, password_confirmation) => {
    const { data } = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation,
    });
    localStorage.setItem('token', data.access_token);
    setUser(data.user);
    return data;
  };

  const login = async (email, password) => {
    const { data } = await api.post('/login', { email, password });
    localStorage.setItem('token', data.access_token);
    setUser(data.user);
    return data;
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } finally {
      localStorage.removeItem('token');
      setUser(null);
    }
  };

  const value = {
    user,
    loading,
    register,
    login,
    logout,
    isAuthenticated: !!user,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
};

// Usage Example:
// 
// 1. Wrap your app in layout.js:
//    import { AuthProvider } from '@/context/AuthContext';
//    
//    export default function RootLayout({ children }) {
//      return (
//        <html>
//          <body>
//            <AuthProvider>
//              {children}
//            </AuthProvider>
//          </body>
//        </html>
//      );
//    }
//
// 2. Use in any component:
//    'use client';
//    import { useAuth } from '@/context/AuthContext';
//    
//    export default function LoginPage() {
//      const { login, user, isAuthenticated } = useAuth();
//      
//      const handleSubmit = async (e) => {
//        e.preventDefault();
//        try {
//          await login(email, password);
//          router.push('/dashboard');
//        } catch (error) {
//          console.error('Login failed:', error);
//        }
//      };
//      
//      return <form onSubmit={handleSubmit}>...</form>;
//    }
//
// 3. Protected route example:
//    'use client';
//    import { useAuth } from '@/context/AuthContext';
//    import { useRouter } from 'next/navigation';
//    import { useEffect } from 'react';
//    
//    export default function ProtectedPage() {
//      const { user, loading } = useAuth();
//      const router = useRouter();
//      
//      useEffect(() => {
//        if (!loading && !user) {
//          router.push('/login');
//        }
//      }, [user, loading, router]);
//      
//      if (loading) return <div>Loading...</div>;
//      if (!user) return null;
//      
//      return <div>Welcome, {user.name}!</div>;
//    }
