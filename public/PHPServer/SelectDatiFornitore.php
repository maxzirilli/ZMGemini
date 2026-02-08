<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'ClassStampaConto.php';
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


          // $JSONAnswer->DatiSituazioneContabileFornitore = array();

          // $Parametri->StatoConti = true;

          // $AConnection = new TExtraStampaConto($Parametri, true);
          // $JSONAnswer->DatiSituazioneContabileFornitore = $AConnection->ServerSideScript(false);

          $JSONAnswer->DatiSituazioneContabileFornitore = array();

          if(isset($Parametri->ChiaveFornitore) && $Parametri->ChiaveFornitore != -1)
          {
            $Parametri->ChiaveCliente = $Parametri->ChiaveFornitore;
            $Parametri->StatoConti = true;
            
            $AConnection           = new TExtraStampaConto($Parametri, true);
            $DatiSituazione        = $AConnection->ServerSideScript(false);

            $JSONAnswer->DatiSituazioneContabileFornitore = $DatiSituazione;

            $TotDarePeriodo  = 0;
            $TotAverePeriodo = 0;

           if(isset($DatiSituazione->LsConti->BAND_SUMMARY))
           {
               $TotDarePeriodo  = 0;
               $TotAverePeriodo = 0;
           
               foreach ($DatiSituazione->LsConti->BAND_SUMMARY as $Riga)
               {
                   $TotDarePeriodo  += isset($Riga->LB_DARE)  ? floatval(str_replace(',', '.', $Riga->LB_DARE))  : 0;
                   $TotAverePeriodo += isset($Riga->LB_AVERE) ? floatval(str_replace(',', '.', $Riga->LB_AVERE)) : 0;
               }
           
               $SaldoPeriodo = $TotDarePeriodo - $TotAverePeriodo;
           
               $TotDarePeriodo  = round($TotDarePeriodo, 2);
               $TotAverePeriodo = round($TotAverePeriodo, 2);
               $SaldoPeriodo    = round($SaldoPeriodo, 2);
           
               $JSONAnswer->DatiSituazioneContabileFornitore->SaldoPeriodo = (object)
               [
                   'LB_DARE'  => number_format($TotDarePeriodo,  2, ',', '.') . '€',
                   'LB_AVERE' => number_format($TotAverePeriodo, 2, ',', '.') . '€',
                   'LB_SALDO' => number_format($SaldoPeriodo,    2, ',', '.') . '€'
               ];
           }
          }
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