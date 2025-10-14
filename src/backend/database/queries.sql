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
        trust_score INT DEFAULT 5,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

-- 2. books
CREATE TABLE
    books (
        book_id BIGINT PRIMARY KEY AUTO_INCREMENT,
        seller_id BIGINT NOT NULL,
        book_name VARCHAR(255) NOT NULL,
        description TEXT,
        `condition` ENUM ('NEW', 'GOOD', 'FAIR') NOT NULL,
        year_of_purchase YEAR,
        cost_at_purchase DECIMAL(10, 2),
        current_selling_price DECIMAL(10, 2) NOT NULL,
        negotiation ENUM ('YES', 'NO', 'UNSURE'),
        FOREIGN KEY (seller_id) REFERENCES users (user_id)
    );

-- 3. book images
CREATE TABLE
    book_images (
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