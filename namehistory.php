<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type:application/json");

(isset($_GET['uuid'])&&$_GET['uuid'] != "") or die('{"Error":"No Username or UUID given!"}');;

$uuid = $_GET['uuid'];
if(strlen($uuid)<=16 && strlen($uuid)>=3){
  $usernameRegex= "/[a-zA-Z0-9_]{3,16}/"; #According to https://help.mojang.com/customer/en/portal/articles/928638-minecraft-usernames?b_id=5408
  preg_match($usernameRegex, $uuid) or die('{"Error":"Invalid Username!"}');
  #Convert username too uuid
  
  $uuid = getUUIDFromUsername($uuid);
}
$uuid = preg_replace('/[\s-]/', '', $uuid);
$UUIDRegex = '/^[0-9a-f]{32}$/';
preg_match($UUIDRegex, $uuid) or die('{"Error":"Invalid UUID!"}');
$url = "https://api.mojang.com/user/profiles/".$uuid."/names";
$response = file_get_contents($url);
if($response != null) or die('{"Error":"Mojang does not know that UUID!"}');
print_r($response);



function getUUIDFromUsername($username){
	$url = "https://api.mojang.com/users/profiles/minecraft/".$username;
	$obj = file_get_contents($url);
	($obj != null) or die('{"Error":"Mojang does not know that username!"}');
	$uuid = json_decode($obj)->id;
  return $uuid;
}
?>
