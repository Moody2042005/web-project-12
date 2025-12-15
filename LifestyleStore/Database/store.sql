-- Create database
CREATE DATABASE IF NOT EXISTS store;
USE store;

-- Users table (changed from email to username)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(15),
    city VARCHAR(50),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Items/Products table
CREATE TABLE items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    category VARCHAR(50),
    stock INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart/Orders table
CREATE TABLE users_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    status ENUM('Added to cart', 'Confirmed', 'Shipped', 'Delivered') DEFAULT 'Added to cart',
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- Insert sample users
INSERT INTO users (username, password, name, contact, city, address) VALUES
('john_doe', MD5(MD5('password123')), 'John Doe', '1234567890', 'New York', '123 Main St'),
('jane_smith', MD5(MD5('securepass')), 'Jane Smith', '0987654321', 'Los Angeles', '456 Oak Ave');

-- Insert sample products
INSERT INTO items (name, price, image, description, category) VALUES
('Samsung Galaxy S23', 69999.00, 'phone1.jpg', 'Latest Samsung smartphone with AMOLED display', 'Mobile'),
('Apple iPhone 15', 89999.00, 'phone2.jpg', 'Apple latest iPhone with A16 Bionic chip', 'Mobile'),
('Nikon DSLR Camera', 54999.00, 'camera1.jpg', 'Professional DSLR camera with 24MP sensor', 'Camera'),
('Canon EOS R5', 129999.00, 'camera2.jpg', 'Mirrorless camera with 8K video recording', 'Camera'),
('Rolex Submariner', 599999.00, 'watch1.jpg', 'Luxury diving watch', 'Watch'),
('Casio G-Shock', 8999.00, 'watch2.jpg', 'Rugged sports watch', 'Watch'),
('Leather Jacket', 7999.00, 'shirt1.jpg', 'Premium leather jacket', 'Clothing'),
('Cotton T-Shirt', 999.00, 'shirt2.jpg', 'Comfortable cotton t-shirt', 'Clothing');