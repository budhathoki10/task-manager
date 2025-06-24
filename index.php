<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "project_taskmanager_kushal";


$conn = mysqli_connect($host, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$createDb = "CREATE DATABASE IF NOT EXISTS $dbname";
mysqli_query($conn, $createDb);

mysqli_select_db($conn, $dbname);


$table = "CREATE TABLE IF NOT EXISTS kushal_manager (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task VARCHAR(250),
    priority ENUM('High Priority', 'Medium priority', ' Less Priority') DEFAULT 'High Priority',
    deadline DATE,
    status ENUM('Pending', 'Complete') DEFAULT 'Pending',
    created_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn,$table);


if($_SERVER["REQUEST_METHOD"]=='POST' && isset($_POST['inputtitle'])){

    $title =$_POST["inputtitle"];
    $priority= $_POST["select"];
    $date=$_POST['date'];
    $status= $_POST['status'];
    if($title!="" && $priority!=""&& $date!="" && $status!=""){
        $fill= "insert into kushal_manager (task,priority,deadline,status) values(
        '$title','$priority','$date','$status')";
        mysqli_query($conn,$fill);
        header("Location:index.php");
        exit();
    }
}
$html = file_get_contents("task.html");


$sql = "SELECT * FROM kushal_manager ORDER BY deadline ASC, priority DESC";
$result = mysqli_query($conn, $sql);

$today = date('Y-m-d');
$taskList = "";

while ($row = mysqli_fetch_assoc($result)) {
    $title = htmlspecialchars($row['task']);
    $priority = $row['priority'];
    $deadline = $row['deadline'];
    $status = $row['status'];

    $warning = "";
    if ($deadline < $today && $status != 'Complete') {
        $warning = " <span style='color: red;'>[Time over]</span>";
    }

    $taskList .= "Task: $title | Priority: $priority | Deadline: $deadline | Status: $status $warning<br>";
}


echo str_replace("{{Result}}", $taskList, $html);
