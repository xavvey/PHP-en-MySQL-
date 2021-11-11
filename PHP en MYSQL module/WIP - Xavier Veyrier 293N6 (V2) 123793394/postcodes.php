<!DOCTYPE html>
<html>
<head>
<title>Postcodes wijzigen</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" /> 
</head>
<body>

<div>
    <h1>Postcode overzicht</h1>
    <a href='home_ledenlijst.php' target='blank'>Naar ledenoverzicht</a>
</div>  

<div class='postcode-form'>
    <h3>Voeg nieuwe postcode toe</h3>

    <form action="includes/add.php" method="POST"><b>
        <label for="postcode">
            Postcode:
            <input type="text" name="postcode" pattern='^[1-9][0-9]{3}[\s]?[A-Za-z]{2}' placeholder="1234AB" required>
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
</div>

<div>
    <h3>Postcodes:</h3>

    <?php 
    require_once 'includes/connection.php';

    $check_postcodes = "SELECT 1 FROM postcodes"; 
    $postcodes_bestaan = $conn->query($check_postcodes);
    if(!$postcodes_bestaan) { echo '<h3>Er staan nog geen postcodes in de database. Voeg eerst een postcode toe.</h3>'; }
    else { ?>
    <table>
        <tbody>
            <tr>
                <td>Postcode    </td>
                <td>Straat      </td>
                <td>Woonplaats  </td>
                <td>Delete      </td>
            <tr>
            <?php
            $postcodes_query = "SELECT * FROM postcodes
                                    ORDER BY postcode";

            $result = $conn->query($postcodes_query);
            if(!$result) die ("<span style='color:red'>" . "Kon geen postcodes ophalen van de database. Klik a.u.b. op het pijltje terug in de browser en probeert u het opnieuw". "</span>");
            $rows = $result->num_rows;

            for($j = 0 ; $j < $rows ; ++$j) 
            { 
            $row = $result->fetch_array(MYSQLI_ASSOC); ?>
            <tr>
                <td><?php echo htmlspecialchars($row["postcode"]) ?>    </td>
                <td><?php echo htmlspecialchars($row["adres"]) ?>       </td>
                <td><?php echo htmlspecialchars($row["woonplaats"]) ?>  </td>
                <td><?php echo '<a href="includes/delete.php?postcode=' . $row["postcode"] . '">Delete</a></td>' ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>

</body>
</html>