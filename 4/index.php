<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8' />
<script type="text/javascript" src="jquery.min.js"></script>
</head>
<body>
<?php 
    $colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
    $user_colour = array_rand($colours);
?>
    <script>
        
$(document).ready(function(){
    //create a new WebSocket object.
    var wsUri = "ws://192.168.1.42:9000";   
    websocket = new WebSocket(wsUri); 
    
    websocket.onopen = function(ev) { // connection is open 
        $('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
    }

    $('#send-btn').click(function(){ //use clicks message send button   
        var mymessage = $('#message').val(); //get message text
        var myname = $('#name').val(); //get user name
        
        if(myname == ""){ //empty name?
            alert("Enter your Name please!");
            return;
        }
        if(mymessage == ""){ //emtpy message?
            alert("Enter Some message Please!");
            return;
        }
        
        //prepare json data
        var msg = {
        message: mymessage,
        name: myname,
        color : '<?php echo $colours[$user_colour]; ?>'
        };
        //convert and send data to server
        websocket.send(JSON.stringify(msg));
    });
    
    //#### Message received from server?
    websocket.onmessage = function(ev) {
        var msg = JSON.parse(ev.data); //PHP sends Json data
        var type = msg.type; //message type
        //alert(type);
        var umsg = msg.message; //message text
        var uname = msg.name; //user name
        var ucolor = msg.color; //color

        if(type == 'usermsg') 
        {
            $('#message_box').append("<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+uname+"</span> : <span class=\"user_message\">"+umsg+"</span></div>");
        }
        if(type == 'system')
        {
            $('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
        }
        
        $('#message').val(''); //reset text
    };
    
    websocket.onerror   = function(ev){$('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
    websocket.onclose   = function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");}; 
});
        
    </script>
    
    
    <div class="chat_wrapper">
        <div class="message_box" id="message_box">
            
            <output id="list"></output>
        </div>
        <div class="panel">
        <input type="text" name="name" id="name" placeholder="Your Name" maxlength="10" style="width:20%"  />
        <input type="text" name="message" id="message" placeholder="Message" maxlength="80" style="width:60%" />
        <button id="send-btn">Send</button>
        </div>
    </div>
    

    <script>
      function handleFileSelect(evt) {
        evt.stopPropagation();
        evt.preventDefault();

        var files = evt.dataTransfer.files; // FileList object.

        // files is a FileList of File objects. List some properties.
        var output = [];
        for (var i = 0, f; f = files[i]; i++) {
          output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ') - ',
                      f.size, ' bytes, last modified: ',
                      f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a',
                      '</li>');
        }
        document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
      }

      function handleDragOver(evt) {
        evt.stopPropagation();
        evt.preventDefault();
        evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
      }

      // Setup the dnd listeners.
      var dropZone = document.getElementById('message_box');
      dropZone.addEventListener('dragover', handleDragOver, false);
      dropZone.addEventListener('drop', handleFileSelect, false);
    </script>
    <style>
        #message_box {
          border:2px dashed #BBBBBB;
          border-radius:5px;
          color:#BBBBBB;
          font-family:bold, Vollkorn;
          font-size:10pt;
          font-stretch:normal;
          font-style:normal;
          font-variant:normal;
          font-weight:normal;
          line-height:normal;
          padding:25px;
        }
    </style>
    
    
</body>
</html>