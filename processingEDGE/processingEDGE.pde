import java.util.Random;
import java.util.Arrays;
import websockets.*;
import java.io.File;
import ddf.minim.*;
import ddf.minim.ugens.*;
import ddf.minim.spi.*;

WebsocketServer socket;

//CODIGO DE SOMM

boolean init = true;

boolean soundTerminated = false;
boolean soundSaved = false;
boolean reproduce = false;

boolean soundTerminated2 = false;
boolean soundTerminated3 = false;
boolean soundSaved2 = false;
boolean reproduce2 = false;

boolean stage1 = true;
boolean stage2 = false;
boolean stage3 = false;

boolean playAudio = false;

boolean lineDrawer = true;

Minim minim;
AudioPlayer player;
AudioRecorder recorder, recorder2, recorder3;
AudioOutput out;
FilePlayer play, play2, play3;
AudioInput in;

//CODIDO IMAGENS

float[][] kernel = {{ -1, -1, -1}, 
  { -1, 9, -1}, 
  { -1, -1, -1}};

PGraphics pgMask, pgImage;
PImage myImage, img, imgMask, wave;
PImage baseImg;
PImage[] maskImg;
color white;
int nrImages;
int[] arraynrs;
boolean displayImg = false;
boolean clearBackground = false; 
int r = 0;

int counter = 0;


void setup() {
  
  size(1000,1080);
  background(0);
  
  imageDeleter();
  soundDeleter();
  
  socket = new WebsocketServer(this, 1337, "/p5websocket");
  
  minim = new Minim(this);
  //player = minim.loadFile("voicerecording.wav");
  in = minim.getLineIn(Minim.STEREO, 2048);
  recorder = minim.createRecorder(in, "myrecording.wav");
  recorder2 = minim.createRecorder(in, "myrecording2.wav");
  recorder3 = minim.createRecorder(in, "myrecording3.wav");
  out = minim.getLineOut( Minim.STEREO );
  
  imageMode(CENTER);
  
  pgMask = createGraphics(400, 400);
  pgImage = createGraphics(400, 400);
  
  white = color(255, 255, 255,255);
  
}



void draw() {
  
  if(clearBackground) {
    background(0);
    clearBackground = false;
  }
  
  if(displayImg){
   
   for(int i = 0; i <nrImages - 1 ; i++){
     if( i == 0) {
       image(baseImg,width/2,height/2);
     } else if( i == nrImages - 1){
       maskImage(maskImg[i], baseImg);
     } else {
       maskImage(maskImg[i], maskImg[i+1]);
     }
   }
   
   lineDrawer = false;
   displayImg = false;
   imageDeleter();
 }
 
 if(playAudio) {
   if(lineDrawer) {
     background(0);
     stroke(255);
     linhas();
   }
   if (init) {
      player.play();
    }
  
    if ( player.isPlaying() )
    {
  
      IniciarGravacao();
      init = false;
    } else if ( player.position() == player.length() )
    {
  
      if (stage1) {
        lineDrawer = false;
        stageOne();
      }
  
      if (stage2) {
        stageTwo();
      }
  
      if (stage3) {
        stageThree();
      }
    }
 }
 
 //noLoop();
 
}

void maskImage(PImage img, PImage img2) {
  
 pgMask.beginDraw();
 pgMask.image(detectEdges(img),0,0);
 pgMask.filter(THRESHOLD,0.8);
 pgMask.filter(INVERT);
 pgMask.filter(BLUR);
 pgMask.endDraw();
 
 pgImage.beginDraw();
 pgImage.image(img2,0,0);
 pgImage.mask(pgMask);
 pgImage.endDraw();
 

 pgImage.filter(GRAY);
 image(pgImage,width/2,height/2, 600, 600);
}

void loader(){
  
  maskImg = new PImage[nrImages];
  arraynrs = new int[nrImages];
  
  for(int i = 0 ; i<nrImages; i++){
    arraynrs[i] = i;
  }
  
  shuffleArray(arraynrs);
 // noLoop();
  
  for(int i = 0 ; i<nrImages ; i++){
    if(i == 0){
      maskImg[arraynrs[i]] = loadImage("image.png");
    } else {
      maskImg[arraynrs[i]] = loadImage("image ("+i+").png"); 
    }
  }
  
  baseImg = maskImg[0];
  baseImg.filter(GRAY);
  
  //loop();
  
}

void webSocketServerEvent(String msg){
 
 println(msg);
 
 nrImages = parseInt(msg);
 
 if(nrImages > 0 && nrImages != 1000) {
   counter ++;
   delay(500);
   loader();
   displayImg = true;
   //loop();
 } else if(nrImages == 1000) {
   lineDrawer = true;
   delay(1500);
   if( r == 0){
     player = minim.loadFile("voicerecording.wav");
   } else {
     player = minim.loadFile("voicerecording ("+r+").wav");
   }
   recorder = minim.createRecorder(in, "myrecording.wav");
   recorder2 = minim.createRecorder(in, "myrecording2.wav");
   recorder3 = minim.createRecorder(in, "myrecording3.wav");
   init = true;

   soundTerminated = false;
   soundSaved = false;
   reproduce = false;

   soundTerminated2 = false;
   soundTerminated3 = false;
   soundSaved2 = false;
   reproduce2 = false;

   stage1 = true;
   stage2 = false;
   stage3 = false;
  
   t = 0;
   
   r++;
   
   playAudio = true;
 } else if(nrImages == 0) {
   //loop();
   clearBackground = true;
   playAudio = false;
   recorder.endRecord();
   recorder2.endRecord();
   recorder3.endRecord();
   if(counter != 0) {
     play.pause();
     play2.pause();
     play3.pause();
     play.close();
     play2.close();
     play3.close();
   }
   //soundDeleter();
 }
 
 
}

void shuffleArray(int[] array) {
  
  Random rng = new Random();
 
  // i is the number of items remaining to be shuffled.
  for (int i = array.length; i > 1; i--) {
 
    // Pick a random element to swap with the i-th element.
    int j = rng.nextInt(i);  // 0 <= j <= i-1 (0-based array)
 
    // Swap array elements.
    int tmp = array[j];
    array[j] = array[i-1];
    array[i-1] = tmp;
  }
  
}

void linhas() {

  for (int i = 0; i < player.bufferSize() - 1; i++)
  {
    float x1 = map( i, 0, player.bufferSize(), (width/2)-300, (width/2)+300 );
    float x2 = map( i+1, 0, player.bufferSize(), (width/2)-300, (width/2)+300 );
    line( x1, (height/2)-75 + player.left.get(i)*50, x2, (height/2)-75 + player.left.get(i+1)*50 );
    line( x1, (height/2)+75 + player.right.get(i)*50, x2, (height/2)+75 + player.right.get(i+1)*50 );
  }

  /*// draw a line to show where in the song playback is currently located
  float posx = map(player.position(), 0, player.length(), 0, width);
  stroke(0, 200, 0);
  line(posx, 0, posx, height);*/
}

void imageDeleter() {
  
  for(int i = 0 ; i < nrImages ; i++){
    if(i == 0){
      String fileName = dataPath("image.png");
      File f = new File(fileName);
      if (f.exists()) {
        f.delete();
      }
    } else {
      String fileName = dataPath("image ("+i+").png");
      File f = new File(fileName);
      if (f.exists()) {
        f.delete();
      }
    }
  }
  
}

void soundDeleter() {
  String fileName = dataPath("voicerecording.wav");
      File f = new File(fileName);
      if (f.exists()) {
        f.delete();
      }
}
