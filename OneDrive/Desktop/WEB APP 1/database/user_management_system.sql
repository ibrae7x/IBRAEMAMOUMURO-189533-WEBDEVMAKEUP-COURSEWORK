-- =====================================================
-- User Management System Database
-- DSA Assignment - Ibrae Mamo Umuro (189533)
-- Date: August 17, 2025
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `user_management_system` 
CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Use the database
USE `user_management_system`;

-- =====================================================
-- Table: users
-- Description: Stores all user information for the system
-- =====================================================

CREATE TABLE IF NOT EXISTS `users` (
    `userId` INT(11) NOT NULL AUTO_INCREMENT,
    `Full_Name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone_Number` VARCHAR(20) DEFAULT NULL,
    `User_Name` VARCHAR(50) NOT NULL,
    `Password` VARCHAR(255) NOT NULL,
    `UserType` ENUM('Super_User', 'Administrator', 'Author') NOT NULL,
    `AccessTime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `profile_Image` VARCHAR(255) DEFAULT NULL,
    `Address` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_active` BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (`userId`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `User_Name` (`User_Name`),
    KEY `UserType` (`UserType`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =====================================================
-- Table: articles
-- Description: Stores all articles in the system
-- =====================================================

CREATE TABLE IF NOT EXISTS `articles` (
    `article_id` INT(11) NOT NULL AUTO_INCREMENT,
    `authorId` INT(11) NOT NULL,
    `article_title` VARCHAR(255) NOT NULL,
    `article_full_text` TEXT NOT NULL,
    `article_created_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `article_last_update` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `article_display` ENUM('yes', 'no') DEFAULT 'yes',
    `article_order` INT(11) DEFAULT 0,
    PRIMARY KEY (`article_id`),
    KEY `authorId` (`authorId`),
    KEY `article_display` (`article_display`),
    KEY `article_created_date` (`article_created_date`),
    KEY `article_order` (`article_order`),
    CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`authorId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =====================================================
-- Insert Default Data
-- =====================================================

-- Insert default Super_User
-- Password: admin123 (hashed using PHP password_hash function)
INSERT INTO `users` (
    `Full_Name`, 
    `email`, 
    `phone_Number`, 
    `User_Name`, 
    `Password`, 
    `UserType`, 
    `Address`
) VALUES (
    'Super Administrator',
    'admin@example.com',
    '+1234567890',
    'superadmin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'Super_User',
    '123 Admin Street, Admin City'
);

-- Insert sample Administrator
INSERT INTO `users` (
    `Full_Name`, 
    `email`, 
    `phone_Number`, 
    `User_Name`, 
    `Password`, 
    `UserType`, 
    `Address`
) VALUES (
    'John Administrator',
    'john.admin@example.com',
    '+1234567891',
    'johnadmin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'Administrator',
    '456 Admin Avenue, Admin City'
);

-- Insert sample Authors
INSERT INTO `users` (
    `Full_Name`, 
    `email`, 
    `phone_Number`, 
    `User_Name`, 
    `Password`, 
    `UserType`, 
    `Address`
) VALUES 
(
    'Alice Writer',
    'alice.writer@example.com',
    '+1234567892',
    'alicewriter',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'Author',
    '789 Writer Street, Writer City'
),
(
    'Bob Author',
    'bob.author@example.com',
    '+1234567893',
    'bobauthor',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'Author',
    '321 Author Lane, Author Town'
),
(
    'Carol Blogger',
    'carol.blogger@example.com',
    '+1234567894',
    'carolblogger',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'Author',
    '654 Blogger Boulevard, Blog City'
);

-- Insert sample articles
INSERT INTO `articles` (
    `authorId`, 
    `article_title`, 
    `article_full_text`, 
    `article_display`, 
    `article_order`
) VALUES 
(
    3, -- Alice Writer
    'Welcome to Our User Management System',
    '<h2>Introduction</h2><p>Welcome to our comprehensive user management system! This platform is designed to provide a seamless experience for managing users and content across different organizational levels.</p><h3>Key Features</h3><ul><li>Role-based access control</li><li>Secure authentication</li><li>Article management</li><li>User profile management</li></ul><p>We hope you find this system useful and efficient for your organizational needs.</p>',
    'yes',
    1
),
(
    4, -- Bob Author
    'Getting Started Guide',
    '<h2>How to Get Started</h2><p>This guide will help you navigate through the system and make the most of its features.</p><h3>For Administrators</h3><p>As an administrator, you can manage author accounts and oversee all articles in the system. Use the dashboard to access these features quickly.</p><h3>For Authors</h3><p>Authors can create, edit, and manage their own articles. The article editor supports rich text formatting for better content presentation.</p><h3>Security</h3><p>Always remember to log out when finished and keep your credentials secure.</p>',
    'yes',
    2
),
(
    5, -- Carol Blogger
    'Best Practices for Content Creation',
    '<h2>Content Creation Guidelines</h2><p>Creating engaging and well-structured content is essential for effective communication.</p><h3>Writing Tips</h3><ol><li>Keep your content clear and concise</li><li>Use proper headings and formatting</li><li>Include relevant examples</li><li>Proofread before publishing</li></ol><h3>Technical Considerations</h3><p>When creating technical content, always consider your audience''s level of expertise and provide appropriate explanations for complex topics.</p><p>Remember to use the preview feature before publishing your articles.</p>',
    'yes',
    3
),
(
    3, -- Alice Writer
    'System Security Features',
    '<h2>Security in Our System</h2><p>Security is a top priority in our user management system. Here are the key security features implemented:</p><h3>Authentication</h3><ul><li>Secure password hashing</li><li>Session management</li><li>Automatic logout on inactivity</li></ul><h3>Data Protection</h3><ul><li>SQL injection prevention</li><li>XSS protection</li><li>Input validation and sanitization</li></ul><h3>Access Control</h3><p>Role-based permissions ensure users can only access features appropriate to their role.</p>',
    'yes',
    4
),
(
    4, -- Bob Author
    'Advanced Features Overview',
    '<h2>Advanced System Features</h2><p>Our system includes several advanced features to enhance productivity and user experience.</p><h3>Article Management</h3><p>The article management system supports rich text editing, draft saving, and publication scheduling.</p><h3>User Profiles</h3><p>Users can customize their profiles with personal information and profile images.</p><h3>Reporting</h3><p>Built-in reporting features help administrators track system usage and user activity.</p><p>These features work together to create a comprehensive content management solution.</p>',
    'yes',
    5
),
(
    5, -- Carol Blogger
    'Draft Article: Future Enhancements',
    '<h2>Planned Enhancements</h2><p>This is a draft article discussing potential future enhancements to the system.</p><h3>Upcoming Features</h3><ul><li>Email notifications</li><li>Advanced search functionality</li><li>Export capabilities</li><li>Multi-language support</li></ul><p>These enhancements will be rolled out in future updates based on user feedback and requirements.</p><p><em>Note: This is a draft article and is not yet published.</em></p>',
    'no',
    6
);

-- =====================================================
-- Create Indexes for Better Performance
-- =====================================================

-- Additional indexes for users table
CREATE INDEX `idx_users_usertype_active` ON `users` (`UserType`, `is_active`);
CREATE INDEX `idx_users_created_at` ON `users` (`created_at`);

-- Additional indexes for articles table
CREATE INDEX `idx_articles_author_display` ON `articles` (`authorId`, `article_display`);
CREATE INDEX `idx_articles_display_created` ON `articles` (`article_display`, `article_created_date`);

-- =====================================================
-- Create Views for Common Queries
-- =====================================================

-- View for active users with basic information
CREATE OR REPLACE VIEW `active_users_view` AS
SELECT 
    `userId`,
    `Full_Name`,
    `email`,
    `User_Name`,
    `UserType`,
    `AccessTime`,
    `created_at`
FROM `users`
WHERE `is_active` = 1
ORDER BY `Full_Name`;

-- View for published articles with author information
CREATE OR REPLACE VIEW `published_articles_view` AS
SELECT 
    a.`article_id`,
    a.`article_title`,
    a.`article_full_text`,
    a.`article_created_date`,
    a.`article_last_update`,
    a.`article_order`,
    u.`Full_Name` AS `author_name`,
    u.`User_Name` AS `author_username`
FROM `articles` a
JOIN `users` u ON a.`authorId` = u.`userId`
WHERE a.`article_display` = 'yes' AND u.`is_active` = 1
ORDER BY a.`article_created_date` DESC;

-- View for article statistics
CREATE OR REPLACE VIEW `article_stats_view` AS
SELECT 
    u.`Full_Name` AS `author_name`,
    u.`UserType`,
    COUNT(a.`article_id`) AS `total_articles`,
    SUM(CASE WHEN a.`article_display` = 'yes' THEN 1 ELSE 0 END) AS `published_articles`,
    SUM(CASE WHEN a.`article_display` = 'no' THEN 1 ELSE 0 END) AS `draft_articles`,
    MAX(a.`article_created_date`) AS `latest_article_date`
FROM `users` u
LEFT JOIN `articles` a ON u.`userId` = a.`authorId`
WHERE u.`UserType` = 'Author' AND u.`is_active` = 1
GROUP BY u.`userId`, u.`Full_Name`, u.`UserType`
ORDER BY `total_articles` DESC;

-- =====================================================
-- Insert Activity Log Table (Optional)
-- =====================================================

CREATE TABLE IF NOT EXISTS `activity_log` (
    `log_id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `activity` VARCHAR(255) NOT NULL,
    `details` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `user_id` (`user_id`),
    KEY `created_at` (`created_at`),
    CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =====================================================
-- Default System Settings (Optional)
-- =====================================================

CREATE TABLE IF NOT EXISTS `system_settings` (
    `setting_id` INT(11) NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_id`),
    UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Insert default settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'User Management System', 'The name of the website'),
('session_timeout', '3600', 'Session timeout in seconds'),
('max_file_size', '2097152', 'Maximum file upload size in bytes'),
('allowed_file_types', 'jpg,jpeg,png,gif', 'Allowed file extensions for uploads'),
('articles_per_page', '10', 'Number of articles to display per page'),
('enable_email_notifications', '0', 'Enable email notifications (1=yes, 0=no)'),
('maintenance_mode', '0', 'Enable maintenance mode (1=yes, 0=no)');

-- =====================================================
-- Sample Data Summary
-- =====================================================

/*
DEFAULT CREDENTIALS:
==================
Super User:
- Username: superadmin
- Password: admin123
- Email: admin@example.com

Administrator:
- Username: johnadmin  
- Password: admin123
- Email: john.admin@example.com

Authors:
- Username: alicewriter
- Password: admin123
- Email: alice.writer@example.com

- Username: bobauthor
- Password: admin123  
- Email: bob.author@example.com

- Username: carolblogger
- Password: admin123
- Email: carol.blogger@example.com

SAMPLE ARTICLES:
===============
- 5 Published articles
- 1 Draft article
- Articles cover system introduction, guides, and best practices

DATABASE FEATURES:
=================
- Foreign key constraints
- Indexes for performance
- Views for common queries
- Activity logging capability
- System settings management
- Proper character encoding (UTF-8)
*/

-- =====================================================
-- END OF SQL FILE
-- =====================================================
