<?php 

require_once('xmlHandler.php');

if (!isset($_COOKIE["name"])) {
    header("Location: error.html");
    exit;
}

// create the chatroom xml file handler
$xmlh = new xmlHandler("chatroom.xml");
if (!$xmlh->fileExist()) {
    header("Location: error.html");
    exit;
}

// get the name from the cookie
$name = $_COOKIE["name"];
        
$xmlh->openFile();

// get the users element
$users_node = $xmlh->getElement("users");

// get all user nodes
$users_array = $xmlh->getChildNodes("user");

$messages_node = $xmlh->getElement("messages");
$messages_array = $xmlh->getChildNodes("message");

if($users_array != null) {
    // delete the current user from the users element
    foreach ($users_array as $user) {
        $username = $xmlh->getAttribute($user, "name");
        if ($username == $name)
            $xmlh->removeElement($users_node, $user);
    }
}

if($messages_node != null) {
    while($messages_node->hasChildNodes()){
        $messages_node->removeChild($messages_node->firstChild);
    }
}   
        
$xmlh->saveFile();

// clear the cookie
setcookie("name", "");

header("Location: login.html");

?>
