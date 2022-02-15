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
    $lidnummer = get_post($conn, 'lidnummer');
    $voornaam = get_post($conn, 'voornaam');
    $achternaam = get_post($conn, 'achternaam');
    $huisnummer = get_post($conn, 'huisnummer');
    $postcode = get_post($conn, 'postcode');
    $phone_nmbrs = get_post($conn, 'phone_nmbrs');
    $emails = get_post($conn, 'emails');
  
    if (isset($_POST["add_member"])) {   
        $stmt_lid = $conn->prepare("INSERT INTO leden (naam, voornaam, huisnummer, postcode) VALUES(?, ?, ?, ?)");
        $stmt_lid->bind_param('ssss', $achternaam, $voornaam, $huisnummer, $postcode);
        $stmt_lid->execute();
        $stmt_lid->close();
        
        $lidnummer = $conn->insert_id;

        insertEmails($conn, $emails, $lidnummer);
        insertTelnrs($conn, $phone_nmbrs, $lidnummer);
    }

    if (isset($_POST['update_member'])) {   
        $stmt = $conn->prepare("UPDATE leden SET naam=?, voornaam=?, huisnummer=?, postcode=? WHERE lidnummer=?");
        $stmt->bind_param('ssssi', $achternaam, $voornaam, $huisnummer, $postcode, $lidnummer);
        $stmt->execute();
        $stmt->close();
        
        deleteEmails($conn, $lidnummer);
        deleteTelnrs($conn, $lidnummer);

        insertEmails($conn, $emails, $lidnummer);
        insertTelnrs($conn, $phone_nmbrs, $lidnummer);

        header("Location: index.php");
    }
}

$postcode_query = "SELECT * FROM postcodes
ORDER BY postcode"; 

$postcode_result = $conn->query($postcode_query);

if (!$postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
            Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

$num_postcodes = $postcode_result->num_rows;

$lidnummer = "";
$voornaam = "";
$achternaam = "";
$huisnummer = "";
$postcode = "";
$straat = "";
$woonplaats = "";
$emails = [];
$phone_nmbrs = [];

$form_title = "Voeg lid toe";
$form_action = "add_member";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET["action"])) {
        $lidnummer = $_GET['lidnummer'];

        if ($_GET['action'] == 'delete_member') {
            deleteEmails($conn, $lidnummer);
            deleteTelnrs($conn, $lidnummer);

            $del_member_stmt = $conn->prepare("DELETE FROM leden WHERE lidnummer=?");
            $del_member_stmt->bind_param('i', $lidnummer);
            $del_member_stmt->execute();

            header("Location: index.php");
        }

        if ($_GET["action"] == "update_member") {
            $form_title = "Update lid";
            $form_action = "update_member";

            $stmt = $conn->prepare("SELECT * FROM leden NATURAL JOIN postcodes WHERE lidnummer=?");
            $stmt->bind_param('i', $lidnummer);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
            
            $stmt_email = $conn->prepare("SELECT * FROM emails WHERE lidnummer=?");
            $stmt_email->bind_param('i', $lidnummer);
            $stmt_email->execute();
            $email_result = $stmt_email->get_result();
            $num_emails = $email_result->num_rows;
            
            $stmt_tel = $conn->prepare("SELECT * FROM telefoonnummers WHERE lidnummer=?");
            $stmt_tel->bind_param('i', $lidnummer);
            $stmt_tel->execute();
            $tel_result = $stmt_tel->get_result();
            $num_tels = $tel_result->num_rows;
            
            $voornaam = htmlspecialchars($result['voornaam']);
            $achternaam = htmlspecialchars($result['naam']);
            $huisnummer = htmlspecialchars($result['huisnummer']);
            $postcode = htmlspecialchars($result['postcode']);
            $straat = htmlspecialchars($result['adres']);
            $woonplaats = htmlspecialchars($result['woonplaats']);

            for ($i=0; $i < $num_emails; $i++) {
                $result = $email_result->fetch_assoc();
                array_push($emails, $result["email"]);
            }

            for ($i=0; $i < $num_tels; $i++) {
                $result = $tel_result->fetch_assoc();
                array_push($phone_nmbrs, $result["telefoonnummer"]);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Vereniging ledenlijst</title>
        <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" />    
    </head>
<body>
    <?php 
        if (getNumDbTables($conn, $database) < 1) { // kleiner dan 1 want als 1 ander tabel bestaat dan wordt deze ook getoond
            echo "<span style='color:red'>" . "Niet alle tabellen bestaan in de database. Voeg deze eerst toe en probeer het opnieuw" . "</span>";
            exit;
        } else {
            if(!empty($_SESSION['message'])) {
                $message = $_SESSION['message'];
                echo "<span style='color:red'>" . $message . "</span>";
            }
            ?>
            <h1>Verenigingsoverzicht</h1>
            <a href='postcodes.php'>Naar postcode overzicht</a>

            <div class="leden-form">     
                <h3><?php echo $form_title ?></h3>
                <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <?php if (isset($_GET["action"]) == "update_member" && !empty($lidnummer)) { ?>
                    <label for="lidnummer">
                        Lidnummer:
                        <input type="text" name="lidnummer" value="<?php echo $lidnummer; ?>" readonly>
                    </label>
                    <?php } ?>
                    <label for="naam">
                        Voornaam:
                        <input type="text" name="voornaam" value="<?php echo $voornaam; ?>" required>
                    </label>
                    <label for="achternaam">
                        Achternaam:
                        <input type="text" name="achternaam" value="<?php echo $achternaam; ?>" required>
                    </label>
                    <label for="huisnummer">
                        Huisnummer:
                        <input type="text" name="huisnummer" value="<?php echo $huisnummer; ?>" required>
                    </label>
                    <label for="postcode">
                        Postcode:
                        <select name="postcode" required>
                            <option selected value="<?php echo $postcode; ?>">
                                <?php echo $postcode; ?> - <?php echo $straat; ?> - <?php echo $woonplaats; ?>
                            </option>
                            <?php     
                            for ($p = 0; $p < $num_postcodes; ++$p) {
                                $row = $postcode_result->fetch_array(MYSQLI_ASSOC);
                        
                                $postcode = htmlspecialchars($row['postcode']);
                                $straat = htmlspecialchars($row['adres']);
                                $woonplaats = htmlspecialchars($row['woonplaats']);
                        
                                echo "<option value='$postcode'>" . $postcode . " - " . $straat . " - " . $woonplaats . "</option>"; 
                            } 
                            // selectPostcodeOptions($conn);
                            ?>
                        </select>
                    </label>
                    <label for="emailadres">
                        E-mailadres(sen):
                        <input type="text" name="emails" value="<?php echo implode(",", $emails); ?>" pattern="^(\s?[^\s,]+@[^\s,]+\.[^\s,]+\s?,)*(\s?[^\s,]+@[^\s,]+\.[^\s,]+)$" title=" Bevat een '@' en een '.'  Meerdere met komma scheiden">
                    </label>
                    <label for="telnr">
                        Telefoonummer(s):
                        <input type="text" name="phone_nmbrs" value="<?php echo implode(",", $phone_nmbrs) ?>" pattern="^(\d{10}(\d{3})?,)*(\d{10}(\d{3})?)$" title="10 of 13 cijfers. Geen letters. Buitelandse nummers met 0031. Meerder met komma scheiden">
                    </label>
                    <button type="submit" name='<?php echo $form_action ?>'>Opslaan</button>
                </form><br>
            </div>
            <?php
            if (
                isset($_GET["action"])
                && $_GET["action"] == "update_member"
            ) {
            ?>  
            <a href="index.php">Cancel update</a>
            <?php
            }

            if (
                !(isset($_GET["action"])
                && $_GET["action"] == "update_member")
            ) {
                ?>            
                <div>
                    <h3>Leden:</h3>
                    <table>
                        <tbody>
                            <?php
                            $select_query = "SELECT * FROM leden  
                                                NATURAL JOIN postcodes
                                                ORDER BY lidnummer";

                            $select_result = $conn->query($select_query);
                            if (!$select_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                                                        Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

                            $num_members = $select_result->num_rows;

                            if ($num_members == 0) { echo "<h2>Er zijn geen leden gevonden in de database.</h2>"; //Bij bestaande tabellen die leeg zijn, wordt dit getoond. Optie om leden toe te voegen bestaat dan wel.
                            } else {
                            ?>
                                <tr>
                                    <th>Lidnummer</th>
                                    <th>Voornaam</th>
                                    <th>Achternaam</th>
                                    <th>Huisnummer</th>
                                    <th>Straat</th>
                                    <th>Postcode</th>
                                    <th>Woonplaats</th>
                                    <th>E-mailadres(sen)</th>
                                    <th>Telefoonnummer(s)</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                </tr>
                            <?php
                            }
                            for ($j = 0 ; $j < $num_members ; ++$j) {
                                $row = $select_result->fetch_array(MYSQLI_ASSOC);

                                $email_query = "SELECT * FROM emails WHERE lidnummer='$row[lidnummer]'";
                                $email_result = $conn->query($email_query);
                                $num_emails = $email_result->num_rows;

                                $telnr_query = "SELECT * FROM telefoonnummers WHERE lidnummer='$row[lidnummer]'";
                                $telnr_result = $conn->query($telnr_query);
                                $num_telnrs = $telnr_result->num_rows;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["lidnummer"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["voornaam"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["naam"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["huisnummer"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["adres"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["postcode"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["woonplaats"]); ?></td>
                                    <td>
                                        <?php
                                        for ($i=0; $i < $num_emails ; $i++) {
                                            $subrow = $email_result->fetch_array(MYSQLI_ASSOC);
                                            echo htmlspecialchars($subrow['email']) . "<br>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        for ($i=0; $i < $num_telnrs ; $i++) {
                                            $subrow = $telnr_result->fetch_array(MYSQLI_ASSOC);
                                            echo htmlspecialchars($subrow['telefoonnummer']) . "<br>";
                                        }
                                        ?>
                                    </td>
                                    <td><a href="index.php?action=update_member&lidnummer=<?php echo $row["lidnummer"] ?>">Update lid</a></td>
                                    <td><a href="index.php?action=delete_member&lidnummer=<?php echo $row["lidnummer"] ?>">Delete lid</a></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php
            } 
        } 
        ?>
</body>
</html>