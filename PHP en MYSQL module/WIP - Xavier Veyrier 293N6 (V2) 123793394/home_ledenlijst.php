<!DOCTYPE html>
<html>
<head>
<title>Vereniging ledenlijst</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" />    
</head>
<body>

<div class="text-right">
    <a class='right-link' href='postcodes.php' target='blank'>Postcodes toevoegen/verwijderen</a>
</div>

<h1>Verenigingsoverzicht</h1>

<div class="leden-form"> 
    
    <h3>Voeg nieuw lid toe:</h3>
   
    <form action="includes/toevoegen_ledenlijst.php" method="POST"><b>
        <label for="naam">
            Voornaam:
            <input type="text" name="voornaam">
        </label>
        <label for="achternaam">
            Achternaam:
            <input type="text" name="achternaam">
        </label>
        <label for="huisnummer">
            Huisnummer:
            <input type="text" name="huisnummer">
        </label>
        <label for="postcode">
            Postcode:
            <select name="postcode">
                <option disabled selected value>------</option>
                <?php include 'includes/select_postcodes.php'; ?>
            </select>
        </label>
        <label for="emailadres">
            E-mailadres(sen):
            <input type="text" name="emailadres" placeholder="Komma's tussen 2 of meer emails">
        </label>
        <label for="telnr">
            Telefoonummer(s):
            <input type="text" name="telnr" placeholder="Komma's tussen 2 of meer telnrs" >
        </label></b>
        <button type="submit" name='add_member'>Voeg lid toe</button>
    </form><br>
</div>

<div>

    <h3>Ledenoverzicht</h3>

    <table>
        <tbody>
            <?php include 'includes/lees_ledenlijst.php'; ?>
        </tbody>
    </table>
</div>

</body>
</html>