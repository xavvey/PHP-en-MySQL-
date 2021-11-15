<?php
require_once __DIR__ .'../connection.php';

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
        echo '<td>Lidnummer</td>';
        echo '<td>Voornaam</td>';
        echo '<td>Achternaam</td>';
        echo '<td>Huisnummer</td>';
        echo '<td>Straat</td>';
        echo '<td>Postcode</td>';
        echo '<td>Woonplaats</td>';
        echo '<td>E-mailadres(sen)</td>';
        echo '<td>Telefoonnummer(s)</td>';
        echo '<td>Update</td>';
        echo '<td>Delete</td>';
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
                            JOIN postcodes
                            WHERE lidnummer='$lidnummer'";

    $select_lid_result = $conn->query($select_lid_query);
    if(!$select_lid_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                                Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $gegevens_lid = $select_lid_result->fetch_array(MYSQLI_ASSOC);
    print_r($gegevens_lid);
    echo '<br>';
    print_r($_GET);
    echo '<br>';
    print_r(array_keys($_GET));

    foreach($gegevens_lid as $data => $info)
    {
        // The part after the first if() doesn't work-> it changes all rows, I know
        if($gegevens_lid[$data] == array_keys($_GET))
        {
            echo '<tr>';
            echo '<form action="update.php method="POST"';
            echo '<td><b>' . ucfirst(htmlspecialchars($data)) . '</b></td>';
            echo '<td><input type="text" name="' . $data . '" value="' . $gegevens_lid[$info] . '"></td>';
            echo '<input type="hidden" name="lidnummer" value="' . $gegevens_lid['lidnummer'] . '">';
            echo '<td><button type="submit">Save</button></td>';
            echo '</form>';
            echo '</tr>';
        }
        else
        {
            echo '<tr>';
            if($data == 'lidnummer' || $data == 'adres' || $data == 'woonplaats')
            {
                
                echo '<td><b>' . ucfirst(htmlspecialchars($data)) . '</b></td>';
                echo '<td>' . htmlspecialchars($info) . '</td>';
                echo '<td> ---- </td>';
                echo '<td> ---- </td>';
                
            } 
            else
            {
                echo '<td><b>' . ucfirst(htmlspecialchars($data)) . '</b></td>';
                echo '<td>' . htmlspecialchars($info) . '</td>';
                echo '<td><a href="lid.php?lidnummer=' . $gegevens_lid['lidnummer'] . '&' . $data . '=' . $info . '">Update</td>';
                echo '<td> ---- </td>';
            }
            echo '</tr>';
        }
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
                echo '<td><a href="lid.php?lidnummer=' . $init_row['lidnummer'] . '&telefoonnummer=' . $subrow['telefoonnummer'] . '">Update</td>';
                echo '<td><a href="includes/delete.php?telefoonnummer=' . $subrow["telefoonnummer"] . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>'; 
                echo '</tr>';
            }
            elseif($db_table == 'emails') 
            { 
            echo '<td><b> Email' . " ". $num  . '</td></b>'; 
            echo '<td>' . htmlspecialchars($subrow[$db_column]) . '</td>';
            echo '<td><a href="lid.php?lidnummer=' . $init_row['lidnummer'] . '&email=' . $subrow['email'] . '">Update</td>';
            echo '<td><a href="includes/delete.php?email=' . $subrow["email"] . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>'; 
            echo '</tr>';
            }
            $num += 1;
        }
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
?>