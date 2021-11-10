<?php
require_once __DIR__ .'../connection.php';

$lidnummer = $_POST['lidnummer'];
$voornaam = $_POST['voornaam'];
$achternaam = $_POST['naam'];
$huisnummer = $_POST['huisnummer'];
$postcode = $_POST['postcode'];
$emails = $_POST['email'];
$telnr = $_POST['telnrs'];

$stmt = $conn->prepare('UPDATE leden SET voornaam=?, naam=?, huisnummer=?, postcode=? WHERE lidnummer=?');
$stmt->bind_param('ssssi', $voornaam, $achternaam, $huisnummer, $postcode, $lidnummer );
$stmt->execute();

$stmt->close();
$conn->close();

function update_contact_details($conn, $input, $db_table, $db_column, $lidnummer)
{
    if($input != "")
    {
        $stmt = $conn->prepare('UPDATE ' . $db_table . ' SET ' . $db_column . '=? WHERE lidnummer=?');

        $contacts_arr = explode(",", $input);  

        foreach($contacts_arr as $contact)
        {      
            $stmt->bind_param('ssi', $contact,);
            $stmt->execute();
        }
    }
}


header("location: ../home_ledenlijst.php");
?>