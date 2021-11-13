<?php
require_once 'includes/connection.php';

if(isset($_GET['lidnummer']))
{ 
    $lidnummer = $_GET['lidnummer']; 

    $select_lid_query = "SELECT * FROM leden 
                            JOIN postcodes
                            WHERE lidnummer='$lidnummer'";             
    $select_lid_result = $conn->query($select_lid_query);
    $gegevens_lid = $select_lid_result->fetch_array(MYSQLI_ASSOC);

    foreach($gegevens_lid as $data => $info)
    {
        if($data == 'lidnummer' || $data == 'adres' || $data == 'woonplaats')
        {
            echo '<tr>';
            echo '<td>' . ucfirst(htmlspecialchars($data)) . '</td>';
            echo '<td>' . htmlspecialchars($info) . '</td>';
            echo '<td> ---- </td>';
            echo '</tr>';
        } 
        else
        {
            echo '<tr>';
            echo '<td>' . ucfirst(htmlspecialchars($data)) . '</td>';
            echo '<td>' . htmlspecialchars($info) . '</td>';
            echo '<td> update </td>';
            echo '</tr>';
        }
    }

    $select_lid_telnrs = "SELECT * FROM telefoonnummers
                            WHERE lidnummer='$lidnummer'";
    
    $lid_telnrs_result = $conn->query($select_lid_telnrs);
    $num_telrs = $lid_telnrs_result->num_rows;

    $num = 1;
    for($t = 0; $t < $num_telrs; ++$t)
    {
        $tel_row = $lid_telnrs_result->fetch_array(MYSQLI_ASSOC);

        echo '<tr>';
        echo '<td> Telefoon' . " ". $num  . '</td>';
        echo '<td>' . htmlspecialchars($tel_row['telefoonnummer']) . '</td>';
        echo '<td> update </td>';
        echo '</tr>';

        $num += 1;
    }
} 
?>