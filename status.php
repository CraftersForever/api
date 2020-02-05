<?php
header("Access-Control-Allow-Origin: *");
$servername = "localhost";
$port = "3306";
$database = "status";
$username = "status";
$password = "****"; #Hidden for security reasons


$conn   = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    echo $conn->connect_error;
    die('{"Error":"Connection to Database failed"}');
}

mysqli_select_db($conn, $database);
$sql    = "SELECT
  subset.timestamp as timestamp,
  subset.slots as slots,
  subset.status as status,
  Spieler.Spieler_UUID as Spieler_UUID,
  Spieler.Spieler_Name as Spieler_Name
FROM
  (
    SELECT
      *
    FROM
      Status
    HAVING
      Status.timestamp = (SELECT MAX(Status.timestamp) as max FROM Status)
  ) as subset
  LEFT JOIN OnlineSpieler on subset.timestamp = OnlineSpieler.timestamp
  LEFT JOIN Spieler on OnlineSpieler.Spieler_UUID = Spieler.Spieler_UUID
";
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
	
    if ($row['Spieler_UUID'] != null) {
        $onlinePlayer    = array(
            "uuid" => $row['Spieler_UUID'],
            "name" => $row['Spieler_Name']
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
