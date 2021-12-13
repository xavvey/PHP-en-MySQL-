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

if(isset($_GET['lidnummer'])) 
{ 
    $lidnummer = $_GET['lidnummer'];
}
?>
<div class="contact-form">     
    <h3>Voeg contactgegevens toe:</h3>
    <form action="includes/create.php" method="POST"><b>
        <label for="telefoonnummer">
            Telefoonnummer:
            <input type="text" name="telefoonnummer" maxlength="13" required>
        </label>
        <input type='hidden' name='lidnummer' value='<?php echo $lidnummer ?>'>
        <button type="submit" name='add_telnr'>Voeg telnr toe</button>
    </form><br>
    <form action="includes/create.php" method="POST">        
        <label for="email">
            Email:
            <input type="email" name="email" required>
        </label>
        <input type='hidden' name='lidnummer' value='<?php echo $lidnummer ?>'>
        <button type="submit" name='add_email'>Voeg email toe</button></b>
    </form><br>
</div>

<div>
    <h3>Lid:</h3>
    <form action="includes/update.php" method="POST">
        <table>
            <tbody>
                <tr>
                    <th>#</th>
                    <th>Info</th>
                    <th>Delete</th>
                </tr>
                <?php 
                include 'includes/helpers.php';

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
                $conn->close();                
                ?>
                <td colspan="3" ><button type="submit">Save</button></td>          
            </tbody>
        </table>
    </form>
</div>

</body>
</html>