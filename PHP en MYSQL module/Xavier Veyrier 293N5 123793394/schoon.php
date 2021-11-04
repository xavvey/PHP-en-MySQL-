<!DOCTYPE html>
<html>
    <head>
        <title>Schoon data</title>
    </head>
    <body>
    <?php
        require_once 'vereniging_connection.php';
        $conn = new mysqli($hostname, $username, $password, $database);
        if ($conn->connect_error) 
            die("Er is iets mis gegaan met het tot stand brengen van de verbinding met de database. 
                    Controleer of u de juiste database wilt bereiken, of deze bestaat en of uw inloggegevens kloppen");

        $del_tel_query = "DELETE FROM telefoonnummers";
        $del_tel_result = $conn->query($del_tel_query);
        if(!$del_tel_result) die ("Verwijderen van telefoonnummers mislukt. <br>");
    
        $del_mail_query = "DELETE FROM emails";
        $del_mail_result = $conn->query($del_mail_query);
        if(!$del_mail_result) die ("Verwijderen van emails mislukt. <br>");
    
        $del_lid_query = "DELETE FROM leden";
        $del_lid_result = $conn->query($del_lid_query);
        if(!$del_lid_result) die ("Verwijderen van leden mislukt. <br>"); 

    ?>
    <h2> Alle leden zijn verwijderd. Postcodes zijn blijven staan.</h2>
    <?php
        $conn->close();
    ?>
    </body>
</html>