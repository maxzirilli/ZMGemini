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

      class TStampaResocontoRitenuteClienti
      {
        public $DetailResocontoFatture       = null;
        public $BAND_GROUP_FOOTER       = null;
        public $BAND_INTESTAZIONE = null;
        public $BAND_PAGE_FOOTER = null;
        public $QRChildBand1 = null;
      }
      
      class TDatiTotali
      {
        public $LB_TOT_FATTURE = null;
        public $LB_TOT_RITENUTE = null; 
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 		
        public $QR_LOGO = null;
      }

      class TDatiFattura
      {
        public $LB_NUMERO = null;
        public $LB_DATA = null;
        public $LB_INTESTATARIO = null;
        public $LB_TOTALE = null;
        public $LB_RITENUTA = null;      
      }

      class TReportResocontoRitenuteFatture extends TZReport
      {
         private $FCambioColore  = false;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'DetailResocontoFatture')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
           }
         }
      }

      class TExtraStampaResocontoRitenutaFatture extends TAdvQuery
      {  
        public $TotaleResocontoFatture         = 0;
        public $TotaleResocontoFattureRitenute = 0;
        
        function __construct()
        {
          parent::__construct();
          $this->TotaleResocontoFatture         = 0;
          $this->TotaleResocontoFattureRitenute = 0;
        }

        private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
            $Result = new TStampaResocontoRitenuteClienti();
            $QueryPart = explode('FROM fatture',$this->FGetQueryCompiled($PDODBase,
                                                                        'Fatture', 
                                                                        'SelectSQL',
                                                                        'SelectFatture', 
                                                                          get_object_vars($Parametri)));
            $QueryChiavi = 'SELECT fatture.CHIAVE FROM fatture ' . $QueryPart[count($QueryPart) - 1];
            $QueryChiavi = explode('LIMIT', $QueryChiavi)[0];
            $ArrayTotaliFatture = TSystemInformation::GetTotaliFattura($PDODBase,$QueryChiavi,true);

            $QueryDatiResoconto = 'SELECT fatture.NUMERO, 
                                          fatture.DATA, 
                                          fatture.RAGIONE_SOCIALE, 
                                          fatture.CHIAVE,
                                          (SELECT DESCRIZIONE 
                                             FROM cond_pagamento
                                            WHERE cond_pagamento.CHIAVE = fatture.COND_PAGAMENTO) AS COND_PAGAMENTO
                                     FROM fatture ' . $QueryPart[count($QueryPart) - 1];
            $QueryDatiResoconto = explode('LIMIT', $QueryDatiResoconto)[0];
            $Result->DetailResocontoFatture = array();
            if($Query = $PDODBase->query($QueryDatiResoconto))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
				        $DatiFattura = new TDatiFattura();
                if(!is_null($Row['NUMERO']))
                  $DatiFattura->LB_NUMERO = 'Fatt.ra nr.' . $Row['NUMERO'];
                else $DatiFattura->LB_NUMERO = 'Avviso fattura';
                $DatiFattura->LB_DATA = date("d/m/Y", strtotime($Row['DATA']));
                $DatiFattura->LB_INTESTATARIO = $Row['RAGIONE_SOCIALE'];

                for($i = 0; $i < count($ArrayTotaliFatture); $i++)
                {
                  if($ArrayTotaliFatture[$i]->IdFattura == $Row['CHIAVE'])
                  {
                    $DatiFattura->LB_TOTALE = EuropeanCurrencyFormat($ArrayTotaliFatture[$i]->Totale);
                    $DatiFattura->LB_RITENUTA = EuropeanCurrencyFormat($ArrayTotaliFatture[$i]->TotaleRitenuta);   
                    $this->TotaleResocontoFatture += $ArrayTotaliFatture[$i]->Totale;
                    $this->TotaleResocontoFattureRitenute += $ArrayTotaliFatture[$i]->TotaleRitenuta;
                    break;
                  }
                }
                
                
                
                array_push($Result->DetailResocontoFatture,$DatiFattura);
              }
            }  
          
            $Result->BAND_GROUP_FOOTER = array();
				    $DatiTotali = new TDatiTotali();
            $DatiTotali->LB_TOT_FATTURE = EuropeanCurrencyFormat($this->TotaleResocontoFatture);
            $DatiTotali->LB_TOT_RITENUTE = EuropeanCurrencyFormat($this->TotaleResocontoFattureRitenute);
            array_push($Result->BAND_GROUP_FOOTER,$DatiTotali);
            
            
            $Result->BAND_INTESTAZIONE = array();
				    $DatiIntestazione = new TDatiIntestazione();
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 
            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);
          
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

            $Report = new TReportResocontoRitenuteFatture();
            $Report->LoadFromFile('ModelliStampe/QrStampaResocontoRitenutaFatture.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 
            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      

      $AConnection = new TExtraStampaResocontoRitenutaFatture();
      $AConnection->ServerSideScript();      
