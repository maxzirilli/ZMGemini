<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'ClassStampaContoCliente.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQuerySelectDatiCliente extends TAdvQuery
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $Parametri = JSON_decode($_POST['Params']); 

          $this->SelectDatiCliente($Parametri, $JSONAnswer, $PDODBase);
        }

        private function SelectDatiCliente($Parametri, &$JSONAnswer, $PDODBase)
        {
          $JSONAnswer->Dettaglio = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->Dettaglio, 'Dettaglio');


          $JSONAnswer->DatiClienteFiliali = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteFiliali, 'Filiali');


          $JSONAnswer->DatiClienteRitenutePagate = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteRitenutePagate, 'RitenutePagate');


          $JSONAnswer->DatiClienteRitenute = array();
          $JSONAnswer->DatiClienteRitenute = TSystemInformation::GetRitenutaClienteXAnno($PDODBase,$Parametri->CHIAVE,-1); 


          $JSONAnswer->DatiClienteAgenda = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteAgenda, 'Agenda');


          $JSONAnswer->DatiRitenuteCliente = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiRitenuteCliente, 'RitenuteCliente');


          $JSONAnswer->DatiClienteTelefono = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteTelefono, 'TelefonoClienti');


          $JSONAnswer->DatiClienteFilialiOrari = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteFilialiOrari, 'OrariFiliali');


          $JSONAnswer->DatiClienteFilialiTelefoni = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteFilialiTelefoni, 'TelefonoFiliali');


          $JSONAnswer->DatiClienteScontiProdotti = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteScontiProdotti, 'ListaProdotti');


          $JSONAnswer->DatiClienteAllegati = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->DatiClienteAllegati, 'AllegatiClienti');


          $JSONAnswer->ListaFattureCliente = array();
          $this->SelectQueryCliente($PDODBase, $Parametri, $JSONAnswer->ListaFattureCliente, 'ListaFattureCliente');
          if(count($JSONAnswer->ListaFattureCliente) > 0)
            $this->FInserisciTotaliNelleFatture($PDODBase, $JSONAnswer);


          $JSONAnswer->DatiSituazioneContabile = array();

          if(isset($Parametri->ChiaveCliente) && $Parametri->ChiaveCliente != -1)
          {
            $Parametri->StatoConti = true;
            
            $AConnection           = new TExtraStampaContoCliente($Parametri, true);
            $JSONAnswer->DatiSituazioneContabile = $AConnection->ServerSideScript(false);
          }
        }

        private function FInserisciTotaliNelleFatture($PDODBase, &$JSONAnswer)
        {
              $StringaChiavi = '';

              for($j = 0; $j < count($JSONAnswer->ListaFattureCliente); $j++)
                $StringaChiavi .= $JSONAnswer->ListaFattureCliente[$j]['CHIAVE'] . ',';

              $StringaChiavi = rtrim($StringaChiavi, ",");

              if($StringaChiavi != '')
              {
                $ArrTotaliFattura = TSystemInformation::GetTotaliFattura($PDODBase, $StringaChiavi);

                for($i = 0; $i < count($ArrTotaliFattura); $i++)
                  for($j = 0; $j < count($JSONAnswer->ListaFattureCliente); $j++)
                    if($ArrTotaliFattura[$i]->IdFattura == $JSONAnswer->ListaFattureCliente[$j]['CHIAVE'])
                    {
                      $JSONAnswer->ListaFattureCliente[$j]['TOTALE_FATTURA'] = $ArrTotaliFattura[$i]->NettoAPagare;
                      break;
                    }
              }
        }
 
        private function SelectQueryCliente($PDODBase, $Parametri, &$Array, $NomeQuery)
        {
              $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                    'Clienti', 
                                                    'SelectDettaglio',
                                                    $NomeQuery, 
                                                    get_object_vars($Parametri));
              if($Query = $PDODBase->query($SQLBody))
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                    array_push($Array,$Row);
                }
        }
      }

      $Connection = new TAdvQuerySelectDatiCliente();
      $Connection->ServerSideScript();
?>