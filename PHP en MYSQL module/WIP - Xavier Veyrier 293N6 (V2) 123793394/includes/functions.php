<?php
require_once __DIR__ . '../connection.php';

function getNumDbTables($conn, $database) 
{
    $show_tables_query = "SHOW TABLES FROM $database";
    $show_tables_result = $conn->query($show_tables_query);
    $num_tables = $show_tables_result->num_rows;

    return $num_tables;
}

function selectPostcodeOptions($conn) 
{
    $postcode_query = "SELECT * FROM postcodes
                        ORDER BY postcode"; 

    $postcode_result = $conn->query($postcode_query);

    if (!$postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                                    Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $num_postcodes = $postcode_result->num_rows;

    for ($p = 0; $p < $num_postcodes; ++$p) {
        $row = $postcode_result->fetch_array(MYSQLI_ASSOC);

        $postcode = htmlspecialchars($row['postcode']);
        $straat = htmlspecialchars($row['adres']);
        $woonplaats = htmlspecialchars($row['woonplaats']);

        echo "<option value='$postcode'>" . $postcode . " - " . $straat . " - " . $woonplaats . "</option>"; 
    } 

    $postcode_result->close();
}

function insertEmails($conn, $input, $lidnummer)
{
    if ($input == "") { return; }   
    else {
        $stmt = $conn->prepare('INSERT INTO emails VALUES (?, ?)');
        $contacts_arr = explode('\r\n', $input);
        
        foreach($contacts_arr as $contact) {  
            if ($contact == "") { 
                continue;   
            } else {
                $stmt->bind_param('si', $contact, $lidnummer);
                $stmt->execute();
            }
        }
        $stmt->close();  
    }
}

function insertTelnrs($conn, $input, $lidnummer)
{
    if ($input == "") { return; }   
    else {
        $stmt = $conn->prepare('INSERT INTO telefoonnummers VALUES (?, ?)');
        $contacts_arr = explode('\r\n', $input); 
   
        foreach($contacts_arr as $contact) {
            if ($contact == "") {
                continue;
            } else {
                $stmt->bind_param('si', $contact, $lidnummer);
                $stmt->execute();
            }
        }   
        $stmt->close();
    }   
}

function deleteEmails($conn, $lidnummer)
{
    $del_email_stmt = $conn->prepare("DELETE FROM emails WHERE lidnummer=?");
    $del_email_stmt->bind_param('i', $lidnummer);
    $del_email_stmt->execute();
    $del_email_stmt->close();
}

function deleteTelnrs($conn, $lidnummer)
{
    $del_tel_stmt = $conn->prepare("DELETE FROM telefoonnummers WHERE lidnummer=?");
    $del_tel_stmt->bind_param('i', $lidnummer);
    $del_tel_stmt->execute();
    $del_tel_stmt->close();
}

function showErrorOrRedirect($affected_rows, $message, $redirect_page)
{
    $page = $redirect_page . ".php";

    if($affected_rows <= 0) { // niet $affected_rows == 0 -> bij bestaande postcode $stmt->affected_rows = -1 
        echo "<span style='color:red'>" . $message . "</span>";       
    } else {
        header("Location: $page");
        exit;
    }
}

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}
?>