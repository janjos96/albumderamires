<!DOCTYPE HTML>
<html>
<head>

  <script src='https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js'></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <script src="https://cdn.webrtc-experiment.com/MediaStreamRecorder.js"> </script>

  <script src="diff_match_patch.js"></script>

  <script type="text/javascript">

    function getDifferentWords( fraseCorreta , fraseEscutada ){
        var defaultWords = fraseCorreta.split(" ");
        var testWords = fraseEscutada.split(" ");
        var result = 0;

        var tamanho = fraseCorreta.trim().split(/\s+/).length;

        for(var i = 0; i < tamanho; i++){
          if(defaultWords[i] != testWords[i]){
            result++;
          }
        }

        return result;
    }

    var frases = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "frases.json",
            'dataType': "json",
            'success': function (data) {
                json = data;
            }
        });
        return json;
    })();

    var keywords = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "keywords.json",
            'dataType': "json",
            'success': function (data) {
                json = data;
            }
        });
        return json;
    })();

    let matched = false;
    let currentPageNr = 0;
    let tempNr;
    let currentPageTxt
    let currentPagePercent;
    let ocrresult;
    let currentPageJsonNumber;
    let currentPageKwNumber;
    const possiblepageslist = ["1","2","3","4","5","6","7","8","9","10","11","13","14","15","16","17","18","19","20","21","22"];
    let pageActive = false;

    // We need to check if the browser supports WebSockets
    if ("WebSocket" in window) {
        // Before we can connect to the WebSocket, we need to start it in Processing.
        var ws = new WebSocket("ws://localhost:1337/p5websocket");
    } else {
        // The browser doesn't support WebSocket
        alert("WebSocket NOT supported by your Browser!");
    }
    // Now we can start the speech recognition
    // Supported only in Chrome
    // Once started, you need to allow Chrome to use the microphone
    var recognition = new webkitSpeechRecognition();
    // Be default, Chrome will only return a single result.
    // By enabling "continuous", Chrome will keep the microphone active.
    recognition.continuous = false;
    recognition.lang = "pt-PT";

    recognition.onstart = function() {
      console.log("1");

    }

    recognition.onspeechstart = function() {

      var mediaConstraints = {
        audio: true
      };

      navigator.getUserMedia(mediaConstraints, onMediaSuccess, onMediaError);

      function onMediaSuccess(stream) {
          var mediaRecorder = new MediaStreamRecorder(stream);
          mediaRecorder.mimeType = 'audio/wav'; // check this line for audio/wav
          mediaRecorder.ondataavailable = function (blob) {
              // POST/PUT "Blob" using FormData/XHR2
              if(matched){
                var blobURL = URL.createObjectURL(blob);
                document.getElementById("outputDiv").innerHTML = '<a href="' + blobURL + '">' + blobURL + '</a>';
                download(blobURL, "voicerecording");
              }
          };
          mediaRecorder.start(200000);

          recognition.onresult = function(event) {
              // Get the current result from the results object
              var transcript = event.results[event.results.length-1][0].transcript;
              // Send the result string via WebSocket to the running Processing Sketch

              //verifica diferenças
              /*if(transcript != currentPageTxt){
                if(getDifferentWords(currentPageTxt,transcript) === currentPagePercent);
                console.log("YES");
                matched = true;
              }*/


              if(transcript.toLowerCase() === currentPageTxt){
                console.log("YES");
                matched = true;
              }

              if(matched){
                ws.send("1000");
                window.open("getimage.php?pagenr="+currentPageJsonNumber+"&imagesqtt="+currentPageKwNumber, '_blank');
                //ws.send("start");
              }

              mediaRecorder.stop();

              console.log(transcript);
              matched = false;

          }

      }

      function onMediaError(e) {
          console.error('media error', e);
      }

  }



    // Start the recognition
    recognition.start();

    // Restart the recognition on timeout
    recognition.onend = function(){
      console.log("2");
      recognition.start();
    }


    function download( url, filename ) {
	     var link = document.createElement('a');
	     link.setAttribute('href',url);
	     link.setAttribute('download',filename);
	     var event = document.createEvent('MouseEvents');
	     event.initMouseEvent('click', true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
	     link.dispatchEvent(event);
    }

  </script>
</head>
<body>

  <div id="container">

      <video autoplay></video>
      <button>Take snapshot</button>
      <canvas></canvas>

  </div>


  <script>

    /*
     *  Copyright (c) 2015 The WebRTC project authors. All Rights Reserved.
     *
     *  Use of this source code is governed by a BSD-style license
     *  that can be found in the LICENSE file in the root of the source
     *  tree.
     */

    'use strict';

    // Put variables in global scope to make them available to the browser console.
    var video = document.querySelector('video');
    var canvas = window.canvas = document.querySelector('canvas');
    canvas.width = 480;
    canvas.height = 360;

    var videoSource = "495372b1756e4ab21c2135208c4cb60fa97d50921d717f5753f6d8dea4ac215e";

    var button = document.querySelector('button');

    setInterval(function(){

      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;


      canvas.getContext('2d').
      drawImage(video,305,260,50,40, 0, 0, canvas.width, canvas.height);

      canvas.getContext("2d").beginPath();
      canvas.getContext("2d").lineWidth="1";
      canvas.getContext("2d").strokeStyle="red";
      //canvas.getContext("2d").rect(305,250,50,50);
      canvas.getContext("2d").stroke();

      Tesseract.recognize(canvas, {
        lang: 'por'
      }).then(function(result){
        ocrresult=result.text;
        console.log(ocrresult);
        ocrresult = ocrresult.match(/\d/g);
        if(ocrresult != null){
          ocrresult = ocrresult.join("");
        }
        if( isInt(ocrresult) ) {
          tempNr=ocrresult;
          if(tempNr != currentPageNr && possiblepageslist.includes(tempNr) && !pageActive){
            currentPageNr = tempNr
            currentPageJsonNumber = "number"+currentPageNr;

            currentPageTxt = frases.pages[currentPageJsonNumber][0].toLowerCase();

            currentPageKwNumber = keywords.pages[currentPageJsonNumber].split(" ").length;

            pageActive = true;

            console.log(pageActive);
            console.log(currentPageTxt);
            console.log(currentPageKwNumber);
          }
        } else if (pageActive) {
          pageActive=false;
          currentPageTxt = null;
          currentPageNr = null;
          currentPageKwNumber = null;

          ws.send("0");

          console.log(pageActive);
        }
    });

  }, 2000);

    var constraints = {
      audio: false,
      video: true
    };

    function handleSuccess(stream) {
      window.stream = stream; // make stream available to browser console
      video.srcObject = stream;
    }

    function handleError(error) {
      console.log('navigator.getUserMedia error: ', error);
    }

    navigator.mediaDevices.getUserMedia(constraints).
        then(handleSuccess).catch(handleError);


        function isInt(value) {
          return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
        }


  </script>

  <div id="outputDiv">empty</div>

</body>
</html>
