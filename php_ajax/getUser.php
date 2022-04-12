<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>uzivatele</title>
</head>
<body>

<?php
    $q = intval($_GET['q']);
    var_dump($q);
    $con = mysqli_connect('localhost', 'root', 'root', 'ajax_php');
    if (!$con) {
        die('chyba pripojeni k databazi ...');
    }
    mysqli_select_db($con, 'ajax_php');
    $sql = "SELECT * FROM users where id = $q";
    $result = mysqli_query($con, $sql);
    // print_r($result);

    echo "
        <table>
            <tr>
            <th>id</th>
            <th>firstname</th>
            <th>lastname</th>
            <th>age</th>
            <th>hometown</th>
            <th>job</th>
            </tr>
        ";
    while($row = mysqli_fetch_array($result)){
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['firstname'] . "</td>";
        echo "<td>" . $row['lastname'] . "</td>";
        echo "<td>" . $row['age'] . "</td>";
        echo "<td>" . $row['hometown'] . "</td>";
        echo "<td>" . $row['job'] . "</td>";
        echo "</tr>";
        
    }
    echo "</table>";
    mysqli_close($con);

?> 

</body>
</html>