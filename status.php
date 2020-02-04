<?php
header("Access-Control-Allow-Origin: *");
$servername = "localhost";
$port       = "3306";
$database   = "****"; #hidden for security reasons
$username   = "****"; #hidden for security reasons
$password   = "****"; #hidden for security reasons

$conn   = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    echo $conn->connect_error;
    die('{\"Error\":\"Connection to Database failed\"}');
}

mysqli_select_db($conn, $database);
$sql    = "SELECT
  Status.timestamp,
  Spieler.Spieler_UUID,
  Spieler.Spieler_Name
FROM
  Status
  LEFT JOIN OnlineSpieler on Status.timestamp = OnlineSpieler.timestamp
  INNER JOIN Spieler on OnlineSpieler.Spieler_UUID = Spieler.Spieler_UUID
WHERE
  Status.timestamp = (
    SELECT
      MAX(Status.timestamp)
    FROM
      Status";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('{"Error":"SQL Error"}');
}
$timestamp     = 0;
$maxslots      = 0;
$status        = 0;
$onlinePlayers = array();

foreach ($result as $row) {
    $timestamp = $row['timestamp'];
    $maxslots  = $row['slots'];
    $status    = $row['status'];
    if ($result['Spieler_UUID'] != null) {
        $onlinePlayer    = array(
            "uuid" => $result['Spieler_UUID'],
            "name" => $result['Spieler_Name']
        );
        $onlinePlayers[] = $onlinePlayer;
    }
}

if (outdated($timestamp)) {
    die('{"status":"offline"}');
}
$outputObject = array(
    "status" => "online",
    "slots" => $maxslots,
    "players" => $onlinePlayers
);
echo json_encode($outputObject);




function outdated($entered)
{
    $current = round(microtime(true) * 1000);
    $diff    = $current - $entered;
    if ($diff > 62000) {
        return true;
    } else {
        return false;
    }
}

?>
