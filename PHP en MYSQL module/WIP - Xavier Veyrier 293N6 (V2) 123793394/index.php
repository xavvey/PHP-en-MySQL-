<!DOCTYPE html>
<html>
<head>
<title>Vereniging ledenlijst</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" />    
</head>
<body>

<?php 
// include 'includes/read.php';
require_once 'includes/connection.php';

$show_tables_query = "SHOW TABLES FROM vereniging";
$show_tables_result = $conn->query($show_tables_query);
if(!$show_tables_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

$num_tables = $show_tables_result->num_rows;

if($num_tables == 0) {echo "<span style='color:red'>" . "Geen tabellen in de database gevonden. Voeg deze eerst toe en probeer het opnieuw" . "</span>"; }
else {
?>

<div>
    <h1>Verenigingsoverzicht</h1>
    <a href='postcodes.php'>Naar postcode overzicht</a>
</div>

<div class="leden-form">     
    <h3>Voeg nieuw lid toe:</h3>
   
    <form action="includes/create.php" method="POST"><b>
        <label for="naam">
            Voornaam:
            <input type="text" name="voornaam" required>
        </label>
        <label for="achternaam">
            Achternaam:
            <input type="text" name="achternaam" required>
        </label>
        <label for="huisnummer">
            Huisnummer:
            <input type="text" name="huisnummer" required>
        </label>
        <label for="postcode">
            Postcode:
            <select name="postcode" required>
                <option disabled selected value>------</option>
                <?php     
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
                ?>
            </select>
        </label>
        <label for="emailadres">
            E-mailadres(sen):
            <input type="email" name="emailadres" placeholder="email1@mail.nl, email2@mail.com. email3@mail.nl" multiple>
        </label>
        <label for="telnr">
            Telefoonummer(s):
            <input type="text" name="telnr" placeholder="0611457894, +318826549524" multiple>
        </label></b>
        <button type="submit" name='add_member'>Voeg lid toe</button>
    </form><br>
</div>

<div>
    <h3>Leden:</h3>

    <table>
        <tbody>
            <?php  
            include 'includes/helpers.php';
            
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
            ?>
        </tbody>
    </table>
</div>
<?php } ?>
</body>
</html>