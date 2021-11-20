<!DOCTYPE html>
<html>
<head>
<title>Vereniging ledenlijst</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" />    
</head>
<body>

<?php 
include 'includes/read.php';

// if($conn->connect_error()) { die ("<span style='color:red'>" . "Er is iets mis gegaan met het tot stand brengen van de verbinding met de database. 
// Controleer of u de juiste database wilt bereiken, of deze bestaat en of uw inloggegevens kloppen" . "</span>");}
// else
if(read_db_tables($conn) == 0) {echo "<span style='color:red'>" . "Geen tabellen in de database gevonden. Voeg deze eerst toe en probeer het opnieuw" . "</span>"; }
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
                <?php show_postcode_dropdown($conn); ?>
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
            <?php show_member_table($conn); ?>
        </tbody>
    </table>
</div>
<?php } ?>
</body>
</html>