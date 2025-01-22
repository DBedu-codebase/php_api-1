CREATE TABLE user (
    user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    username VARCHAR(255) NOT NULL UNIQUE COMMENT 'Username',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email Address',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Password Hash',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Time'
) COMMENT='Table to store user information';

CREATE TABLE blog (
    post_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    title VARCHAR(255) NOT NULL COMMENT 'Post Title',
    content TEXT NOT NULL COMMENT 'Post Content',
    author_id INT NOT NULL COMMENT 'Foreign Key to Users Table',
    published_at DATETIME COMMENT 'Publication Time',
    FOREIGN KEY (author_id) REFERENCES user(user_id) ON DELETE CASCADE
) COMMENT='Table to store blog posts with reference to authors';
