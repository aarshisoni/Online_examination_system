<?php
// Start session
session_start();

// Include database connection file
include "connect.php";

// Check if student is logged in
if (!isset($_SESSION['studentid'])) {
    // Redirect to login page if not logged in
    header("Location: student_login.php");
    exit();
}

// Fetch student's name from the register table using session studentid
$studentid = $_SESSION['studentid'];

// Fetch title_name and teacherid from selected_student table
$query_info = "SELECT title_name, teacherid FROM selected_student WHERE studentid = '$studentid'";
$result_info = mysqli_query($con, $query_info);

// Check if query was successful and if a row was returned
if ($result_info && mysqli_num_rows($result_info) > 0) {
    // Fetch the first row from the result set
    $row_info = mysqli_fetch_assoc($result_info);
    // Store the title_name and teacherid in variables
    $titleName = $row_info['title_name'];
    $teacherid = $row_info['teacherid'];

    // Fetch questions related to the teacher and title
    $sql_fetch_questions = "SELECT * FROM quiz_questions WHERE teacherid = '$teacherid' AND title_name = '$titleName'";
    $result_questions = $con->query($sql_fetch_questions);

    // Check if there are questions available
    if ($result_questions->num_rows > 0) {
        
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
            font-size: 30px;
            padding-top: 10px; /* Add some padding to separate the border from the content */
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
            font-size:50px;
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

        input[type="radio"]{
            width:30px;
          
        }
        .question {
                    margin-bottom: 20px;
                    font-size: 25   px;
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
            font-weight:600;
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
        </head>
        <body>
            <h1>Generated Quiz for <?php echo $titleName; ?></h1>
            <div class="total-marks-container"><p>Total Marks: <?php echo $totalMarks; ?></p></div>
            <form method="post" action="student_quiz_result.php?title_name=<?php echo urlencode($titleName); ?>&teacherid=<?php echo $teacherid; ?>">
                <input type="hidden" name="title_name" value="<?php echo $titleName; ?>">
                <input type="hidden" name="teacherid" value="<?php echo $teacherid; ?>">

                <?php
                // Display randomly selected questions
                $question_number = 1; // Counter for question number
                while ($row = $result_questions->fetch_assoc()) {
                    echo "<div class='question'>";
                    echo "Question " . $question_number . ": " . $row['question'] . "<br></br>";
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

            <!-- <script type="text/javascript"> 
                function preventBack() { 
                    window.history.forward();  
                } 
                  
                setTimeout("preventBack()", 0); 
                  
                window.onunload = function () { null }; 
            </script> -->
        </body>
        </html>

        <?php
    } else {
        // No questions available for this title
        echo "No questions available for $titleName";
    }
} else {
    // Title name not provided
    echo "Title name not provided.";
}
?>
