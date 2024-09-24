CREATE TABLE register (
     fname VARCHAR(50),
    studentid VARCHAR(50) PRIMARY KEY,
    email VARCHAR(100),
    branch VARCHAR(50),
    year VARCHAR(50),
    dob VARCHAR(50),
    gender VARCHAR(10),
    phone VARCHAR(20),
    password VARCHAR(100),
    cpassword VARCHAR(100)
);




-- // table name :-  admin_register 
CREATE TABLE admin_register (
    teacherid VARCHAR(50) PRIMARY KEY,
    fname VARCHAR(50),
    email VARCHAR(100),
    branch VARCHAR(50),
    dob VARCHAR(50),
    gender VARCHAR(10),
    phone VARCHAR(20),
    password VARCHAR(100),
    cpassword VARCHAR(100)
);


-- table name :- quiz_questions


CREATE TABLE quiz_questions (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    teacherid VARCHAR(255),
    title_name VARCHAR(255),
    question_number INT(11),
    question TEXT,
    option1 TEXT,
    option2 TEXT,
    option3 TEXT,
    option4 TEXT,
    correct_option VARCHAR(200),
    marks INT(11)
);


CREATE TABLE quiz_title (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    teacherid VARCHAR(200) NOT NULL,
    title_name VARCHAR(200) NOT NULL
) ;

CREATE TABLE selected_student (
    sid INT(11) PRIMARY KEY AUTO_INCREMENT,
    teacherid VARCHAR(200) NOT NULL,
    title_name VARCHAR(200) NOT NULL,
    studentid VARCHAR(200) NOT NULL
) ;

CREATE TABLE submit_score (
    sid INT(100) PRIMARY KEY AUTO_INCREMENT,
    studentid VARCHAR(200) NOT NULL,
    teacherid VARCHAR(200) NOT NULL,
    title_name VARCHAR(200) NOT NULL,
    marks INT (200) NOT NULL
) ;
