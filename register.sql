-- Online Examination System Database Schema

CREATE DATABASE IF NOT EXISTS `database1`;
USE `database1`;

-- Student Registration Table
CREATE TABLE IF NOT EXISTS `register` (
    `fname` VARCHAR(50) NOT NULL,
    `studentid` VARCHAR(50) NOT NULL PRIMARY KEY,
    `email` VARCHAR(100) NOT NULL,
    `branch` VARCHAR(50) DEFAULT NULL,
    `year` VARCHAR(50) DEFAULT NULL,
    `dob` VARCHAR(50) DEFAULT NULL,
    `gender` VARCHAR(10) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `password` VARCHAR(100) NOT NULL,
    `cpassword` VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin/Teacher Registration Table
CREATE TABLE IF NOT EXISTS `admin_register` (
    `teacherid` VARCHAR(50) NOT NULL PRIMARY KEY,
    `fname` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `branch` VARCHAR(50) DEFAULT NULL,
    `dob` VARCHAR(50) DEFAULT NULL,
    `gender` VARCHAR(10) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `password` VARCHAR(100) NOT NULL,
    `cpassword` VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Quiz Title / Subject Table
CREATE TABLE IF NOT EXISTS `quiz_title` (
    `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `teacherid` VARCHAR(200) NOT NULL,
    `title_name` VARCHAR(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Quiz Questions Table
CREATE TABLE IF NOT EXISTS `quiz_questions` (
    `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `teacherid` VARCHAR(255) NOT NULL,
    `title_name` VARCHAR(255) NOT NULL,
    `question_number` INT(11) NOT NULL,
    `question` TEXT NOT NULL,
    `option1` TEXT NOT NULL,
    `option2` TEXT NOT NULL,
    `option3` TEXT NOT NULL,
    `option4` TEXT NOT NULL,
    `correct_option` VARCHAR(200) NOT NULL,
    `marks` INT(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Selected / Assigned Students Table
CREATE TABLE IF NOT EXISTS `selected_student` (
    `sid` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `teacherid` VARCHAR(200) NOT NULL,
    `title_name` VARCHAR(200) NOT NULL,
    `studentid` VARCHAR(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Student Submitted Scores Table
CREATE TABLE IF NOT EXISTS `submit_score` (
    `sid` INT(100) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `studentid` VARCHAR(200) NOT NULL,
    `teacherid` VARCHAR(200) NOT NULL,
    `title_name` VARCHAR(200) NOT NULL,
    `marks` INT(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Demo Records for immediate testing:
-- Faculty Login: Teacher ID = T101, Password = password123
INSERT IGNORE INTO `admin_register` (`teacherid`, `fname`, `email`, `branch`, `dob`, `gender`, `phone`, `password`, `cpassword`)
VALUES ('T101', 'Dr. Sarah Sharma', 'sarah.sharma@example.com', 'CSE', '1985-06-15', 'female', '9876543210', 'password123', 'password123');

-- Student Login: Student ID = S101, Password = password123
INSERT IGNORE INTO `register` (`fname`, `studentid`, `email`, `branch`, `year`, `dob`, `gender`, `phone`, `password`, `cpassword`)
VALUES ('Aarav Patel', 'S101', 'aarav.patel@example.com', 'CSE', '3', '2003-09-20', 'male', '9123456780', 'password123', 'password123');

-- Sample Quiz
INSERT IGNORE INTO `quiz_title` (`id`, `teacherid`, `title_name`)
VALUES (1, 'T101', 'Web_Development_Basics');

-- Sample Questions
INSERT IGNORE INTO `quiz_questions` (`id`, `teacherid`, `title_name`, `question_number`, `question`, `option1`, `option2`, `option3`, `option4`, `correct_option`, `marks`) VALUES
(1, 'T101', 'Web_Development_Basics', 1, 'Which HTML tag is used to create a hyperlink?', '<link>', '<a>', '<href>', '<hyperlink>', 'option2', 2),
(2, 'T101', 'Web_Development_Basics', 2, 'Which CSS property controls the text size?', 'font-style', 'text-size', 'font-size', 'text-style', 'option3', 2),
(3, 'T101', 'Web_Development_Basics', 3, 'In PHP, variable names must start with which symbol?', '&', '$', '#', '@', 'option2', 2),
(4, 'T101', 'Web_Development_Basics', 4, 'Which SQL keyword is used to retrieve data from a database?', 'GET', 'EXTRACT', 'SELECT', 'FETCH', 'option3', 2),
(5, 'T101', 'Web_Development_Basics', 5, 'Which JavaScript method is used to write to the browser console?', 'console.write()', 'console.output()', 'console.log()', 'console.print()', 'option3', 2);

-- Assign Sample Student S101 to Web_Development_Basics
INSERT IGNORE INTO `selected_student` (`sid`, `teacherid`, `title_name`, `studentid`)
VALUES (1, 'T101', 'Web_Development_Basics', 'S101');
