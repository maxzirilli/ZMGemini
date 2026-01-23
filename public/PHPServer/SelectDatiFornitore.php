<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'ClassStampaContoFornitore.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQuerySelectDatiFornitore extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $Parametri = JSON_decode($_POST['Params']); 

          $this->SelectDatiFornitore($Parametri, $JSONAnswer, $PDODBase);
        }

        private function SelectDatiFornitore($Parametri, &$JSONAnswer, $PDODBase)
        {
          $JSONAnswer->Dettaglio = array();
          $this->SelectQueryFornitore($PDODBase, $Parametri, $JSONAnswer->Dettaglio, 'SelectFornitore');


          $JSONAnswer->DatiForitoreTelefono = array();
          $this->SelectQueryFornitore($PDODBase, $Parametri, $JSONAnswer->DatiForitoreTelefono, 'TelefonoFornitori');


          $JSONAnswer->DatiFornitoreCodici = array();
          $this->SelectQueryFornitore($PDODBase, $Parametri, $JSONAnswer->DatiFornitoreCodici, 'SelectCodiciFornitore');


          $JSONAnswer->DatiSituazioneContabileFornitore = array();

          $Parametri->StatoConti = true;

          $AConnection = new TExtraStampaContoFornitore($Parametri, true);
          $JSONAnswer->DatiSituazioneContabileFornitore = $AConnection->ServerSideScript(false);
        }
 
        private function SelectQueryFornitore($PDODBase, $Parametri, &$Array, $NomeQuery)
        {
              $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                    'Fornitori', 
                                                    'SelectFornitore',
                                                    $NomeQuery, 
                                                    get_object_vars($Parametri));

              if($Query = $PDODBase->query($SQLBody))
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                    array_push($Array,$Row);
                }
        }
      }

      $Connection = new TAdvQuerySelectDatiFornitore();
      $Connection->ServerSideScript();
?>