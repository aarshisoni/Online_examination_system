<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Button</title>
    <style> 
        .container {
            text-align: center;
            margin:5%;
        }
        .start-button {
            color: white;
            background-color: #037808;
            padding: 12px;
            font-size: large;
            border-radius: 5px;
        }
        .start-button:hover {
            background-color: #229027;
        }
        .inst {
            background-color: rgb(235, 210, 210);
        border: outset  3px;
        border-radius: 10px;
        margin-bottom: 20px;
       margin-top: 20px;
       border-radius: 5px;
      
    }

    .inst h1{
        font-size:40px;
        text-align:center;
        
    }

    .inst p {
        font-size: 30px;
        text-align: center;
    }

    .inst ul {
        margin-top: 0;
        padding-left: 20px;
        list-style-type: square;
    }

    .inst ul li {
        margin-bottom: 8px;
        font-size: 20px;
        margin-left: 10px;
    }
    </style>
</head>
<body>
<div class="inst">
    <p>
        <h1><strong>Instructions</strong></h1>
        <ul  >
            <li>Click on the "Start Quiz" button to begin the quiz.</li>
            <li>After submitting the form, your results will be displayed.</li>
            <li>Once the results are shown, you cannot submit the form again or obtain marks by going back.</li>
        </ul>
    </p>
</div>

    <div class="container">
        <button class="start-button" onclick="startFunction()">Start Quiz</button>
    </div>

    <script>
    function startFunction() {
        alert("Let's get started!");
        window.location.href = 'student_generate_quiz.php';
    }
</script>

</body>
</html>