<?php
session_start();

// Include database connection file
include 'connect.php'; // Assuming this file contains the database connection details

// Check if teacher is logged in
if (!isset($_SESSION['teacherid'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Fetch teacher's ID from session
$teacherid = $_SESSION['teacherid'];

// Fetch title_name from previous page
if (isset($_GET['title_name'])) {

    $title_name = $_GET['title_name'];

    // Fetch questions related to the teacher and title
    $sql_fetch_questions = "SELECT * FROM quiz_questions WHERE teacherid = '$teacherid' AND title_name = '$title_name'";
    $result_questions = $con->query($sql_fetch_questions);

    // Check if there are questions available
    if ($result_questions->num_rows > 0) {
        // Display form to generate quiz
        
        // Initialize total marks
        $totalMarks = 0;

        // Loop through each question to calculate total marks
        while ($row = $result_questions->fetch_assoc()) {
            // Add marks of the current question to total marks
            $totalMarks += intval($row["marks"]);
        }

        // Reset result pointer to the beginning
        $result_questions->data_seek(0);
?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Generate Quiz</title>
            <style>
                 <style>
                 .marks {
            float: right;
        }

        /* Conditional style for the first question */
        .question:first-child {
            border-top: none; /* Remove top border for the first question */
        }
            .question {
                margin-bottom: 20px;
                font-size: 23px;
                padding-top: 10px; /* Add some padding to separate the border from the content */
            }
            input[type="radio"]{
            width:30px;
          
        }

            .options {
                margin-left: 20px;
                margin-top:40px;
            }

            .options label {
                display: block;
                margin-bottom: 10px;
            }

        /* New CSS for border and centering */
        form {
            border: 2px solid #000;
            padding: 20px;
            border-radius: 10px;
            width: 80%; /* Set the width of the form */
            margin: 0 auto; /* Center the form horizontally */
            background-color:  rgba(221, 221, 221, 0.908);
            font-size:30px;
           padding-left:40px;
            
        }

        /* CSS for submit button */
        input[type="submit"] {
            background-color: green; /* Darker green on hover */
            border:2px solid green;
            color: white; /* White text */
            padding: 10px 17px; /* Padding */
            text-align: center; /* Center text */
            text-decoration: none; /* Remove underline */
            display: inline-block; /* Make it inline */
            font-size: 16px; /* Font size */
            margin: 4px 2px 0; /* Margin */
            cursor: pointer; /* Cursor style */
            border-radius: 8px; /* Border radius */
        }

        input[type="submit"]:hover {
            
            background-color: #4CAF50; /* Green background */
        }
        .question {
                    margin-bottom: 20px;
                    font-size: 23px;
                    padding-top: 10px;
                    position: relative; /* Ensure relative positioning for absolute positioning of marks */
                }

                .marks {
                    position: absolute;
                    top: 0;
                    right: 0;
                    font-weight: bold;
                }
        /* CSS for header */
        h1 {
            font-size:40px;
            margin:2% 0 3% 10%;
        }
        /* CSS for marks content */
        /* .marks {
            float: right;
        } */
        /* CSS to center-align total marks */
        .total-marks-container {
            margin-left: 78%;
            font-size: 23px;
        }

        /* Conditional style for the first question */
        .question:first-child {
            border-top: none; /* Remove top border for the first question */
        }

        /* CSS for toggle switch */
        .toggle-container {
            display: flex;
            /* float:left; */
            align-items: center;
        }

        .toggle-container #timerStatus {
            font-size: 23px; /* Increase font size */
            margin:1% 0.5% 1.3% 48%; /* Add margin to the timer status */
            
        }

        .toggle-container #timerInput {
            margin-right: 10px; /* Add margin to the right of the timer input */
            display:none;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 40px; /* Decrease width */
            height: 20px; /* Decrease height */
            /* Add margin to the right of the switch */
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 20px; /* Adjust border-radius accordingly */
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px; /* Adjust height of slider handle */
            width: 16px; /* Adjust width of slider handle */
            left: 2px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(20px); /* Adjust translation based on new width */
            -ms-transform: translateX(20px); /* Adjust translation based on new width */
            transform: translateX(20px); /* Adjust translation based on new width */
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 20px; /* Adjust border-radius accordingly */
        }

        .slider.round:before {
            border-radius: 50%;
        }
        #timerInputDiv{
            margin:0 0.5% 1.3% 48%;
            font-size: 20px;
        }
    </style>
             <body>
            <h1>Generated Quiz for <?php echo $title_name; ?></h1>
            <div class="total-marks-container"><p>Total Marks: <?php echo $totalMarks; ?></p></div>
            <form id="quizForm" method="post" action="quiz_result.php">
                <input type="hidden" name="teacherid" value="<?php echo $teacherid; ?>">
                <input type="hidden" name="title_name" value="<?php echo $title_name; ?>">

                <?php
                // Display randomly selected questions
                $question_number = 1; // Counter for question number
                while ($row = $result_questions->fetch_assoc()) {
                    echo "<div class='question'>";
                    echo "Question " . $question_number . ": " . $row['question'] . "<br>";
                    echo "<label><input type='radio' name='question_" . $row['id'] . "' value='option1'>" . $row['option1'] . "</label><br>";
                    echo "<label><input type='radio' name='question_" . $row['id'] . "' value='option2'>" . $row['option2'] . "</label><br>";
                    echo "<label><input type='radio' name='question_" . $row['id'] . "' value='option3'>" . $row['option3'] . "</label><br>";
                    echo "<label><input type='radio' name='question_" . $row['id'] . "' value='option4'>" . $row['option4'] . "</label><br>";
                    echo "<span class='marks'>(Marks: " . $row['marks'] . ")</span><br>";
                    echo "</div>";
                    $question_number++; // Increment question number
                }
                ?>

                <input type="submit" value="Submit Quiz">
            </form>

            <!-- <script>
                // Function to submit the quiz automatically after a specified time
                function submitQuizAutomatically(timeLimit) {
                    setTimeout(function(){
                        document.getElementById("quizForm").submit();
                    }, timeLimit * 60 * 1000); // Convert minutes to milliseconds
                }

                // Call the function to submit quiz automatically after a specified time
                var quizDuration = <?php echo isset($_POST['quiz_duration']) ? $_POST['quiz_duration'] : 30; ?>; // Default quiz duration is 30 minutes
                submitQuizAutomatically(quizDuration);
            </script> -->
        </body>
        </html>

<?php
    } else {
        // No questions available for this title
        echo "No questions available for $title_name";
    }
} else {
    // Title name not provided
    echo "Title name not provided.";
}
?>