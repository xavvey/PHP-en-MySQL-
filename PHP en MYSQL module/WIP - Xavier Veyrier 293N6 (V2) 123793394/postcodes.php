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

    <form action="includes/create.php" method="POST"><b>
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
</div>

<div>
    <h3>Postcodes:</h3>
    <?php 
    include 'includes/read.php';
    show_postcode_table($conn); 
    ?>
</div>

</body>
</html>