<?php 
require_once __DIR__ . '../connection.php';

if(isset($_POST["add_member"])) 
{   
    $voornaam = get_post($conn, 'voornaam');
    $achternaam = get_post($conn, 'achternaam');
    $huisnummer = get_post($conn, 'huisnummer');
    $postcode = get_post($conn, 'postcode');
    $telnrs = get_post($conn, 'telnr');
    $emails = get_post($conn, 'emailadres');

    $stmt_lid = $conn->prepare('INSERT INTO leden (naam, voornaam, huisnummer, postcode) VALUES(?, ?, ?, ?)');
    $stmt_lid->bind_param('ssss', $achternaam, $voornaam, $huisnummer, $postcode);
    $stmt_lid->execute();  

    insert_contact_details($conn, $telnrs, 'telefoonnummers');
    insert_contact_details($conn, $emails, 'emails');

    header("location: ../home_ledenlijst.php");
    $stmt_lid->close();
}

if(isset($_POST["add_postcode"]))
{
    $postcode   = get_post($conn, "postcode");
    $adres      = get_post($conn, "straat");
    $woonplaats = get_post($conn, "woonplaats");

    $stmt = $conn->prepare('INSERT INTO postcodes VALUES(?, ?, ?)');
    $stmt->bind_param('sss', $postcode, $adres, $woonplaats);

    $stmt->execute();

    if($stmt->affected_rows != 1)
    { 
        echo '<script> alert("Postcode niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en/of probeer het opnieuw.") </script>';
        echo '<script> window.location.href = "../postcodes.php" </script>';         
    } 
    else
    {
        header("location: ../postcodes.php");
    }

    $stmt->close();    
}  

function insert_contact_details($conn, $input, $db_table)
{
    if($input != "")
    {
        $stmt = $conn->prepare('INSERT INTO ' . $db_table . ' VALUES (?, LAST_INSERT_ID())');

        $contacts_arr = explode(",", $input);  

        foreach($contacts_arr as $contact)
        {      
            $stmt->bind_param('s', $contact,);
            $stmt->execute();
        }
    }
}

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}
?> 