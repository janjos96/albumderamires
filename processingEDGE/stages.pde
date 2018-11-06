int t = 0 ;

void stageOne () {
  println("STAGE 1");
  soundTerminated = true;
  if (soundTerminated) { 
    TerminarGravacao(); 
    soundSaved = true;
  }


  if (soundSaved) { 
    GuardarFicheiro();
    reproduce = true;
  }

  if (reproduce) { 

    ReproduzirFicheiro();
    IniciarGravacao2();
    stage1 = false;
    stage2 = true;
    soundTerminated = false;
    soundTerminated2 = true;
  }
}




void stageTwo () {


  if ( play.position() == play.length()  ) {

    println("STAGE 2");

    soundTerminated2 = true; 

    if (soundTerminated2) { 
      TerminarGravacao2(); 
      soundSaved2 = true;
    }


    if (soundSaved2) { 
      GuardarFicheiro2();
      reproduce2 = true;
    }

    if (reproduce2) {
      ReproduzirFicheiro2();
      IniciarGravacao3();
      soundTerminated2 = false; 
      soundTerminated3 = true; 
      stage2 = false;
      stage1 = false;
      stage3 = true;
      t=1;
    }
  } else if (t == 1) {


    if ( play3.position() == play3.length()  ) {
      println("STAGE 2.1");
      soundTerminated2 = true; 

      if (soundTerminated2) { 
        TerminarGravacao2(); 
        soundSaved2 = true;
      }


      if (soundSaved2) { 

        GuardarFicheiro2();

        reproduce2 = true;
      }

      if (reproduce2) {
        ReproduzirFicheiro2();
        recorder3 = minim.createRecorder(in, "myrecording3.wav");
        IniciarGravacao3();
        soundTerminated2 = false; 
        soundTerminated3 = true; 
        stage2 = false;
        stage1 = false;
        stage3 = true;
      }
    }
  }
}



void stageThree () {

  if ( play2.position() == play2.length()) {

    println("STAGE 3");

    soundTerminated3 = true;

    if (soundTerminated3) { 
      TerminarGravacao3(); 
      soundSaved = true;
    }


    if (soundSaved) { 

      GuardarFicheiro3();

      reproduce = true;
    }

    if (reproduce) { 

      ReproduzirFicheiro3();
      recorder2 = minim.createRecorder(in, "myrecording2.wav"); 
      IniciarGravacao2();
      stage1 = false;
      stage2 = true;
      soundTerminated = false;
      soundTerminated2 = true;
      stage3 = false;
      play.rewind();
    }
  }
}
