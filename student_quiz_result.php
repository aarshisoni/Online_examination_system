<!DOCTYPE html>
<html>
<head>
    <title>Quiz Result</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        p {
            margin-bottom: 20px;
        }

        button, input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Border around form */
        form {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Start session
    session_start();

    // Check if student is logged in
    if (!isset($_SESSION['studentid'])) {
        // Redirect to login page if not logged in
        header("Location: student_login.php");
        exit();
    }

    // Include database connection file
    include "connect.php";

    // Fetch student's name from the register table using session studentid
    $studentid = $_SESSION['studentid'];

    // Fetch title_name and teacherid from selected_student table
    if (isset($_GET['title_name']) && isset($_GET['teacherid'])) {
        $title_name = $_GET['title_name'];
        $teacherid = $_GET['teacherid'];

        // Fetch questions related to the teacher and title
        $sql_fetch_questions = "SELECT * FROM quiz_questions WHERE teacherid = '$teacherid' AND title_name = '$title_name'";
        $result_questions = $con->query($sql_fetch_questions);

        // Initialize variables for marks calculation
        $total_questions = $result_questions->num_rows;
        $total_marks = 0;
        $correct_answers = 0; // Track the number of correct answers
        $question_number = 1; // Initialize question number

// Initialize total marks
$totalMarks = 0;

// Loop through each question to calculate total marks
while ($row = $result_questions->fetch_assoc()) {
    // Add marks of the current question to total marks
    $totalMarks += intval($row["marks"]);
}

        // Calculate marks and store correct and student answers
        while ($row = $result_questions->fetch_assoc()) {
            $question_id = $row['id'];
            $selected_option_key = 'question_' . $question_id; // Construct the key for accessing the selected option
            $selected_option = isset($_POST[$selected_option_key]) ? $_POST[$selected_option_key] : ''; // Check if option is selected
            $correct_option = $row['correct_option']; // Get the ID of the correct option

            // Check if the correct and student answers are not null before accessing them
            $correct_answer = isset($row['option' . substr($correct_option, -1)]) ? $row['option' . substr($correct_option, -1)] : '';
            $student_answer = isset($row['option' . substr($selected_option, -1)]) ? $row['option' . substr($selected_option, -1)] : '';

            // Increment total marks if the student answer matches the correct answer
            if ($selected_option === $correct_option) {
                $total_marks += $row['marks'];
                $correct_answers++; // Increment correct answers counter
            }

            // Display question and answers
            echo "<p>";
            echo "Question $question_number: " . $row['question'] . "<br>"; // Display question number
            echo "Correct Answer: " . $correct_answer . "<br>";
            echo "Your Answer: " . ($student_answer !== '' ? $student_answer : 'No answer selected') . "<br>";
            echo "</p>";

            $question_number++; // Increment question number
        }

        // Display total marks and correct answers
        echo "<h2>You scored $total_marks out of $totalMarks</h2>";
        echo "<p>You answered $correct_answers out of $total_questions questions correctly.</p>";

        // Insert score into submit_score table
        $sql_insert_score = "INSERT INTO submit_score (studentid, teacherid, title_name, marks) VALUES ('$studentid', '$teacherid', '$title_name', '$total_marks')";
        if ($con->query($sql_insert_score) === TRUE) {
            echo "Score submitted successfully.";
        } else {
            // Check if it's a duplicate entry error
            if ($con->errno == 1062) {
                echo "You have already submitted the quiz.";
            } else {
                echo "Error: " . $sql_insert_score . "<br>" . $con->error;
            }
        }
    } else {
        echo "Title name or teacher ID not provided.";
    }

    $con->close();
    ?>
    <form action="quiz_logout.php" method="post"></form>
        <input type="submit" value="OK">
    
</div>

</body>
</html>
