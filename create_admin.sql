-- Insert initial admin user
-- Username: admin
-- Email: admin@naaliatan.com
-- Password: admin123
-- Hash: $2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS

INSERT INTO user (username, email, password, roles, created_at, display_name, is_active) 
VALUES (
    'admin', 
    'admin@naaliatan.com', 
    '$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS', 
    '["ROLE_ADMIN"]', 
    NOW(), 
    'Administrator', 
    1
);
