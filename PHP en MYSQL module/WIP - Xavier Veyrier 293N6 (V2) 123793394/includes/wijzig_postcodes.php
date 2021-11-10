<?php
require_once __DIR__ . '../connection.php';

if(isset($_POST["add_postcode"]))
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
}  

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}

?>