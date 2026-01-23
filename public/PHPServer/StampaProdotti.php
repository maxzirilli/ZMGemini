<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
 	    include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
 	    include_once PATH_LIBRERIE . 'ZEconomicFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaProdotti
      {
        public $BAND_INTESTAZIONE    = null;
        public $BAND_SETTORE         = null;
        public $QRSubDetail1         = null;
        public $QRChildBand1         = null;
        public $BAND_PRODOTTI        = null;
        public $BAND_PAGE_FOOTER     = null;
        public $BAND_HEADER_PRODOTTI = null;
      }

      class TDatiProdotto
      {
        public $LB_DESCRIZIONE        = null;
        public $LB_QUANTITA           = null;
        public $LB_SOGLIA_DI_ALLARME  = null;
        public $SETTORE               = '';
      }

      class TDatiSettore
      {
        public $LB_SETTORE = '';
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null; 
        public $QR_LOGO               = null;
      }

      class TReportProdotti extends TZReport
      {
         private $FCambioColore  = false;
         public  $BandaHeaderProdotti = null;
         private $FLastSettore = '';

         public function BeforeBandRepetitionPrint(&$ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         { 
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
            
           if($ABand->Name == 'BAND_PRODOTTI')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
              $this->BandaHeaderProdotti[0]->LB_SETTORE = $ASingleRecord->SETTORE;
              $ABand->ForceNewPage = 'False';
              if($ASingleRecord->SETTORE != $this->FLastSettore && $this->FLastSettore != '')
                $ABand->ForceNewPage = 'True';
              $this->FLastSettore = $ASingleRecord->SETTORE;
           }
         }
      }

      class TExtraStampaProdotti extends TAdvQuery
      {
        private function FGetDatiStampa($Report,$Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
          $Result = new TStampaProdotti();

          $Result->BAND_INTESTAZIONE = array();
          $DatiIntestazione = new TDatiIntestazione();
          TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
          if($NomeLogo != '')
            $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 
          array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

          $Result->BAND_PRODOTTI = array();
          $Result->BAND_HEADER_PRODOTTI = array();
          $Report->BandaHeaderProdotti = &$Result->BAND_HEADER_PRODOTTI;
          array_push($Result->BAND_HEADER_PRODOTTI,new TDatiSettore());

          $Result->QRSubDetail1        = array();


          $SQLBody = "SELECT prodotti.DESCRIZIONE,
		                         qnt_x_magazzino.QUANTITA_MAGAZZINO,
		                         qnt_x_magazzino.SOGLIA_ALLARME,
                             prodotti.PRODOTTO_COMPOSTO,
                             settori.DESCRIZIONE AS SETTORE
                        FROM prodotti 
                             JOIN settori ON settori.CHIAVE = prodotti.ID_SETTORE
                             LEFT JOIN qnt_x_magazzino ON qnt_x_magazzino.ID_PRODOTTO = prodotti.CHIAVE
                    ORDER BY settori.DESCRIZIONE,prodotti.DESCRIZIONE";

          if($Query = $PDODBase->query($SQLBody))
          {
            $PrimaRiga = true;
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            {
              if($PrimaRiga)
              {
                $PrimaRiga = false;
                $Result->BAND_HEADER_PRODOTTI[0]->LB_SETTORE = $Row['SETTORE'];
              } 
              if($Row['PRODOTTO_COMPOSTO'] != 'T')
              {
                $DatiProdotto = new TDatiProdotto();
                $DatiProdotto->LB_DESCRIZIONE = $Row['DESCRIZIONE'];
                $DatiProdotto->LB_QUANTITA = number_format($Row['QUANTITA_MAGAZZINO']/100,2, ',', '.');
                $DatiProdotto->LB_SOGLIA_DI_ALLARME = $Row['SOGLIA_ALLARME'];
                $DatiProdotto->SETTORE = $Row['SETTORE'];
                array_push($Result->BAND_PRODOTTI,$DatiProdotto);
              }
            }
          }


          $Result->BAND_SETTORE = array();
          $RigaSettore = new stdClass();
          array_push($Result->BAND_SETTORE,$RigaSettore);

          $Result->QRChildBand1 = array();
          $DatiBanda = new stdClass();
          array_push($Result->QRChildBand1,$DatiBanda);

          $Result->BAND_PAGE_FOOTER = array();
          $RigaFooter = new stdClass();  
          array_push($Result->BAND_PAGE_FOOTER,$RigaFooter);

          return $Result;
        }

        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
            $Parametri = json_decode($_POST['Params']);

            $NomeLogo = '';
            $SQLBody = "SELECT impostazioni.LOGO_AZIENDA
                          FROM impostazioni";

            if($Query = $PDODBase->query($SQLBody))
               while($Row = $Query->fetch(PDO::FETCH_ASSOC))
               { 
                 if(isset($Row['LOGO_AZIENDA']))
                 {
                    $NomeLogo = date("Y-m-d_H_i_s_U");
                    ForceDirectory(NOME_CARTELLA_FILE_TEMP);
                    file_put_contents(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg',base64_decode(explode(',',$Row['LOGO_AZIENDA'])[1]));
                 }
               }

            $Report = new TReportProdotti();
            $Report->BandaHeaderProdotti = null;
            $Report->LoadFromFile('ModelliStampe/QrStampaProdotti.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Report,$Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }

      $AConnection = new TExtraStampaProdotti();
      $AConnection->ServerSideScript();  
      
    