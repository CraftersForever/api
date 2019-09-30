<?php
header("Access-Control-Allow-Origin: *");
$servername = "localhost";
$port = "3306";
$database = "****"; #hidden for security reasons
$username = "****"; #hidden for security reasons
$password = "****"; #hidden for security reasons

$output = "";
$conn = new mysqli($servername, $username, $password);
if($conn -> connect_error){
    echo $conn->connect_error;
    $output = "{\"Error\":\"Connection to Database failed\"}";
}else{
    mysqli_select_db($conn,$database);
    $sql = "SELECT * FROM Status WHERE timestamp = (SELECT MAX(timestamp) as max FROM Status);";
    $result = mysqli_query($conn, $sql);
    if(!$result){
        $output = "{\"Error\":\"SQL Error\"}";
    }else{
        $timestamp = 0;
        $maxslots = 0;
        $status = 0;
        foreach($result as $row){
            $timestamp = $row['timestamp'];
            $maxslots = $row['slots'];
            $status = $row['status'];
        }

        if(outdated($timestamp)){
            $output = "{\"status\":\"offline\"}";
        }else{
            $onlinePlayers = array();
            $sqlOnlinePlayers = "SELECT Spieler.Spieler_UUID, Spieler.Spieler_Name FROM OnlineSpieler INNER JOIN Spieler on OnlineSpieler.Spieler_UUID = Spieler.Spieler_UUID WHERE timestamp=".$timestamp.";";
            $resultOnlinePlayers = mysqli_query($conn, $sqlOnlinePlayers);
            foreach($resultOnlinePlayers as $resultOnlinePlayer){
                $onlinePlayer = array(
                    "uuid" => $resultOnlinePlayer['Spieler_UUID'],
                    "name" => $resultOnlinePlayer['Spieler_Name']);
                $onlinePlayers[] = $onlinePlayer;
            }
            $outputObject = array(
                "status" => "online",
	"slots" => $maxslots,
	"players" => $onlinePlayers);
	$output = json_encode($outputObject);
}
    }
}
echo $output;


function outdated($entered){
    $current = round(microtime(true)*1000);
    $diff = $current - $entered;
    if($diff >62000){
        return true;
    }else{
        return false;
    }
}

?>
