<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";
$teacherid = $_SESSION['teacherid'];

$search_query = isset($_GET["search_query"]) ? trim($_GET["search_query"]) : "";
$filter_branch = isset($_GET["filter_branch"]) ? trim($_GET["filter_branch"]) : "";
$filter_year = isset($_GET["filter_year"]) ? trim($_GET["filter_year"]) : "";

// Build search query with prepared statement
$query = "SELECT * FROM register WHERE 1=1";
$params = [];
$types = "";

if (!empty($search_query)) {
    $query .= " AND (fname LIKE ? OR studentid LIKE ?)";
    $like = "%" . $search_query . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if (!empty($filter_branch)) {
    $query .= " AND branch = ?";
    $params[] = $filter_branch;
    $types .= "s";
}

if (!empty($filter_year)) {
    $query .= " AND year = ?";
    $params[] = $filter_year;
    $types .= "s";
}

$query .= " ORDER BY fname ASC";

$stmt = $con->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$data = $stmt->get_result();
$total = $data->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Examination Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header { 
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 18px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        header h2 { margin: 0; font-size: 22px; font-weight: 600; }
        .header-nav { display: flex; gap: 12px; }
        .nav-btn {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: 0.2s;
        }
        .nav-btn:hover { background: rgba(255,255,255,0.35); }
        .container {
            max-width: 1250px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #edf2f7;
            flex-wrap: wrap;
            gap: 15px;
        }
        .search-form {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-form input, .search-form select {
            padding: 9px 14px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn-search {
            background: #1e3c72;
            color: white;
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
        }
        th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:hover { background-color: #f8fafc; }
        .btn-sm {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-edit { background: #3498db; color: white; }
        .btn-del { background: #e74c3c; color: white; }
        .empty-state { text-align: center; padding: 40px; color: #718096; }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-user-graduate"></i> Manage Student Records</h2>
    <div class="header-nav">
        <a href="admin_dashboard.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
</header>

<div class="container">
    <div class="filter-bar">
        <form method="get" action="student_list.php" class="search-form">
            <input type="text" name="search_query" placeholder="Search name or student ID..." value="<?php echo htmlspecialchars($search_query); ?>">
            <select name="filter_branch">
                <option value="">All Branches</option>
                <option value="CSE" <?php if ($filter_branch == 'CSE') echo 'selected'; ?>>CSE</option>
                <option value="Electrical" <?php if ($filter_branch == 'Electrical') echo 'selected'; ?>>Electrical</option>
                <option value="Civil" <?php if ($filter_branch == 'Civil') echo 'selected'; ?>>Civil</option>
                <option value="mechanical" <?php if (strtolower($filter_branch) == 'mechanical') echo 'selected'; ?>>Mechanical</option>
            </select>
            <select name="filter_year">
                <option value="">All Years</option>
                <option value="1" <?php if ($filter_year == '1') echo 'selected'; ?>>1st Year</option>
                <option value="2" <?php if ($filter_year == '2') echo 'selected'; ?>>2nd Year</option>
                <option value="3" <?php if ($filter_year == '3') echo 'selected'; ?>>3rd Year</option>
                <option value="4" <?php if ($filter_year == '4') echo 'selected'; ?>>4th Year</option>
            </select>
            <button type="submit" class="btn-search"><i class="fas fa-search"></i> Search</button>
            <?php if (!empty($search_query) || !empty($filter_branch) || !empty($filter_year)): ?>
                <a href="student_list.php" style="color: #718096; font-size: 14px; text-decoration: none;">Clear</a>
            <?php endif; ?>
        </form>
        <span style="color: #718096; font-size: 14px; font-weight: 600;">Total Students: <?php echo $total; ?></span>
    </div>

    <?php if ($total > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Email Address</th>
                    <th>Branch</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $data->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['fname']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['studentid']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><span style="background: #e8f5e9; color: #2e7d32; padding: 3px 8px; border-radius: 12px; font-weight: 600; font-size: 12px;"><?php echo htmlspecialchars($row['branch']); ?></span></td>
                        <td>Year <?php echo htmlspecialchars($row['year']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td>
                            <a href="studentList_update.php?studentid=<?php echo urlencode($row['studentid']); ?>" class="btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="studentList_delete.php?studentid=<?php echo urlencode($row['studentid']); ?>" class="btn-sm btn-del" onclick="return confirm('Delete this student? This will also remove all their exam submissions.');"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-user-slash" style="font-size: 40px; color: #cbd5e0; margin-bottom: 10px;"></i>
            <p>No registered students found matching your search filter.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
