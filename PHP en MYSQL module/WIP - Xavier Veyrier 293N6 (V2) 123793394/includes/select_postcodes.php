<?php
require_once __DIR__ . '../connection.php';

$postcode_query = "SELECT * FROM postcodes ORDER BY postcode";

$postcode_result = $conn->query($postcode_query);
if(!$postcode_result) die ("<span style='color:red'>" . "Kon geen gegevens van de database ophalen. 
                    Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw" . "</span>");

$num_postcodes = $postcode_result->num_rows;

for($p = 0; $p < $num_postcodes; ++$p)
{
    $row = $postcode_result->fetch_array(MYSQLI_ASSOC);

    $postcode = htmlspecialchars($row['postcode']);
    echo "<option value='$postcode'>$postcode</option>"; 
}
?>
