<?php
require_once 'includes/connection.php';
require_once 'includes/functions.php';

if (isset($_POST['update_lid'])) {
    $lidnummer = get_post($conn, 'lidnummer');
    $num_telnrs = get_post($conn, 'num-telnrs');
    $num_emails = get_post($conn, 'num-emails');

    $num_data_affected = 0;
    foreach ($_POST as $data => $info) {        
        $info = get_post($conn, $data);

        if ($data == 'naam' || $data == 'voornaam' || $data == 'huisnummer' || $data == 'postcode') {
            $stmt_data = $conn->prepare("UPDATE leden SET " . $data . "=? WHERE lidnummer=?");
            $stmt_data->bind_param('si', $info, $lidnummer);
            $stmt_data->execute();

            $affected_info = $stmt_data->affected_rows;
            $stmt_data->close();
        }    
        $num_data_affected += $affected_info;      
    }
    
    for ($t = 0; $t < $num_telnrs; ++$t) {
        $telnr_num = $t + 1;
        $telnr_new = get_post($conn, 'telefoonnummer' . $telnr_num);
        $telnr_old = get_post($conn, 'oud-telnr' . $telnr_num);

        if ($telnr_new != $telnr_old) {    
            delete_row($conn, 'telefoonnummers', 'telefoonnummer', $telnr_old);                             
            $affected_row = insert_row($conn, 'telefoonnummers', $telnr_new, $lidnummer);
        }
    }
    $num_data_affected += $affected_row;

    for ($t = 0; $t < $num_emails; ++$t) {
        $email_num = $t + 1;
        $email_new = get_post($conn, 'email' . $email_num);
        $email_old = get_post($conn, 'oud-email' . $email_num);

        if($email_new != $email_old)
        {           
            delete_row($conn, 'emails', 'email', $email_old);       
            $affected_row = insert_row($conn, 'emails', $email_new, $lidnummer);
        }
    }
    $num_data_affected += $affected_row;

    if ($num_data_affected < 1) {
        echo "<span style='color:red'>" . "Het lijkt erop dat niets gewijzigd is. Ga terug, controleer de gegevens en of de postcode bestaat. Probeert u het opnieuw" . "</span>";  
    }

    $conn->close(); 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update lid</title>
        <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" /> 
    </head>
<body>
<?php
if (getNumDbTables($conn, $database) == 0) {echo "<span style='color:red'>" . "Geen tabellen in de database gevonden. Voeg deze eerst toe en probeer het opnieuw" . "</span>"; // Bij geen tabellen wordt dit getoond.
} else { 
?>
    <h1>Lid</h1>
    <a href='index.php'>Naar ledenoverzicht</a><br>
    <a href='postcodes.php'>Naar postcode overzicht</a>

    <?php
    if (!isset($_GET['lidnummer'])) { 
    ?>
    
    <h3>Voeg nieuw lid toe:</h3>

    <div class="leden-form">     
        <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
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
                    ?>
                </select>
            </label>
            <label for="emailadres">
                E-mailadres(sen):
                <!-- <input type="text" name="emailadres" placeholder="plaats bij meerdere email adressen deze op een nieuwe regel" multiple> -->
                <textarea name="emailadres" cols="45" rows="4" placeholder="Scheidt meerdere email adressen met een enter"></textarea>
            </label>
            <label for="telnr">
                Telefoonummer(s):
                <!-- <input type="text" name="telnr" placeholder="0611457894, +318826549524" multiple> -->
                <textarea name="telnr" cols="45" rows="4" placeholder="Scheidt meerdere telefoonnummers met een enter"></textarea>
            </label>
            <button type="submit" name='add_member'>Voeg lid toe</button>
        </form><br>
    </div>
    <?php
    } else {
        $lidnummer = $_GET['lidnummer'];
    ?>
    <h3>Lid:</h3>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
        <table>
            <tbody>
                <tr>
                    <th>Lidnummer</th>
                    <th>Voornaam</th>
                    <th>Voornaam</th>
                    <th>Voornaam</th>
                </tr>
                <?php 
                $select_lid_query = "SELECT * FROM leden 
                                        INNER JOIN postcodes ON postcodes.postcode = leden.postcode
                                        WHERE lidnummer='$lidnummer'";

                $select_lid_result = $conn->query($select_lid_query);
                if (!$select_lid_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                    Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

                $gegevens_lid = $select_lid_result->fetch_array(MYSQLI_ASSOC);

                foreach ($gegevens_lid as $data) {
                var_dump($data);
                    echo '<tr>';
                    echo '<td><b>' . ucfirst(htmlspecialchars($data)) . '</b></td>';
                    echo '<td>' . htmlspecialchars($info) . '</td>';
                    echo '</tr>';

                }            
                
                $select_lid_result->close();
                ?>
                <td colspan="2" style="text-align:center"><button type="submit" name="update_lid">Save</button></td>          
            </tbody>
        </table>
    </form>    
    <?php
    }
    ?>
<?php
} 
?>
</body>
</html>