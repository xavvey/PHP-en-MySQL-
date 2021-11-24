<?php
require_once __DIR__ .'../connection.php';

if(isset($_POST['lidnummer']))
{
    $lidnummer = get_post($conn, 'lidnummer');
    $num_telnrs = get_post($conn, 'num-telnrs');
    $num_emails = get_post($conn, 'num-emails');

    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    $num_data_affected = 0;
    foreach($_POST as $data => $info)
    {        
        $info = get_post($conn, $data);

        if($data == 'naam' || $data == 'voornaam' || $data == 'huisnummer' || $data == 'postcode')
        {
            $stmt_data = $conn->prepare('UPDATE leden SET '. $data . '=? WHERE lidnummer=?');
            $stmt_data->bind_param('si', $info, $lidnummer);
            $stmt_data->execute();

            $affected_info = $stmt_data->affected_rows;
            $stmt_data->close();
        }    
        elseif(substr($data, 0, strlen('telefoonnummer')) == 'telefoonnummer' || substr($data, 0, strlen('oud-telnr')) == 'oud-telnr')
        {
            for($t = 0; $t < $num_telnrs; ++$t)
            {
                $telnr_num = $t + 1;
                $telnr_new = get_post($conn, 'telefoonnummer' . $telnr_num);
                $telnr_old = get_post($conn, 'oud-telnr' . $telnr_num);
        
                if($telnr_new != $telnr_old)
                {
                    $stmt_ins_tel = $conn->prepare('INSERT INTO telefoonnummers VALUES (?, ?)');
                    $stmt_ins_tel->bind_param('si', $telnr_new, $lidnummer);
                    $stmt_ins_tel->execute();
        
                    $stmt_del_tel = $conn->prepare('DELETE FROM telefoonnummers WHERE telefoonnummer=?');
                    $stmt_del_tel->bind_param('s', $telnr_old);
                    $stmt_del_tel->execute();
                    
                    $affected_tel = $stmt_ins_tel->affected_rows;
                    echo $affected_tel . '<br>';

                    $stmt_del_tel->close();
                    $stmt_ins_tel->close();
                }
            }
        }
        $num_data_affected += $affected_info;
        
    }
    echo $num_data_affected;
    // if($num_data_affected < 1)
    // {
    //     echo '<script> alert("Het lijkt erop dat niets gewijzigd is. Controleer alle gegevens. Ook of de postcode die u eventueel wilt toevoegen al bestaat. Probeer het opnieuw.") </script>';
    //     echo '<script> window.history.go(-1) </script>'; 
    // }
    // else
    // {
    //     echo '<script> alert("Gegevens aangepast. U kunt verder gaan met wijzigen of naar een andere pagina gaan.") </script>';
    //     echo '<script> window.location.href = "../lid.php?lidnummer=' . $lidnummer . '" </script>';  
    // }
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