1. Reading files in JavaScript using the File APIs (file_api.php)
	http://www.html5rocks.com/en/tutorials/file/dndfiles/

2. Share files using WebRTC Data Channel 
- demo:
	http://webrtc.github.io/samples/src/content/datachannel/filetransfer/ 
- simple code: 
	https://github.com/webrtc/samples/tree/master/src/content
- doc: 
	http://www.html5rocks.com/en/tutorials/webrtc/basics
        http://www.html5rocks.com/en/tutorials/webrtc/datachannels/
	https://www.webrtc-experiment.com/docs/how-file-broadcast-works.html
	https://www.webrtc-experiment.com/docs/rtc-datachannel-for-beginners.html
	https://www.webrtc-experiment.com/docs/Share-Files-using-Filejs.html	

3. Note:
- WebRTC implements the following APIs: MediaStream, RTCPeerConnection, RTCDataChannel
- WebRTC's RTCDataChannel API: peer-to-peer communication of generic data.   
- RTCDataChannel is supported by Chrome 25, Opera 18 and Firefox 22 and above.

- RTCDataChannel provides:
    + Gaming
    + Remote desktop applications
    + Real-time text chat
    + File transfer
    + Decentralized networks

- File Sharing:
    + Read a file in JavaScript using the File API
    + Make a peer connection between clients, using RTCPeerConnection.
    + Create a data channel between clients, using RTCDataChannel.