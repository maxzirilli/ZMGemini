<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZEconomicFunct.php';
      include_once 'ClassForMail.php';
      include_once PATH_LIBRERIE . 'ZGenericFunct.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
      include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';

      require_once PATH_LIBRERIE . 'mailer_PHP8/src/PHPMailer.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/SMTP.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/Exception.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaFattura
      {
        public $BAND_INTESTAZIONE = null;
        public $BAND_SUMMARY  = null;
        public $HEADER_BAND_VOCI = null;
        public $BAND_VOCI = null;
        public $HEADER_CASSE_PREVIDENZIALI = null;
        public $BAND_CASSE_PREVIDENZIALI = null;
        public $HEADER_BAND_RITENUTE = null;
        public $BAND_RITENUTE = null;
      }

      class TInfoStyleFattura
      {
        public $FontStyle = null;
      }

      class TDatiVociFattura
      {
        public $BENE_SERVIZIO  = null;
        public $LB_CODICE      = null;
        public $LB_TOTALE_RIGA = null;
        public $LB_QUANTITA    = null;
        public $LB_PREZZO      = null;
        public $LB_IVA         = null;
        public $LB_SCONTO1     = null;
        public $LB_SCONTO2     = null; 
        public $LB_SCONTO3     = null; 
        public $LB_UNITA       = null; 
        public $__InfoStyle    = null;
      }
      
      class TDatiVociCasse
      {
        public $CASSA_DESCRIZIONE = null;
        public $CASSA_ALIQUOTA    = null;
        public $CASSA_IMPORTO     = null;
        public $CASSA_IVA         = null;
      }

      class TDatiVociRitenute
      {
        public $RITENUTA_TIPO = null;
        public $RITENUTA_ALIQUOTA = null;
        public $RITENUTA_IMPORTO = null;
      }

      class TDatiIntestazione
      {
        public $INTESTATARIO = null;  
        public $DESTINATARIO = null; 
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null;
        public $LB_CONDIZIONI_PAGAMENTO = null;  
        public $LB_OGGETTO = null;    
        public $LB_NS_IBAN = null;
        public $LB_IBAN    = null;
        public $LB_NUM_DOCUMENTO = null;
        public $LB_DATA_DOCUMENTO = null;
        public $CAUSALE_FATTURA = null;
        public $DestinatarioPresente = false;
        public $QR_LOGO = null;
      }

      class TDatiHeader
      {
        public $DESCRIZIONE = null;
      }

      class TDatiSummary
      {
        public $LB_TOT_IMPONIBILE   = null; 
        public $LB_TOT_IMPOSTA      = null; 
        public $LB_TOTALE           = null; 
        public $LB_PERC_RIT_ACCONTO = null;
        public $LB_IMPONIBILE_RIT_ACCONTO = null;
        public $LB_RIT_ACCONTO      = null;
        public $LB_NETTO_A_PAGARE   = null;
        public $LB_NS_IBAN          = null;
        public $LB_IMPONIBILE1      = null;
        public $LB_IVA1             = null;
        public $LB_IMPOSTA1         = null;
        public $LB_IMPONIBILE2      = null;
        public $LB_IVA2             = null;
        public $LB_IMPOSTA2         = null;
        public $LB_IMPONIBILE3      = null;
        public $LB_IVA3             = null;
        public $LB_IMPOSTA3         = null;
        public $RATA_IMPONIBILE1    = null;
        public $RATA_DATA1          = null;
        public $RATA_IMPONIBILE2    = null;
        public $RATA_DATA2          = null;
        public $RATA_IMPONIBILE3    = null;
        public $RATA_DATA3          = null;
        public $StampaOrdine        = false;

      } 

      class TDatiCasse
      {
        public $QRLabel41 = null;
      }

      class TDatiRitenute
      {
        public $QRLabel19 = null;
      }

      class TReportFattura extends TZReport
      {
         private $FReportEnded = false;
         private $DestinatarioPresente = false;

         private function FSetVisibilita($ABand,$ObjectName,$Hidden)
         {
             $TmpObject = $this->GetObjectFromBand($ABand,$ObjectName);
             if($TmpObject != null)
                $TmpObject->Hidden = $Hidden;
         }

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'BAND_SUMMARY')
           {
              $this->FSetVisibilita($ABand,"QRLabel31",!$ASingleRecord->StampaOrdine);
              $this->FSetVisibilita($ABand,"QRLabel11",!$ASingleRecord->StampaOrdine);

              $PrintBand = $this->FReportEnded;
           }
           if($ABand->Name == 'BAND_INTESTAZIONE')
           {
             $this->FSetVisibilita($ABand,"DESTINATARIO",!$ASingleRecord->DestinatarioPresente);
             $this->FSetVisibilita($ABand,"QRLabel17",!$ASingleRecord->DestinatarioPresente);
             $this->FSetVisibilita($ABand,"QRShape42",!$ASingleRecord->DestinatarioPresente);
           }
           if($ABand->Name == 'BAND_CAUSALE')
              $PrintBand = !is_null($ASingleRecord->CAUSALE_FATTURA) && trim($ASingleRecord->CAUSALE_FATTURA) != '';
         }

         public function AfterAllRepetitionPrinted($ABand)
         {
           if($ABand->Name == 'BAND_VOCI')
              $this->FReportEnded = true;
         }
      }

      class TExtraStampaFatturaPassiva extends TAdvQueryClassForMail
      {
          private $Parametri                = null;
          private $InvioEmail               = null;
          private $ControlloEmailGiaInviata = null;
          

          public function __construct($Parametri, $InvioEmail, $ControlloEmailGiaInviata = false,  $ScriptEsterno = false)
          {
            parent::__construct($ScriptEsterno);
            if(isset($Parametri))
              $this->Parametri    = $Parametri;
            else $this->Parametri = json_decode($_POST['Params']);

            $this->ControlloEmailGiaInviata = $ControlloEmailGiaInviata;
            if(isset($InvioEmail))
              $this->InvioEmail = $InvioEmail;
            else $this->InvioEmail = isset($this->Parametri->InvioEmail)? $this->Parametri->InvioEmail : false;
            $this->TipoDocumento   = TIPO_DOCUMENTO_FATTURA;
          }

          private function FGetDatiStampaSingolaFattura($ChiaveFatturaPassiva, $PDODBase, &$JSONAnswer, $NomeLogo)
          {
            $Result = new TStampaFattura();
            $Result->BAND_INTESTAZIONE = array();
            $DatiIntestazione = new TDatiIntestazione();

            $Result->BAND_SUMMARY = array();
            $DatiSummary = new TDatiSummary();

            
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

            $this->FPrepareParameterValue($ChiaveFatturaPassiva, ':');
            $StampaRate = true;
            $SQLBody = "SELECT fatture_passive.*, 
                               province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE, 
                               nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
                               anagrafiche.BANCA,
                               anagrafiche.CODICE
                          FROM fatture_passive
                               LEFT OUTER JOIN province ON province.CHIAVE = fatture_passive.PROVINCIA_FATTURAZIONE
                               LEFT OUTER JOIN nazioni  ON nazioni.CHIAVE = fatture_passive.NAZIONE_EM_PIVA
                               LEFT OUTER JOIN anagrafiche  ON anagrafiche.CHIAVE = fatture_passive.ID_FORNITORE

                         WHERE fatture_passive.CHIAVE = $ChiaveFatturaPassiva";

            $RagioneSocialeAppoggio = '';
            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {  
                $DatiIntestazione->INTESTATARIO = array( 'ID_FORNITORE: ' . $Row['ID_FORNITORE'], 
                                                         $Row['RAGIONE_SOCIALE'], 
                                                         $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'], 
                                                         $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . (isset($Row['TARGA_PROVINCIA_DESTINAZIONE'])? ' (' . $Row['TARGA_PROVINCIA_FATTURAZIONE'] . ')' : ''),
                                                         $Row['PARTITA_IVA'] != ''?'P.IVA: ' . $Row['PARTITA_IVA'] : '',
                                                         $Row['CODICE_FISCALE'] != ''? 'C.F. ' . $Row['CODICE_FISCALE'] : '');
                
                $RagioneSocialeAppoggio = $Row['RAGIONE_SOCIALE'];

                // $DatiIntestazione->DestinatarioPresente = (!is_null($Row['INDIRIZZO_DESTINAZIONE']) && trim($Row['INDIRIZZO_DESTINAZIONE']) != '') ||
                //                                           (!is_null($Row['DESTINAZIONE']) && trim($Row['DESTINAZIONE']) != '');
         
                // $DatiIntestazione->DESTINATARIO = array($Row['DESTINAZIONE'],
                //                                         $Row['INDIRIZZO_DESTINAZIONE'] . ' ' . $Row['NR_CIVICO_DESTINAZIONE'],
                //                                         $Row['CAP_DESTINAZIONE'] . ' ' . $Row['COMUNE_DESTINAZIONE'] . (isset($Row['TARGA_PROVINCIA_DESTINAZIONE'])? ' (' . $Row['TARGA_PROVINCIA_DESTINAZIONE'] . ')' : ''));
                  
                // $DatiIntestazione->LB_CONDIZIONI_PAGAMENTO = $Row['COND_PAGAMENTO_DESCRIZIONE'];
                // $DatiIntestazione->LB_NS_IBAN = $Row['IBAN'];

                $DatiIntestazione->LB_OGGETTO = $Row['OGGETTO'];
                $DatiIntestazione->LB_IBAN    = isset($Row['IBAN'])? $Row['IBAN'] : '';


                
                $DatiIntestazione->LB_NUM_DOCUMENTO = $Row['NUMERO'];  

                $CambioFormatoData = date("d/m/Y", strtotime($Row['DATA']));  
                $DatiIntestazione->LB_DATA_DOCUMENTO = $CambioFormatoData;

                $DatiSummary->LB_TOTALE = number_format($Row['TOTALE_FATTURA'] / 100,2, ',', '.');
              }
            }
            
            //Essendo una fattura passiva scambio le intestazioni
            $VariabileAppoggio = $DatiIntestazione->INTESTATARIO;

            $DatiIntestazione->INTESTATARIO = $DatiIntestazione->INTESTAZIONE_SOCIETA;
            array_unshift($DatiIntestazione->INTESTATARIO, $DatiIntestazione->DENOMINAZIONE_SOCIETA);

            $DatiIntestazione->INTESTAZIONE_SOCIETA  = $VariabileAppoggio;
            $DatiIntestazione->DENOMINAZIONE_SOCIETA = $RagioneSocialeAppoggio;



            $Result->HEADER_BAND_VOCI = array();
            $DatiHeader = new TDatiHeader();
            $DatiHeader-> DESCRIZIONE = 'Bene/Servizio';
            array_push($Result->HEADER_BAND_VOCI,$DatiHeader);
            
            $Result->BAND_VOCI = array();

            $SQLBody = "SELECT voci_fatture_passive.*
                          FROM voci_fatture_passive
                         WHERE voci_fatture_passive.ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva
                      ORDER BY voci_fatture_passive.NUMERO_LINEA";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {                     
                $DatiVoci = new TDatiVociFattura();
                if(str_contains($Row['BENE_SERVIZIO'], "\n"))
                {
                  $DatiVoci->BENE_SERVIZIO = array();
                  $VettoreStringa = explode("\n", $Row['BENE_SERVIZIO']);
                  foreach($VettoreStringa as $Stringa)
                    array_push($DatiVoci->BENE_SERVIZIO, $Stringa);
                }
                else $DatiVoci->BENE_SERVIZIO = array("BENE_SERVIZIO" => $Row['BENE_SERVIZIO']);

                $DatiVoci->LB_CODICE = array($Row['CODICE_ARTICOLO'] != null? $Row['CODICE_ARTICOLO'] : '');

                if($Row['PREZZO'] == 0)
                {
                  $InfoStyle = new TInfoStyleFattura();      
                  $InfoStyle->FontStyle = '[fsBold]';
                  $DatiVoci->__InfoStyle = array();
                  $DatiVoci->__InfoStyle['BENE_SERVIZIO'] = $InfoStyle;
                  $DatiVoci->LB_QUANTITA = "";
                  $DatiVoci->LB_PREZZO = "";
                  $DatiVoci->LB_TOTALE_RIGA = "";
                  $DatiVoci->LB_IVA = "";
                  $DatiVoci->LB_UNITA = "";
                  $DatiVoci->LB_SCONTO1 = "";
                  $DatiVoci->LB_SCONTO2 = ""; 
                  $DatiVoci->LB_SCONTO3 = ""; 
                }  
                else
                {
                    $DatiVoci->LB_QUANTITA = number_format(($Row['QUANTITA']),2, ',', '.');
                    $DatiVoci->LB_PREZZO   = number_format(($Row['PREZZO']) / 1000, 2, ',', '.');

                    $ImportoRiga = ($Row['PREZZO'] * $Row['QUANTITA']) / 1000;
                    if($Row['SCONTO1']  != 0)
                      $ImportoRiga *= 1 - $Row['SCONTO1'] / 100;
                    if($Row['SCONTO2']  != 0)
                      $ImportoRiga *= 1 - $Row['SCONTO2'] / 100;
                    if($Row['SCONTO3']  != 0)
                      $ImportoRiga *= 1 - $Row['SCONTO3'] / 100;

                    $DatiVoci->LB_TOTALE_RIGA = number_format($Row['TOTALE'] / 1000, 2, ',', '.');
                    
                    $DatiVoci->LB_IVA     = number_format(($Row['IVA']) / 100, 2, ',', '.');
                    $DatiVoci->LB_SCONTO1 = number_format((($Row['SCONTO1']) / 100),2, ',', '.');
                    $DatiVoci->LB_SCONTO2 = number_format((($Row['SCONTO2']) / 100),2, ',', '.'); 
                    $DatiVoci->LB_SCONTO3 = number_format((($Row['SCONTO3']) / 100),2, ',', '.'); 
                }
                array_push($Result->BAND_VOCI,$DatiVoci);
              }
            } 
            else throw new Exception('Impossibile trovare le voci del Fattura');

            //CASSE PREVIDENZIALI
            $Result->HEADER_CASSE_PREVIDENZIALI = array();
            $DatiCasse = new TDatiCasse();
            $DatiCasse-> QRLabel41 = 'Cassa previdenziale';
            array_push($Result->HEADER_CASSE_PREVIDENZIALI,$DatiCasse);
            $Result->BAND_CASSE_PREVIDENZIALI = array();
            
            $SQLBody = "SELECT *
                          FROM casse_previdenziali_fatt_pass
                         WHERE casse_previdenziali_fatt_pass.ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva
                      ORDER BY casse_previdenziali_fatt_pass.ID_FATTURA_PASSIVA";



            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {                     
                $DatiVociCasse = new TDatiVociCasse();
                
                if($Row['IMPORTO'] == 0 && isset($Row['TIPO']) && $Row['TIPO'] != '')
                {
                  $InfoStyle = new TInfoStyleFattura();      
                  $InfoStyle->FontStyle = '[fsBold]';
                  $DatiVociCasse-> CASSA_ALIQUOTA = 0;
                  $DatiVociCasse-> CASSA_IMPORTO  = 0;
                  $DatiVociCasse-> CASSA_IVA      = 0;
                  $DatiVociCasse-> CASSA_DESCRIZIONE = '';

                }
                else
                {
                  $DatiVociCasse->CASSA_IMPORTO     = number_format(($Row['IMPORTO'])/ 100,2, ',', '.');
                  $DatiVociCasse->CASSA_ALIQUOTA    = number_format(($Row['ALIQUOTA'])/ 100,2, ',', '.');
                  $DatiVociCasse->CASSA_IVA         = number_format(($Row['IMPOSTA']) / 100, 2, ',', '.');

                  if($Row['TIPO'] == 0)
                    $DatiVociCasse->CASSA_DESCRIZIONE = 'Non presente';
                  else $DatiVociCasse->CASSA_DESCRIZIONE = VETTORE_CASSE_PREVIDENZIALI[$Row['TIPO']];
                }
                array_push($Result->BAND_CASSE_PREVIDENZIALI, $DatiVociCasse);
              }
            } 
            else throw new Exception('Impossibile trovare dati cassa previdenziale');
            
            
            //RITENUTE FATTURE PASSIVE
            $Result->HEADER_BAND_RITENUTE = array();
            $DatiRitenute = new TDatiRitenute();
            $DatiRitenute-> QRLabel19 = 'Tipo ritenuta';
            array_push($Result->HEADER_BAND_RITENUTE,$DatiRitenute);

            $Result->BAND_RITENUTE = array();

            $SQLBody = "SELECT *
                          FROM ritenute_fatture_passive
                         WHERE ritenute_fatture_passive.ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva
                      ORDER BY ritenute_fatture_passive.ID_FATTURA_PASSIVA";

            $TotaleImportoRitenute = 0;
            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {                     
                $DatiVociRitenute = new TDatiVociRitenute();

                if($Row['IMPORTO'] == 0 && isset($Row['TIPO']) && $Row['TIPO'] != '')
                {
                  $InfoStyle = new TInfoStyleFattura();      
                  $InfoStyle->FontStyle = '[fsBold]';
                  $DatiVociRitenute->__InfoStyle = array();
                  $DatiVociRitenute-> RITENUTA_TIPO     = '';
                  $DatiVociRitenute-> RITENUTA_ALIQUOTA = 0;
                  $DatiVociRitenute-> RITENUTA_IMPORTO  = 0;
                }
                else
                {
                  $TotaleImportoRitenute += $Row['IMPORTO'];
                  $DatiVociRitenute->RITENUTA_TIPO     = VETTORE_TIPO_RITENUTE[$Row['TIPO']];
                  $DatiVociRitenute->RITENUTA_ALIQUOTA = number_format(($Row['ALIQUOTA'])/ 100,2, ',', '.');
                  $DatiVociRitenute->RITENUTA_IMPORTO  = number_format(($Row['IMPORTO'])/ 100,2, ',', '.');
                } 
                array_push($Result->BAND_RITENUTE,$DatiVociRitenute);
              }
            } 
            else throw new Exception('Impossibile trovare dati ritenute');


            //RIEPILOGO ALIQUOTE
            $NumeroRiepiloghi = 0;


            $DatiSummary->LB_IMPONIBILE1 = '';
            $DatiSummary->LB_IVA1        = '';
            $DatiSummary->LB_IMPOSTA1    = '';
            
            $DatiSummary->LB_IMPONIBILE2 = '';
            $DatiSummary->LB_IVA2        = '';
            $DatiSummary->LB_IMPOSTA2    = '';

            $DatiSummary->LB_IMPONIBILE3 = '';
            $DatiSummary->LB_IVA3        = '';
            $DatiSummary->LB_IMPOSTA3    = '';


            $SQLBody = "SELECT riepiloghi_fatture_passive.*
                          FROM riepiloghi_fatture_passive
                         WHERE riepiloghi_fatture_passive.ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva
                      ORDER BY riepiloghi_fatture_passive.ID_FATTURA_PASSIVA";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {                     
                $NumeroRiepiloghi++;

                if($NumeroRiepiloghi == 1)
                {
                  $DatiSummary->LB_IMPONIBILE1 = number_format(($Row['IMPONIBILE'])/ 100,2, ',', '.');
                  $DatiSummary->LB_IVA1        = number_format(($Row['IVA'])/ 100,2, ',', '.');
                  $DatiSummary->LB_IMPOSTA1    = number_format(($Row['IMPOSTA'])/ 100,2, ',', '.');
                }
                if($NumeroRiepiloghi == 2)
                {
                  $DatiSummary->LB_IMPONIBILE2 = number_format(($Row['IMPONIBILE'])/ 100,2, ',', '.');
                  $DatiSummary->LB_IVA2        = number_format(($Row['IVA'])/ 100,2, ',', '.');
                  $DatiSummary->LB_IMPOSTA2    = number_format(($Row['IMPOSTA'])/ 100,2, ',', '.');
                }
                if($NumeroRiepiloghi == 3)
                {
                  $DatiSummary->LB_IMPONIBILE3 = number_format(($Row['IMPONIBILE'])/ 100,2, ',', '.');
                  $DatiSummary->LB_IVA3        = number_format(($Row['IVA'])/ 100,2, ',', '.');
                  $DatiSummary->LB_IMPOSTA3    = number_format(($Row['IMPOSTA'])/ 100,2, ',', '.');
                }
              }
            } 
            else throw new Exception('Impossibile trovare dati cassa previdenziale');



            $Result->BAND_SUMMARY = array();

            $DatiSummary->LB_RIT_ACCONTO = number_format($TotaleImportoRitenute / 100,2, ',', '.');

            // $DatiSummary->LB_TOT_IMPONIBILE = number_format($TotaliFatturaPassiva[0]->SommaImponibile,2, ',', '.');
            // $DatiSummary->LB_TOT_IMPOSTA = number_format($TotaliFatturaPassiva[0]->SommaIva,2, ',', '.');
            // $DatiSummary->LB_NETTO_A_PAGARE = number_format($TotaliFatturaPassiva[0]->NettoAPagare,2, ',', '.');

            // if($IBAN)
            //   $DatiSummary->LB_NS_IBAN = $IBAN;
            
            // for($i = 0; $i < count($TotaliFatturaPassiva[0]->LsIva); $i++)
            // {
            //   if($i == 0)
            //   {
            //     $DatiSummary->LB_IMPONIBILE1 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile,2, ',', '.');
            //     if($TotaliFatturaPassiva[0]->LsIva[$i]->IVA != 0)
            //       $DatiSummary->LB_IVA1 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->IVA,2, ',', '.');
            //     $DatiSummary->LB_IMPOSTA1 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile * $TotaliFatturaPassiva[0]->LsIva[$i]->IVA / 100,2, ',', '.');
                              
            //     $DatiSummary->LB_IMPONIBILE2 = '';
            //     $DatiSummary->LB_IVA2 = '';
            //     $DatiSummary->LB_IMPOSTA2 = '';

            //     $DatiSummary->LB_IMPONIBILE3 = '';
            //     $DatiSummary->LB_IVA3 = '';
            //     $DatiSummary->LB_IMPOSTA3 = '';
            //   }
            //   if($i == 1)
            //   {
            //     if($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile != 0)
            //     { 
            //       $DatiSummary->LB_IMPONIBILE2 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile,2, ',', '.');
            //       if($TotaliFatturaPassiva[0]->LsIva[$i]->IVA != 0)
            //         $DatiSummary->LB_IVA2 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->IVA,2, ',', '.');
            //       $DatiSummary->LB_IMPOSTA2 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile * $TotaliFatturaPassiva[0]->LsIva[$i]->IVA / 100,2, ',', '.');
            //     }
            //   }
            //   if($i == 2)
            //   {
            //     if($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile != 0)
            //     {
            //       $DatiSummary->LB_IMPONIBILE3 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile,2, ',', '.');
            //       if($TotaliFatturaPassiva[0]->LsIva[$i]->IVA != 0)
            //         $DatiSummary->LB_IVA3 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->IVA,2, ',', '.');
            //       $DatiSummary->LB_IMPOSTA3 = number_format($TotaliFatturaPassiva[0]->LsIva[$i]->SommaImponibile * $TotaliFatturaPassiva[0]->LsIva[$i]->IVA / 100,2, ',', '.');
            //     }
            //   }
            // }
            
            array_push($Result->BAND_SUMMARY,$DatiSummary);

           
            $this->FGestioneRate($PDODBase, $ChiaveFatturaPassiva, $DatiSummary, $StampaRate);


            // if($NomeLogo != '')
            //   $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

            return $Result;
          }

          private function FGestioneRate($PDODBase, $ChiaveFatturaPassiva, $DatiSummary, $StampaRate)
          {
            if($StampaRate)
            {
              $SQLBody = "SELECT rate_fatture_passive.*
                            FROM rate_fatture_passive
                          WHERE rate_fatture_passive.ID_FATTURA_PASSIVA = $ChiaveFatturaPassiva";
              $j = 0;
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  if($j == 0)
                  {
                    $DatiSummary->RATA_IMPONIBILE1 = number_format($Row['IMPORTO']/1000,2, ',', '.');
                    $DatiSummary->RATA_DATA1 = date("d/m/Y", strtotime($Row['DATA_SCADENZA']));

                    $DatiSummary->RATA_IMPONIBILE2 = '';
                    $DatiSummary->RATA_DATA2 = '';

                    $DatiSummary->RATA_IMPONIBILE3 = '';
                    $DatiSummary->RATA_DATA3 = '';
                  }
                  if($j == 1)
                  {
                    $DatiSummary->RATA_IMPONIBILE2 = number_format($Row['IMPORTO']/1000,2, ',', '.');
                    $DatiSummary->RATA_DATA2 = date("d/m/Y", strtotime($Row['DATA_SCADENZA']));
                  }
                  if($j == 2)
                  {
                    $DatiSummary->RATA_IMPONIBILE3 = number_format($Row['IMPORTO']/1000,2, ',', '.');
                    $DatiSummary->RATA_DATA3 = date("d/m/Y", strtotime($Row['DATA_SCADENZA']));
                  }
                  $j++;
                }
              }
            }
            else
            {
              $DatiSummary->RATA_IMPONIBILE1 = '';
              $DatiSummary->RATA_DATA1       = '';

              $DatiSummary->RATA_IMPONIBILE2 = '';
              $DatiSummary->RATA_DATA2       = '';

              $DatiSummary->RATA_IMPONIBILE3 = '';
              $DatiSummary->RATA_DATA3       = '';
            }
          }
            
          private function FGetDatiStampa($ChiaviFatturePassive,$PDODBase,&$JSONAnswer,$NomeLogo)
          {
             $Result = array();
             foreach($ChiaviFatturePassive as $Chiave)
                array_push($Result,$this->FGetDatiStampaSingolaFattura($Chiave,$PDODBase,$JSONAnswer,$NomeLogo));
             return $Result;
          }

          protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
          { 
            $Parametri     = $this->Parametri;
            $InvioEmail    = isset($this->InvioEmail)? 
                             $this->InvioEmail : 
                             (isset($Parametri->InvioEmail)? $Parametri->InvioEmail : false);
            $ChiaviFatturePassive = array();
            if(isset($Parametri->Chiave))        
               array_push($ChiaviFatturePassive, $Parametri->Chiave);
            else 
            {
               $QueryPart = explode('FROM',$this->FGetQueryCompiled($PDODBase,
                                                                    'FatturePassive', 
                                                                    'SelectSQL',
                                                                    'SelectFatturePassive', 
                                                                     get_object_vars($Parametri)));

               $QueryChiavi = 'SELECT fatture_passive.CHIAVE FROM' . $QueryPart[count($QueryPart) - 1];

                if($Query = $PDODBase->query($QueryChiavi))
                   while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                         array_push($ChiaviFatturePassive, $Row['CHIAVE']);
            }
            
            if(count($ChiaviFatturePassive) > 100)
               throw new Exception('Impossibile gestire più di 100 fatture');
               
            $NomeLogo = '';
            // $SQLBody = "SELECT impostazioni.LOGO_AZIENDA
            //               FROM impostazioni";

            // if($Query = $PDODBase->query($SQLBody))
            //    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            //    { 
            //      if(isset($Row['LOGO_AZIENDA']))
            //      {
            //         $NomeLogo = date("Y-m-d_H_i_s_U");
            //         ForceDirectory(NOME_CARTELLA_FILE_TEMP);
            //         file_put_contents( NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg', base64_decode(explode(',',$Row['LOGO_AZIENDA'])[1]));
            //      }
            //    }
            $Report = new TReportFattura();
            $Report->LoadFromFile('ModelliStampe/QrStampaFatturaPassiva.json');
            if($InvioEmail)
            {
              $NomeCartella = 'Fattura_' . gmdate('d_m_Y_H_i_s',time());
              ForceDirectory($this->FNomeCartellaAttacMail . '/' . $NomeCartella);
              $NomeFattura = 'Fattura_' . gmdate('m_Y',time()) . '.pdf';
              file_put_contents( $this->FNomeCartellaAttacMail . '/' . $NomeCartella . '/' . $NomeFattura, 
                                 $Report->GetPDF($this->FGetDatiStampa($ChiaviFatturePassive, $PDODBase, $JSONAnswer,$NomeLogo)));
              $JSONAnswer->Messaggio = $this->FSendMailFatturaAllegata($Parametri, $NomeFattura, $NomeCartella, $PDODBase, $JSONAnswer, $ChiaviFatturePassive);

              DeleteFullDir( $this->FNomeCartellaAttacMail . '/' . $NomeCartella, '/' );
            }
            else $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaviFatturePassive, $PDODBase, $JSONAnswer,$NomeLogo))); 


            // if($NomeLogo != '')
            //    unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');
          }

          protected function FSendMailFatturaAllegata($Parametri, $NomeFattura, $NomeCartella, $PDODBase, &$JSONAnswer, $ChiaviFatturePassive)
          {            
              $Oggetto      = $Parametri->Oggetto;
              $Destinatario = $Parametri->Destinatario;
              $Cc           = $Parametri->Cc;
              $Ccn          = $Parametri->Ccn;
              $CorpoEmail   = $Parametri->CorpoEmail;

              $Errore = '';
              $JSONAnswer->Risposta = 'MAIL_INVIATA';
              
              if(INVIO_MAIL_ABILITATO)
              {
                $this->GetParameterEmail($PDODBase, MAIL_ACCOUNT_FATTURE);

                if(isset($this->ControlloEmailGiaInviata) && $this->ControlloEmailGiaInviata)
                {
                  $SQLBody = "SELECT INVIATA_TRAMITE_EMAIL
                                FROM fatture_passive
                               WHERE CHIAVE = " . $ChiaviFatturePassive[0];

                  if($Query = $PDODBase->query($SQLBody))
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    { 
                      if($Row['INVIATA_TRAMITE_EMAIL'] != 'T')
                      {
                        if(!$this->FSendMail($Destinatario, $Cc, $Ccn , $Oggetto, $this->FGetTestoMail($CorpoEmail), $this->FSmtpFromName, $this->FSmtpFromMail, $Errore, $NomeCartella, true, $PDODBase))
                        {
                          $JSONAnswer->ErroreMail    = $Errore;
                          $JSONAnswer->Risposta      = 'ERRORE_INVIO_MAIL';
                        }
                        else
                        {
                          $StringaListaChiavi = '';
                          foreach($ChiaviFatturePassive as $Chiave)
                            $StringaListaChiavi .= $Chiave . ',';

                          if($StringaListaChiavi != '')
                          {
                            $StringaListaChiavi = substr($StringaListaChiavi, 0, -1);

                            $SQLBody = "UPDATE fatture_passive 
                                          SET INVIATA_TRAMITE_EMAIL = 'T'
                                        WHERE CHIAVE IN ($StringaListaChiavi)";
                            
                            $PDODBase->query($SQLBody);
                          }
                        }
                      }
                      else
                      {
                        $JSONAnswer->Risposta        = 'EMAIL_GIA_INVIATA';
                      }
                    }
                }
                else
                {
                  if(!$this->FSendMail($Destinatario, $Cc, $Ccn , $Oggetto, $this->FGetTestoMail($CorpoEmail), $this->FSmtpFromName, $this->FSmtpFromMail, $Errore, $NomeCartella, true, $PDODBase))
                  {
                    $JSONAnswer->ErroreMail    = $Errore;
                    $JSONAnswer->Risposta      = 'ERRORE_INVIO_MAIL';
                  }
                  else
                  {
                    $StringaListaChiavi = '';
                    foreach($ChiaviFatturePassive as $Chiave)
                      $StringaListaChiavi .= $Chiave . ',';

                    if($StringaListaChiavi != '')
                    {
                      $StringaListaChiavi = substr($StringaListaChiavi, 0, -1);

                      $SQLBody = "UPDATE fatture_passive 
                                     SET INVIATA_TRAMITE_EMAIL = 'T'
                                   WHERE CHIAVE IN ($StringaListaChiavi)";
                      
                      $PDODBase->query($SQLBody);
                    }
                  }
                }
              }
          }

          private function FGetTestoMail($Testo)
          {
            $TestoMail = "";
            $TestoMail = file_get_contents('../SendMailDocumento.html');
            $TestoMail = str_replace('_TOKEN_CORPO_EMAIL_', $Testo, $TestoMail);

            return $TestoMail;
          }
      }

