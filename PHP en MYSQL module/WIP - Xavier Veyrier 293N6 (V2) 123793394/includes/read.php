<?php
require_once __DIR__ .'../connection.php';

function read_db_tables($conn)
{
    $show_tables_query = "SHOW TABLES FROM vereniging";
    $show_tables_result = $conn->query($show_tables_query);
    if(!$show_tables_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
    Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $num_tables = $show_tables_result->num_rows;
    return $num_tables;
}

function show_member_table($conn)
{
    $select_query = "SELECT * FROM leden  
                        NATURAL JOIN postcodes
                        ORDER BY lidnummer";

    $select_result = $conn->query($select_query);
    if(!$select_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                                Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $num_members = $select_result->num_rows;

    if($num_members == 0) { echo "<h2>Er zijn geen leden gevonden in de database.</h2>";
    } else {
        echo '<tr>';
        echo '<th>Lidnummer</th>';
        echo '<th>Voornaam</th>';
        echo '<th>Achternaam</th>';
        echo '<th>Huisnummer</th>';
        echo '<th>Straat</th>';
        echo '<th>Postcode</th>';
        echo '<th>Woonplaats</th>';
        echo '<th>E-mailadres(sen)</th>';
        echo '<th>Telefoonnummer(s)</th>';
        echo '<th>Update</th>';
        echo '<th>Delete</th>';
        echo '<tr>';

        for ($j = 0 ; $j < $num_members ; ++$j)
        { 
        $row = $select_result->fetch_array(MYSQLI_ASSOC);   

        echo '<tr>';         
        echo '<td>' . htmlspecialchars($row["lidnummer"])   . '</td>';
        echo '<td>' . htmlspecialchars($row["voornaam"])    . '</td>';
        echo '<td>' . htmlspecialchars($row["naam"])        . '</td>';
        echo '<td>' . htmlspecialchars($row["huisnummer"])  . '</td>';
        echo '<td>' . htmlspecialchars($row["adres"])       . '</td>';
        echo '<td>' . htmlspecialchars($row["postcode"])    . '</td>';
        echo '<td>' . htmlspecialchars($row["woonplaats"])  . '</td>';
        echo '<td>';
        toon_contactgegevens('emails', $row, $conn, 'email', 'leden_table');
        echo '</td>';
        echo '<td>'; 
        toon_contactgegevens('telefoonnummers', $row, $conn, 'telefoonnummer', 'leden_table');
        echo '</td>';  
        echo '<td><a href="lid.php?lidnummer=' . $row["lidnummer"] . '">Update lid</a></td>';
        echo '<td><a href="includes/delete.php?lidnummer=' . $row["lidnummer"] . '">Delete</a></td>';                           
        echo "</tr>";
        }
    }

    $select_result->close();
    $conn->close();
}

function show_single_lid($conn, $lidnummer)
{
    $select_lid_query = "SELECT * FROM leden 
                            NATURAL JOIN postcodes
                            WHERE lidnummer='$lidnummer'";

    $select_lid_result = $conn->query($select_lid_query);
    if(!$select_lid_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                                Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $gegevens_lid = $select_lid_result->fetch_array(MYSQLI_ASSOC);

    // parses url to check which row in member table is clicked on -> function below
    $current_url = parse_url(curPageURL());
    parse_str($current_url['query'], $url_params_assoc);
    $url_param_keys = array_keys($url_params_assoc);
    $url_param_index = array_values($url_params_assoc);

    foreach($gegevens_lid as $data => $info)
    {
        echo '<tr>';
        echo '<td><b>' . ucfirst(htmlspecialchars($data)) . '</b></td>';
        if($url_param_index[1] == $info) 
        { 
            echo '<td><form action="includes/update.php" method="POST"';                  
            echo '<td><input type="text" name="' . $data . '" value="' . $info . '" required></td>';
            echo '<input type="hidden" name="lidnummer" value="' . $gegevens_lid['lidnummer'] . '">';
            echo '<td><button type="submit">Save</button></td>';
            echo '<td>----</td>';
            echo '</form></td>';
        }
        elseif($url_param_keys[1] == 'postcode' && $url_param_index[1] == $info)
        {
            echo '<td><form action="includes/update.php" method="POST"';                  
            echo '<td><input type="text" pattern="^[1-9][0-9]{3}[\s]?[A-Za-z]{2}" name="' . $data . '" value="' . $info . '" required></td>';
            echo '<input type="hidden" name="lidnummer" value="' . $gegevens_lid['lidnummer'] . '">';
            echo '<td><button type="submit">Save</button></td>';
            echo '<td>----</td>';
            echo '</form></td>';
        }
        else
        {
            if($data == 'lidnummer' || $data == 'adres' || $data == 'woonplaats')
            {
                echo '<td>' . htmlspecialchars($info) . '</td>';
                echo '<td> ---- </td>';
                echo '<td> ---- </td>';
                
            } 
            else
            {
                echo '<td>' . htmlspecialchars($info) . '</td>';
                echo '<td><a href="lid.php?lidnummer=' . $gegevens_lid['lidnummer'] . '&' . $data . '=' . $info . '">Update</td>';
                echo '<td> ---- </td>';
            }            
        }
        echo '</tr>';
    }

    toon_contactgegevens('telefoonnummers', $gegevens_lid, $conn, 'telefoonnummer', 'lid_table');
    toon_contactgegevens('emails', $gegevens_lid, $conn, 'email', 'lid_table');

    $select_lid_result->close();
    $conn->close();
}

function toon_contactgegevens($db_table, $init_row, $connection, $db_column, $usage)
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
                echo '<td>' . htmlspecialchars($subrow[$db_column]) . '</td>';
                echo '<td>----</td>';
                echo '<td><a href="includes/delete.php?telefoonnummer=' . $subrow["telefoonnummer"] . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>'; 
                echo '</tr>';
            }
            elseif($db_table == 'emails') 
            { 
                echo '<td><b> Email' . " ". $num  . '</td></b>'; 
                echo '<td>' . htmlspecialchars($subrow[$db_column]) . '</td>';
                echo '<td>----</td>';
                echo '<td><a href="includes/delete.php?email=' . $subrow["email"] . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>'; 
                echo '</tr>';
            }
        }
        $num += 1;
    } 
}

function show_postcode_table($conn)
{
    $postcodes_query = "SELECT * FROM postcodes
    ORDER BY postcode";

    $result = $conn->query($postcodes_query);
    if(!$result) die ("<span style='color:red'>" . "Kon geen postcodes ophalen van de database. Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw". "</span>");
    $rows = $result->num_rows;

    if($rows < 1) { echo '<h3>Er staan nog geen postcodes in de database. Voeg eerst een postcode toe.</h3>'; }
    else 
    {
        echo '<table>';
        echo '<tbody>';
        echo '<tr>';
        echo '<th>Postcode    </th>';
        echo '<th>Straat      </th>';
        echo '<th>Woonplaats  </th>';
        echo '<th>Update      </th>';
        echo '<th>Delete      </th>';
        echo '</tr>';

        for($j = 0 ; $j < $rows ; ++$j) 
        { 
            $row = $result->fetch_array(MYSQLI_ASSOC);

            echo '<tr>';
            if($row['postcode'] == $_GET['postcode'])
            {
                echo '<form action="includes/update.php" method="POST">';
                echo '<td>' . htmlspecialchars($row["postcode"])    . '</td>';
                echo '<td><input type="text" name="adres" value="' . htmlspecialchars($row["adres"]) . '" required></td>';
                echo '<td><input type="text" name="woonplaats" value="' . htmlspecialchars($row["woonplaats"]) . '" required></td>';
                echo '<td><button type="subbmit">Save</td>';
                echo '<td>----</td>';
                echo '<input type="hidden" name="postcode" value="'. $row['postcode'] . '">';
                echo '</form>';
            }
            else
            {              
                echo '<td>' . htmlspecialchars($row["postcode"])    . '</td>';
                echo '<td>' . htmlspecialchars($row["adres"])       . '</td>';
                echo '<td>' . htmlspecialchars($row["woonplaats"])  . '</td>';
                echo '<td><a href="postcodes.php?postcode=' . $row['postcode'] . '">Update</a></td>';
                echo '<td><a href="includes/delete.php?postcode=' . $row["postcode"] . '">Delete</a></td>';
                
            }
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }
}

function show_postcode_dropdown($conn)
{
    $postcode_query = "SELECT * FROM postcodes
                        ORDER BY postcode"; 
        

    $postcode_result = $conn->query($postcode_query);
    if(!$postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                                    Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $num_postcodes = $postcode_result->num_rows;

    for($p = 0; $p < $num_postcodes; ++$p)
    {
        $row = $postcode_result->fetch_array(MYSQLI_ASSOC);

        $postcode = htmlspecialchars($row['postcode']);
        $straat = htmlspecialchars($row['adres']);
        $woonplaats = htmlspecialchars($row['woonplaats']);

        echo "<option value='$postcode'>" . $postcode . " - " . $straat . " - " . $woonplaats . "</option>"; 
    }
}

function curPageURL() 
{
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
     $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
     $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

?>