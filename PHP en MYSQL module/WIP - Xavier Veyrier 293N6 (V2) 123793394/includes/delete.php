<?php
require_once __DIR__ . '../connection.php';

if(isset($_GET['lidnummer']))
{
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

header("location: ../home_ledenlijst.php");
}

if(isset($_GET['postcode']))
{
    $postcode = $_GET['postcode'];

    $del_postcode_query = "DELETE FROM postcodes WHERE postcode='$postcode'";
    $del_postcode_result = $conn->query($del_postcode_query);
    if(!$del_postcode_result) die ("Verwijderen van postcode mislukt. <br>");

    header("location: ../postcodes.php");
}

$conn->close();
?>