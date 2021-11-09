<?php
$conn = new mysqli('localhost', 'root', 'mysql', 'vereniging');
if ($conn->connect_error) 
    die( "<span style='color:red'>" . "Er is iets mis gegaan met het tot stand brengen van de verbinding met de database. 
            Controleer of u de juiste database wilt bereiken, of deze bestaat en of uw inloggegevens kloppen" . "</span>");

echo __DIR__;
?>