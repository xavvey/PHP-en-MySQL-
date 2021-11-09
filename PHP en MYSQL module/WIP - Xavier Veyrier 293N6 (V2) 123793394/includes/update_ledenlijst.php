<?php
require_once __DIR__ .'../connection.php';

$lidnummer = $_POST['lidnummer'];
$voornaam = $_POST['voornaam'];
$achternaam = $_POST['naam'];
$huisnummer = $_POST['huisnummer'];
$postcode = $_POST['postcode'];

$stmt = $conn->prepare('UPDATE leden SET voornaam=?, naam=?, huisnummer=?, postcode=? WHERE lidnummer=?');
$stmt->bind_param('ssssi', $voornaam, $achternaam, $huisnummer, $postcode, $lidnummer );
$stmt->execute();

$stmt->close();
$conn->close();

header("location: ../home_ledenlijst.php");
?>