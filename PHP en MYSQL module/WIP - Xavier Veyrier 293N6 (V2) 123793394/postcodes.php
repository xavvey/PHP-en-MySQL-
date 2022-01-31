<?php
require_once 'includes/connection.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $postcode = get_post($conn, "postcode");
    $adres = get_post($conn, "straat");
    $woonplaats = get_post($conn, "woonplaats");

    if (isset($_POST["add_postcode"])) {
        $stmt = $conn->prepare('INSERT INTO postcodes VALUES(?, ?, ?)');
        $stmt->bind_param('sss', $postcode, $adres, $woonplaats);
        $stmt->execute();
        $affected_rows = $stmt->affected_rows;

        showErrorOrRedirect(
            $affected_rows, 
            "Postcode niet toegevoegd. Waarschijnlijk bestaat deze al. Probeer het opnieuw.",
            "postcodes",
        );

        $stmt->close();
    }

    if (isset($_POST['update_postcode'])) {
        $stmt = $conn->prepare('UPDATE postcodes SET adres=?, woonplaats=? WHERE postcode=?');
        $stmt->bind_param('sss', $adres, $woonplaats, $postcode);
        $stmt->execute();
        $affected_rows = $stmt->affected_rows;

        showErrorOrRedirect(
            $affected_rows, 
            "Postcode niet aangepast. Het lijkt erop dat niets gewijzigd is. Controleer de gegevens en probeer het opnieuw.",
            "postcodes",
        );

        $stmt->close();
    }

    if (isset($_POST['delete_postcode'])) {
        $stmt = $conn->prepare("DELETE FROM postcodes WHERE postcode=?");
        $stmt->bind_param('s', $postcode);
        $stmt->execute();
        $affected_rows = $stmt->affected_rows;

        showErrorOrRedirect(
            $affected_rows, 
            "Verwijderen van postcode mislukt. Probeert u het opnieuw",
            "postcodes",
        );

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Postcodes wijzigen</title>
        <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" /> 
    </head>
<body>
    <?php
    if (getNumDbTables($conn, $database) == 0) {echo "<span style='color:red'>" . "Geen tabellen in de database gevonden. Voeg deze eerst toe en probeer het opnieuw" . "</span>"; // Bij geen tabellen wordt dit getoond.
    } else { 
        ?>
        <h1>Postcode overzicht</h1>
        <a href='index.php'>Naar ledenoverzicht</a> 

        <?php
        if ($_GET['postcode']) {
            $postcode = $_GET['postcode'];

            $stmt = $conn->prepare("SELECT * FROM postcodes WHERE postcode=?");
            $stmt->bind_param('s', $postcode);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);          
            ?>
            <div class='postcode-form'>
                <h3>Update postcode</h3>
                <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <label for="postcode">
                        Postcode:
                        <input type="text" name="postcode" value="<?php echo htmlspecialchars($result["postcode"]); ?>" readonly>
                    </label>
                    <label for="straat">
                        Straat:
                        <input type="text" name="straat" value="<?php echo htmlspecialchars($result["adres"]); ?>" required>
                    </label>
                    <label for="woonplaats">
                        Woonplaats:
                        <input type="text" name="woonplaats" value="<?php echo htmlspecialchars($result["woonplaats"]); ?>" required>
                    </label>
                    <button type="submit" name='update_postcode'>Update postcode</button>
                </form>
                <br>
                <a href="postcodes.php">Cancel update</a>
            </div>
        <?php
        } else {
        ?>
            <div class='postcode-form'>
                <h3>Voeg nieuwe postcode toe</h3>
                <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <label for="postcode">
                        Postcode:
                        <input type="text" name="postcode" pattern='^[1-9][0-9]{3}?[A-Z]{2}' placeholder="1234AB" title="1234AB (Met hoofdletters; zonder spaties" required>
                    </label>
                    <label for="straat">
                        Straat:
                        <input type="text" name="straat" placeholder = "Kerkstraat" required>
                    </label>
                    <label for="woonplaats">
                        Woonplaats:
                        <input type="text" name="woonplaats" placeholder = "Alkmaar" required>
                    </label>
                    <button type="submit" name='add_postcode'>Voeg postcode toe</button>
                </form><br>    

                <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">           
                    <h3>Verwijder postcode</h3>
                    <label for="postcode">
                        Postcode:
                        <select name="postcode" required>
                            <option disabled selected value>------</option>
                            <?php
                            selectPostcodeOptions($conn);
                            ?>
                        </select> 
                    </label>
                    <button type='submit' name='delete_postcode'>Verwijder postcode</button>    
                </form>        
            </div>

            <h3>Postcodes:</h3>
            <?php 
            $postcodes_query = "SELECT * FROM postcodes
                                    ORDER BY postcode";

            $result = $conn->query($postcodes_query);
            if(!$result) die ("<span style='color:red'>" . "Kon geen postcodes ophalen van de database. Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw". "</span>");
            $rows = $result->num_rows;

            if ($rows == 0) { echo '<h3>Er staan nog geen postcodes in de database. Voeg eerst een postcode toe.</h3>'; }
            else {
                ?>
                <table>
                    <tbody>
                        <tr>
                            <th>Postcode    </th>
                            <th>Straat      </th>
                            <th>Woonplaats  </th>
                            <th>Update      </th>
                        </tr>
                        <?php
                        for ($j = 0 ; $j < $rows ; ++$j) { 
                            $row = $result->fetch_array(MYSQLI_ASSOC);
                        ?>           
                            <tr>
                                <td><?php echo htmlspecialchars($row["postcode"]) ?></td>
                                <td><?php echo htmlspecialchars($row["adres"]) ?></td>
                                <td><?php echo htmlspecialchars($row["woonplaats"]) ?></td>
                                <td><a href="postcodes.php?postcode=<?php echo $row['postcode']; ?>">Update</a></td>
                            </tr>
                        <?php                      
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            }
        }
    } 
    ?>
</body>
</html>