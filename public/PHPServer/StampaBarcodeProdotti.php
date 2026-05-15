<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
 	    include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
 	    include_once PATH_LIBRERIE . 'ZEconomicFunct.php';
      include_once PATH_LIBRERIE . 'ZEAN13Funct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaBarcodeProdotti
      {
        public $BAND_PRODOTTI = null;
        public $BAND_PAGE_FOOTER = null;
      }

      class TDatiProdotto
      {
        public $LB_DESCRIZIONE = null;
        public $LB_BARCODE = null;
      }

      class TReportProdotti extends TZReport
      {
        private $FCambioColore = false;

        public function BeforeBandRepetitionPrint(&$ABand,$ASingleRecord,&$PrintBand,$NextRecord)
        {
            if($ABand->Name == 'BAND_PRODOTTI')
            {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
            }
        }
      }

      class TExtraStampaBarcodeProdotti extends TAdvQuery
      {
        private function FGetDatiStampa($Report,$Parametri,$PDODBase,&$JSONAnswer)
        {
          $Result = new TStampaBarcodeProdotti();

          $Result->BAND_PRODOTTI = array();

          $SQLBody = "SELECT NOME_PRODOTTO,
                             BARCODE
                        FROM prodotti
                       WHERE BARCODE IS NOT NULL AND BARCODE <> ''
                       ORDER BY NOME_PRODOTTO";

          if($Query = $PDODBase->query($SQLBody))
          {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                $Dati = new TDatiProdotto();

                $Dati->LB_DESCRIZIONE = $Row['NOME_PRODOTTO'];
                $Dati->LB_BARCODE     = TZEAN13::GetEAN13Code($Row['BARCODE']);

                array_push($Result->BAND_PRODOTTI,$Dati);
              }
          }

          $Result->BAND_PAGE_FOOTER = array();
          array_push($Result->BAND_PAGE_FOOTER,new stdClass());

          return $Result;
        }

        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
          $Parametri = json_decode($_POST['Params']);

          $Report = new TReportProdotti();
          $Report->LoadFromFile('ModelliStampe/QrStampaBarcodeProdotti.json');
          $JSONAnswer->PDF = base64_encode(
              $Report->GetPDF(
                $this->FGetDatiStampa($Report,$Parametri,$PDODBase,$JSONAnswer)
              )
          );
        }
      }

      $AConnection = new TExtraStampaBarcodeProdotti();
      $AConnection->ServerSideScript();