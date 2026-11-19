# Online Examination System 🎓💻

A full-stack, responsive web application for conducting and managing online tests, quizzes, and university examinations. Built purely with **HTML5, CSS3, JavaScript, PHP, and MySQL**.

---

## 🌟 Key Highlights & Features

### 👨‍🏫 Faculty / Teacher Panel
- **Multi-Faculty Architecture**: Multiple teachers can create accounts, author courses, and conduct examinations independently.
- **Course & Exam Authoring**: Create new examinations, define question banks with customizable marks per question, and preview tests before publishing.
- **Targeted Student Assignment**: Selectively assign exams to specific students, branches, and academic years.
- **Real-Time Gradebook & Analytics**: View student submissions, scores obtained, percentages, class averages, and pass/fail statuses.
- **Directory Management**: Search and filter students and faculty members by department and year.
- **Account Security & Password Management**: Self-service change password functionality with security validation and visibility toggles.

### 👨‍🎓 Student / Examinee Panel
- **Personalized Exam Dashboard**: View all live examinations assigned by course instructors.
- **Live Test Portal**: Clean, distraction-free examination interface with question counters and instant options.
- **Immediate Evaluation**: Instant grading upon submission with full question-by-question breakdown showing correct vs. submitted answers.
- **Performance History**: Complete record of all completed exams and scores over time.
- **One-Time Submission Protection**: Enforces examination integrity by preventing duplicate submissions.
- **Account Security & Password Management**: Dedicated change password portal to update credentials securely anytime.

---

## 🛠️ Technology Stack
- **Frontend**: HTML5, CSS3 (Modern Flexbox & Grid layouts, Glassmorphism UI), Vanilla JavaScript
- **Backend**: PHP 8.x (Prepared Statements, Session Authentication, OOP MySQLi)
- **Database**: MySQL / MariaDB (Relational schema with cascade integrity)
- **Local Environment**: XAMPP / Apache

---

## 🚀 Quick Setup & Installation Guide

### Prerequisites
- Install [XAMPP](https://www.apachefriends.org/) (Apache and MySQL).

### 1. Database Configuration
1. Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Open **phpMyAdmin** in your browser: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3. Create a new database named:
   ```sql
   database1
   ```
4. Click on `database1`, go to the **Import** tab, choose `register.sql` from this repository, and click **Go**.

### 2. Run the Application
You can run the project in either of two ways:

#### Option A: Using PHP Built-in Development Server (Fastest)
Open terminal in the project folder and run:
```bash
php -S localhost:8000
```
Then visit: [http://localhost:8000/MainPage.html](http://localhost:8000/MainPage.html)

#### Option B: Using XAMPP htdocs
Copy this project folder into `C:\xampp\htdocs\Online_examination_system`.
Then visit: [http://localhost/Online_examination_system/MainPage.html](http://localhost/Online_examination_system/MainPage.html)

---

## 🔑 Demo Login Credentials

The database comes pre-seeded with sample accounts for immediate testing:

| Role | Username / ID | Password | Department |
| :--- | :--- | :--- | :--- |
| **Faculty / Admin** | `T101` | `password123` | Computer Science (CSE) |
| **Student / User** | `S101` | `password123` | Computer Science (Year 3) |

---

## 📂 Project Architecture

```
Online_examination_system/
├── MainPage.html                # Modern landing page & portal gateway
├── connect.php                  # Database connection with error handling
├── register.sql                 # Complete MySQL schema & seed data
│
├── adm_register.php             # Faculty registration
├── admin_login.php              # Faculty authentication
├── admin_dashboard.php          # Main faculty management dashboard
├── new_title.php                # Create new course / exam title
├── quiz_questions.php           # Add & manage questions for an exam
├── student_list_to_select.php   # Assign eligible students to exam
├── selected_student_list.php    # Manage assigned students
├── display_quize.php            # Faculty exam listing & actions
├── student_score.php            # Faculty gradebook & results view
├── admin_list.php               # Faculty directory
├── admin_profile.php            # Faculty profile settings
├── admin_change_password.php    # Faculty password change portal
├── admin_logout.php             # Faculty session termination
│
├── student_register.php         # Student registration
├── student_login.php            # Student authentication
├── student_dashboard.php        # Main student portal & active tests
├── student_quiz_dashboard.php   # Live examinations list
├── student_start.php            # Pre-exam instructions & verification
├── student_generate_quiz.php    # Live examination interface
├── student_quiz_result.php      # Scorecard & answer key review
├── student_score_dashboard.php  # Student past exam history & marks
├── student_profile.php          # Student profile settings
├── student_change_password.php  # Student password change portal
├── student_logout.php           # Student session termination
│
├── student_list.php             # Student administration directory
├── studentList_update.php       # Update student records
└── studentList_delete.php       # Delete student records
```

---

## 🔒 Security Enhancements
- All database queries migrated to **Prepared Statements** (`$stmt->prepare()`) to prevent SQL injection.
- Session-based access control on all administrative and student endpoints.
- Duplicate ID validation during registration to ensure unique records.
