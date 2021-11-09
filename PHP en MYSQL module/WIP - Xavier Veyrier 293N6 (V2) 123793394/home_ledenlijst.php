<!DOCTYPE html>
<html>
<head>
<title>Vereniging ledenlijst</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" />    
</head>
<body>

<h3>Voeg hieronder een nieuw lid toe:</h3>

    <div class="leden-form">    
    <form action="includes/toevoegen_ledenlijst.php" method="POST"><b>
        <label for="naam">Voornaam:</label>
        <input type="text" id="voornaam" name="voornaam"><br>
        <label for="achternaam">Achternaam:</label>
        <input type="text" id="achternaam" name="achternaam"><br>
        <label for="huisnummer">Huisnummer:</label>
        <input type="text" id="huisnummer" name="huisnummer"><br>
        <label for="postcode">Postcode:</label><br>
        <select id="postcode" name="postcode">
            <option disabled selected value>------</option>
            <?php include 'includes/select_postcodes.php'; ?>
        </select><br>
        <label for="emailadres">E-mailadres(sen):</label>
        <input type="text" id="emailadres" name="emailadres" placeholder="Komma tussen meer emails"><br>
        <label for="telnr">Telefoonummer(s):</label>
        <input type="text" id="telnr" name="telnr" placeholder="Komma tussen meer telnrs" ><br></b>
        <br><button type="submit" id = 'add_member' name='add_member'>Voeg lid toe</button>
    </form><br><br>
    </div>

<h1>Ledenoverzicht</h1>

    <div>
        <table>
            <tbody>
                <?php include 'includes/lees_ledenlijst.php'; ?>
            </tbody>
        </table>
    </div>

</body>
</html>