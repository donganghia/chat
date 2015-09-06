
var uc = 1;
var ms;
var name = '';
var valert = 0;
var pmkc = '';
var flcf = '';
// var site_url = window.location.protocol+'//192.168.30.44';
// var port = '11171';
var senderids = 1;
var plfv = '';
// var urm = 0;
var ip = 0;
var room = '';
var xhr_sm;
var notification;
var aj_ip = false;

$("document").ready(function()
{
	var imgdivcontent ='<input type="file" id="files" name="files[]"><div id="files_list"></div>';

	$('#start').click(function() {
		if($.trim($('#nam').val()) != '') {
			name = $('#nam').val();
			initchat();
			if (typeof ms != 'undefined' && ms != null) {
				if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype != '') {
					$('#identity').hide();
					$('#pro_name').html('@) '+name);
					// room = 'General';
					setui();
					$('#profile').click(function() {
						if($('#profile_box').is(':hidden')) {
							$('#profile_box').show();
						} else {
							$('#profile_box').hide();
						}
					});
					//
					$('#avchat').click(function() {
						initav();
					});
				}
			}
		}
	});
	//
	$('#fp').click(function() {
		var unm = $.trim($('#nam').val());
		var eid = $.trim($('#eid').val());
		if(unm != '' && eid != '') {
			name = '';
			var url = site_url+'cp.php';
			if(!aj_ip) {
				aj_ip = true;
				$.ajax({ url:url, type:'POST', async:false, data:{ 'unm':unm, 'eid':eid }, success:function(resp) {
					if($.trim(resp) == 'wait') {
						alert('A change password link has been mailed to your email-id.');
						name = eid = pass = '';
					} else if($.trim(resp) == 'e-error') {
						alert('Error while processing, please try again later.');
						name = eid = pass = '';
					} else {
						name = eid = pass = '';
						alert('Details are not valid or account is inactive.');
					}
					aj_ip = false;
				}, complete: function(jqxhr, status) { aj_ip = false; }
				});
			}
		}
	});
	//
	$('#srch_frnd').bind('keyup',function(e) {
		if($.trim($(this).val()) == '' && $.trim(flcf)!='clbk') {
			$('.mc_frnd_list > li[class^=groupData]').show();
		} else if($.trim($(this).val()) != '' && $.trim($(this).val()) != $.trim($(this).attr('title'))) {
			$('.mc_frnd_list > li[class^=groupData] > a > label'+':not(:icontains("'+$.trim($(this).val())+'"))').closest('li').hide();
			$('.mc_frnd_list > li[class^=groupData] > a > label'+':icontains("'+$.trim($(this).val())+'")').closest('li').show();
		}
		flcf = '';
	});
	//
	$('.mc_frlist').find('span.icon').live('click', function() {
		$('#srch_frnd').val($.trim($('#srch_frnd').attr('title')));
		if($('.groupData_'+$(this).closest('h4').attr('class')+':last').is(':hidden')) {
			$('.groupData'+"_"+$(this).closest('h4').attr('class')).show();
		} else {
			$('.groupData'+"_"+$(this).closest('h4').attr('class')).hide();
		}
	});
	$('#srch_msgs').live('keyup',function(e) {
		if(($.trim($(this).val()) == '' || $.trim($(this).val()) == 'Filter Messages') && $.trim(flcf)!='clbk') {
			$('.mc_msglist > .msg-row').show();
		} else if($.trim($(this).val()) != '' && $.trim($(this).val()) != 'Filter Messages') {
			$('.mc_msglist > .msg-row'+':not(:icontains("'+$.trim($(this).val())+'"))').hide();
			$('.mc_msglist > .msg-row'+':icontains("'+$.trim($(this).val())+'")').show();
		}
		flcf = '';
	});
	//
	$('#frmreply').bind('submit', function(e) { frmsubmit(e); });
	$("#vMessage").bind("keyup", function(e) {
		if(e.keyCode == 16 || e.keyCode == 17 || e.keyCode == 18) {
			pmkc = 13;
		}
	});
	$("#vMessage").bind("keydown", function(e) {
		if(e.keyCode == 13 && (pmkc == 16 || pmkc == 17 || pmkc == 18)) {
			// e.preventDefault();
			// $('#replay_msg').trigger('click');
			// return false;
			pmkc = e.keyCode;
			return e.keyCode;
		} else if(e.keyCode == 13) {
			e.preventDefault();
			cancelEventBubble(e);
			setTimeout(function() {
				$('#send').trigger('click');
			}, 1);
			return false;
		}
		pmkc = e.keyCode;
		// alert(e.keyCode);
	});
	//
	$('#send').click(function(e) { sendmsgs(e); return false; });
	$('body').click(function() {
		$('title').html($('title').html().replace(' *',''));
	});
	$('body').focus(function() {
		$('title').html($('title').html().replace(' *',''));
	});
	//
});

function cancelEventBubble(e) {
	var evt = e ? e : window.event;
	if (evt.stopPropagation) { evt.stopPropagation(); }
	if (evt.cancelBubble!=null) { evt.cancelBubble = true; }
	if (evt.stopImmediatePropagation) { evt.stopImmediatePropagation(); }
}

function addToContact(jqel) {
	var cnm = jqel.parent().find('.msr').html();
	if ($.trim(cnm) != '') {
		var url = site_url+'newcontact.php';
		if (!aj_ip) {
			aj_ip = true;
			$.ajax({ url: url, type: 'POST', data:{ 'c':cnm, 'name':name }, success:function(resp) {
				if($.trim(resp) == 'success') {
					jqel.parent().slideUp('slow');
					jqel.parent().remove();
				}
				aj_ip = false;
			}, complete: function(jqxhr, status) { aj_ip = false; }
			});
		}
	}
}

function setui() {
	$('#search').click(function() {
		if($('#search_box').is(':hidden')) {
			$('#search_box').show();
			$('#contact_box').hide();
		} else {
			$('#search_box').hide();
		}
	});
	$('#groups').click(function() {
		if($('#group_box').is(':hidden')) {
			$('#group_box').show();
			$('.a2g').show();
			$('#glist').show();
			$('#grlist').show();
		} else {
			$('#group_box').hide();
			$('.a2g').hide();
			$('#glist').hide();
			$('#grlist').hide();
		}
	});
	$('#contacts').click(function() {
		url = site_url+'csrch.php';
		if (!aj_ip) {
			aj_ip = true;
			$.ajax({ url: url, type: 'POST', data:{ 'name':name }, success:function(resp) {
				var rsp = $.parseJSON(resp);
				var clist = '';
				var a2g = ($('#group_box').is(':hidden'))? 'none' : '';
				if(rsp != null) {
					for(var e in rsp['con']) {
						var styl = '';
						console.log(rsp['con'][e]);
						// if(typeof rsp['con'][e]['ol'] != 'undefined' && rsp['con'][e]['ol'] != null) {
						if(typeof rsp['con'][e] == "string") {
							clist = clist + '<div id="msc'+e+'" class="member pointer" title="'+rsp['con'][e]+'" ><label class="cmsr pointer" style="'+styl+'">'+rsp['con'][e]+'</label> <span class="rjct" style="float:right;" title="Remove"> &nbsp; x </span> <span class="a2g" title="Add To Group" style="float:right; display:'+a2g+';"> &nbsp; + </span></div>';
						} else {
							for(var cn in rsp['con'][e]) {
								var lst = rsp['con'][e][cn];
								if(lst.indexOf('ol:') != -1) {
									lst = lst.replace('ol:','');
									styl = 'font-weight:bold';
								}
								clist = clist + '<div id="msc'+e+'" class="member pointer" title="'+cn+'"><label class="cmsr pointer" style="'+styl+'" title="(Last Seen: '+lst+')">'+cn+'</label> <span class="rjct" style="float:right;" title="Remove"> &nbsp; x </span> <span class="a2g" title="Add To Group" style="float:right; display:'+a2g+';"> &nbsp; + </span></div>';
								break;
							}
						}

					}
				}
				var rlist = '';
				if(rsp != null) {
					for(var e in rsp['conr']) {
						rlist = rlist + '<div id="msr'+e+'" class="member pointer" title="'+rsp['conr'][e]+'" ><label class="cmsr pointer">'+rsp['conr'][e]+'</label> <span class="rjct" title="Reject" style="float:right;"> &nbsp; x </span> <span class="acpt" title="Accept" style="float:right;"> &nbsp; + </span></div>';
					}
				}
				var glist = '';
				if(rsp != null) {
					for(var e in rsp['grp']) {
						glist = glist + '<div id="msg'+e+'" class="group pointer" title="'+rsp['grp'][e]+'" ><label class="cmsr pointer">'+rsp['grp'][e]+'</label> <span class="rjctg" title="Remove" style="float:right;"> &nbsp; x </span> <span class="selcg" title="Add To Select" style="float:right;"> &nbsp; + </span></div>';
					}
				}
				var grlist = '';
				if(rsp != null) {
					for(var e in rsp['grpr']) {
						grlist = grlist + '<div id="msgr'+e+'" class="group pointer" title="'+rsp['grpr'][e]+'" ><label class="cmsr pointer">'+rsp['grpr'][e]+'</label> <span class="rjctg" title="Reject" style="float:right;"> &nbsp; x </span> <span class="acptg" title="Accept" style="float:right;"> &nbsp; + </span></div>';
					}
				}
				if(clist == '') {
					$('#clist').html('No Contacts');
					$('#clist').show();
					$('#search_box').hide();
					$('#contact_box').show();
				}
				if(clist != '' || rlist != '' || glist != '' || grlist != '') {
					$('#clist').html(clist);
					$('#clist').show();
					$('#rlist').html(rlist);
					$('#rlist').show();
					$('#glist').html(glist);
					// $('#glist').show();
					$('#grlist').html(grlist);
					// $('#grlist').show();
					//
					$('#search_box').hide();
					$('#contact_box').show();
					//
					biCChat();
					acptCReq();
					rjctCReq();
					biGChat();
					//
					add2Grp();
					selcGrp();
					rjctGrp();
					acptGrp();
					//
				} else {
					$('#clist').html('None Found !');
					$('#clist').show();
				}
				aj_ip = false;
			}, complete: function(jqxhr, status) { aj_ip = false; }
			});
		}
	});
	//
	$('#ssrch').keyup(function(e) {
		if(e.keyCode == 13) {
			url = site_url+'ssrch.php';
			if (!aj_ip) {
				aj_ip = true;
				$.ajax({ url: url, type: 'POST', data:{ 'qk':$('#ssrch').val(), 'name':name }, success:function(resp) {
					var rsp = $.parseJSON(resp);
					var list = '';
					for(var e in rsp) {
						list = list + '<div id="ms'+e+'" class="member pointer" title="'+rsp[e]+'" ><label class="msr">'+rsp[e]+'</label> <span class="add_con" title="Add As Contact" style="float:right;"> &nbsp; + </span></div>';
					}
					console.log(list);
					if(list != '') {
						$('#slist').html(list);
						$('#slist').show();
						//
						$('.add_con').click(function() {
							addToContact($(this));
						});
					} else {
						$('#slist').html('None Found !');
						$('#slist').show();
					}
					aj_ip = false;
				}, complete: function(jqxhr, status) { aj_ip = false; }
				});
			}
		}
	});
	//
	$('#csrch').keyup(function(e) {
		if(e.keyCode == 13) {
			var val = $('#csrch').val();
			if (val != '') {
				$('#clist > div.member').hide();
				$('#clist > div.member:contains("'+val+'")').show();
				//
				$('#rlist > div.member').hide();
				$('#rlist > div.member:contains("'+val+'")').show();
			} else {
				$('#clist > div.member').show();
				$('#rlist > div.member').show();
			}
		}
	});
	//
	$('#mgrp').click(function() {
		var grpcon = $('#grpcon').val();
		var grpnms = $('#grpnms').val();
		var url = site_url+'managegroup.php';
		// var jqel = $(this);
		if(!aj_ip) {
			aj_ip = true;
			$.ajax({ url: url, type: 'POST', data:{ 'grpcon':grpcon, 'grpnms':grpnms, 'name':name }, success: function(resp) {
				if($.trim(resp) == 'success') {
					$('#contacts').trigger('click');
					alert('Group Updated');
				} else {
					alert('Error while processing, verify details before submitting again.');
				}
				aj_ip = false;
			}, complete: function(jqxhr, status) { aj_ip = false; }
			});
		}
	});
	//
}

function acptCReq() {
	$('.acpt').click(function() {
		var c = $(this).parent().attr('title');
		var url = site_url+'acceptcreq.php';
		// var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'c':c, 'name':name }, success:function(resp) {
			if($.trim(resp) == 'success') {
				// jqel.remove();
				$('#contacts').trigger('click');
			}
		}, complete: function(jqxhr, status) { /*aj_ip = false;*/ }
		});
	});
}

function rjctCReq() {
	$('.rjct').click(function() {
		var c = $(this).parent().attr('title');
		var url = site_url+'rejectcreq.php';
		var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'c':c, 'name':name }, success: function(resp) {
			if($.trim(resp) == 'success') {
				jqel.parent().remove();
			}
		}, complete: function(jqxhr, status) { /* aj_ip = false; */ }
		});
	});
}

function add2Grp()
{
	$('.a2g').click(function() {
		if (!$('#grpcon').is('[readonly]')) {
			var c = $(this).parent().attr('title');
			var gm = $('#grpcon').val();
			tmp = gm.replace(/^,|,$/g,'');
			if($.trim(tmp) != '') {
				gm = gm.replace(/,+/g,',');
				if(gm.indexOf(',') === 0) { gm = gm.substring(1, gm.length); }
				if(gm.lastIndexOf(',') === (gm.length-1)) { gm = gm.substring(0, gm.length-1); }
				if(gm.indexOf(c+',') == -1 && gm.indexOf(','+c+',') == -1 && gm.indexOf(','+c) == -1 && $.trim(gm) != c) {
					$('#grpcon').val(gm + ',' + c);
				} else {
					$('#grpcon').val(gm);
				}
			} else {
				$('#grpcon').val(c);
			}
		}
	});
}

function selcGrp()
{
	$('.selcg').click(function() {
		if (!$('#grpcon').is('[readonly]')) {
			var grpnm = $(this).parent().attr('title');
			/* // to allow multiple groups editing at same time (not tested)
			var grpnms = $('#grpnms').val();
			grpnms = grpnms.replace(/^,|,$/g,'');
			grpnms = grpnms.replace(/,+/g,',');
			if(grpnms.indexOf(',') === 0) { grpnms = grpnms.substring(1, grpnms.length); }
			if(grpnms.lastIndexOf(',') === (grpnms.length-1)) { grpnms = grpnms.substring(0, grpnms.length-1); }
			grpnms = $.trim(grpnms);
			if(grpnms != '') { 	// && grpnms.indexOf(',') == -1
				if(grpnms.indexOf(grpnm+',') == -1 && grpnms.indexOf(','+grpnm+',') == -1 && grpnms.indexOf(','+grpnm) == -1 && $.trim(grpnms) != grpnm) {
					$('#grpnms').val(grpnms + ',' + grpnm);
				} else {
					$('#grpnms').val(grpnms);
				}
			} else if(grpnms == '') {*/
				$('#grpnms').val(grpnm);
				$('#grpcon').val('Loading Group Members ...');
				$('#grpcon').attr('readonly','readonly');
				if(!aj_ip) {
					aj_ip = true;
					var url = site_url+'selcgroup.php';
					$.ajax({ url: url, type: 'POST', data:{ 'grpnm':grpnm, 'name':name }, success: function(resp) {
						$('#grpcon').val('');
						$('#grpcon').attr('readonly',false);
						if($.trim(resp) != '') {
							$('#grpcon').val(resp);
						}
						aj_ip = false;
					}, complete: function(jqxhr, status) { aj_ip = false; $('#grpcon').attr('readonly',false); }
					});
				}
			// }
		}
	});
}

function acptGrp()
{
	$('.acptg').click(function() {
		var grpnm = $(this).parent().attr('title');
		var url = site_url+'acceptgreq.php';
		// var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'grpnm':grpnm, 'name':name }, success:function(resp) {
			if($.trim(resp) == 'success') {
				// jqel.remove();
				$('#contacts').trigger('click');
			}
		}, complete: function(jqxhr, status) { /*aj_ip = false;*/ }
		});
	});
}

function rjctGrp()
{
	$('.rjctg').click(function() {
		var grpnm = $(this).parent().attr('title');
		var url = site_url+'rejectgreq.php';
		var jqel = $(this);
		// aj_ip = true;
		$.ajax({ url: url, type: 'POST', data:{ 'grpnm':grpnm, 'name':name }, success: function(resp) {
			if($.trim(resp) == 'success') {
				var id = jqel.parent().attr('id');
				id = id.replace('msg','');
				jqel.parent().remove();
				$('.ctabs').find('[rel="'+id+'"]').remove();
				$('.mc_msglist').find('[rel="'+id+'"]').remove();
				$('.mc_msglist').find('.General').show();
			}
		}, complete: function(jqxhr, status) { /* aj_ip = false; */ }
		});
	});
}

function setChatBoxes(vl, gid) {
	if(typeof gid == 'undefined' || gid == null) {
		gid = '';
	}
	if(typeof gid == 'undefined' || gid == null) { gid = ''; }
	var mselc = '.mc_msglist > .'+vl;
	if(gid != '') {
		mselc = '.mc_msglist > .'+vl+'[rel="'+gid+'"]';
	}
	var tbselc = '.ctabs > span[title="'+vl+'"][rel=""]';
	if(gid != '') {
		tbselc = '.ctabs > span[rel="'+gid+'"]';
	} else {
		// tbselc = '.ctabs > span[rel=""]';
	}
	if($(mselc).length == 0) {
		$('.mc_msglist > div').hide();
		$('.mc_msglist').append('<div class="'+vl+'" rel="'+gid+'" style="height:159px; overflow:auto; border:1px solid #acacac;"></div>');
	}
	if($(tbselc).length == 0) {
		$('.ctabs > .tab-active').removeClass('tab-active');
		$('.ctabs').append('<span class="tab-active pointer" style="display:inline-block; width:150px; max-width:150px; height:13px; overflow:hidden; text-align:center; border:1px solid #cccccc; padding:3px; line-height:15px;" title="'+vl+'" rel="'+gid+'"><label style="display:inline-block; width:100px; overflow:hidden;">'+vl+'</label> <span></span> [<b class="pointer" style="text-transform:lowercase;">x</b>]</span>'); 	// text-transform:capitalize;
	}
	$(tbselc).click(function() {
		var vl = $(this).attr('title');
		if($(mselc).is(':hidden')) {
			$('.mc_msglist > div').hide();
			$(mselc).show();
		}
		$('.ctabs > .tab-active').removeClass('tab-active');
		$(this).addClass('tab-active');
		//
		$(tbselc+' > span').html('');
		var relvl = $(this).attr('rel');
		var q = '';
		if (typeof relvl != 'undefined' && relvl != null && relvl != '') {
			q = group_prefix+vl+'-'+gid+'-'+name;
		} else {
			q = (strcasecmp(name, vl) > 0)? vl+'-'+name : name+'-'+vl;
		}
		if (q != '') {
			$('#history').attr('href', site_url+'history.php?name='+name+'&q='+q);
		}
		if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype == 'ws') {
			if(typeof relvl != undefined && relvl != null && relvl != '') {
				ms.mcevs.send('scmsg:=:'+group_prefix+vl+'-'+relvl);
			} else {
				ms.mcevs.send('scmsg:=:'+vl);
			}
		}
	});
	$(tbselc+' > b').click(function() {
		var vl = $(this).parent().attr('title');
		$(this).parent().remove();
		$(mselc).remove();
		//
		setChatBoxes('General');
	});
	$(mselc).click(function() {
		var vl = $(this).attr('class');
		$(tbselc+' > span').html('');
	});
	$(mselc).focus(function() {
		var vl = $(this).attr('class');
		$(tbselc+' > span').html('');
	});
	if($(mselc).is(':hidden')) {
		$('.mc_msglist > div').hide();
		$(mselc).show();
		$('.ctabs > .tab-active').removeClass('tab-active');
		$(tbselc).addClass('tab-active');
	}
}
setChatBoxes('General');

function setChatUi(vl, gid) {
	var selc = '';
	if(typeof gid != undefined && gid != null) {
		selc = '.mc_msglist > .'+vl+'[rel="'+gid+'"]';
	} else {
		selc = '.mc_msglist > .'+vl;
	}
	if($(selc).length > 0) {
		if($(selc).is(':hidden')) {
			$('.mc_msglist > div').hide();
			$(selc).show();
		}
	} else {
		// $('.mc_msglist').append('<div class="'+vl+'" style="height:159px; overflow:auto; border:1px solid #acacac;"></div>');
		setChatBoxes(vl, gid);
	}
	// $('.mc_msglist > '+vl).show();
	if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype == 'ws') {
		if(typeof gid != undefined && gid != null) {
			ms.mcevs.send('scmsg:=:'+group_prefix+vl+'-'+gid);
		} else {
			ms.mcevs.send('scmsg:=:'+vl);
		}
	}
}

function biCChat() {
	$('#clist > .member > .cmsr').click(function() {
		var ci = $(this).html();
		var url = site_url+'biCChat.php';
		if (!aj_ip) {
			aj_ip = true;
			$.ajax({ url: url, type: 'POST', data:{ 'ci':ci, 'name':name }, success: function(resp) {
				aj_ip = false;
				resp = $.trim(resp);
				if(resp.indexOf('success:') != -1 && resp.indexOf('success:') === 0) {
					setChatUi(ci);
					var q = $.trim(resp.replace('success:',''));
					room = (q != 'gen' && q.toLowerCase() != 'general' && $.trim(q) != '')? q : '';
					$('#history').attr('href', site_url+'history.php?name='+name+'&q='+q);
				} else if(resp == -1) {
					alert('Friend request not yet accepted');
				} else if(resp == 0) {
					alert('Friend request not yet accepted or you are no longer in contacts list of other person');
				} else if(resp == 1) {
					alert('Your request does not seem to exist at other end. Please remove this request and send new request');
				} else {
					alert('Error while proceeding, please try again later');
				}
				aj_ip = false;
			}, complete: function(jqxhr, status) { aj_ip = false; }
			});
		} else {
			alert('please try again after some time');
		}
	});
}

function biGChat() {
	$('#glist > .group > .cmsr').click(function() {
		var gi = $(this).html();
		var gid = $(this).parent().attr('id');
		if(typeof gid != undefined && gid != null) {
			gid = gid.replace('msg','');
		}
		var url = site_url+'biGChat.php';
		if (!aj_ip) {
			aj_ip = true;
			$.ajax({ url: url, type: 'POST', data:{ 'gi':gi, 'gid':gid, 'name':name }, success: function(resp) {
				aj_ip = false;
				resp = $.trim(resp);
				if(resp.indexOf('success:') != -1 && resp.indexOf('success:') === 0) {
					setChatUi(gi, gid);
					var q = $.trim(resp.replace('success:',''));
					room = (q != 'gen' && q.toLowerCase() != 'general' && $.trim(q) != '')? q : '';
					// if(q.indexOf('g:') != -1 && q.indexOf('g:') === 0) {
						q = q+'-'+name;
					// }
					$('#history').attr('href', site_url+'history.php?name='+name+'&q='+q);
				} else if(resp == -1) {
					alert('Request not yet accepted');
				} else if(resp == 0) {
					alert('Request not yet accepted');
				} else if(resp == 1) {
					alert('Your request does not seem to exist at other end. Please remove this request and send new request');
				} else {
					alert('Error while proceeding, please try again later');
				}
				aj_ip = false;
			}, complete: function(jqxhr, status) { aj_ip = false; }
			});
		} else {
			alert('please try again after some time');
		}
	});
}

function initchat() {
	var unm = $.trim($('#nam').val());
	var eid = $.trim($('#eid').val());
	var pass = $.trim($('#pass').val());
	var cpcode = $.trim($('#cpcode').html());
	if(unm != '' && eid != '' && pass != '') {
		name = '';
		var url = site_url+'chkusr.php';
		if(!aj_ip) {
			aj_ip = true;
			$.ajax({ url:url, type:'POST', async:false, data:{ 'unm':unm, 'eid':eid, 'pass':pass, 'cp':cpcode }, success:function(resp) {
				if(resp.indexOf('success:') != -1 && resp.indexOf('success:') === 0) {
					name = $.trim(resp.replace('success:',''));
					$('#identity').hide();
					$('#pro_name').html('@) '+name);
				} else if($.trim(resp) == 'wait') {
					alert('A verification link has been mailed to your email-id.');
					name = eid = pass = '';
				} else if($.trim(resp) == 'e-error') {
					alert('Error while registration, please try again later.');
					name = eid = pass = '';
				} else {
					name = eid = pass = '';
					alert('Details are not valid or account is inactive.');
				}
				aj_ip = false;
			}, complete: function(jqxhr, status) { aj_ip = false; }
			});
		}
		//
		if(name != '') {
			var mcslink = site_url+'nchat.php';
			ms = new $('#msgchat').msgchat({ msg_selc:'.mc_msglist', flt_selc:'.mc_frlist', msg_link:mcslink, clbk_fnc:'setchatdtls', on_open:'joinedchat', params:name }); // user this 	, ajaxchat:true
			$('#history').attr('href', site_url+'history.php?name='+name+'&q=General-'+name);
			if(typeof ms.mcevs != 'undefined' && ms.mcevs != null && typeof ms.chattype != 'undefined' && ms.chattype != null && ms.chattype != 'ws') {
				// setTimeout(function() { sendmsgs('', "* joined the chat"); }, 300);
			}
			// check permission for notification
			if (typeof ms != 'undefined' && ms != null) {
				$('#notify').click(function() {
					if (window.webkitNotifications) {
						if (window.webkitNotifications.checkPermission() != 0) { window.webkitNotifications.requestPermission(); }
					} else {
						alert("Your browser doesn't support HTML5 notifications!");
					}
				});
			}
		} else {
			//
		}
	}
	// console.log(ms);
	// console.log(ms.close());
}

function initav()
{
	if ($.trim(room) != '') {
		// create our webrtc connection
		var webrtc = new WebRTC({
			// the id/element dom element that will hold "our" video
			localVideoEl: 'selfvid',
			// the id/element dom element that will hold remote videos
			remoteVideosEl: 'rvids',
			// immediately ask for camera access
			autoRequestMedia: true,
			log: false
		});
		// when it's ready, join if we got a room from the URL
		webrtc.on('readyToCall', function () {
			if (room) webrtc.joinRoom(room);
			$('#avc').show();
		});
	}
}

function strip_tags (input, allowed) {
	// making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
		return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	});
}

function frmsubmit(e, vl)
{
	if(ms.chattype == 'ws') { return false; }
	try {
		e.preventDefault();
	} catch (ex) { }
	// var URL = site_url+"app/mcchat/controller/insertmessage.php";
	var url = site_url+"reply.php";
	// $('#name').val(name);
	if(typeof vl != 'undefined' && vl != null) {
		$('#vMessage').val(vl);
	}
	var ci = $('.mc_msglist > div:visible').attr('class');
	var gcid = $('.mc_msglist > div:visible').attr('rel');
	if(typeof ci == 'undefined' || ci == null) { ci = ''; }
	if(typeof gcid == 'undefined' || gcid == null) { gcid = ''; }
	if(ci != '' && name != '') {
		$('#name').val(name);
		$('#ci').val(ci);
		$('#gcid').val(gcid);
		$('#frmreply').ajaxSubmit({
			url: url,
			type: 'post',
			success: function(resp, status, xhr, frm) {
				$('.soundalert').html('.');
				$("#vMessage").val('');
				$("#files").val('');
				// $("#uploading").html(imgdivcontent);
				// $("#uploading").css('display','none');
			}
		});
	}
}

function sendmsgs(ev, vl)
{
	if(typeof ms != 'undefined' && ms != null) {
		if(typeof vl == 'undefined' || vl == null) {
			vl = $('#vMessage').val();
		}
		vl = $.trim(vl);
		// vl = vl + "<hr style='border-style:dashed'/>";
		// console.log(ms.chattype);
		if(ms.chattype == 'ws') {
			// ms.mcevs.binaryType = 'arraybuffer';
			ms.mcevs.send(vl);
			$('#vMessage').val('');
			// file upload
			var files = document.querySelector('#files').files;
			var msg = "";
			if(files.length > 0 && ip == 0) {
				for(var l=0; l<files.length; l++) {
					if(files[l].size > 0) {
						if(! (xhr_sm instanceof XMLHttpRequest)) {
							xhr_sm = new XMLHttpRequest();
						}
						var url = window.location.toString();
						url = url.substring(0, url.indexOf('sc.php'))+"filehandler.php";
						xhr_sm.open('POST', url, true);
						//
						xhr_sm.upload.onprogress = function(e) {
							if (e.lengthComputable) {
							  var percentComplete = (e.loaded / e.total) * 100;
							  console.log(percentComplete + '% uploaded');
							}
						};
						xhr_sm.onload = function() {
							if (this.status == 200) {
								var resp = JSON.parse(this.response);
								if(msg == "") { msg = "File(s) : "; }
								msg = msg + "<br />" + resp.txt;
								// ms.mcevs.send(resp.txt);
								// console.log('response: ', resp);
								//var image = document.createElement('img');
								//image.src = resp.dataUrl;
								//document.body.appendChild(image);
								ip = ip + 1;
								// if(ip == -1) {
								if(ip == files.length) {
									ms.mcevs.send(msg);
									ip = 0;
									msg = '';
									setTimeout(function() { $('#files').val(''); }, 300);
								}
								// console.log(ip);
							};
						};
						var fd = new FormData();
						fd.append("file", files[l]);
						// ip = 1;
						xhr_sm.send(fd);
						// console.log(l);
						// console.log(files.length);
						if(l == (files.length-1)) {
							//ip = -1;
							// $('#files').val('');
						}
					}
				}
			}
		} else {
			// $('#frmreply').submit();
			frmsubmit('', vl);
		}
		// console.log(ms.mcevs);
	} else {
		alert('no connection');
	}
	return false;
}

function joinedchat() {
	// setTimeout(function() { sendmsgs('', ""); }, 300); 	// * joined the chat
}

function setchatdtls(lid, dtls, opts)
{
	var options = opts.split(',');
	console.log(dtls);
	// console.log(options);
	var msgs = '';
	var frnds = '';
	var cid = 'General';
	if($.trim(dtls) != '') {
		if(lid == 'xhr' && typeof dtls['data'] != 'undefined' && dtls['data'] != null) {
			dtls = dtls['data'];
		}
	}
	if(typeof dtls != 'undefined' && dtls != null) {
		for(var v in dtls) {
			//
			var el_selc = 'title="'+cid+'"';
			var gid = '';
			var cb_selc = '.'+cid; 	// .toLowerCase();
			if(typeof dtls[v] != 'undefined' && dtls[v] != null) {
				msgs = dtls[v];
				cid = (v != 'gen')? v : 'General';
				if(cid.indexOf(group_prefix) != -1 && cid.indexOf(group_prefix) === 0) {
					cid = cid.substring(2, cid.length);
					gid = cid.substring(cid.indexOf('-')+1);
					cid = cid.substring(0, cid.indexOf('-'));
					el_selc = 'rel="'+gid+'"';
					cb_selc = '[rel="'+gid+'"]';
				}
			}
			el_selc = 'title="'+cid+'"';
			cb_selc = '.'+cid;
			//
			if($.trim(msgs) != '') {
				if(parseInt(senderids,10) == 0 || isNaN(parseInt(senderids,10)) || parseInt(plfv,10) == 0 || isNaN(parseInt(plfv,10))) {
					$('.nomsg').hide();
					if(uc != 0) {
						// console.log(msgs);
						// console.log(options[0]+'>>'+options[1]+' > '+cb_selc);
						$(options[0]).find(options[1]+' > '+cb_selc).html(msgs+"<hr style='border-style:dashed;' />"); 	// +"<br/>"
						// msg_alert_sound();
						// urm = urm + 1;
						var urm = 0;
						if($('.ctabs > span['+el_selc+'] > span').length > 0) {
							urm = $('.ctabs > span['+el_selc+'] > span').html().replace('(','').replace(')','');
						} else {
							// $('msg_recv').html('');
						}
						if(isNaN(parseInt(urm))) { urm = 0; }
						urm = parseInt(urm) + 1;
						// $('title').html("("+urm+")");
						$('title').html($('title').html().replace(' *','') + ' *');
						$('.ctabs > span['+el_selc+'] > span').html('('+urm+')');
					} else {
						uc = 1;
					}
					plfv = senderids;
				} else {
					$('.nomsg').hide();
					if(uc != 0) {
						$(options[0]).find(options[1]+' > '+cb_selc).append(msgs+"<hr style='border-style:dashed;' />"); 	// "<br/>"+msgs+"<br/>"
						// msg_alert_sound();
						// urm = urm + 1;
						var urm = 0;
						if($('.ctabs > span['+el_selc+'] > span').length > 0) {
							urm = $('.ctabs > span['+el_selc+'] > span').html().replace('(','').replace(')','');
						}
						if(isNaN(parseInt(urm))) { urm = 0; }
						urm = parseInt(urm) + 1;
						// $('title').html("("+urm+")");
						$('title').html($('title').html().replace(' *','') + ' *');
						$('.ctabs > span['+el_selc+'] > span').html('('+urm+')');
					} else {
						uc = 1;
					}
				}
				//$(options[0]).find(options[1]).attr('innerHTML', $(options[0]).find(options[1]).attr('innerHTML'));
				if($(options[0]).find('.scrollnew:checked').length > 0) {
					// $(options[0]).find(options[1]).scrollTop(parseInt($(options[0]).find(options[1])[0].scrollHeight, 10));
					try {
						$(options[0]).find(options[1]+' > '+cb_selc).scrollTop(parseInt($(options[0]).find(options[1]+' > '+cb_selc)[0].scrollHeight, 10));
					} catch (e) { }
				}
				// sound alert
				if($(options[0]).find('.alertsound:checked').length > 0) {
					msg_alert_sound();
				}
				// notification alert
				if (window.webkitNotifications.checkPermission() == 0) {
					try {
						notification.cancel(); 	// notification.close();
					} catch(e) { }
					notification = null;
					if (msgs.length > 100) {
						notification = window.webkitNotifications.createNotification('', 'Message ('+cid+')', strip_tags(msgs,'').substring(0,100) + ' ...');
					} else {
						notification = window.webkitNotifications.createNotification('', 'Message ('+cid+')', strip_tags(msgs,''));
					}
					try {
						notification.onclick = function() { window.focus(); notification.cancel(); }
					} catch(e) {  }
					notification.show();
					// setTimeout('notification.cancel();', 10000);
				}
			} else if($.trim(msgs) == '' && (parseInt(senderids,10) == 0 || isNaN(parseInt(senderids,10)) || parseInt(plfv,10) == 0 || isNaN(parseInt(plfv,10)))) {
				// $('.mc_msglist > .nomsg').show();
			}
			//
		}
	}
}

function msg_alert_sound() {
	// if(valert == 0) { return false; }
	if($('.soundalert').html()=='.') {
		$('.soundalert').html('');
	} else {
		// $('.soundalert').html('<embed height="1px" width="1px" src="audio/alert.mp3" autostart="true" volume="100" loop="false" />'); 	// hidden="true"
		$('.soundalert').html('');
		$('.soundalert').html('<object width="1" height="1"><param name="src" value="audio/alert.mp3"><param name="autoplay" value="true"><param name="controller" value="true"><param name="bgcolor" value="#ffffff"><embed type="audio/mpeg" src="audio/alert.mp3" autostart="true" loop="false" width="1" height="1" controller="true" bgcolor="#ffffff"></embed></object>');
	}
}

function strcasecmp (f_string1, f_string2) {
	var string1 = (f_string1 + '').toLowerCase();
	var string2 = (f_string2 + '').toLowerCase();
	if (string1 > string2) {
		return 1;
	} else if (string1 == string2) {
		return 0;
	}
	return -1;
}

function sleep(milliseconds) {
	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds){
			break;
		}
	}
}
