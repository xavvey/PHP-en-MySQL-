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
            <?php 
            include 'includes/read.php';
            show_single_lid($conn);
            ?>          
        </tbody>
    </table>
</div>


</body>
</html>