<?php
require_once __DIR__ . '../connection.php';

if(isset($_GET['lidnummer']))
{
    $lidnummer = $_GET["lidnummer"];

    $del_telnrs_query = "DELETE FROM telefoonnummers WHERE lidnummer='$lidnummer'";
    $del_telnrs_result = $conn->query($del_telnrs_query);
    if(!$del_telnrs_result) die ("<span style='color:red'>" . "Verwijderen van telefoonnummers mislukt. <br>" . "</span>");

    $del_mails_query = "DELETE FROM emails WHERE lidnummer='$lidnummer'";
    $del_mails_result = $conn->query($del_mails_query);
    if(!$del_mails_result) die ("<span style='color:red'>" . "Verwijderen van emails mislukt. <br>" . "</span>");

    $del_lid_query = "DELETE FROM leden WHERE lidnummer='$lidnummer'";
    $del_lid_result = $conn->query($del_lid_query);
    if(!$del_lid_result) die ("<span style='color:red'>" . "Verwijderen van leden mislukt. <br>" . "</span>");   

    header("location: ../home_ledenlijst.php");

    $del_telnrs_result->close();
    $del_mails_result->close();
    $del_lid_result ->close();
}

if(isset($_GET['postcode']))
{
    $postcode = $_GET['postcode'];

    $del_postcode_query = "DELETE FROM postcodes WHERE postcode='$postcode'";
    $del_postcode_result = $conn->query($del_postcode_query);
    if(!$del_postcode_result) die ("Verwijderen van postcode mislukt. <br>");

    header("location: ../postcodes.php");

    $del_postcode_result->close();
}

if(isset($_GET['telefoonnummer']))
{
    $telefoonnummer = $_GET['telefoonnummer'];
    $lidnummer = $_GET['lidnummer'];

    echo $telefoonnummer;
    echo $lidnummer;

    $del_tel_query = "DELETE FROM telefoonnummers WHERE telefoonnummer='$telefoonnummer'";
    $del_tel_result = $conn->query($del_tel_query);
    if(!$del_tel_result) die ("<span style='color:red'>" . "Verwijderen van telefoonnummer mislukt. Probeert u het opnieuw<br>" . "</span>");

    header("location: ../lid.php?lidnummer='" . $lidnummer  . ");

    $del_tel_result->close();
}

$conn->close();
?>