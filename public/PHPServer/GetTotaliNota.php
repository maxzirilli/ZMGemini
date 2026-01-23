<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQueryGetTotaliNota extends TAdvQuery
      {
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
              $Parametri     = JSON_decode($_POST['Params']); 
              
              $ArrTotaliNota = TSystemInformation::GetTotaliNota($PDODBase, $Parametri->ChiaviNota);

              $JSONAnswer->TotaliNota = $ArrTotaliNota;
            }
      }

      $Connection = new TAdvQueryGetTotaliNota();
      $Connection->ServerSideScript();
?>