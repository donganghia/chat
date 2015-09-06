<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <!-- http://www.html5rocks.com/en/tutorials/file/dndfiles/ -->
</head>    
<body> 
    <script src="jquery.min.js"></script>  

    <script>
        // Check for the various File API support.
        if (window.File && window.FileReader && window.FileList && window.Blob) {
          // Great success! All the File APIs are supported.
        } else {
          alert('The File APIs are not fully supported in this browser.');
        }
    </script>  
    <h3>Reading files in JavaScript using the File APIs</h3>
    
    
    <!-- ----------------------- Using form input for selecting ----------------------- -->
    1. Using form input for selecting</br>
    <input type="file" id="file" name="file[]" multiple />
    <output id="list"></output>

    <script>
      function handleFileSelect(evt) {
        var file = evt.target.file; // FileList object

        // files is a FileList of File objects. List some properties.
        var output = [];
        for (var i = 0, f; f = file[i]; i++) {
          output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ') - ',
                      f.size, ' bytes, last modified: ',
                      f.lastModifiedDate ? f.lastModifiedDate.toLocaleDateString() : 'n/a',
                      '</li>');
        }
        document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
      }

      document.getElementById('file').addEventListener('change', handleFileSelect, false);
    </script>
    
    
    <!-- -----------------------  Using form input for selecting  ----------------------- -->
    </br> 2. Using form input for selecting</br>
    <div id="drop_zone">Drop files here</div>
    <output id="list"></output>

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
      var dropZone = document.getElementById('drop_zone');
      dropZone.addEventListener('dragover', handleDragOver, false);
      dropZone.addEventListener('drop', handleFileSelect, false);
    </script>
    <style>
        #drop_zone {
          border:2px dashed #BBBBBB;
          border-radius:5px;
          color:#BBBBBB;
          font-family:bold, Vollkorn;
          font-size:20pt;
          font-stretch:normal;
          font-style:normal;
          font-variant:normal;
          font-weight:normal;
          line-height:normal;
          padding:25px;
          text-align:center;
        }
    </style>
</body>      
</html>
