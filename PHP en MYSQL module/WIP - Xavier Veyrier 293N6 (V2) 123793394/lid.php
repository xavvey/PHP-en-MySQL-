<!DOCTYPE html>
<html>
<head>
<title>Update lid</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" /> 
</head>
<body>

<div>
    <h1>Update lid</h1>
    <a href='index.php'>Naar ledenoverzicht</a><br>
    <a href='postcodes.php'>Naar postcode overzicht</a>
</div>

<?php
require_once 'includes/connection.php';
include 'includes/helpers.php';

if(isset($_GET['lidnummer'])) 
{ 
    $lidnummer = $_GET['lidnummer'];
}
?>
<div class="contact-form">     
    <h3>Voeg contactgegevens toe:</h3>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
        <label for="telefoonnummer">
            Telefoonnummer:
            <input type="text" name="telefoonnummer" maxlength="13" required>
        </label>
        <input type='hidden' name='lidnummer' value='<?php echo $lidnummer ?>'>
        <button type="submit" name='add_telnr'>Voeg telnr toe</button>
    </form><br>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">        
        <label for="email">
            Email:
            <input type="email" name="email" required>
        </label>
        <input type='hidden' name='lidnummer' value='<?php echo $lidnummer ?>'>
        <button type="submit" name='add_email'>Voeg email toe</button>
    </form><br>
    <?php
    if(isset($_POST['add_telnr']))
    {
        $telnr = get_post($conn, 'telefoonnummer');
        $lidnummer = get_post($conn, 'lidnummer');
    
        $stmt_telnr = $conn->prepare("INSERT INTO telefoonnummers VALUES(?,?)");
        $stmt_telnr->bind_param('si', $telnr, $lidnummer);
        $stmt_telnr->execute();
    
        if($stmt_telnr->affected_rows != 1)
        { 
            echo '<script> alert("Telefoonnummer niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en/of probeer het opnieuw.") </script>';
            // echo '<script> window.location.href = "lid.php?lidnummer=' . $lidnummer . '" </script>';
            echo '<script> window.history.go(-1) </script>';         
        } 
        else
        {
            header("location: lid.php?lidnummer=$lidnummer");
        }
    
        $stmt_telnr->close();
        $conn->close();
    }
    
    if(isset($_POST['add_email']))
    {
        $email = get_post($conn, 'email');
        $lidnummer = get_post($conn, 'lidnummer');
    
        $stmt_email = $conn->prepare("INSERT INTO emails VALUES(?,?)");
        $stmt_email->bind_param('si', $email, $lidnummer);
        $stmt_email->execute();
    
        if($stmt_email->affected_rows != 1)
        { 
            echo '<script> alert("Emailadres niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en/of probeer het opnieuw.") </script>';
            // echo '<script> window.location.href = "lid.php?lidnummer=' . $lidnummer . '" </script>'; 
            echo '<script> window.history.go(-1) </script>';         
        } 
        else
        {
            header("location: lid.php?lidnummer=$lidnummer");
        }
    
        $stmt_email->close();
        $conn->close();
    }
    ?>
</div>

<div>
    <h3>Lid:</h3>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
        <table>
            <tbody>
                <tr>
                    <th>#</th>
                    <th>Info</th>
                    <th>Delete</th>
                </tr>
                <?php 
                $select_lid_query = "SELECT * FROM leden 
                                        INNER JOIN postcodes ON postcodes.postcode = leden.postcode
                                        WHERE lidnummer='$lidnummer'";

                $select_lid_result = $conn->query($select_lid_query);
                if(!$select_lid_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                                        Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

                $gegevens_lid = $select_lid_result->fetch_array(MYSQLI_ASSOC);

                foreach($gegevens_lid as $data => $info)
                {
                echo '<tr>';
                echo '<td><b>' . ucfirst(htmlspecialchars($data)) . '</b></td>';

                if($data == 'lidnummer' || $data == 'adres' || $data == 'woonplaats')
                {
                    echo '<td>' . htmlspecialchars($info) . '</td>';
                    echo '<td> ---- </td>';
                    
                } 
                elseif($data == 'postcode')
                {
                    echo '<td><input type="text" pattern="^[1-9][0-9]{3}[\s]?[A-Za-z]{2}" name="' . $data . '" value="' . $info . '" required></td>';
                    echo '<td> ---- </td>';
                }
                else
                {
                    echo '<td><input type="text" name="' . $data . '" value="' . $info . '" required></td>';
                    echo '<td> ---- </td>';
                }            
                echo '<input type="hidden" name="lidnummer" value="' . $gegevens_lid['lidnummer'] . '">'; 
                echo '</tr>';
                }

                toon_contactgegevens('telefoonnummers', $gegevens_lid, $conn, 'telefoonnummer', 'lid_table');
                toon_contactgegevens('emails', $gegevens_lid, $conn, 'email', 'lid_table');

                $select_lid_result->close();
                              
                ?>
                <td colspan="3" ><button type="submit" name="update_lid">Save</button></td>          
            </tbody>
        </table>
    </form>
    <?php
    if(isset($_POST['update_lid']))
    {
        $lidnummer = get_post($conn, 'lidnummer');
        $num_telnrs = get_post($conn, 'num-telnrs');
        $num_emails = get_post($conn, 'num-emails');
    
        $num_data_affected = 0;
        foreach($_POST as $data => $info)
        {        
            $info = get_post($conn, $data);
    
            if($data == 'naam' || $data == 'voornaam' || $data == 'huisnummer' || $data == 'postcode')
            {
                $stmt_data = $conn->prepare("UPDATE leden SET " . $data . "=? WHERE lidnummer=?");
                $stmt_data->bind_param('si', $info, $lidnummer);
                $stmt_data->execute();
    
                $affected_info = $stmt_data->affected_rows;
                $stmt_data->close();
            }    
            $num_data_affected += $affected_info;      
        }
        
        for($t = 0; $t < $num_telnrs; ++$t)
        {
            $telnr_num = $t + 1;
            $telnr_new = get_post($conn, 'telefoonnummer' . $telnr_num);
            $telnr_old = get_post($conn, 'oud-telnr' . $telnr_num);
    
            if($telnr_new != $telnr_old)
            {                  
                $stmt_del_tel = $conn->prepare("DELETE FROM telefoonnummers WHERE telefoonnummer=?");
                $stmt_del_tel->bind_param('s', $telnr_old);
                $stmt_del_tel->execute();
                
                $stmt_ins_tel = $conn->prepare("INSERT INTO telefoonnummers VALUES (?, ?)");
                $stmt_ins_tel->bind_param('si', $telnr_new, $lidnummer);
                $stmt_ins_tel->execute();
    
                $affected_tel = $stmt_ins_tel->affected_rows;
    
                $stmt_del_tel->close();
                $stmt_ins_tel->close();
            }
        }
        $num_data_affected += $affected_tel;
    
        for($t = 0; $t < $num_emails; ++$t)
        {
            $email_num = $t + 1;
            $email_new = get_post($conn, 'email' . $email_num);
            $email_old = get_post($conn, 'oud-email' . $email_num);
    
            if($email_new != $email_old)
            {                  
                $stmt_del_email = $conn->prepare("DELETE FROM emails WHERE email=?");
                $stmt_del_email->bind_param('s', $email_old);
                $stmt_del_email->execute();
                
                $stmt_ins_email = $conn->prepare("INSERT INTO emails VALUES (?, ?)");
                $stmt_ins_email->bind_param('si', $email_new, $lidnummer);
                $stmt_ins_email->execute();
    
                $affected_email = $stmt_ins_email->affected_rows;
    
                $stmt_del_email->close();
                $stmt_ins_email->close();
            }
        }
        $num_data_affected += $affected_email;
    
        if($num_data_affected < 1)
        {
            echo '<script> alert("Het lijkt erop dat niets gewijzigd is. Controleer alle gegevens. Ook of de postcode die u eventueel wilt toevoegen al bestaat. \n\nU wordt terug geleidt naar de pagina. Probeer het opnieuw.") </script>';
            echo '<script> window.history.go(-1) </script>'; 
        }
        else
        {
            echo '<script> alert("Gegevens aangepast. U kunt verder gaan met wijzigen of naar een andere pagina gaan.") </script>';
            echo '<script> window.location.href = "lid.php?lidnummer=' . $lidnummer . '" </script>';  
        }
    }

    $conn->close();  
    ?>
</div>

</body>
</html>