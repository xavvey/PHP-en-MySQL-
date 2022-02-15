<?php
require_once 'includes/connection.php';
require_once 'includes/functions.php';

session_start();

if(!isset($_SESSION["current_page"])) {
    $_SESSION['current_page'] = basename($_SERVER['PHP_SELF']);
}
$_SESSION['previous_page'] = $_SESSION["current_page"];
$_SESSION["current_page"] = basename($_SERVER['PHP_SELF']);

if ($_SESSION["current_page"] != $_SESSION["previous_page"]) {
    unset($_SESSION["message"]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION["message"]);

    $postcode = get_post($conn, "postcode");
    $adres = get_post($conn, "straat");
    $woonplaats = get_post($conn, "woonplaats");
    $old_postcode = get_post($conn, "old_postcode");

    if (isset($_POST["add_postcode"])) {
        insertPostcode($conn, $postcode, $adres, $woonplaats);
    }

    if (isset($_POST["update_postcode"])) {  
        if($old_postcode == $postcode) {
            $_SESSION["message"] = "Postcode niet aangepast. Het lijkt erop dat de nieuwe postcode gelijk is aan de oude. Controleer de gegevens en/of probeer het opnieuw";
            header("Location: postcodes.php");
        } elseif (deletePostcode($conn, $old_postcode) < 1) {
            $_SESSION["message"] = "Postcode niet aangepast. Wellicht is deze in gebruik. Controlleer de gegevens en/of probeert u het opnieuw.";
        } else {
            insertPostcode($conn, $postcode, $adres, $woonplaats);
        }

        header("Location: postcodes.php");             
    }
}

$postcode = "";
$adres = "";
$woonplaats = "";

$form_title = "Voeg postcode toe";
$form_action = "add_postcode";

if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    if (isset($_GET["action"])) { 
        unset($_SESSION["message"]);
        $postcode = $_GET['postcode'];

        if ($_GET["action"] == 'delete_postcode') {
            deletePostcode($conn, $postcode);
            header("Location: postcodes.php");
        }

        if ($_GET["action"] == "update_postcode") {
            $form_title = "Update postcode";
            $form_action = "update_postcode";
            
            $stmt = $conn->prepare("SELECT * FROM postcodes WHERE postcode=?");
            $stmt->bind_param('s', $postcode);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
       
            $postcode = htmlspecialchars($result['postcode']);
            $adres = htmlspecialchars($result['adres']);
            $woonplaats = htmlspecialchars($result['woonplaats']);
        }
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
    if (getNumDbTables($conn, $database) < 1) { // kleiner dan 1 want als 1 ander tabel bestaat dan wordt deze ook getoond
        echo "<span style='color:red'>" . "Niet alle tabellen bestaan in de database. Voeg deze eerst toe en probeer het opnieuw" . "</span>"; // Bij geen tabellen wordt dit getoond.
        exit;
    } else { 
        if(!empty($_SESSION['message'])) {
            $message = $_SESSION['message'];
            echo "<span style='color:red'>" . $message . "</span>";
        }
        ?>
        <h1>Postcode overzicht</h1>
        <a href='index.php'>Naar ledenoverzicht</a> 

        <div class='postcode-form'>
            <h3><?php echo $form_title ?></h3>
            <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
                <label for="postcode">
                    Postcode:
                    <input type="text" name="postcode" title="1234GE" pattern='^[1-9][0-9]{3}?[A-Z]{2}' value="<?php echo $postcode; ?>" required>
                    <input type="hidden" name="old_postcode" value="<?php echo $postcode; ?>"> <!-- hidden oude postcode voor update -->
                </label>
                <label for="straat">
                    Straat:
                    <input type="text" name="straat" value="<?php echo $adres; ?>" required>
                </label>
                <label for="woonplaats">
                    Woonplaats:
                    <input type="text" name="woonplaats" value="<?php echo $woonplaats; ?>" required>
                </label>
                <button type="submit" name='<?php echo $form_action; ?>'>Opslaan</button>
            </form>
        </div>
        <?php
        if (
            (isset($_GET["action"]) 
            && $_GET["action"] == "update_postcode")
        ) {  
        ?>
        <br>
        <a href="postcodes.php">Cancel update</a>
        <?php
        }

        if (
            !(isset($_GET["action"]) 
            && $_GET["action"] == "update_postcode")
        ) {    
            ?>
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
                            <th>Delete      </th>
                        </tr>
                        <?php
                        for ($j = 0 ; $j < $rows ; ++$j) { 
                            $row = $result->fetch_array(MYSQLI_ASSOC);
                        ?>           
                            <tr>
                                <td><?php echo htmlspecialchars($row["postcode"]) ?></td>
                                <td><?php echo htmlspecialchars($row["adres"]) ?></td>
                                <td><?php echo htmlspecialchars($row["woonplaats"]) ?></td>
                                <td><a href="postcodes.php?action=update_postcode&postcode=<?php echo $row['postcode']; ?>">Update</a></td>
                                <td><a href="postcodes.php?action=delete_postcode&postcode=<?php echo $row['postcode']; ?>">Delete</a></td>
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