<?php 
if(isset($_POST["submit_persoonsgegevens"])) // submit_persoosgegevens refereert naar 'name="submit_persoonsgegevens" van de submit knop.
{   
    $gegevens_arr = array("naam" => $_POST["naam"], 
                            "adres" => $_POST["adres"], 
                            "postcode" => $_POST["postcode"], 
                            "woonplaats" => $_POST["woonplaats"], 
                            "telnr" => $_POST["telnr"], 
                            "email" => $_POST["email"]);

    $fh = fopen("gegevens.txt", "w") or die("Kon bestand niet aanmaken");
    
    // Variabele $line_break schrijft gegevens op een nieuwe regel, behalve bij de eerste set gegevens. 
    $line_break = "";
    foreach($gegevens_arr as $gegevens => $input)
    { 
        fwrite($fh, $line_break . $gegevens . ":" . $input) or die("Kon niet naar bestand schrijven");
        $line_break = "\n";
    }

    fclose($fh);

    $gegevens = explode("\n", file_get_contents("gegevens.txt"));

    date_default_timezone_set("Europe/Amsterdam");
    $timestamp_post = date("l j F Y H:i", time()); 
} 
?> 

<!DOCTYPE html>
<html>
    <head>
        <title>Opslaan en toon persoonsgegevens</title>
    </head>
    <body>
        <h2>Voer alle gegevens hieronder in:</h2>
        <form method="post" action="<?php $_SERVER["PHP_SELF"]; ?>">
            <label for='naam'>Naam:</label><br>
            <input type="text" id="naam" name="naam" placeholder = "John Doe" required><br>
            <label for='adres'>Adres:</label><br>
            <input type="text" id="adres" name="adres" placeholder = "Kerkstraat 10" required><br>
            <label for='postcode'>Postcode:</label><br>
            <input type="text" id="postcode" name="postcode" placeholder = "1592 AB" required><br>
            <label for='woonplaats'>Woonplaats:</label><br>
            <input type="text" id="woonplaats" name="woonplaats" placeholder = "Alkmaar" required><br>
            <label for='telnr'>Telefoonnummer:</label><br>
            <input type="tel" id="telnr" name="telnr" placeholder = "0678945124" required><br>
            <label for='email'>E-mail:</label><br>
            <input type="email" id="email" name="email" placeholder = "johndoe@email.com" required><br><br>
            <input type="submit" name="submit_persoonsgegevens" value="Opslaan"><br>
        </form> <!--bij een tweede form krijgt de submit tag een unieke name attribuut waardoor deze gescheiden wordt middels een PHP if() statement -->
        <?php if(isset($_POST["submit_persoonsgegevens"])) : ?> <!-- ook hier weer scheiding van eventuele forms door de unieke name attribuut -->
            <h1>Op <?php echo $timestamp_post ?> uur de volgende persoon ingelezen</h1>
            <h3>
                <?php 
                for($i=0; $i<count($gegevens); $i++)
                { echo ucfirst((str_replace(":", ": ", $gegevens[$i]))) . "<br>"; } 
                ?>
            </h3>
        <?php endif; ?>
    </body>
</html>



    

