<?php 
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';
        
  header("Content-Type: application/json;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST, OPTIONS"); 

  class TOggettoAutofattura
  {
    public $CHIAVE             = null;
    public $NUMERO             = null;
    public $ANNO               = null;
    public $ID_FORNITORE       = null;
    public $RAGIONE_SOCIALE    = null;

    public $TOTALE             = null;
  }
  
  class TAdvQueryGetRiassuntoAutofattura extends TAdvQuery
  {
    protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
    {
      $Params    = JSON_decode($_POST['Params']); 
      $Parametri = array();

      foreach ($Params as $key => $value) 
        $Parametri[$key] = $value;
      
      $RiassuntoAutofatture = array();

      $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                          'Autofatture', 
                                          isset($Params->RiassuntoPerAnno)? 'RiassuntoAutoFatturePerAnno' : 'SelectSQL',
                                          isset($Params->RiassuntoPerAnno)? 'Riassunto'                   : 'Lista', 
                                          $Parametri);

      // error_log('$SQLBody: ' . $SQLBody);

      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $ObjAutofattura = new TOggettoAutofattura();

          $ObjAutofattura->CHIAVE             = $Row['CHIAVE'];
          $ObjAutofattura->NUMERO             = $Row['NUMERO'];
          $ObjAutofattura->ANNO               = $Row['ANNO'];
          $ObjAutofattura->ID_FORNITORE       = $Row['ID_FORNITORE'];
          $ObjAutofattura->RAGIONE_SOCIALE    = $Row['RAGIONE_SOCIALE'];

          $ArrTotaliAutofattura               = TSystemInformation::GetTotaliAutofattura($PDODBase, intval($Row['CHIAVE']));

          $ObjAutofattura->TOTALE             = $ArrTotaliAutofattura[0]->NettoAPagare;

          array_push($RiassuntoAutofatture, $ObjAutofattura);
        }
      }       
      else throw new Exception('Impossibile caricare il Riassunto delle Note di credito');

      $JSONAnswer->RiassuntoAutofatture = $RiassuntoAutofatture;
    }
  }

  $Connection = new TAdvQueryGetRiassuntoAutofattura();
  $Connection->ServerSideScript();