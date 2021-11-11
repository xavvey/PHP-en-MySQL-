<?php 
require_once 'includes/connection.php';

if(isset($_GET['lidnummer']) && $row['lidnummer'] == $_GET['lidnummer'])
{ 
    $lidnummer = $_GET['lidnummer']; 
    
    $select_lid_query = "SELECT * FROM leden WHERE lidnummer='$lidnummer'
                            NATURAL JOIN postcodes
                            NATURAL JOIN telefoonnummers
                            NATURAL JOIN emails";
    
    $select_lid_result = $conn->query($select_lid_query);
    $rows = $select_lid_result->num_rows;
    
    for($j = 0 ; $j < $rows ; ++$j) 
    { 
        $row = $result->fetch_array(MYSQLI_ASSOC);
        echo $row;
        printf($row);
        echo htmlspecialchars($row['naam']);

    }
    echo '<tr>';
    echo '<td>Naam:</td>';
    echo '<td>' ;

} 
?>