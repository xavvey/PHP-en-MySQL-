<!DOCTYPE html>
<html>
<head>
<title>Update lid</title>
  <link rel="stylesheet" type="text/css" href="includes/CSS/general_styling.css" /> 
</head>
<body>

<div>
    <h1>Update lid</h1>
    <a href='home_ledenlijst.php'>Naar ledenoverzicht</a><br>
</div>

<?php
include 'includes/read.php'; 
if(isset($_GET['lidnummer'])) 
{ 
    $lidnummer = $_GET['lidnummer'];
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
        <button type="submit" name='add_email'>Voeg email toe</button></b>
    </form><br>
</div>

<div>
    <h3>Lid:</h3>
    <table>
        <tbody>
            <tr>
                <td>#</td>
                <td>Info</td>
                <td>Pas aan</td>
                <td>Delete</td>
            </tr>
            <?php show_single_lid($conn, $lidnummer); ?>          
        </tbody>
    </table>
</div>
<?php } ?>


</body>
</html>