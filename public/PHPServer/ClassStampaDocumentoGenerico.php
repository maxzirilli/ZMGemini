<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
      include_once 'ClassForMail.php';
      
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/PHPMailer.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/SMTP.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/Exception.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  


      class TStampaDocumentoGenerico
      {
        public $BAND_INTESTAZIONE    = array();
        public $BAND_SUMMARY         = array();
        
        public $BAND_OGGETTO         = array();
      }

      class TInfoStyle
      {
        public $FontStyle = null;
      }

      class TDatiIntestazioneGenerico
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null;
        public $QR_LOGO               = null;
      }

      class TDatiDescrizione
      {
        public $LB_TITOLO   = '';
        public $DESCRIZIONE = array();
      }

      class TReportDocumentoGenerico extends TZReport
      {
         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
            TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
         }
      }

      class TExtraStampaDocumentoGenerico extends TAdvQuery
      {           
        private $Parametri        = null;
        
        public function __construct($Parametri,  $ScriptEsterno = false)
        {
          parent::__construct($ScriptEsterno);

          if(isset($Parametri))
            $this->Parametri = $Parametri;
          else $this->Parametri = json_decode($_POST['Params']);
        }  

        private function FGetDatiStampa($Descrizione, $Titolo, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
          $Result           = new TStampaDocumentoGenerico();
          $DatiIntestazione = new TDatiIntestazioneGenerico();
          
          TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

          $DatiDescrizione = new TDatiDescrizione();
          $DatiDescrizione->LB_TITOLO   = isset($Titolo)? $Titolo : '';

          if(is_array($Descrizione))
          {
            for($i = 0; $i < count($Descrizione); $i++ )
              array_push($DatiDescrizione->DESCRIZIONE, $Descrizione[$i]);
          }
          else
          {
            $DatiDescrizione->DESCRIZIONE = array($Descrizione);
          }
          
          array_push($Result->BAND_OGGETTO, $DatiDescrizione);

          if($NomeLogo != '')
            $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg';

          array_push($Result->BAND_INTESTAZIONE, $DatiIntestazione);

          return $Result;
        }

        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {        
            $Descrizione = $this->Parametri->Descrizione;    
            $Titolo      = $this->Parametri->Titolo;    

            $StampaRicevuta     = false;  
            
            $NomeLogo = '';
            $SQLBody = "SELECT impostazioni.LOGO_AZIENDA
                          FROM impostazioni";


            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                if(isset($Row['LOGO_AZIENDA']))
                {
                  $NomeLogo = date("Y-m-d_H_i_s_U");
                  ForceDirectory(NOME_CARTELLA_FILE_TEMP);
                  file_put_contents(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg',base64_decode(explode(',',$Row['LOGO_AZIENDA'])[1]));
                }
              }
            }

            $Report = new TReportDocumentoGenerico();
            $Report->LoadFromFile('ModelliStampe/QrStampaDocumentoGenerico.json');
             
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Descrizione, $Titolo, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
              unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');    
        }
      }
