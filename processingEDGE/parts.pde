void IniciarGravacao() {

  recorder.beginRecord();
  println("gravação iniciada");
}

void TerminarGravacao() {

  recorder.endRecord();
  println("gravação terminada");
}



void GuardarFicheiro() {
  play = new FilePlayer( recorder.save() );
  println("ficheiro Guardado");
}



void ReproduzirFicheiro() {

  play.patch(out);
  play.play();
  println("ficheiro a reproduzir");
}



/*------------------------------------------------------------*/


void IniciarGravacao2() {

  recorder2.beginRecord();
  println("gravação iniciada2");
}

void TerminarGravacao2() {

  recorder2.endRecord();
  println("gravação terminada2");
}



void GuardarFicheiro2() {
  play2 = new FilePlayer( recorder2.save() );
  println("ficheiro Guardado2");
}



void ReproduzirFicheiro2() {

  play2.patch(out);
  play2.play();
  println("ficheiro a reproduzir2");
}



/*------------------------------------------------------------*/


void IniciarGravacao3() {

  recorder3.beginRecord();
  println("gravação iniciada3");
}

void TerminarGravacao3() {

  recorder3.endRecord();
  println("gravação terminada3");
}



void GuardarFicheiro3() {
  play3 = new FilePlayer( recorder3.save() );
  println("ficheiro Guardado3");
}



void ReproduzirFicheiro3() {

  play3.patch(out);
  play3.play();
  println("ficheiro a reproduzir3");
}
