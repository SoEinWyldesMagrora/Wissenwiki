<?php
  $hostname = "localhost";
  $username = "mr_codespace_bot";
  $password = "Bruchw3d3l";
  $dbname = "mr_codespace_bot";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
?>
