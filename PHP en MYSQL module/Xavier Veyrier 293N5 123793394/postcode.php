<!DOCTYPE html>
<html>
    <head>
        <title>Postcodes toevoegen</title>
    </head>
    <body>
    <?php
        require_once 'vereniging_connection.php';
        $conn = new mysqli($hostname, $username, $password, $database);
        if ($conn->connect_error) 
            die("Er is iets mis gegaan met het tot stand brengen van de verbinding met de database. 
                    Controleer of u de juiste database wilt bereiken, of deze bestaat en of uw inloggegevens kloppen");

        $check_postcodes = "SELECT 1 FROM postcodes"; 
        $postcodes_bestaat = $conn->query($check_postcodes);

        if($postcodes_bestaat)
        {
            if( isset($_POST["submit_postcode"]))
            {
                $postcode   = get_post($conn, "postcode");
                $adres      = get_post($conn, "adres");
                $woonplaats = get_post($conn, "woonplaats");

                $stmt = $conn->prepare('INSERT INTO postcodes VALUES(?, ?, ?)');
                $stmt->bind_param('sss', $postcode, $adres, $woonplaats);

                $stmt->execute();

                if($stmt->affected_rows != 1)
                { 
                    ?>
                        <h3>Postcode niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en/of probeer het opnieuw.</h3>
                    <?php
                }

                $stmt->close();
            }        
    ?>
        <h2>Voer een nieuwe postcode toe:</h2>
        <form method="post" action="<?php $_SERVER["PHP_SELF"]; ?>">
            <label for='postcode'>Postcode:</label><br>
            <input type="text" id="postcode" name="postcode" pattern='^[1-9][0-9]{3}[A-Z]{2}' placeholder="1234AB" required><br>
            <label for='adres'>Adres:</label><br>
            <input type="text" id="adres" name="adres" placeholder = "Kerkstraat" required><br>
            <label for='woonplaats'>Woonplaats:</label><br>
            <input type="text" id="woonplaats" name="woonplaats" placeholder = "Alkmaar" required><br><br>
            <input type="submit" name="submit_postcode" value="Postcode opslaan"><br>
        </form>
        <br>
        <h2>Ingevoerde postcodes:</h2>
        <?php 
            $postcodes_query = "SELECT * FROM postcodes";
            $result = $conn->query($postcodes_query);
            if(!$result) die ("Kon geen postcodes ophalen van de database. Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw");
            $rows = $result->num_rows;

            if($rows > 0) 
            {
                for($j = 0 ; $j < $rows ; ++$j) { $row = $result->fetch_array(MYSQLI_ASSOC); ?>
                <p>    
                    <?php   
                        echo htmlspecialchars($row["adres"])     . ", " .
                             htmlspecialchars($row["postcode"])  . " "  .
                             htmlspecialchars($row["woonplaats"]); 
                    ?>
                    <br>
                </p>                    
                <?php } 
            } else { ?>
        <h3> Er zijn nog geen postcodes ingevoerd.</h3>
        <?php } 
            } else { ?>
            <h2>Tabel 'postcodes' niet gevonden. Maak eerst een tabel aan voordat u postcodes toe kan voegen</h2>
        <?php 
            }

        function get_post($conn, $var)
        {
            return $conn->real_escape_string($_POST[$var]);
        }

        $conn->close();
        ?>
    </body>
</html>