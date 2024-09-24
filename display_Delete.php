<?php
@include "confi.php";

$studentid=$_GET['$studentid'];
$query="DELETE FROM register WHERE studentid='$studentid'";
$data=mysqli_query($conn,$query);

if($data){ 
    echo "Record deleted";
}
else{
    echo "failed to delete";
}
?>