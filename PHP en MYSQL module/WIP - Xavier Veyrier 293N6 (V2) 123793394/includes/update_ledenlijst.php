<?php
require_once __DIR__ .'../connection.php';

$lidnummer = $_POST['lidnummer'];
$voornaam = $_POST['voornaam'];
$naam = $_POST['naam'];
$huisnummer = $_POST['huisnummer'];

$stmt = $conn->prepare('UPDATE leden SET voornaam=?, naam=?, huisnummer=? WHERE lidnummer=?');
$stmt->bind_param('sssi', $voornaam, $naam, $huisnummer, $lidnummer);
$stmt->execute();

$stmt->close();
$conn->close();

header("location: ../home_ledenlijst.php");
?>