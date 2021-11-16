<?php
require_once __DIR__ .'../connection.php';

if(isset($_POST['naam']))
{
    $lidnummer = get_post($conn, 'lidnummer');
    $naam = get_post($conn, 'naam');
    
    $stmt_naam = $conn->prepare('UPDATE leden SET naam=? WHERE lidnummer=?');
    $stmt_naam->bind_param('si', $naam, $lidnummer);
    $stmt_naam->execute(); 
    
    if($stmt_naam->affected_rows != 1)
    {
        echo '<script> alert("Naam niet aangepast. Controleer of deze hetzelfde is of probeer het opnieuw.") </script>';
        echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';         
    } 
    else
    {
        header("location: ../lid.php?lidnummer=$lidnummer");
    }
    
    $stmt_naam->close();    
}

if(isset($_POST['voornaam']))
{
    $lidnummer = get_post($conn, 'lidnummer');
    $voornaam = get_post($conn, 'voornaam');
    
    $stmt_voornaam = $conn->prepare('UPDATE leden SET voornaam=? WHERE lidnummer=?');
    $stmt_voornaam->bind_param('si', $voornaam, $lidnummer);
    $stmt_voornaam->execute(); 
    
    if($stmt_voornaam->affected_rows != 1)
    {
        echo '<script> alert("Voornaam niet aangepast. Controleer of deze hetzelfde is of probeer het opnieuw.") </script>';
        echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';         
    } 
    else
    {
        header("location: ../lid.php?lidnummer=$lidnummer");
    }
    
    $stmt_voornaam->close();    
}

if(isset($_POST['huisnummer']))
{
    $lidnummer = get_post($conn, 'lidnummer');
    $huisnummer = get_post($conn, 'huisnummer');
    
    $stmt_huisnummer = $conn->prepare('UPDATE leden SET huisnummer=? WHERE lidnummer=?');
    $stmt_huisnummer->bind_param('si', $huisnummer, $lidnummer);
    $stmt_huisnummer->execute(); 
    
    if($stmt_huisnummer->affected_rows != 1)
    {
        echo '<script> alert("Huisnummer niet aangepast. Controleer of deze hetzelfde is of probeer het opnieuw.") </script>';
        echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';         
    } 
    else
    {
        header("location: ../lid.php?lidnummer=$lidnummer");
    }
    
    $stmt_huisnummer->close();    
}

if(isset($_POST['postcode']))
{
    $lidnummer = get_post($conn, 'lidnummer');
    $postcode = get_post($conn, 'postcode');
    
    $stmt_postcode = $conn->prepare('UPDATE leden SET postcode=? WHERE lidnummer=?');
    $stmt_postcode->bind_param('si', $postcode, $lidnummer);
    $stmt_postcode->execute(); 
    
    if($stmt_postcode->affected_rows != 1)
    {
        echo '<script> alert("Postcode niet aangepast. Controleer of deze voldoet aan format 1234AB , of de postcode al is toegevoegd, of probeer het opnieuw") </script>';
        echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';         
    } 
    else
    {
        header("location: ../lid.php?lidnummer=$lidnummer");
    }
    
    $stmt_postcode->close();    
}

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}
?>