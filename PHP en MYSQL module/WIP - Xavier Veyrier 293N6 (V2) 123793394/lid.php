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
include 'includes/read.php'; 

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
            <input type="text" name="telefoonnummer">
        </label>
        <input type='hidden' name='lidnummer' value='<?php echo $lidnummer ?>'>
        <button type="submit" name='add_telnr'>Voeg telnr toe</button>
    </form><br>
    <form action="includes/create.php" method="POST">        
        <label for="email">
            Email:
            <input type="text" name="email">
        </label>
        <input type='hidden' name='lidnummer' value='<?php echo $lidnummer ?>'>
        <button type="submit" name='add_email'>Voeg email toe</button></b>
    </form><br>
</div>

<div>
    <h3>Lid:</h3>
    <table>
        <tbody>
            <tr>
                <th>#</th>
                <th>Info</th>
                <th>Pas aan</th>
                <th>Delete</th>
            </tr>
            <?php show_single_lid($conn, $lidnummer); ?>          
        </tbody>
    </table>
</div>



</body>
</html>