<?php

// get the name from cookie
$name = "";
if (isset($_COOKIE["name"])) {
    $name = $_COOKIE["name"];
}

print "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Message Page</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script language="javascript" type="text/javascript">
        //<![CDATA[
        function Message(name, content) {
            this.name = name;
            this.content = content;
        }

        var chatroom ;
        var loadTimer = null;
        var request;
        var datasize;
        var lastMsgID;

        function load() {
            var username = document.getElementById("username");
            if (username.value == "") {
                loadTimer = setTimeout("load()", 100);
                return;
            }


            loadTimer = null;
            datasize = 0;
            lastMsgID = 0;
            chatroom = document.getElementById('chatroom');
            getUpdate();
            chatroom.scrollTop.value = chatroom.scrollHeight.value
            console.log("scroll top " + chatroom.scrollTop); 
        }

        function unload() {
            var username = document.getElementById("username");
            if (username.value != "") {
                request = new XMLHttpRequest();
                request.open("POST", "logout.php", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(null);
                username.value = "";
            }
            if (loadTimer != null) {
                loadTimer = null;
                clearTimeout("load()", 100);
            }
        }

        function getUpdate() {
            request = new XMLHttpRequest();
            request.onreadystatechange = stateChange;
            request.open("POST", "server.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("datasize=" + datasize);
        }

        function stateChange() {
            if (request.readyState == 4 && request.status == 200 && request.responseText) {
                var xmlDoc;
                try {
                    xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                    xmlDoc.loadXML(request.responseText);
                } catch (e) {
                    var parser = new DOMParser();
                    xmlDoc = parser.parseFromString(request.responseText, "text/xml");
                }
                datasize = request.responseText.length;
                updateChat(xmlDoc);
                getUpdate();
            }
        }

        function updateChat(xmlDoc) {
            var messages = xmlDoc.getElementsByTagName("message");
            var msgStr = []
            
            for (var i = lastMsgID; i < messages.length; i++) {
                // Obtain user name and message content from each message node,
                // and add to the variable msg
                // We use "|" as a separator to separate each user name and message content
                var msg = messages.item(i);
                msgStr.push(new Message(msg.getAttribute("name"), msg.firstChild.nodeValue))
            }
            lastMsgID = messages.length;
            console.log("updateChat" +msgStr.length);

            addMsgsToChatroom(msgStr);
        }

        function addMsgsToChatroom(msgStr){
            for (var i = 0; i < msgStr.length ; i++){
                var msg = document.createElement("p");
                var node = document.createTextNode(msgStr[i].name + " : " +msgStr[i].content);
                msg.appendChild(node);
                chatroom.appendChild(msg);
            }
            chatroom.scrollTop = chatroom.scrollHeight  
        }

        //]]>
        </script>
    </head>
    <body style="text-align: left" onload="load()" onunload="unload()">
        <!--
        <object id="chatroom_object">
            <embed classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0"
                id="chatroom" width="800" height="350">
                <param name="movie" value="chat.swf" />
                <param name="quality" value="high" />
                <param name="play" value="false" />
                <param name="swliveconnect" value="true" />
            </embed>
        </object>-->
        <div id="chatroom">
            Chat
        </div>
        <form action="">
            <input type="hidden" value="<?php print $name; ?>" id="username" />
        </form>
    </body>
</html>
