<html>
<head>
    <title></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link  href="css/cropper.css" rel="stylesheet">
    <style>
    img {
      max-width: 100%; /* This rule is very important, please do not ignore this! */
    }
    </style>
    <script src="js/cropper.js"></script>
    <script src="js/reimg.js"></script>

    <?php
    if (isset($_GET["pagenr"]) && isset($_GET["imagesqtt"])) {
      $n = $_GET['pagenr'];
      $q =  $_GET['imagesqtt'];
      print "<script> var pageJsonNr = '$n'; var imagesQuantity = '$q';</script>";
    }

    ?>

    <script type="text/javascript">


    var cropper;
    var cropperValues = [];
    var i = 0; //percorre array de keywords

    // We need to check if the browser supports WebSockets
    if ("WebSocket" in window) {
        // Before we can connect to the WebSocket, we need to start it in Processing.
        var ws = new WebSocket("ws://localhost:1337/p5websocket");
    } else {
        // The browser doesn't support WebSocket
        alert("WebSocket NOT supported by your Browser!");
    }

    var frases = (function() {
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


    var a = frases.pages[pageJsonNr].split(" ");

    function getImage(key){

            var imageloaded = false;

            $.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?",
            {
                content_type: "1",
                is_commons: "true",
                tags: key,
                tagmode: "all",
                format: "json"
                
            },

            function(data) {
                var rnd = Math.floor(Math.random() * data.items.length);
                var image_src = data.items[rnd]['media']['m'].replace("_m", "_b");

                return new Promise(function(resolve) {
                  console.log("gotten");
                  $("body").append("<img id='theImg' src="+image_src+" >");
                  resolve(cropImage());
                });

            });
    }



        function cropImage(){ //quando devolver o resultado, fazer o saveimg e depois

              var image = document.getElementById('theImg');
              cropper = new Cropper(image, {
                aspectRatio: 16 / 16,
                autoCrop: true,
                movable: false,
                zoomable: false,
                cropBoxMovable: false,
                cropBoxResizable: false,
                zoomOnTouch: false,
              /*  zoomOnWheel: false,
                scalable: false,*/

                crop: function(event) {
                  console.log(event.detail.x);
                  console.log(event.detail.y);
                  console.log(event.detail.width);
                  console.log(event.detail.height);
                  console.log(event.detail.rotate);
                  console.log(event.detail.scaleX);
                  console.log(event.detail.scaleY);

                  return new Promise(function(resolve) {
                    console.log("cropped");
                    resolve(saveValues());
                  });

                }
              });

            }






            function saveValues(){

               var c = cropper.getCroppedCanvas();
               var myImage = c.toDataURL("image/jpg");
               cropperValues.push(myImage + "*");
               console.log("values");
               console.log(cropperValues);

               var png = ReImg.fromCanvas(c).toPng();
               ReImg.fromCanvas(c).downloadPng();

             return new Promise(function(resolve) {
               console.log("saved");

               if(i == a.length){
                 console.log("finished");
                 resolve( abc() );
                 window.close();
               }

               if(i < a.length){
                 $("body").empty();
                 console.log("cleared");
                 resolve(getImage(a[i]));
                 i++;
               }
             });


            }


            function abc(){
              ws.send(imagesQuantity);
              //window.open("audioWave.html", '_blank');
            }



      /*var timeint  =  setInterval(function(){

                getImage(a[i]);
                i++;

                if(i > a.length){


                clearInterval(timeint);
                window.close();
                $(document.body).append('<form id="forma" action="save.php" method="POST"><input type="hidden" name="aid" value="'+ cropperValues.toString() + '"></form>');
                setTimeout(function(){$('#forma').submit();},2000); // submete a form

              }

            },4000);*/



            $(document).ready(function(){
              getImage(a[i]);
              i++;
            });


    </script>

</head>
<body>

</body>
</html>
