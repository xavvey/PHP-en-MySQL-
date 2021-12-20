<?php
$hostname = 'localhost';
$username = 'root';
$password = 'mysql';
$database = 'vereniging';

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) 
    die( "<span style='color:red'>" . "Er is iets mis gegaan met het tot stand brengen van de verbinding met de database. 
            Controleer of u de juiste database wilt bereiken, of deze bestaat en of uw inloggegevens kloppen" . "</span>");

?>