<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZGenericFunct.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  


      class TStampaDDTEntrante
      {
        public $BAND_INTESTAZIONE    = array();
        public $BAND_SUMMARY         = array();
        public $HEADER_BAND_PRODOTTI = array();
        public $QRSubDetail1         = array();
      }

      class TInfoStyle
      {
        public $FontStyle = null;
      }

      class TDatiVoci
      {
        public $DESCRIZIONE = array();
        public $LB_QUANTITA = null;
        public $LB_UNITA = null;
        public $__InfoStyle = null;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null;
        public $INTESTATARIO          = null;
        public $DESTINATARIO          = null;
        public $LB_NUM_DOCUMENTO      = null;
        public $LB_DATA_DOCUMENTO     = null;
        public $NOTE_DDT              = null;
        public $LB_CAUSALE            = null;
        public $LB_TRASPORTO_A_MEZZO  = null;
        public $QR_LOGO               = null;
      }

      class TDatiSummary
      {
        public $QRLabel11 = null;
        public $QRLabel13 = null;
        public $QRLabel14 = null;
        public $QRLabel15 = null;
        public $QRLabel16 = null;
        public $QRLabel18 = null; 
      }

      class TDatiHeader
      {
        public $QRLabel4 = null;
      }

      class TReportDDT extends TZReport
      {
         private $FReportEnded = false;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'BAND_SUMMARY')
              $PrintBand = $this->FReportEnded;
         }

         public function AfterAllRepetitionPrinted($ABand)
         {
           if($ABand->Name == 'QRSubDetail1')
              $this->FReportEnded = true;
         }
      }

      class TExtraStampaDDT extends TAdvQuery
      {            
        private function FGetDatiStampa($ChiaveDDT, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
			   
			   $Result           = new TStampaDDTEntrante();
			   $DatiIntestazione = new TDatiIntestazione();
			   
			   TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

			   
				 $DatiSummary = new TDatiSummary();

			   $this->FPrepareParameterValue($ChiaveDDT, ':');
			   $SQLBody = "SELECT ddt_entranti.*, 
                            province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE, 
                            nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
                            (SELECT TARGA FROM province WHERE ddt_entranti.PROVINCIA_DESTINAZIONE = province.CHIAVE) AS TARGA_PROVINCIA_DESTINAZIONE,
                            causali.DESCRIZIONE AS DESCRIZIONE_CAUSALE,
                            anagrafiche.CODICE,
                            anagrafiche.CODICE
                       FROM ddt_entranti
                            LEFT OUTER JOIN province  ON province.CHIAVE = ddt_entranti.PROVINCIA_FATTURAZIONE
                            LEFT OUTER JOIN nazioni   ON nazioni.CHIAVE = ddt_entranti.NAZIONE_EM_PIVA
                            LEFT OUTER JOIN causali   ON causali.CHIAVE = ddt_entranti.CAUSALE
                            LEFT OUTER JOIN anagrafiche   ON anagrafiche.CHIAVE = ddt_entranti.ID_ANAGRAFICA
                      WHERE ddt_entranti.CHIAVE = $ChiaveDDT";

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {  
                    $DatiIntestazione->INTESTATARIO = array( $Row['RAGIONE_SOCIALE'], 
                                                             $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'], 
                                                             $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . ' (' . $Row['TARGA_PROVINCIA_FATTURAZIONE'] . ')',
                                                             'P.IVA: ' . $Row['PARTITA_IVA'] . ' C.F. ' . $Row['CODICE_FISCALE']);
                    
                    if(isset($Row['CODICE']) && $Row['CODICE'] != '')
                      array_unshift($DatiIntestazione->INTESTATARIO, 'CODICE CLIENTE: ' . $Row['CODICE']);
                    if(isset($Row['CODICE']) && $Row['CODICE'] != '')
                      array_unshift($DatiIntestazione->INTESTATARIO, 'CODICE FORNITORE: ' . $Row['CODICE']);

                    $DatiIntestazione->DESTINATARIO = array( $Row['DESTINAZIONE'],
                                                             $Row['INDIRIZZO_DESTINAZIONE'] . ' ' . $Row['NR_CIVICO_DESTINAZIONE'],
                                                             $Row['CAP_DESTINAZIONE'] . ' ' . $Row['COMUNE_DESTINAZIONE'] . ($Row['TARGA_PROVINCIA_DESTINAZIONE'] != ''? '(' . $Row['TARGA_PROVINCIA_DESTINAZIONE'] . ')' : '' ));

                    $DatiIntestazione->LB_NUM_DOCUMENTO = $Row['NUMERO_DDT'];
                    $CambioFormatoData = isset($Row['DATA_DDT'])? date("d/m/Y", strtotime($Row['DATA_DDT'])) : '';
                    $DatiIntestazione->LB_DATA_DOCUMENTO = $CambioFormatoData;
                    $DatiIntestazione->NOTE_DDT = $Row['NOTE'];
                    $DatiIntestazione->LB_CAUSALE = $Row['DESCRIZIONE_CAUSALE'];
                    $DatiIntestazione->LB_TRASPORTO_A_MEZZO = $Row['TRASPORTO_A_MEZZO'];

                    $DatiSummary->QRLabel11 = $Row['ASPETTO_BENI'];
                    $DatiSummary->QRLabel13 = isset($Row['DATA_INIZIO'])? date("d/m/Y", strtotime($Row['DATA_INIZIO'])) : '';
                    $DatiSummary->QRLabel14 = $Row['NUMERO_COLLI'];
                    $DatiSummary->QRLabel15 = $Row['PESO'];
                    $DatiSummary->QRLabel16 = isset($Row['ORA_INIZIO']) ? date("H:i", strtotime($Row['ORA_INIZIO'])) : '';
                    $DatiSummary->QRLabel18 = $Row['PORTO'];
                  }
                }
				
                $DatiHeader = new TDatiHeader();
                $DatiHeader->QRLabel4 = ' DESCRIZIONE';
                array_push($Result->HEADER_BAND_PRODOTTI,$DatiHeader);
                

                $SQLBody = "SELECT voci_ddt_entranti.*, unita_di_misura.DESCRIZIONE AS DESCRIZIONE_UNITA_DI_MISURA
                              FROM voci_ddt_entranti
                              LEFT OUTER JOIN unita_di_misura ON unita_di_misura.CHIAVE = voci_ddt_entranti.UNITA_DI_MISURA
                             WHERE voci_ddt_entranti.ID_DDT = $ChiaveDDT
                             ORDER BY voci_ddt_entranti.ORDINAMENTO";


                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {                     
                    $DatiVoci = new TDatiVoci();

                    if(str_contains($Row['DESCRIZIONE'], "\n"))
                    {
                      $DatiVoci->DESCRIZIONE = array();
                      $VettoreStringa = explode("\n", $Row['DESCRIZIONE']);
                      foreach($VettoreStringa as $Stringa)
                        array_push($DatiVoci->DESCRIZIONE, $Stringa);
                    }
                    else $DatiVoci->DESCRIZIONE = array($Row['DESCRIZIONE']);
                    
                    if($Row['QUANTITA'] == 0)
                    {
                      $InfoStyle = new TInfoStyle();      
                      $InfoStyle->FontStyle = '[fsBold]';
                      $DatiVoci->__InfoStyle = array();
                      $DatiVoci->__InfoStyle['DESCRIZIONE'] = $InfoStyle;
                      $DatiVoci->LB_QUANTITA = "";
                      $DatiVoci->LB_UNITA = "";
                    }  
                    else
                    {
                      $DatiVoci->LB_QUANTITA = number_format(($Row['QUANTITA'])/ 100,2);
                      $DatiVoci->LB_UNITA = ($Row['DESCRIZIONE_UNITA_DI_MISURA']);
                    }
					          array_push($Result->QRSubDetail1,$DatiVoci);
                  }
                } 
                else throw new Exception('Impossibile trovare le voci del documento di trasporto');
             
               if($NomeLogo != '')
                $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

               array_push($Result->BAND_SUMMARY,$DatiSummary);
               array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

               return $Result;
        }
                        
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {        
               $ChiaveDDT = json_decode($_POST['Params'])->Chiave;    
               
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

               $Report = new TReportDDT();
               $Report->LoadFromFile('ModelliStampe/QrStampaDDTUscente.json');
               $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaveDDT, $PDODBase, $JSONAnswer,$NomeLogo))); 

               if($NomeLogo != '')
                  unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');            
            }
      }

      $AConnection = new TExtraStampaDDT();
      $AConnection->ServerSideScript();