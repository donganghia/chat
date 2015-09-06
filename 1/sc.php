<?php
include_once('common.php');
$name = (isset($_GET['name']))? $_GET['name'] : '';
$cpcode = (isset($_GET['cpcode']))? $_GET['cpcode'] : '';
if(is_string($name)) { $name = trim($name); } else { $name = ''; }
if(is_string($cpcode)) { $cpcode = trim($cpcode); } else { $cpcode = ''; }
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Message</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript" src="js/socket.io.js"></script>
<script type="text/javascript" src="js/simplewebrtc.js"></script>
<script type="text/javascript" src="js/messagechat.js"></script>
<script>
	// var site = '<?php // echo $site; ?>';
	var site_url = window.location.protocol+'//'+'<?php echo $site_uri; ?>';
	var port = '<?php echo $port; ?>';
    var group_prefix = '<?php echo $group_prefix; ?>';
</script>
</head>
<body>
<div id="identity" style="border:1px solid #cccccc; background:#c0c0c0; padding:5px; margin-bottom:1px;">
	<span style="display:inline-block;">
		<label style="width:10px;">Username:</label>
		<input type="text" id="nam" name="nam" title="Enter your name" value="<?php echo $name; ?>" />
	</span>
	<span style="display:inline-block;">
		<label style="width:10px;">Email - Id:</label>
		<input type="text" id="eid" name="eid" title="Enter your email-id"/>
	</span>
	<span style="display:inline-block;">
		<label style="width:10px;"><?php echo ($name != '' && $cpcode != '')? 'New' : ''; ?> Password:</label>
		<input type="password" id="pass" name="pass" title="Enter your password"/>
	</span>
	<span id="cp" style="display:none;">
		<span id="uname"><?php echo $name; ?></span>
		<span id="cpcode"><?php echo $cpcode; ?></span>
	</span>
	<input class="pointer" id="start" name="start" type="button" value="Chat" />
	<input class="pointer" id="fp" name="fp" type="button" value="?" title="Forgot Password" />
</div>
<div id="sc">
	<div id="menu" style="width:99.7%; background:#c0c0c0; border:1px solid #cccccc; display:inline-block; vertical-align:top; padding:1px;">
		<div class="mlist pointer">
			<span id="profile" style="display:inline-block;" title="Profile">[@]</span> <!--icn-->
			<span id="search" style="display:inline-block;" title="Search People">[S]</span>
			<span id="contacts" style="display:inline-block;" title="Contacts">[C]</span>
			<span id="groups" style="display:inline-block;" title="Groups">[G]</span>
			<span id="status" style="display:inline-block; color:#a0a0a0;" title="Status">[M]</span>
			<span id="logout" style="display:inline-block; color:#a0a0a0;" title="Logout">[O]</span>
		</div>
	</div>
	<div id="profile_box" style="display:none; background:#f3f3f3; border:1px solid #e0e0e0;">
		<span id="pro_name"></span>
	</div>
	<div id="search_box" style="border:1px solid #cccccc; padding:1px; margin-bottom:1px; display:none;">
		<div style="margin-bottom:1px;"><label style="width:10px;">Find People:</label> <input type="text" id="ssrch" name="ssrch" title="Find People" style="width:70%;" /></div>
		<div id="slist" style="max-height:100px; overflow:auto; display:none;"></div>
	</div>
	<div id="group_box" style="border:1px solid #cccccc; padding:1px; margin-bottom:1px; display:none;">
		<div style="margin-bottom:1px;">
			<label style="width:10px;">Add Contacts:</label> <input type="text" id="grpcon" name="grpcon" title="Add Contacts" style="width:70%;" /> <br/>
			<label style="width:10px;">Select Groups:</label> <input type="text" id="grpnms" name="grpnms" title="Select Groups" style="width:70%;" /> <button id="mgrp" name="mgrp">Submit</button>
		</div>
	</div>
	<div id="contact_box" style="border:1px solid #cccccc; padding:1px; margin-bottom:1px; display:none;">
		<div style="margin-bottom:1px;"><label style="width:87px; display:inline-block;">Filter By :</label> <input type="text" id="csrch" name="csrch" title="Find Contacts" style="width:70%;" /></div>
		<div id="clist" style="max-height:100px; overflow:auto; display:none;"></div>
		<div id="rlist" style="max-height:100px; overflow:auto; display:none;"></div>
		<div id="glist" style="max-height:100px; overflow:auto; display:none;"></div>
		<div id="grlist" style="max-height:100px; overflow:auto; display:none;"></div>
	</div>
	<div id="msgchat" style="width:100%; display:inline-block;">
		<div id="srch_msgs" style="display:none"></div>
		<div class="mc_frlist" style="display:none"></div>
		<div class="ctabs" style="line-height:1px; margin-top:1px;">
			<span class="tab-active pointer" style="display:inline-block; width:150px; max-width:150px; height:13px; overflow:hidden; text-align:center; border:1px solid #cccccc; padding:3px; text-transform:capitalize; line-height:15px;" title="General" rel=""><label style="display:inline-block; width:100px; overflow:hidden;">General</label> <span></span> <!--(<b class="pointer" style="text-transform:lowercase;">x</b>)--></span>
		</div>
		<div class="mc_msglist">
			<div class="General" style="height:159px; overflow:auto; border:1px solid #acacac;" rel=""></div>
		</div>
		<div class="frmreply">
			<form name="frmreply" id="frmreply" method="post">
				<input type="hidden" id="name" name="name" style="display:none; visibility:hidden;" />
				<input type="hidden" id="ci" name="ci" style="display:none; visibility:hidden;" />
				<input type="hidden" id="gcid" name="gcid" style="display:none; visibility:hidden;" />
				<textarea id="vMessage" name="vMessage" style="width:99.5%; height:50px; border:1px solid #acacac;" maxlength="10101"></textarea>
				<div style="float:right;">
					<input type="checkbox" checked="checked" class="alertsound" /> Sound Alert
					<input type="checkbox" checked="checked" class="scrollnew" /> Scroll
					[<a id="history" href="" target="_blank">History</a>]
				</div>
				<div id="uploading">
					<input type="file" id="files" name="files[]" multiple="multiple" />
					<div id="files_list"></div>
				</div>
				<input type="button" id="send" name="send" value="Send" />
				<input type="button" id="avchat" name="avchat" value="AV-CHAT" />
				<input type="button" id="notify" value="Notifications" style="float:right;" />
			</form>
		</div>
		<div class="soundalert" style="position:absolute; left:50000px;"></div>
	</div>
	<div id="avc" style="display:none; margin-left:5px;width:1%;vertical-align:top;">
		<video id="selfvid" name="selfvid" autoplay controls></video>
		<div id="rvids"></div>
	</div>
</div>
<script type="text/javascript" src="js/chat.js" async="async"></script>
</body>
</html>