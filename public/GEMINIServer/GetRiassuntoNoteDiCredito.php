<?php 
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';
        
  header("Content-Type: application/json;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST, OPTIONS"); 

  class TOggettoNotaDiCredito
  {
    public $CHIAVE             = null;
    public $NUMERO             = null;
    public $ANNO               = null;
    public $ID_CLIENTE         = null;
    public $RAGIONE_SOCIALE    = null;
    public $INVIATA_ALLO_SDI   = null;
    public $PAGATO             = null;
    public $RATE_TOTALI        = null;
    public $RATE_NON_PAGATE    = null;

    public $TOTALE_NOTA       = null;
  }
  
  class TAdvQueryGetRiassuntoNoteDiCredito extends TAdvQuery
  {
    protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
    {
      $Params    = JSON_decode($_POST['Params']); 
      $Parametri = array();

      foreach ($Params as $key => $value) 
        $Parametri[$key] = $value;
      

      $RiassuntoNoteDiCredito = array();


      $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                          'NoteDiCredito', 
                                          isset($Params->RiassuntoPerAnno)? 'RiassuntoNotePerAnno' : 'FiltroNote',
                                          isset($Params->RiassuntoPerAnno)? 'Riassunto'            : 'FiltroNote', 
                                          $Parametri);

      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $ObjNotaDiCredito = new TOggettoNotaDiCredito();

          $ObjNotaDiCredito->CHIAVE             = $Row['CHIAVE'];
          $ObjNotaDiCredito->NUMERO             = $Row['NUMERO'];
          $ObjNotaDiCredito->ANNO               = $Row['ANNO'];
          $ObjNotaDiCredito->ID_CLIENTE         = $Row['ID_CLIENTE'];
          $ObjNotaDiCredito->RAGIONE_SOCIALE    = $Row['RAGIONE_SOCIALE'];
          $ObjNotaDiCredito->INVIATA_ALLO_SDI   = $Row['INVIATA_ALLO_SDI'];
          $ObjNotaDiCredito->PAGATO             = $Row['PAGATO'];
          $ObjNotaDiCredito->RATE_TOTALI        = $Row['RATE_TOTALI'];
          $ObjNotaDiCredito->RATE_NON_PAGATE    = $Row['RATE_NON_PAGATE'];

          $ArrTotaliNota = TSystemInformation::GetTotaliNota($PDODBase, intval($Row['CHIAVE']));

          $ObjNotaDiCredito->TOTALE_NOTA = $ArrTotaliNota[0]->NettoAPagare;

          array_push($RiassuntoNoteDiCredito, $ObjNotaDiCredito);
        }
      }       
      else throw new Exception('Impossibile caricare il Riassunto delle Note di credito');

      $JSONAnswer->RiassuntoNoteDiCredito = $RiassuntoNoteDiCredito;
    }
  }

  $Connection = new TAdvQueryGetRiassuntoNoteDiCredito();
  $Connection->ServerSideScript();