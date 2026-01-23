<?php

include_once 'Configurations.php';
const NOTE_INSTALLAZIONE_ZMSOFTWARE   = 'ZMSOFTWARE';
const NOTE_INSTALLAZIONE_GENERICO     = 'GENERICO';

class TAccessRights
{
  private $FNoteInstallazione = null;
  
  public function __construct()
  {
    $this->FNoteInstallazione = json_decode(NOTE_INSTALLAZIONE);
  }

}


$GlobalAccessRights = new TAccessRights();