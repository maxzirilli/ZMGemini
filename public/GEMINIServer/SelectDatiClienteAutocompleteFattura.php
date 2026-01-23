<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'ClassStampaContoCliente.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQuerySelectDatiClienteAutocompleteFattura extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $Parametri = JSON_decode($_POST['Params']); 

          $this->SelectDatiCliente($Parametri, $JSONAnswer, $PDODBase);
        }

        private function SelectDatiCliente($Parametri, &$JSONAnswer, $PDODBase)
        {
          $JSONAnswer->Dettaglio = array();
          $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                              'Clienti', 
                                              'SelectCliente',
                                              'SelectCliente', 
                                              get_object_vars($Parametri));

          if($Query = $PDODBase->query($SQLBody))
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            { 
                array_push($JSONAnswer->Dettaglio, $Row);
            }


          $JSONAnswer->DatiSituazioneContabile = array();
          $Parametri->StatoConti = true;
          $AConnection           = new TExtraStampaContoCliente($Parametri, true);
          $JSONAnswer->DatiSituazioneContabile = $AConnection->ServerSideScript(false);


        }
      }

      $Connection = new TAdvQuerySelectDatiClienteAutocompleteFattura();
      $Connection->ServerSideScript();
?>