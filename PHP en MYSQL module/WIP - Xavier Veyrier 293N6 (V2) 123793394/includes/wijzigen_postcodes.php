<?php
require_once __DIR__ . '../connection.php';

$check_postcodes = "SELECT 1 FROM postcodes"; 
$postcodes_bestaan = $conn->query($check_postcodes);

if(isset($_POST["add_postcode"]))
{
    $postcode   = get_post($conn, "postcode");
    $adres      = get_post($conn, "straat");
    $woonplaats = get_post($conn, "woonplaats");

    $stmt = $conn->prepare('INSERT INTO postcodes VALUES(?, ?, ?)');
    $stmt->bind_param('sss', $postcode, $adres, $woonplaats);

    $stmt->execute();

    if($stmt->affected_rows != 1)
    { 
        echo '<script> alert("Postcode niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en/of probeer het opnieuw.") </script>';
        echo '<script> window.location.href = "../postcodes.php" </script>';         
    } 
    else
    {
        header("location: ../postcodes.php");
    }

    $stmt->close();    
}        

function get_post($conn, $var)
{
    return $conn->real_escape_string($_POST[$var]);
}

function show_postcodes_table($connection)
{
    $postcodes_query = "SELECT * FROM postcodes
                            ORDER BY postcode";

    $result = $connection->query($postcodes_query);
    if(!$result) die ("Kon geen postcodes ophalen van de database. Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw");
    $rows = $result->num_rows;

    echo '<tr>';
    echo '<td>Postcode</td>';
    echo '<td>Straat</td>';
    echo '<td>Woonplaats</td>';
    echo '<td>Delete</td>';
    echo '<tr>';
    
    for($j = 0 ; $j < $rows ; ++$j) 
    { 
        $row = $result->fetch_array(MYSQLI_ASSOC);

        echo '<tr>';
        echo '<td>' . htmlspecialchars($row["postcode"])    . '</td>';
        echo '<td>' . htmlspecialchars($row["adres"])       . '</td>';
        echo '<td>' . htmlspecialchars($row["woonplaats"])  . '</td>';
        echo '<td><a href="includes/delete.php?postcode=' . $row["postcode"] . '">Delete</a></td>';
        echo "</tr>";
    } 
}
?>