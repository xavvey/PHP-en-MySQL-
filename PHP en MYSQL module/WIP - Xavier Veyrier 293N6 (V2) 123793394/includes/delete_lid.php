<?php
require_once __DIR__ . '../connection.php';

$lidnummer = $_GET["lidnummer"];

$del_tel_query = "DELETE FROM telefoonnummers WHERE lidnummer='$lidnummer'";
$del_tel_result = $conn->query($del_tel_query);
if(!$del_tel_result) die ("Verwijderen van telefoonnummers mislukt. <br>");

$del_mail_query = "DELETE FROM emails WHERE lidnummer='$lidnummer'";
$del_mail_result = $conn->query($del_mail_query);
if(!$del_mail_result) die ("Verwijderen van emails mislukt. <br>");

$del_lid_query = "DELETE FROM leden WHERE lidnummer='$lidnummer'";
$del_lid_result = $conn->query($del_lid_query);
if(!$del_lid_result) die ("Verwijderen van leden mislukt. <br>");    


$conn->close();

header("location: ../home_ledenlijst.php");
?>