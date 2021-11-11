<!DOCTYPE html>
<html>
<head>
<title>Postcodes wijzigen</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" /> 
</head>
<body>

<div>
    <h1>Postcode overzicht</h1>
    <a href='home_ledenlijst.php' target='blank'>Naar ledenoverzicht</a>
</div>

<?php include 'includes/wijzigen_postcodes.php'; ?>   

<div class='postcode-form'>
    <h3>Voeg nieuwe postcode toe</h3>

    <form action="includes/wijzigen_postcodes.php" method="POST"><b>
        <label for="postcode">
            Postcode:
            <input type="text" name="postcode" pattern='^[1-9][0-9]{3}[\s]?[A-Za-z]{2}' placeholder="1234AB" required>
        </label>
        <label for="straat">
            Straat:
            <input type="text" name="straat" placeholder = "Kerkstraat" required>
        </label>
        <label for="woonplaats">
            Woonplaats:
            <input type="text" name="woonplaats" placeholder = "Alkmaar" required>
        </label></b><button type="submit" name='add_postcode'>Voeg postcode toe</button>
    </form><br>
</div>

<div>
    <h3>Ledenoverzicht</h3>

    <?php 
    if(!$postcodes_bestaan) { echo '<h3>Er staan nog geen postcodes in de database. Voeg eerst een postcode toe.</h3>'; }
    else { ?>
    <table>
        <tbody>
            <?php show_postcodes_table($conn); ?>
        </tbody>
    </table>
    <?php } ?>
</div>

</body>
</html>