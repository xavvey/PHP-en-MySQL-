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

if(isset($_POST['add_telnr']))
{
    $telnr = get_post($conn, 'telefoonnummer');
    $lidnummer = get_post($conn, 'lidnummer');

    $stmt_telnr = $conn->prepare("INSERT INTO telefoonnummers VALUES(?,?)");
    $stmt_telnr->bind_param('si', $telnr, $lidnummer);
    $stmt_telnr->execute();

    if($stmt_telnr->affected_rows != 1)
    { 
        echo '<script> alert("Telefoonnummer niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en/of probeer het opnieuw.") </script>';
        echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';         
    } 
    else
    {
        header("location: ../lid.php?lidnummer=$lidnummer");
    }

    $stmt_telnr->close();
}

if(isset($_POST['add_email']))
{
    $email = get_post($conn, 'email');
    $lidnummer = get_post($conn, 'lidnummer');

    $stmt_email = $conn->prepare("INSERT INTO emails VALUES(?,?)");
    $stmt_email->bind_param('si', $email, $lidnummer);
    $stmt_email->execute();

    if($stmt_email->affected_rows != 1)
    { 
        echo '<script> alert("Emailadres niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en/of probeer het opnieuw.") </script>';
        echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';         
    } 
    else
    {
        header("location: ../lid.php?lidnummer=$lidnummer");
    }

    $stmt_email->close();
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