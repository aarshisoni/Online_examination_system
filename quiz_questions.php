<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['teacherid'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Include database connection file
include 'connect.php';

// Fetch teacher's ID from session
$teacherid = $_SESSION['teacherid'];

// Fetch title_name from URL parameters
if (isset($_GET['title_name'])) {
    $title_name = $_GET['title_name'];


    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['delete_question'])) {
            // Delete question
            $delete_question_id = $_POST['delete_question'];

            // Delete record from quiz_questions table
            $sql_delete = "DELETE FROM quiz_questions WHERE id = '$delete_question_id' AND teacherid = '$teacherid' AND title_name = '$title_name'";
            if ($con->query($sql_delete) === TRUE) {
                //echo "Question deleted successfully.";
            } else {
                echo "Error deleting question: " . $con->error;
            }
        } else {
            // Retrieve form data
            $question_number = $_POST['question_number'];
            $question = $_POST['question'];
            $option1 = $_POST['option1'];
            $option2 = $_POST['option2'];
            $option3 = $_POST['option3'];
            $option4 = $_POST['option4'];
            $correct_option = $_POST['correct_option'];
            $marks = $_POST['marks'];

            // Insert data into quiz_questions table
            $sql_insert = "INSERT INTO quiz_questions (teacherid, title_name, question_number, question, option1, option2, option3, option4, correct_option, marks) 
                           VALUES ('$teacherid', '$title_name', '$question_number', '$question', '$option1', '$option2', '$option3', '$option4', '$correct_option', '$marks')";

            if ($con->query($sql_insert) === TRUE) {
                //echo "New record inserted successfully.";
            } else {
                echo "Error: " . $sql_insert . "<br>" . $con->error;
            }
        }
    }
} else {
    // Handle case when title_name is not provided in the URL
    $title_name = ""; // Initialize $title_name as an empty string
    // echo "Title name not provided in URL.";
}

// Fetch questions and options from quiz_questions table
$sql_fetch_questions = "SELECT * FROM quiz_questions WHERE teacherid = '$teacherid' AND title_name = '$title_name'";
$result_questions = $con->query($sql_fetch_questions);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Questions</title>
    <!-- Add your CSS styles here -->
    <style>
        body {
            font-family: Arial;
            background-color: rgb(235, 210, 210);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-right: 12%;
            margin-top: -26%;
        }

        h1 {
            font-size: 27px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-size: 18px;
            color: #555;
        }

        input[type="text"],
        input[type="submit"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            padding: 8px 16px;
            /* Reduced padding */
            width: 200px; /* Set width to 200px */
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .delete-btn,
            input[type="submit"] {
            width: 200px; 
        }


        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        li:hover {
            background-color: #f0f0f0;
        }

        .question-number {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .delete-btn {
            background-color: red;
            border: 2px solid red;
            padding: 7px;
            color: white;
            border-radius: 10px;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 200px; /* Set width to 200px */
        }

        .delete-btn:hover {
            background-color: #f44336;
        }

        .main-buttons {
            display: flex;
            flex-direction: column;
            margin-top:-6%;
        }

        .main-buttons > div {
            margin-bottom: 10px;
        }


        .button {
            background-color: #037808;
            width:200px;
            font-size:16px;
            color: white;
            padding: 10px;
            font-size: large;
            border-radius: 5px;
            cursor: pointer;
            margin:10% 10% 0 4%;

        }

        .selected-student-list-button {
            background-color: #037808;
            width:200px;
            font-size:16px;
            color: white;
            padding: 10px;
            font-size: large;
            border-radius: 5px;
            cursor: pointer;
            margin:2% 10% 0 4%;
        }
        .generate-button {
            background-color: #037808;
            width:200px;
            font-size:16px;
            color: white;
            padding: 10px;
            font-size: large;
            border-radius: 5px;
            cursor: pointer;
            margin:2% 10% 0 4%;

        }

        .selected-student-list-button:hover,
        .button:hover {
            background-color: #229027;
        }

        .generate-button:hover {
            background-color: #229027;; /* Change the background color on hover */
        }

        .back-button {
            display: inline-block;
            background-color: #008CBA;
            color: white;
            margin: 2% 0 30px 5%;
            padding: 10px;
            text-decoration: none;
            border-radius: 50%; /* For making the button circular */
            width: 30px; /* Adjust size of the circle */
            height: 30px; /* Adjust size of the circle */
            line-height: 28px; /* For centering the arrow */
            text-align: center;
            font-size: 24px;
            box-shadow: 4px 4px 6px #888888; /* Adding border shadow */
        }

        .back-button:hover {
            background-color: #005f6b;
        }
    </style>

    <script>
    function checkdelete() {
        return confirm('Are you sure you want to delete this record?');
    }
</script>   
</head>

<body>
    <!-- Back button -->
    <a href="admin_dashboard.php?teacherid=<?php echo $teacherid; ?>&title_name=<?php echo urlencode($title_name); ?>" class="back-button" style="font-size: 30px; font-weight: 500;">&#8592;</a>
<div class="main-buttons">
    <div>
        <a href="student_list_to_select.php?title_name=<?php echo $title_name; ?>&teacherid=<?php echo $teacherid; ?>">
            <button class="button" onclick="nextPage()">Student List</button>
        </a>
    </div>

    <div>
        <a href="selected_student_list.php?title_name=<?php echo $title_name; ?>&teacherid=<?php echo $teacherid; ?>">
            <button class="selected-student-list-button" onclick="nextPage()">Selected Student List</button>
        </a>
    </div>

    <div>
        <a href="generate_quiz.php?title_name=<?php echo $title_name; ?>&teacherid=<?php echo $teacherid; ?>">
            <button class="generate-button" onclick="nextPage()">Generate Quiz</button>
        </a>
    </div>
</div>




        <div class="container">
            <h1>Add Questions for <?php echo $title_name; ?></h1>
            <!-- Form to add new questions -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?teacherid=$teacherid&title_name=$title_name"; ?>">
                <!-- Add your form fields here -->
                <label for="question_number">Question Number:</label><br>
                <input type="text" id="question_number" name="question_number" required><br><br>

                <label for="question">Question:</label><br>
                <input type="text" id="question" name="question" required><br><br>

                <label for="option1">Option 1:</label><br>
                <input type="text" id="option1" name="option1" required><br><br>

                <label for="option2">Option 2:</label><br>
                <input type="text" id="option2" name="option2" required><br><br>

                <label for="option3">Option 3:</label><br>
                <input type="text" id="option3" name="option3" required><br><br>

                <label for="option4">Option 4:</label><br>
                <input type="text" id="option4" name="option4" required><br><br>

                <label for="marks">Marks:</label><br>
                <input type="text" id="marks" name="marks" required><br><br>

                <label for="correct_option">Correct Option:</label><br>
                <select id="correct_option" name="correct_option" required>
                <option value="">Choose correct option</option>
                    <option value="option1">Option 1</option>
                    <option value="option2">Option 2</option>
                    <option value="option3">Option 3</option>
                    <option value="option4">Option 4</option>
                </select><br><br>

                <input type="submit" value="Submit">
            </form>

            <!-- Display fetched questions and options -->
            <h2>Questions</h2>
            <ul>
                <?php
                if (isset($title_name) && $result_questions !== false && $result_questions->num_rows > 0) {
                    while ($row = $result_questions->fetch_assoc()) {
                        echo "<li>";
                        echo "Question Number: " . $row['question_number'] . "<br>";
                        echo "Question: " . $row['question'] . "<br>";
                        echo "Option 1: " . $row['option1'] . "<br>";
                        echo "Option 2: " . $row['option2'] . "<br>";
                        echo "Option 3: " . $row['option3'] . "<br>";
                        echo "Option 4: " . $row['option4'] . "<br>";
                        echo "Correct Option: " . $row['correct_option'] . "<br>";
                        echo "Marks: " . $row['marks'] . "<br>";
                        echo "<form method='post'><input type='hidden' name='delete_question' value='" . $row['id'] . "'><input type='submit' value='Delete' onclick='return checkdelete()'></form>";
                        echo "</li>";
                    }
                } else {
                    echo "<p>No questions found.</p>";
                }
                ?>
            </ul>

            <!-- <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="title_name" value="<?php echo $title_name; ?>">
            <input type="submit" name="upload_quiz" value="Upload Quiz">
        </form> -->

        </div>
</body>

</html>