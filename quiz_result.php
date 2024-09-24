<?php
session_start();
// Include database connection file
include 'connect.php';

// Fetch data from the submitted form
$teacherid = $_POST['teacherid'];
$title_name = $_POST['title_name'];

// Fetch student ID from the register table
$sql_fetch_studentid = "SELECT studentid FROM selected_student WHERE teacherid = '$teacherid' AND title_name = '$title_name'";
$result_studentid = $con->query($sql_fetch_studentid);

if ($result_studentid->num_rows > 0) {
    // If there is a matching record in the register table
    $row = $result_studentid->fetch_assoc();
    $studentid = $row['studentid'];

    // Fetch questions related to the teacher and title
    $sql_fetch_questions = "SELECT * FROM quiz_questions WHERE teacherid = '$teacherid' AND title_name = '$title_name'";
    $result_questions = $con->query($sql_fetch_questions);

    // Initialize variables for marks calculation
    $total_questions = $result_questions->num_rows;
    $total_marks = 0;

    // Calculate marks
    while ($row = $result_questions->fetch_assoc()) {
        $question_id = $row['id'];
        $selected_option_key = 'question_' . $question_id; // Construct the key for accessing the selected option
        $selected_option = isset($_POST[$selected_option_key]) ? $_POST[$selected_option_key] : ''; // Check if option is selected
        $correct_option = $row['correct_option']; // Get the ID of the correct option

        if ($selected_option === $correct_option) {
            $total_marks += $row['marks']; // Increment total marks by the marks of the current question
        }
    }

    // Fetch total marks for the quiz
    $sql_fetch_total_marks = "SELECT SUM(marks) AS total_marks FROM quiz_questions WHERE teacherid = '$teacherid' AND title_name = '$title_name'";
    $result_total_marks = $con->query($sql_fetch_total_marks);
    $row_total_marks = $result_total_marks->fetch_assoc();
    $total_marks_quiz = $row_total_marks['total_marks'];

    // Display marks obtained
    echo "<h2>You scored $total_marks out of $total_marks_quiz</h2>";

    // Display questions with user's selected options and correct options
    $result_questions = $con->query($sql_fetch_questions); // Reset result pointer
    $question_number = 1; // Counter for question number
    while ($row = $result_questions->fetch_assoc()) {
        $question_id = $row['id'];
        $selected_option_key = 'question_' . $question_id; // Construct the key for accessing the selected option
        $selected_option = isset($_POST[$selected_option_key]) ? $_POST[$selected_option_key] : ''; // Get the ID of the selected option
        $correct_option = $row['correct_option']; // Get the ID of the correct option

        // Fetch option content for selected option
        $selected_option_content = $row['option' . substr($selected_option, -1)];

        // Fetch option content for correct option
        $correct_option_content = $row['option' . substr($correct_option, -1)];

        echo "<p>";
        echo "Question " . $question_number . ": " . $row['question'] . "<br>";
        echo "Your Answer: " . ($selected_option_content !== '' ? $selected_option_content : 'No answer selected') . "<br>"; // Display user's selected option or a message if no answer selected
        echo "Correct Answer: " . $correct_option_content . "<br>"; // Display correct option
        echo "</p>";
        $question_number++; // Increment question number
    }

    // Submit score to database
    $table_name = 'submit_score';

    // Check if the student's score for the same quiz already exists
    $sql_check_existing_score = "SELECT * FROM $table_name WHERE studentid = '$studentid' AND title_name = '$title_name'";
    $result_existing_score = $con->query($sql_check_existing_score);

    if ($result_existing_score->num_rows > 0) {
        // If the student has already submitted the quiz
        echo "You have already submitted the quiz.";
    } else {
        // If the student has not submitted the quiz, proceed with inserting the score
        // Insert data into the database table
        $sql_insert_score = "INSERT INTO $table_name (sId, studentid, title_name, marks) VALUES (NULL, '$studentid', '$title_name', '$total_marks')";
        if ($con->query($sql_insert_score) === TRUE) {
            echo "Score submitted successfully.";
        } else {
            echo "Error: " . $sql_insert_score . "<br>" . $con->error;
        }
    }

    // Destroy session data to log out the user
    session_unset();
    session_destroy();
} else {
    echo "No matching record found in the register table for teacher ID '$teacherid' and title name '$title_name'.";
}

$con->close();
?>

<form action="quiz_logout.php" method="post">
    <input type="submit" value="OK">
</form>
