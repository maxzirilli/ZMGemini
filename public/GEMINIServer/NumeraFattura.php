<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQueryNumeraEdInviaFattura extends TAdvQuery
      {
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
              $Parametri     = JSON_decode($_POST['Params']); 
              $this->FPrepareParameterValue($Parametri->ChiaveFattura, ':');
              $SQLBody = "CALL ASSEGNA_NUMERO_FATTURA($Parametri->ChiaveFattura)";
              if(!$PDODBase->query($SQLBody))
                 throw new Exception('Impossibile assegnare la numerazione');
            }
      }

      $Connection = new TAdvQueryNumeraEdInviaFattura();
      $Connection->ServerSideScript();
?>