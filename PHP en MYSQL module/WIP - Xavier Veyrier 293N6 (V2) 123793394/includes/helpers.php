<?php
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
                echo '<input type="hidden" name="num-telnrs" value="' . $num . '">';  
                echo '<td><input type="text" name="telefoonnummer' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '" maxlength="13" required></td>';
                echo '<input type="hidden" name="oud-telnr' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '">'; 
                echo '<td><a href="includes/delete.php?telefoonnummer=' . rawurlencode($subrow["telefoonnummer"]) . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>';                
            }
            elseif($db_table == 'emails') 
            { 
                echo '<td><b> Email' . " ". $num  . '</td></b>';
                echo '<input type="hidden" name="num-emails" value="' . $num . '">';  
                echo '<td><input type="email" name="email' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '" required></td>';
                echo '<input type="hidden" name="oud-email' . $num . '" value="' . htmlspecialchars($subrow[$db_column]) . '">'; 
                echo '<td><a href="includes/delete.php?email=' . rawurlencode($subrow["email"]) . '&lidnummer=' . $init_row["lidnummer"] . '">Delete</a></td>'; 
            }
            echo '</tr>';
        }
        $num += 1;
    } 
}
?>