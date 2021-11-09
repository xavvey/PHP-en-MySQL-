<!DOCTYPE html>
<html>
<head>
<title>Vereniging ledenlijst</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" />    
</head>
<body>

<h1>Verenigingsoverzicht</h1>

<h3>Voeg hieronder een nieuw lid toe:</h3>

    <div class="leden-form">    
    <form action="includes/toevoegen_ledenlijst.php" method="POST">
        <b>
        <label for="naam">
            Voornaam:
            <input type="text" id="voornaam" name="voornaam">
        </label>
        <label for="achternaam">
            Achternaam:
            <input type="text" id="achternaam" name="achternaam">
        </label>
        <label for="huisnummer">
            Huisnummer:
            <input type="text" id="huisnummer" name="huisnummer">
        </label>
        <label for="postcode">
            Postcode:
            <select id="postcode" name="postcode">
                <option disabled selected value>------</option>
                <?php include 'includes/select_postcodes.php'; ?>
            </select>
        </label>
        <label for="emailadres">
            E-mailadres(sen):
            <input type="text" id="emailadres" name="emailadres" placeholder="Komma tussen meer emails">
        </label>
        <label for="telnr">
            Telefoonummer(s):
            <input type="text" id="telnr" name="telnr" placeholder="Komma tussen meer telnrs" >
        </label>
        </b>
        <button type="submit" id = 'add_member' name='add_member'>Voeg lid toe</button>
    </form><br>
    </div>

<h3>Ledenoverzicht</h3>

    <div>
        <table>
            <tbody>
                <?php include 'includes/lees_ledenlijst.php'; ?>
            </tbody>
        </table>
    </div>

</body>
</html>