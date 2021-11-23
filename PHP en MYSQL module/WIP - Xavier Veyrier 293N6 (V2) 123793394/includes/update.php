<?php
require_once __DIR__ .'../connection.php';

if(isset($_POST['lidnummer']))
{
    $lidnummer = get_post($conn, 'lidnummer');

    $num_data_affected = 0;
    foreach($_POST as $data => $info)
    {
        echo $data . " " . $info;
        echo '<br>';
        
    //     $info = get_post($conn, $data);

    //     if($data != 'lidnummer')
    //     {
    //         $stmt_data = $conn->prepare('UPDATE leden SET '. $data . '=? WHERE lidnummer=?');
    //         $stmt_data->bind_param('si', $info, $lidnummer);
    //         $stmt_data->execute();

    //         $affected = $stmt_data->affected_rows;
    //     }       
    //     $num_data_affected += $affected;
    // }
    
    // if($num_data_affected < 1)
    // {
    //     echo '<script> alert("Het lijkt erop dat niets gewijzigd is. Controleer alle gegevens. Ook of de postcode die u eventueel wilt toevoegen al bestaat. Probeer het opnieuw.") </script>';
    //     echo '<script> window.history.go(-1) </script>'; 
    // }
    // else
    // {
    //     echo '<script> alert("Gegevens aangepast. U kunt verder gaan met wijzigen of naar een andere pagina gaan.") </script>';
    //     echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';  
    }
    // $stmt_data->close();
}

if(isset($_POST['postcode']) && isset($_POST['adres']) && isset($_POST['woonplaats']))
{
    $postcode = get_post($conn, 'postcode');
    $adres = get_post($conn, 'adres');
    $woonplaats = get_post($conn, 'woonplaats');

    $stmt_postcode = $conn->prepare('UPDATE postcodes SET adres=?, woonplaats=? WHERE postcode=?');
    $stmt_postcode->bind_param('sss', $adres, $woonplaats, $postcode);
    $stmt_postcode->execute();

    if($stmt_postcode->affected_rows != 1)
    {
        echo '<script> alert("Postcode niet aangepast. Controleer de gegevens en probeer het opnieuw") </script>';
        echo '<script> window.history.go(-1) </script>';         
    } 
    else
    {
        header("location: ../postcodes.php");
    }
    
    $stmt_postcode->close();  
}

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}
?>