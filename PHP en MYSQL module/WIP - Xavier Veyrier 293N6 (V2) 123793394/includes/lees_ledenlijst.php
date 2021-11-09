<?php
require_once __DIR__ .'../connection.php';

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
        if(isset($_GET['lidnummer']) && $row['lidnummer'] == $_GET['lidnummer'])
        {
            echo '<form action="includes/update_ledenlijst.php" method="POST">';
            echo '<td>' . $row["lidnummer"] . '</td>';
            echo '<td><input type="text" name="voornaam" value="' . $row['voornaam'] . '"></td>';
            echo '<td><input type="text" name="naam" value="' . $row['naam'] . '"></td>';
            echo '<td><input type="text" name="huisnummer" value="' . $row['huisnummer'] . '"></td>';
            echo '<td>' . $row["adres"] . '</td>';
            echo '<td><select id="postcode" name="postcode">';
                toon_table_form_postcodes($conn);
            echo '</select></td>';
            echo '<td>' . $row["woonplaats"] . '</td>';
            echo '<td><input type="email" name="email" value="';
                toon_contactgegevens('emails', $row, $conn, 'email', 'form_data');
            echo '"multiple></td>';
            echo '<td><input type="tel" name="telnrs" value="';
                toon_contactgegevens('telefoonnummers', $row, $conn, 'telefoonnummer', 'form_data');
            echo '"multiple></td>';         
            echo '<td><button type="submit">Save</button></td>';
            echo '<td>----</td>';
            echo '</form>';
        } else {           
            echo '<td>' . htmlspecialchars($row["lidnummer"])   . '</td>';
            echo '<td>' . htmlspecialchars($row["voornaam"])    . '</td>';
            echo '<td>' . htmlspecialchars($row["naam"])        . '</td>';
            echo '<td>' . htmlspecialchars($row["huisnummer"])  . '</td>';
            echo '<td>' . htmlspecialchars($row["adres"])       . '</td>';
            echo '<td>' . htmlspecialchars($row["postcode"])    . '</td>';
            echo '<td>' . htmlspecialchars($row["woonplaats"])  . '</td>';
            echo '<td>';
                toon_contactgegevens('emails', $row, $conn, 'email', 'table_data');
            echo '</td>';
            echo '<td>'; 
                toon_contactgegevens('telefoonnummers', $row, $conn, 'telefoonnummer', 'table_data');
            echo '</td>';  
            echo '<td><a href="home_ledenlijst.php?lidnummer=' . $row["lidnummer"] . '">Update</a></td>';
            echo '<td><a href="includes/delete_lid.php?lidnummer=' . $row["lidnummer"] . '">Delete</a></td>';                     
        }       
        echo "</tr>";
    }
}

$select_result->close();
$conn->close();

function toon_contactgegevens($db_table, $init_row, $connection, $db_column, $usage)
{
    $subquery = "SELECT * FROM $db_table WHERE lidnummer='$init_row[lidnummer]'";
    $subresult = $connection->query($subquery);
    if(!$subresult) die ("Er ging iets mis met het ophalen van de contactgegevens. Probeert u het nog een keer.");

    $subrows = $subresult->num_rows;

    $seperator = "";
    for($c = 0; $c < $subrows; ++$c)
    {
        $subrow = $subresult->fetch_array(MYSQLI_ASSOC);
        
        if($usage == "form_data")
        {
            echo $seperator . htmlspecialchars($subrow[$db_column]); 
            $seperator = ", ";        
        }
        elseif($usage == 'table_data')
        {
            echo htmlspecialchars($subrow[$db_column]) . "<br>";
        }
    } 
}

function toon_table_form_postcodes($conn)
{
    $postcode_query = "SELECT * FROM postcodes
                            ORDER BY postcode";           

    $postcode_result = $conn->query($postcode_query);
    if(!$postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
    Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $num_postcodes = $postcode_result->num_rows;
    $lidnummer = $_GET['lidnummer'];

    $lid_postcode_query = "SELECT postcode FROM leden WHERE lidnummer='$lidnummer'"; 
    $lid_postcode_result = $conn->query($lid_postcode_query);
    if(!$lid_postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
        Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

    $lid_row = $lid_postcode_result->fetch_array(MYSQLI_ASSOC);   
    $lid_postcode = htmlspecialchars($lid_row['postcode']);

    echo "<option value='$lid_postcode' selected>$lid_postcode</option>";
    for($p = 0; $p < $num_postcodes; ++$p)
    {
        $row = $postcode_result->fetch_array(MYSQLI_ASSOC);

        $postcode = htmlspecialchars($row['postcode']);
        echo "<option value='$postcode'>$postcode</option>"; 
    }
}
?>