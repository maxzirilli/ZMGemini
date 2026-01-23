<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQueryGetDettagliRitenuta extends TAdvQuery
      {
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
              $Parametri        = JSON_decode($_POST['Params']); 

              $ListaTotaliFatture         = array();
              $ListaTotaliFattureFiltrate = array();

              $ListaTotaliFatture = TSystemInformation::GetRitenutaClienteXAnno($PDODBase, $Parametri->ChiaveCliente, $Parametri->Anno, true);

              for($i = 0; $i < count($ListaTotaliFatture); $i++)
              {
                  if($ListaTotaliFatture[$i]->TotaleRitenuta != 0)
                    array_push($ListaTotaliFattureFiltrate, $ListaTotaliFatture[$i]);
                  else
                  {
                    if(isset($ListaTotaliFatture[$i]->IsRitenutaDiAcconto) && $ListaTotaliFatture[$i]->IsRitenutaDiAcconto)
                      array_push($ListaTotaliFattureFiltrate, $ListaTotaliFatture[$i]);
                  }
              }


              $JSONAnswer->ListaTotaliFatture = array();
              array_push($JSONAnswer->ListaTotaliFatture, $ListaTotaliFattureFiltrate);
            }

      }

      $Connection = new TAdvQueryGetDettagliRitenuta();
      $Connection->ServerSideScript();