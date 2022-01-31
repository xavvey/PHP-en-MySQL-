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

function insertEmails($conn, $input, $lidnummer=null)
{
    if ($input == "") {
        return;
    } else {
        if (empty($lidnummer)) {
            $stmt = $conn->prepare('INSERT INTO emails VALUES (?, LAST_INSER_ID())');
            $contacts_arr = explode('\r\n', $input);
            foreach($contacts_arr as $contact) {      
                $stmt->bind_param('s', $contact);
                $stmt->execute();
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare('INSERT INTO emails VALUES (?, ?)');
            $contacts_arr = explode('\r\n', $input);
            foreach($contacts_arr as $contact) {      
                $stmt->bind_param('si', $contact, $lidnummer);
                $stmt->execute();
            }
            $stmt->close();
        }      
    }
}

function insertTelnrs($conn, $input, $lidnummer="LAST_INSERT_ID()")
{
    if ($input == "") {
        return;
    } else {
        $stmt = $conn->prepare('INSERT INTO telefoonnummers VALUES (?, ?)');

        $contacts_arr = explode('\r\n', $input); // alleen \n werkt niet in textarea input field

        foreach($contacts_arr as $contact) {      
            $stmt->bind_param('si', $contact, $lidnummer);
            $stmt->execute();
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

    if($affected_rows == 0) {
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

// function queryMemberContacts($conn, $init_row, $db_table)
// {
//     $subquery = "SELECT * FROM $db_table WHERE lidnummer='$init_row[lidnummer]'";
//     $subresult = $conn->query($subquery);
//     if(!$subresult) die ("<span style='color:red'>" . "Er ging iets mis met het ophalen van de contactgegevens. Probeert u het nog een keer." . "</span>");
    
//     return $subresult;
// }

// function show_member_contacts($db_table, $init_row, $connection, $db_column, $usage) // 4x gebruikt
// {
//     $subquery = "SELECT * FROM $db_table WHERE lidnummer='$init_row[lidnummer]'";
//     $subresult = $connection->query($subquery);
//     if(!$subresult) die ("<span style='color:red'>" . "Er ging iets mis met het ophalen van de contactgegevens. Probeert u het nog een keer." . "</span>");

//     $subrows = $subresult->num_rows;

//     $num = 1;
//     for($c = 0; $c < $subrows; ++$c)
//     {
//         $subrow = $subresult->fetch_array(MYSQLI_ASSOC);

//         if($usage == 'leden_table')
//         {            
//             echo htmlspecialchars($subrow[$db_column]) . "<br>";
//         }
//         elseif($usage == 'lid_table')
//         {   
//             echo '<tr>';    
//             if($db_table == 'telefoonnummers')
//             { 
//                 echo '<td><b> Telefoonnummer' . " ". $num  . '</b></td>';
//                 echo '<input type="hidden" name="num-telnrs" value="' . $num . '">';  
//                 echo '<td><input type="text" name="telefoonnummer' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '" maxlength="13" required></td>';
//                 echo '<input type="hidden" name="oud-telnr' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '">'; 
//                 echo '<td><a href="lid.php?telefoonnummer=' . rawurlencode($subrow["telefoonnummer"]) . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>';                
//             }
//             elseif($db_table == 'emails') 
//             { 
//                 echo '<td><b> Email' . " ". $num  . '</td></b>';
//                 echo '<input type="hidden" name="num-emails" value="' . $num . '">';  
//                 echo '<td><input type="email" name="email' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '" required></td>';
//                 echo '<input type="hidden" name="oud-email' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '">'; 
//                 echo '<td><a href="lid.php?email=' . rawurlencode($subrow["email"]) . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>'; 
//             }
//             echo '</tr>';
//         }
//         $num += 1;
//     } 

//     $subresult->close();
// }


function insert_row($conn, $db_table, $new_data, $lidnummer) // 4x gebruikt
{
    $stmt_ins_row = $conn->prepare("INSERT INTO $db_table VALUES(?,?)");
    $stmt_ins_row->bind_param('si', $new_data, $lidnummer);
    $stmt_ins_row->execute();

    $affected_row = $stmt_ins_row->affected_rows;
    $stmt_ins_row->close;

    return $affected_row;
}

// function delete_row($conn, $db_table, $db_column, $row_reference) // 8x gebruikt
// {
//     $stmt_del_row = $conn->prepare("DELETE FROM $db_table WHERE $db_column=?");
//     $stmt_del_row->bind_param('s', $row_reference);
//     if(!$stmt_del_row->execute()) die ("<span style='color:red'>" . "Verwijderen van " . $db_column . " mislukt. Probeert u het opnieuw<br>" . "</span>");
//     $stmt_del_row->close();
// }

?>