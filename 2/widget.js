// Connect to PeerJS, have server assign an ID instead of providing one
// Showing off some of the configs available with PeerJS :).

$(document).ready(function () {
    var peer;
    var connectedPeers = {};
    var TYPE_MSG_CHAT = 1;
    var TYPE_MSG_NOTIFY = 2;
    var NUM_KEYUP = 0;
    
    $('#formlogin').on('submit', function(e) {
        e.preventDefault();
        var pid = $.trim($('#peerid').val());
        if (!pid) {
            alert('Please enter your ID');
            return false;
        }
        peer = new Peer(pid, {host: SERVER_IP, port: SERVER_PORT});
        $('#logout').hide();
        $('#pid').text(pid);
        $('#logged').show();
        
        
        function connect(c) {
            // Handle a chat connection.
            if (c.label === 'chat') {
                var chatbox = $('<div></div>').addClass('connection').addClass('active').attr('id', c.peer);
                var header = $('<h1></h1>').html('Chat with <strong>' + c.peer + '</strong>');
                var messages = $('<div><em>Peer connected.</em></div>').addClass('messages');
                chatbox.append(header);
                chatbox.append(messages);

                // Select connection handler.
                chatbox.on('click', function () {
                    if ($(this).attr('class').indexOf('active') === -1) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
                $('.filler').hide();
                $('#connections').append(chatbox);

                c.on('data', function (data) {
                    if (data.type === TYPE_MSG_CHAT) {
                        messages.find('.typing').remove();
                        messages.append('<div><span class="peer">' + c.peer + '</span>: ' + data.content + '</div>');
                    } else if (data.type === TYPE_MSG_NOTIFY) {
                        if (data.numKeyup === 1)
                            messages.append('<div class="typing">' + data.content + '</div>');
                        else if (data.numKeyup === 0)
                            messages.find('.typing').remove();
                    }
                });

                c.on('close', function () {
                    alert(c.peer + ' has left the chat.');
                    chatbox.remove();
                    if ($('.connection').length === 0) {
                        $('.filler').show();
                    }
                    delete connectedPeers[c.peer];
                });
            } else if (c.label === 'file') {
                c.on('data', function (data) {
                    // If we're getting a file, create a URL for it.
                    if (data.constructor === ArrayBuffer) {
                        var dataView = new Uint8Array(data);
                        var dataBlob = new Blob([dataView]);
                        var url = window.URL.createObjectURL(dataBlob);
                        $('#' + c.peer).find('.messages').append('<div><span class="file">' +
                                c.peer + ' has sent you a <a target="_blank" href="' + url + '">file</a>.</span></div>');
                    }
                });
            }
            connectedPeers[c.peer] = 1;
        }
        
        // Show this peer's ID.
        //peer.on('open', function (id) {
        //    $('#pid').text(id);
        //});

        // Await connections from others
        peer.on('connection', connect);

        peer.on('error', function (err) {
            alert($('#rid').val() + ' maybe not online now!');
            //return false;
            console.log(err);
        })
        
        // Prepare file drop box.
        var box = $('#box');
        box.on('dragenter', doNothing);
        box.on('dragover', doNothing);
        box.on('drop', function (e) {
            e.originalEvent.preventDefault();
            var file = e.originalEvent.dataTransfer.files[0];
            eachActiveConnection(function (c, $c) {
                if (c.label === 'file') {
                    c.send(file);
                    $c.find('.messages').append('<div><span class="file">You sent a file.</span></div>');
                }
            });
        });
        function doNothing(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Connect to a peer


        $('#connect').click(function () {
            var requestedPeer = $('#rid').val();
            if (!requestedPeer) {
                alert('Please enter ID which you want to chat');
                return false;
            }

            if (!connectedPeers[requestedPeer]) {
                // Create 2 connections, one labelled chat and another labelled file.
                var c = peer.connect(requestedPeer, {
                    label: 'chat',
                    serialization: 'json',
                    metadata: {message: 'hi i want to chat with you!'}
                });
                c.on('open', function () {
                    connect(c);
                });
                c.on('error', function (err) {
                    alert(err);
                });
                var f = peer.connect(requestedPeer, {label: 'file', reliable: true});
                f.on('open', function () {
                    connect(f);
                });
                f.on('error', function (err) {
                    alert(err);
                });
            }
            connectedPeers[requestedPeer] = 1;
        });

        // Close a connection.
        $('#close').click(function () {
            eachActiveConnection(function (c) {
                c.close();
            });
        });

        // Send a chat message to all active connections.
        $('#send').on('submit', function (e) {
            e.preventDefault();
            // For each active connection, send the message.
            var msg = $('#text').val();
            var msgObj = {content: msg, type: TYPE_MSG_CHAT};
            eachActiveConnection(function (c, $c) {
                if (c.label === 'chat') {
                    c.send(msgObj);
                    $c.find('.messages').append('<div><span class="you">You: </span>' + msgObj.content
                            + '</div>');
                }
            });
            // Reset after submitting
            $('#text').val('');
            $('#text').focus();
            NUM_KEYUP = 0;
        });

        // Display "typing.." when type message
        $('#text').keyup(function (e) {
            var msg = $(this).val();
        
            eachActiveConnection(function (c, $c) {
            if (c.label === 'chat') {
                if ($.trim(msg) !== '') {
                    NUM_KEYUP++;
                    var msgObj = {
                        content: $('#pid').text() + ' is typing...', 
                        type: TYPE_MSG_NOTIFY, 
                        numKeyup: NUM_KEYUP
                    };
                } else {
                    NUM_KEYUP = 0;
                    var msgObj = {
                        content: '', 
                        type: TYPE_MSG_NOTIFY, 
                        numKeyup: NUM_KEYUP
                    };
                }
                c.send(msgObj);
            }
        });
        
    });

        // Goes through each active peer and calls FN on its connections.
        function eachActiveConnection(fn) {
            var actives = $('.active');
            var checkedIds = {};
            actives.each(function() {
            var peerId = $(this).attr('id');

                if (!checkedIds[peerId]) {
                var conns = peer.connections[peerId];
                    for (var i = 0, ii = conns.length; i < ii; i += 1) {
                    var conn = conns[i];
                fn(conn, $(this));
            }
            }

            checkedIds[peerId] = 1;
        });
    }

        // Show browser version
        $('#browsers').text(navigator.userAgent);
    });
});

// Handle a connection object.


$(document).ready(function () {
    
});

    // Make sure things clean up properly.

window.onunload = window.onbeforeunload = function(e) {
    if (!!peer && !peer.destroyed) {
        peer.destroy();
    }
};