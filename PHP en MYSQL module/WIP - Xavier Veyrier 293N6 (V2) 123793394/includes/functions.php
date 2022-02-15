<?php
require_once __DIR__ . '/connection.php';

function getNumDbTables($conn, $database) //2x gebruikt
{
    $show_tables_query = "SHOW TABLES FROM $database";
    $show_tables_result = $conn->query($show_tables_query);
    $num_tables = $show_tables_result->num_rows;

    return $num_tables;
}

function insertEmails($conn, $input, $lidnummer) // 2x gebruikt
{
    if ($input == "") { return; }   
    else {
        $stmt = $conn->prepare('INSERT INTO emails VALUES (?, ?)');
        $contacts_arr = explode(',', $input);
        
        foreach($contacts_arr as $contact) {  
            if ($contact == "") { 
                continue;   
            } else {
                $stmt->bind_param('si', $contact, $lidnummer);
                if (!$stmt->execute()) {
                    $_SESSION["message"] = "Niet alle contactgegevens toegevoegd. Wellicht dat sommige al gebruikt worden of dat u ze dubbel invoert? Controleer de email adressen/telefoonnummers en/of probeert u het nog een keer.";
                }
            }
        }
        $stmt->close();  
    }
}

function insertTelnrs($conn, $input, $lidnummer) //2x gebruikt
{
    if ($input == "") { return; }   
    else {
        $stmt = $conn->prepare('INSERT INTO telefoonnummers VALUES (?, ?)');
        $contacts_arr = explode(',', $input); 
   
        foreach($contacts_arr as $contact) {
            if ($contact == "") {
                continue;
            } else {
                $stmt->bind_param('si', $contact, $lidnummer);
                if (!$stmt->execute()) {
                    $_SESSION["message"] = "Niet alle contactgegevens toegevoegd. Wellicht dat sommige al gebruikt worden of dat u ze dubbel invoert? Controleer de email adressen/telefoonnummers en/of probeert u het nog een keer.";
                }
            }
        }   
        $stmt->close();
    }   
}

function insertPostcode($conn, $postcode, $adres, $woonplaats) // 2x gebruikt
{
    $stmt = $conn->prepare('INSERT INTO postcodes VALUES(?, ?, ?)');
    $stmt->bind_param('sss', $postcode, $adres, $woonplaats);

    if(!$stmt->execute()) {
        $_SESSION["message"] = "Postcode niet toegevoegd. Mischien bestaat deze al. Controleert u de gegevens en/of probeert u het opnieuw";
    }
}

function deleteEmails($conn, $lidnummer) //2x gebruikt
{
    $del_email_stmt = $conn->prepare("DELETE FROM emails WHERE lidnummer=?");
    $del_email_stmt->bind_param('i', $lidnummer);
    $del_email_stmt->execute();
    $del_email_stmt->close();
}

function deleteTelnrs($conn, $lidnummer) //2x gebruikt
{
    $del_tel_stmt = $conn->prepare("DELETE FROM telefoonnummers WHERE lidnummer=?");
    $del_tel_stmt->bind_param('i', $lidnummer);
    $del_tel_stmt->execute();
    $del_tel_stmt->close();
}

function deletePostcode($conn, $postcode) //2x gebruikt
{
    $stmt = $conn->prepare("DELETE FROM postcodes WHERE postcode=?");
    $stmt->bind_param('s', $postcode);
    
    if(!$stmt->execute()) {
        $_SESSION['message'] = "Kon postcode niet verwijderen. Waarschijnlijk is deze in gebruik. Controleer of deze in gebruik is en/of probeert u het opnieuw.";
    } 

    return $stmt->affected_rows;
}

function get_post($conn, $var) //1x gebruikt
{
    return $conn->real_escape_string($_POST[$var]);
}
?>