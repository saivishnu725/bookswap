-- a: create database "bookswap"
CREATE DATABASE IF NOT EXISTS bookswap;

-- b: select th db 
USE bookswap;

-- c: create all tables
-- 1. users
CREATE TABLE
    users (
        user_id BIGINT PRIMARY KEY AUTO_INCREMENT,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        date_of_birth DATE,
        college_name VARCHAR(255),
        phone_primary VARCHAR(20) NOT NULL UNIQUE,
        phone_secondary VARCHAR(20),
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

-- 2. books
CREATE TABLE
    books (
        book_id BIGINT PRIMARY KEY AUTO_INCREMENT,
        seller_id BIGINT NOT NULL,
        book_name VARCHAR(255) NOT NULL,
        descr TEXT,
        `condition` ENUM ('NEW', 'GOOD', 'FAIR', 'POOR') NOT NULL,
        year_of_purchase YEAR,
        cost_at_purchase DECIMAL(10, 2),
        current_selling_price DECIMAL(10, 2) NOT NULL,
        negotiation ENUM ('YES', 'NO', 'UNSURE'),
        FOREIGN KEY (seller_id) REFERENCES users (user_id)
    );

-- 3. book images
CREATE TABLE
    IF NOT EXISTS book_images (
        image_id BIGINT PRIMARY KEY AUTO_INCREMENT,
        book_id BIGINT NOT NULL,
        image_url VARCHAR(2048) NOT NULL,
        uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (book_id) REFERENCES books (book_id) ON DELETE CASCADE
    );

-- author stuff
-- 4. all authors
CREATE TABLE
    author (
        author_id BIGINT PRIMARY KEY AUTO_INCREMENT,
        author_name VARCHAR(255) NOT NULL
    );

-- 5. authors of the books
CREATE TABLE
    book_authors (
        book_id BIGINT NOT NULL,
        author_id BIGINT NOT NULL,
        PRIMARY KEY (book_id, author_id),
        FOREIGN KEY (book_id) REFERENCES books (book_id) ON DELETE CASCADE,
        FOREIGN KEY (author_id) REFERENCES author (author_id) ON DELETE CASCADE
    );

-- end author stuff
-- 6. Book interests/requests table
CREATE TABLE
    IF NOT EXISTS book_interests (
        interest_id BIGINT PRIMARY KEY AUTO_INCREMENT,
        book_id BIGINT NOT NULL,
        buyer_id BIGINT NOT NULL,
        seller_id BIGINT NOT NULL,
        status ENUM ('requested', 'approved', 'rejected', 'sold') DEFAULT 'requested',
        interest_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        approved_at DATETIME NULL,
        FOREIGN KEY (book_id) REFERENCES books (book_id) ON DELETE CASCADE,
        FOREIGN KEY (buyer_id) REFERENCES users (user_id) ON DELETE CASCADE,
        FOREIGN KEY (seller_id) REFERENCES users (user_id) ON DELETE CASCADE,
        UNIQUE KEY unique_interest (book_id, buyer_id)
    );

-- 7. Purchases table
CREATE TABLE
    purchases (
        purchase_id BIGINT PRIMARY KEY AUTO_INCREMENT,
        book_id BIGINT NOT NULL,
        buyer_id BIGINT NOT NULL,
        seller_id BIGINT NOT NULL,
        purchase_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        final_price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (book_id) REFERENCES books (book_id),
        FOREIGN KEY (buyer_id) REFERENCES users (user_id),
        FOREIGN KEY (seller_id) REFERENCES users (user_id)
    );

-- 8. Add status column to books table
ALTER TABLE books
ADD COLUMN status ENUM ('available', 'sold') DEFAULT 'available';

-- 9. Add author handling to books (temporary until author system is fully implemented)
ALTER TABLE books
ADD COLUMN author_name VARCHAR(255);

-- Insert sample users
INSERT INTO users (email, password_hash, first_name, last_name, date_of_birth, college_name, phone_primary, phone_secondary) VALUES
('alice.johnson@university.edu', 'hashed_password_1', 'Alice', 'Johnson', '1999-05-15', 'State University', '12345677890', NULL),
('bob.smith@college.edu', 'hashed_password_2', 'Bob', 'Smith', '2000-08-22', 'Tech College', '1234567891', '1234567892'),
('carol.davis@university.edu', 'hashed_password_3', 'Carol', 'Davis', '1998-12-10', 'State University', '1234567893', NULL),
('david.wilson@college.edu', 'hashed_password_4', 'David', 'Wilson', '2001-03-30', 'Tech College', '1234567894', NULL),
('emma.brown@university.edu', 'hashed_password_5', 'Emma', 'Brown', '1999-07-18', 'State University', '1234567895', '1234567896');

-- Insert sample book images with simplified URLs
INSERT INTO book_images (book_id, image_url) VALUES (21, '1.png'), (22, '2.png'), (23, '3.png'), (24, '4.png'), (25, '5.png'), (26, '6.png'), (27, '7.png'), (28, '8.png'), (29, '9.png'), (20, '10.png'); 