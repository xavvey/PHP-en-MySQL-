<?php
require_once __DIR__ . '../connection.php';

$postcode_query = "SELECT * FROM postcodes
                        ORDER BY postcode"; 
                            

$postcode_result = $conn->query($postcode_query);
if(!$postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                    Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

$num_postcodes = $postcode_result->num_rows;
$lidnummer = $_GET['lidnummer'];

if($lidnummer)
{   
    $lid_postcode_query = "SELECT postcode FROM leden WHERE lidnummer='$lidnummer'"; 
    $lid_postcode_result = $conn->query($lid_postcode_query);
    if(!$lid_postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                        Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");
    
    $lid_row = $lid_postcode_result->fetch_array(MYSQLI_ASSOC);   
    $lid_postcode = htmlspecialchars($lid_row['postcode']);

    echo "<option value='$lid_postcode' selected>$lid_postcode</option>";
    for($p = 0; $p < $num_postcodes; ++$p)
    {
        $row = $postcode_result->fetch_array(MYSQLI_ASSOC);

        $postcode = htmlspecialchars($row['postcode']);
        echo "<option value='$postcode'>$postcode</option>"; 
    }
} 
else 
{
    for($p = 0; $p < $num_postcodes; ++$p)
    {
        $row = $postcode_result->fetch_array(MYSQLI_ASSOC);

        $postcode = htmlspecialchars($row['postcode']);
        echo "<option value='$postcode'>$postcode</option>"; 
    }
}





// if(!$lidnummer)
// {   
//     for($p = 0; $p < $num_postcodes; ++$p)
//     {
//         $row = $postcode_result->fetch_array(MYSQLI_ASSOC);
    
//         $postcode = htmlspecialchars($row['postcode']);
//         echo "<option value='$postcode'>$postcode</option>"; 
//     }
// }
// else
// {
//     $lid_postcode_query = "SELECT postcode FROM leden WHERE lidnummer='$lidnummer'"; 
//     $lid_postcode_result = $conn->query($lid_postcode_query);
//     if(!$lid_postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
//                         Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

//     $lid_row = $lid_postcode_result->fetch_array(MYSQLI_ASSOC);   
//     $lid_postcode = htmlspecialchars($lid_row['postcode']);

//     for($p = 0; $p < $num_postcodes; ++$p)
//     {
//         $row = $postcode_result->fetch_array(MYSQLI_ASSOC);

//         $postcode = htmlspecialchars($row['postcode']);
//         echo "<option value='$lid_postcode' selected>$lid_postcode</option>";
//         echo "<option value='$postcode'>$postcode</option>"; 
//     }
// }
?>
