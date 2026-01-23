<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'ClassStampaContoCliente.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQuerySelectDatiFattura extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $Parametri = JSON_decode($_POST['Params']); 

          $this->SelectDatiFattura($Parametri, $JSONAnswer, $PDODBase);
        }

        private function SelectDatiFattura($Parametri, &$JSONAnswer, $PDODBase)
        {
          $JSONAnswer->Dettaglio                  = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->Dettaglio, 'Dettaglio');


          $JSONAnswer->DatiAllegati               = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->DatiAllegati, 'AllegatiFattura');


          $JSONAnswer->DatiVoci                   = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->DatiVoci, 'VociFattura');


          $JSONAnswer->DatiRate                   = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->DatiRate, 'RateFattura');


          $JSONAnswer->DatiLog                    = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->DatiLog, 'LogFattura');


          $JSONAnswer->DatiRateNoteDiCreditoCorrelate = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->DatiRateNoteDiCreditoCorrelate, 'RateNoteDiCreditoCorrelate');

          $JSONAnswer->DatiPreventiviCollegati    = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->DatiPreventiviCollegati, 'PreventiviCollegati');

          $JSONAnswer->NoteDiCreditoAperteCliente  = array();
          $this->SelectQueryFattura($PDODBase, $Parametri, $JSONAnswer->NoteDiCreditoAperteCliente, 'NoteDiCreditoAperteCliente');


          if(!isset($Parametri->ChiaveCliente) || $Parametri->ChiaveCliente == 0 || $Parametri->ChiaveCliente == '')
          {
            $ChiaveClienteFattura      = $this->FGetChiaveClienteFattura($PDODBase, $Parametri);
            $Parametri->ChiaveCliente  = $ChiaveClienteFattura;
            $Parametri->CHIAVE_CLIENTE = $ChiaveClienteFattura;
          }
          if(isset($Parametri->ChiaveCliente))
          {
            $JSONAnswer->DatiSituazioneContabile    = array();
            $Parametri->StatoConti                  = true;
            $AConnection                            = new TExtraStampaContoCliente($Parametri, true);
            $JSONAnswer->DatiSituazioneContabile    = $AConnection->ServerSideScript(false);
          }
        }
 
        private function FGetChiaveClienteFattura($PDODBase, $Parametri)
        {
          $SQLBody = "SELECT ID_CLIENTE FROM fatture WHERE CHIAVE = $Parametri->CHIAVE";

          if($Query = $PDODBase->query($SQLBody))
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              return $Row['ID_CLIENTE'];
        }

        private function SelectQueryFattura($PDODBase, $Parametri, &$Array, $NomeQuery)
        {
              $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                  'Fatture', 
                                                  'SelectDettaglio',
                                                  $NomeQuery, 
                                                  get_object_vars($Parametri));

              if($Query = $PDODBase->query($SQLBody))
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  array_push($Array,$Row);
        }
      }

      $Connection = new TAdvQuerySelectDatiFattura();
      $Connection->ServerSideScript();
?>