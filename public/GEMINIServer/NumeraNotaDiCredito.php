<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQueryNumeraEdInviaNotaDiCredito extends TAdvQuery
      {
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
              $Parametri     = JSON_decode($_POST['Params']); 
              $SQLBody = "CALL ASSEGNA_NUMERO_NOTA_DI_CREDITO($Parametri->ChiaveNota)";
              if(!$PDODBase->query($SQLBody))
                 throw new Exception('Impossibile assegnare la numerazione');
            }
      }

      $Connection = new TAdvQueryNumeraEdInviaNotaDiCredito();
      $Connection->ServerSideScript();
?>