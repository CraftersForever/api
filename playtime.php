<?php
header("Access-Control-Allow-Origin: *");


function getPlayTimeFromPlayer($playerUUID)
{
    $servername = "localhost";
    $port = "3306";
    $database = "****"; #hidden for security reasons
    $username = "****"; #hidden for security reasons
    $password = "****"; #hidden for security reasons
    $output = "";
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        echo $conn->connect_error;
        $output = "{\"Error\":\"Connection to Database failed\"}";
    } else {
        mysqli_select_db($conn, $database);
        $sql = "SELECT COUNT(*)*10 AS playtime FROM OnlineSpieler WHERE Spieler_UUID = \"" . $playerUUID . "\";";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            $output = "{\"Error\":\"SQL Error\"}";
        } else {
            $playtime = 0;
            foreach ($result as $row) {
                $playtime = $row['playtime'];
            }
            $playtime = $playtime / 60;
            $output = '{"uuid":"'.$playerUUID.'","playtime":'.$playtime.'}';
        }
    }
    echo $output;
}

function getUUIDFromGetParameter()
{
    $input = $_GET['uuid'];
    if (!isset($input)) {
        die("{\"Error:\":\"No UUID passed to the server. Use GET Parameter 'uuid'\"");
    }
    $uuid = trim($input);
    $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
    preg_match($UUIDv4, $uuid) or die('{\"Error\":\"Invalid UUID! Please use UUID with slashes\"}');
    return $uuid;
}

getPlayTimeFromPlayer(getUUIDFromGetParameter());
?>
