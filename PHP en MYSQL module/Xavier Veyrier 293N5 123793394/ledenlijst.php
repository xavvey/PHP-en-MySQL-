<!DOCTYPE html>
<html>
    <head>
        <title>Toon ledenlijst</title>
    </head>
    <body>
    <?php
        require_once 'vereniging_connection.php';
        $conn = new mysqli($hostname, $username, $password, $database);
        if ($conn->connect_error) 
            die("Er is iets mis gegaan met het tot stand brengen van de verbinding met de database. 
                    Controleer of u de juiste database wilt bereiken, of deze bestaat en of uw inloggegevens kloppen");

        $query = "SELECT * FROM leden  
                    JOIN postcodes USING (postcode)
                    JOIN emails USING (lidnummer)
                    JOIN telefoonnummers USING (lidnummer)";

        $result = $conn->query($query);
        if(!$result) die ("Kon geen gegevens van de database ophalen. Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw");
        
        $rows = $result->num_rows;

        if($rows == 0) { ?> <h2>Er zijn geen leden gevonden in de database.</h2> 
        <?php 
        } else {
            for ($j = 0 ; $j < $rows ; ++$j) { $row = $result->fetch_array(MYSQLI_ASSOC); ?>      
            <p>
                <?php   
                    echo htmlspecialchars($row["lidnummer"])   . "- " .
                    htmlspecialchars($row["voornaam"])         . " "  .
                    htmlspecialchars($row["naam"])             . "- " .   
                    htmlspecialchars($row["adres"])            . " "  .                              
                    htmlspecialchars($row["huisnummer"])       . ", " .
                    htmlspecialchars($row["postcode"])         . " "  .
                    htmlspecialchars($row["woonplaats"])       . ", " .
                    htmlspecialchars($row["email"])            . ", " .
                    htmlspecialchars($row["telefoonnummer"]); ?><br>
            </p>                   
        <?php }
            }

        $result->close();
        $conn->close();
    ?>
    </body>
</html>



