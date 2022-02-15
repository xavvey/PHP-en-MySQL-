<?php 
require_once 'includes/connection.php';
require_once 'includes/functions.php';

session_start();
$notifications = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $lidnummer = get_post($conn, 'lidnummer');
    $voornaam = get_post($conn, 'voornaam');
    $achternaam = get_post($conn, 'achternaam');
    $huisnummer = get_post($conn, 'huisnummer');
    $postcode = get_post($conn, 'postcode');
    $telnrs = get_post($conn, 'telnr');
    $emails = get_post($conn, 'emailadres');
  
    if (isset($_POST["add_member"])) {   
        $stmt_lid = $conn->prepare("INSERT INTO leden (naam, voornaam, huisnummer, postcode) VALUES(?, ?, ?, ?)");
        $stmt_lid->bind_param('ssss', $achternaam, $voornaam, $huisnummer, $postcode);
        $stmt_lid->execute();
        $stmt_lid->close();
        
        $lidnummer = $conn->insert_id;

        $failed_emails = insertEmails($conn, $emails, $lidnummer);
        foreach ($failed_emails as $failed_email) {
            $notifications[] = [
                'title' => 'Email is al in gebruik.',
                'body' => $failed_email
            ];
        }

        insertTelnrs($conn, $telnrs, $lidnummer);
    }

    if (isset($_POST['update_member'])) {   
        $stmt = $conn->prepare("UPDATE leden SET naam=?, voornaam=?, huisnummer=?, postcode=? WHERE lidnummer=?");
        $stmt->bind_param('ssssi', $achternaam, $voornaam, $huisnummer, $postcode, $lidnummer);
        $stmt->execute();
        $stmt->close();
        
        deleteEmails($conn, $lidnummer);
        deleteTelnrs($conn, $lidnummer);

        $failed_emails = insertEmails($conn, $emails, $lidnummer);
        foreach ($failed_emails as $failed_email) {
            $notifications[] = [
                'title' => 'Email is al in gebruik.',
                'body' => $failed_email
            ];
        }
        insertTelnrs($conn, $telnrs, $lidnummer);

        $stmt->close();
    }
}

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

            $del_member_stmt->close();
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
                array_push($phone_nmbrs, $result);
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
    <?php foreach ($notifications as $notification) { ?>
        <div class="notification">
            <span class="title"><?php echo $notification['title']; ?></span>
            <span class="body"><?php echo $notification['body']; ?></span>
        </div>
    <?php 
    } 
        // DELETE
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // DELETE
        
        if (getNumDbTables($conn, $database) < 1) { // kleiner dan 1 want als 1 ander tabel bestaat dan wordt deze ook getoond
            echo "<span style='color:red'>" . "Geen tabellen in de database gevonden. Voeg deze eerst toe en probeer het opnieuw" . "</span>";
            exit;
        } else {
            ?>
            <h1>Verenigingsoverzicht</h1>
            <a href='postcodes.php'>Naar postcode overzicht</a>

            <div class="leden-form">     
                <h3><?php echo $form_title ?></h3>
                <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <?php if (!empty($lidnummer)) { ?>
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
                            selectPostcodeOptions($conn);
                            ?>
                        </select>
                    </label>
                    <label for="emailadres">
                        E-mailadres(sen):
                        <input type="email" name="emails" value="<?php echo implode(",", $emails); ?>">
                        <textarea name="emailadres" cols="45" rows="4"><?php
                        // foreach ($emails as $email) {
                            echo implode(",", $emails);
                        // }
                        ?></textarea>
                    </label>
                    <label for="telnr">
                        Telefoonummer(s):
                        <textarea name="telnr" cols="45" rows="4"><?php           
                        for ($i=0; $i < $num_tels; $i++) {
                            $result = $tel_result->fetch_assoc();
                            echo $result['telefoonnummer'] . "\r\n";
                        }
                        ?></textarea>
                    </label>
                    <button type="submit" name='<?php echo $form_action ?>'>Opslaan</button>
                </form><br>
                <a href="index.php">Cancel update</a>
            </div>
            <?php
            if (!isset($_GET['lidnummer'])) {
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