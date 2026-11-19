<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include 'connect.php';

$teacherid = $_SESSION['teacherid'];
$title_name = isset($_GET['title_name']) ? trim($_GET['title_name']) : '';

if (empty($title_name)) {
    header("Location: display_quize.php");
    exit();
}

// Fetch selected students along with their details
$query = "SELECT register.fname, register.studentid, register.email, register.branch, register.year, register.dob, register.gender, register.phone
          FROM selected_student
          INNER JOIN register ON selected_student.studentid = register.studentid
          WHERE selected_student.teacherid = ? AND selected_student.title_name = ?
          ORDER BY register.fname ASC";

$stmt = $con->prepare($query);
$stmt->bind_param("ss", $teacherid, $title_name);
$stmt->execute();
$result = $stmt->get_result();
$assigned_count = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Students - <?php echo htmlspecialchars($title_name); ?> | ExamPortal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1e3c72;
            --primary-dark: #14284d;
            --accent: #11998e;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #e6f0fa 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--text-dark);
        }

        /* Glassmorphism Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span {
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .btn {
            padding: 0.55rem 1.15rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-outline {
            color: var(--primary);
            border: 1.5px solid var(--primary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #2a5298 100%);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.2);
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-2px);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent) 0%, #38ef7d 100%);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.2);
        }

        .btn-accent:hover {
            opacity: 0.95;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--white);
            padding: 6px 12px;
            font-size: 0.82rem;
            border-radius: 6px;
        }

        .btn-danger:hover {
            background: var(--danger-dark);
        }

        /* Container & Card */
        .container {
            max-width: 1200px;
            width: 92%;
            margin: 2rem auto;
            flex: 1;
        }

        .card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #edf2f7;
        }

        .title-group h1 {
            font-size: 1.6rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .title-group p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .stats-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(17, 153, 142, 0.1);
            color: var(--accent);
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
        }

        /* Live Search Bar */
        .search-bar {
            margin-bottom: 1.5rem;
            position: relative;
            max-width: 400px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            transition: var(--transition);
        }

        .search-bar input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }

        .search-bar i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        /* Modern Table */
        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #edf2f7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-dark);
            font-size: 0.92rem;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        .student-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary) 0%, #2a5298 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .student-name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .badge-branch {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-year {
            background: #fef3c7;
            color: #92400e;
        }

        .empty-state {
            text-align: center;
            padding: 3.5rem 1rem;
        }

        .empty-state i {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 1.25rem 5%;
            text-align: center;
            font-size: 0.85rem;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .nav-links {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>

    <!-- Glassmorphic Top Navbar -->
    <header class="navbar">
        <a href="admin_dashboard.php" class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>ExamPortal</span>
        </a>
        <div class="nav-links">
            <a href="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>" class="btn btn-accent">
                <i class="fas fa-user-plus"></i> Assign More Students
            </a>
            <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>" class="btn btn-outline">
                <i class="fas fa-list-ol"></i> Quiz Questions
            </a>
            <a href="display_quize.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> My Quizzes
            </a>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="page-header">
                <div class="title-group">
                    <h1><i class="fas fa-users-cog"></i> Assigned Students</h1>
                    <p>Exam Title: <strong><?php echo htmlspecialchars($title_name); ?></strong></p>
                </div>
                <div class="stats-badge">
                    <i class="fas fa-user-check"></i> <?php echo $assigned_count; ?> Student<?php echo $assigned_count == 1 ? '' : 's'; ?> Enrolled
                </div>
            </div>

            <?php if ($assigned_count > 0): ?>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="filterInput" onkeyup="filterTable()" placeholder="Quick search by name, ID or branch...">
                </div>

                <div class="table-responsive">
                    <table id="studentsTable">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Student ID</th>
                                <th>Email</th>
                                <th>Branch</th>
                                <th>Year</th>
                                <th>Phone</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">
                                                <?php echo strtoupper(substr($row['fname'], 0, 1)); ?>
                                            </div>
                                            <div class="student-name">
                                                <?php echo htmlspecialchars($row['fname']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($row['studentid']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><span class="badge badge-branch"><?php echo htmlspecialchars($row['branch'] ? $row['branch'] : 'General'); ?></span></td>
                                    <td><span class="badge badge-year"><?php echo htmlspecialchars($row['year'] ? $row['year'] : 'N/A'); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['phone'] ? $row['phone'] : '—'); ?></td>
                                    <td style="text-align: center;">
                                        <a href="delete_student.php?studentid=<?php echo urlencode($row['studentid']); ?>&title_name=<?php echo urlencode($title_name); ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Remove student <?php echo htmlspecialchars($row['fname']); ?> (<?php echo htmlspecialchars($row['studentid']); ?>) from this quiz?');">
                                            <i class="fas fa-user-minus"></i> Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-clock"></i>
                    <h3>No Students Assigned Yet</h3>
                    <p>Students must be assigned to this exam so they can see it and take it on their portal.</p>
                    <a href="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Assign Students Now
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Online Examination System. All rights reserved.</p>
    </footer>

    <script>
        function filterTable() {
            const input = document.getElementById('filterInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('studentsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const text = tr[i].textContent || tr[i].innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
