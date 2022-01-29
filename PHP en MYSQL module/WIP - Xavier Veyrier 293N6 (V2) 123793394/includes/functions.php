<?php
require_once __DIR__ . '../connection.php';

function getNumDbTables($conn, $database) // 3x gebruikt
{
    $show_tables_query = "SHOW TABLES FROM $database";
    $show_tables_result = $conn->query($show_tables_query);
    $num_tables = $show_tables_result->num_rows;

    return $num_tables;
}

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}

function show_member_contacts($db_table, $init_row, $connection, $db_column, $usage) // 4x gebruikt
{
    $subquery = "SELECT * FROM $db_table WHERE lidnummer='$init_row[lidnummer]'";
    $subresult = $connection->query($subquery);
    if(!$subresult) die ("<span style='color:red'>" . "Er ging iets mis met het ophalen van de contactgegevens. Probeert u het nog een keer." . "</span>");

    $subrows = $subresult->num_rows;

    $num = 1;
    for($c = 0; $c < $subrows; ++$c)
    {
        $subrow = $subresult->fetch_array(MYSQLI_ASSOC);

        if($usage == 'leden_table')
        {            
            echo htmlspecialchars($subrow[$db_column]) . "<br>";
        }
        elseif($usage == 'lid_table')
        {   
            echo '<tr>';    
            if($db_table == 'telefoonnummers')
            { 
                echo '<td><b> Telefoonnummer' . " ". $num  . '</b></td>';
                echo '<input type="hidden" name="num-telnrs" value="' . $num . '">';  
                echo '<td><input type="text" name="telefoonnummer' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '" maxlength="13" required></td>';
                echo '<input type="hidden" name="oud-telnr' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '">'; 
                echo '<td><a href="lid.php?telefoonnummer=' . rawurlencode($subrow["telefoonnummer"]) . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>';                
            }
            elseif($db_table == 'emails') 
            { 
                echo '<td><b> Email' . " ". $num  . '</td></b>';
                echo '<input type="hidden" name="num-emails" value="' . $num . '">';  
                echo '<td><input type="email" name="email' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '" required></td>';
                echo '<input type="hidden" name="oud-email' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '">'; 
                echo '<td><a href="lid.php?email=' . rawurlencode($subrow["email"]) . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>'; 
            }
            echo '</tr>';
        }
        $num += 1;
    } 

    $subresult->close();
}

function insert_contact_details($conn, $input, $db_table) // 2x gebruikt
{
    if ($input != "") {
        $stmt = $conn->prepare('INSERT INTO ' . $db_table . ' VALUES (?, LAST_INSERT_ID())');

        $contacts_arr = explode('\r\n', $input); 
        
        // $contacts_arr = preg_split('/\r\n|[\r\n]/', $input); 

        foreach($contacts_arr as $contact) {      
            $stmt->bind_param('s', $contact,);
            $stmt->execute();
        }
        $stmt->close();
    }
}

function insert_row($conn, $db_table, $new_data, $lidnummer) // 4x gebruikt
{
    $stmt_ins_row = $conn->prepare("INSERT INTO $db_table VALUES(?,?)");
    $stmt_ins_row->bind_param('si', $new_data, $lidnummer);
    $stmt_ins_row->execute();

    $affected_row = $stmt_ins_row->affected_rows;
    $stmt_ins_row->close;

    return $affected_row;
}

function delete_row($conn, $db_table, $db_column, $row_reference) // 8x gebruikt
{
    $stmt_del_row = $conn->prepare("DELETE FROM $db_table WHERE $db_column=?");
    $stmt_del_row->bind_param('s', $row_reference);
    if(!$stmt_del_row->execute()) die ("<span style='color:red'>" . "Verwijderen van " . $db_column . " mislukt. Probeert u het opnieuw<br>" . "</span>");
    $stmt_del_row->close();
}

?>