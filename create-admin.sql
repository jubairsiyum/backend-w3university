-- Create or update an admin user
-- Run this SQL in your database client

-- Option 1: Update an existing user to be an admin
UPDATE users 
SET role = 'admin' 
WHERE email = 'test@w3university.com'  -- Change this to your email
LIMIT 1;

-- Option 2: If the user doesn't exist, insert a new admin user
-- Password is 'admin123' (hashed with bcrypt)
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
VALUES (
    'Admin User',
    'admin@w3university.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5MUa7WYX5EJqm',  -- admin123
    'admin',
    NOW(),
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE role = 'admin';

-- Verify the admin user
SELECT id, name, email, role, created_at 
FROM users 
WHERE role = 'admin';
