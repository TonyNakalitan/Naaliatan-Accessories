<?php

// Database connection
$host = 'localhost';
$dbname = 'NAcessoriesDB';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Read and execute SQL
    $sql = file_get_contents('insert_admin.sql');
    $pdo->exec($sql);
    
    echo "✅ Admin user created successfully!\n";
    echo "📋 Login Details:\n";
    echo "   Username: admin\n";
    echo "   Password: admin123\n";
    echo "   Email: admin@naaliatan.com\n";
    echo "🔐 Hash: $2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
