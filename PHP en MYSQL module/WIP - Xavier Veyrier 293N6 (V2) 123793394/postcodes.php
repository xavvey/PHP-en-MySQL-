<!DOCTYPE html>
<html>
<head>
<title>Postcodes wijzigen</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" /> 
</head>
<body>

<div>
    <h1>Postcode overzicht</h1>
    <a href='index.php'>Naar ledenoverzicht</a>
</div>  

<div class='postcode-form'>
    <h3>Voeg nieuwe postcode toe</h3>

    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST"><b>
        <label for="postcode">
            Postcode:
            <input type="text" name="postcode" pattern='^[1-9][0-9]{3}?[A-Z]{2}' placeholder="1234AB" required>
        </label>
        <label for="straat">
            Straat:
            <input type="text" name="straat" placeholder = "Kerkstraat" required>
        </label>
        <label for="woonplaats">
            Woonplaats:
            <input type="text" name="woonplaats" placeholder = "Alkmaar" required>
        </label></b><button type="submit" name='add_postcode'>Voeg postcode toe</button>
    </form><br>
    <?php
    require_once 'includes/connection.php';
    include 'includes/helpers.php';

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
            echo '<script> alert("Postcode niet toegevoegd. Waarschijnlijk bestaat deze al. Controleer de lijst en probeer het opnieuw.") </script>';
            echo '<script> window.history.go(-1) </script>';          
        } 
        else
        {
            header("location: postcodes.php");
        }
    
        $stmt->close();
        $conn->close();    
    } 
    ?>
</div>

<div>
    <h3>Postcodes:</h3>
    <?php 
    $postcodes_query = "SELECT * FROM postcodes
                            ORDER BY postcode";

    $result = $conn->query($postcodes_query);
    if(!$result) die ("<span style='color:red'>" . "Kon geen postcodes ophalen van de database. Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw". "</span>");
    $rows = $result->num_rows;

    if($rows < 1) { echo '<h3>Er staan nog geen postcodes in de database. Voeg eerst een postcode toe.</h3>'; }
    else 
    {
        echo '<table>';
        echo '<tbody>';
        echo '<tr>';
        echo '<th>Postcode    </th>';
        echo '<th>Straat      </th>';
        echo '<th>Woonplaats  </th>';
        echo '<th>Update      </th>';
        echo '<th>Delete      </th>';
        echo '</tr>';

        for($j = 0 ; $j < $rows ; ++$j) 
        { 
            $row = $result->fetch_array(MYSQLI_ASSOC);

            echo '<tr>';
            if($row['postcode'] == $_GET['postcode'])
            {
                echo '<form action="includes/update.php" method="POST">';
                echo '<td>' . htmlspecialchars($row["postcode"])    . '</td>';
                echo '<td><input type="text" name="adres" value="' . htmlspecialchars($row["adres"]) . '" required></td>';
                echo '<td><input type="text" name="woonplaats" value="' . htmlspecialchars($row["woonplaats"]) . '" required></td>';
                echo '<td><button type="subbmit">Save</td>';
                echo '<td>----</td>';
                echo '<input type="hidden" name="postcode" value="'. $row['postcode'] . '">';
                echo '</form>';
            }
            else
            {              
                echo '<td>' . htmlspecialchars($row["postcode"])    . '</td>';
                echo '<td>' . htmlspecialchars($row["adres"])       . '</td>';
                echo '<td>' . htmlspecialchars($row["woonplaats"])  . '</td>';
                echo '<td><a href="postcodes.php?postcode=' . $row['postcode'] . '">Update</a></td>';
                echo '<td><a href="includes/delete.php?postcode=' . $row["postcode"] . '">Delete</a></td>';
                
            }
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    $result->close();
    $conn->close();
    ?>
</div>

</body>
</html>