<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZGenericFunct.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
      include_once PATH_LIBRERIE . 'ZEconomicFunct.php';
      include_once 'ClassForMail.php';

      require_once PATH_LIBRERIE . 'mailer_PHP8/src/PHPMailer.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/SMTP.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/Exception.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaNota
      {
        public $BAND_INTESTAZIONE = null;
        public $HEADER_BAND_PRODOTTI = null;
        public $QRSubDetail1 = null; 
        public $BAND_SUMMARY = null;
      }

      class TInfoStyleNota
      {
        public $FontStyle = null;
      }

      class TDatiVociNota
      {
        public $DESCRIZIONE = null;
        public $LB_QUANTITA = null;
        public $LB_PREZZO = null;
        public $LB_IMPORTO = null;
        public $LB_IVA = null;
        public $LB_UNITA = null;
        public $LB_SCONTO = null; 
        public $__InfoStyle = null;
      }      

      class TDatiHeaderNota
      {
        public $QRLabel4 = null;
      }

      class TDatiIntestazioneNota
      {
        public $INTESTATARIO = null;      
        public $DESTINATARIO = null;  
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null;
        public $LB_CONDIZIONI_PAGAMENTO = null;    
        public $LB_BANCA = null; 
        public $LB_NUM_DOCUMENTO = null;
        public $LB_DATA_DOCUMENTO = null;
        public $NOTE = null;		  
        public $LB_CAUSALE = null;		  
        public $LB_NS_IBAN = null;
        public $QR_LOGO = null;
        public $DestinatarioPresente = false;
      }

      class TDatiSummaryNota
      {
        public $LB_TOT_IMPONIBILE = null; 
        public $LB_TOT_IMPOSTA = null; 
        public $LB_TOTALE = null; 
        public $LB_PERC_RIT_ACCONTO = null;
        public $LB_IMPONIBILE_RIT_ACCONTO = null;
        public $LB_RIT_ACCONTO = null;
        public $LB_NETTO_A_PAGARE = null;
        public $LB_IMPONIBILE1 = null;
        public $LB_IVA1 = null;
        public $LB_IMPOSTA1 = null;
        public $LB_IMPONIBILE2 = null;
        public $LB_IVA2 = null;
        public $LB_IMPOSTA2 = null;
        public $LB_IMPONIBILE3 = null;
        public $LB_IVA3 = null;
        public $LB_IMPOSTA3 = null;
      } 

      class TReportNotaDiCredito extends TZReport
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
              $PrintBand = $this->FReportEnded;
            if($ABand->Name == 'BAND_INTESTAZIONE')
              $this->FSetVisibilita($ABand,"DESTINATARIO",!$ASingleRecord->DestinatarioPresente);
         }

         public function AfterAllRepetitionPrinted($ABand)
         {
           if($ABand->Name == 'QRSubDetail1')
              $this->FReportEnded = true;
         }
      }

      class TExtraStampaNota extends TAdvQueryClassForMail
      { 
        private $Parametri  = null;
        private $InvioEmail = null;
        private $ControlloEmailGiaInviata = null;

        public function __construct($Parametri, $InvioEmail, $ControlloEmailGiaInviata = false, $ScriptEsterno = false)
        {
          parent::__construct($ScriptEsterno);
          
          if(isset($Parametri))
              $this->Parametri = $Parametri;
          else $this->Parametri = json_decode($_POST['Params']);

          $this->ControlloEmailGiaInviata = $ControlloEmailGiaInviata;
          if(isset($InvioEmail))
            $this->InvioEmail = $InvioEmail;
          else $this->InvioEmail = isset($this->Parametri->InvioEmail)? $this->Parametri->InvioEmail : false;
          $this->TipoDocumento = TIPO_DOCUMENTO_NOTA;
        }     

        private function FGetDatiStampaSingolaNota($ChiaveNota, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
			   $Result = new TStampaNota();
			   $Result->BAND_INTESTAZIONE = array();
			   $DatiIntestazione = new TDatiIntestazioneNota();
			   
			   TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

         $IBAN = '';			   
			   $this->FPrepareParameterValue($ChiaveNota, ':');
			   $SQLBody = "SELECT note_di_credito.*, 
                            province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE,
                            nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
							              (SELECT TARGA FROM province WHERE note_di_credito.PROVINCIA_DESTINAZIONE = province.CHIAVE) AS TARGA_PROVINCIA_DESTINAZIONE,
								            cond_pagamento.DESCRIZIONE AS COND_PAGAMENTO_DESCRIZIONE, 
                            conti_correnti_casse.BANCA AS BANCA_CONTO_CORRENTE,
                            conti_correnti_casse.IBAN AS IBAN_CONTO_CORRENTE,
                            causali.DESCRIZIONE AS DESCRIZIONE_CAUSALE,
                            clienti.CODICE_CLIENTE
                       FROM note_di_credito
                            LEFT OUTER JOIN province ON province.CHIAVE = note_di_credito.PROVINCIA_FATTURAZIONE
                            LEFT OUTER JOIN nazioni ON nazioni.CHIAVE = note_di_credito.NAZIONE_EM_PIVA
                            LEFT OUTER JOIN conti_correnti_casse ON conti_correnti_casse.CHIAVE = note_di_credito.ID_CONTO_CORRENTE
                            LEFT OUTER JOIN clienti  ON clienti.CHIAVE = note_di_credito.ID_CLIENTE
                            LEFT OUTER JOIN cond_pagamento ON cond_pagamento.CHIAVE = note_di_credito.COND_PAGAMENTO
                            LEFT OUTER JOIN causali ON causali.CHIAVE = note_di_credito.ID_CAUSALE
                      WHERE note_di_credito.CHIAVE = $ChiaveNota";

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {  
                    $DatiIntestazione->INTESTATARIO = array( 'CODICE CLIENTE: ' . $Row['CODICE_CLIENTE'], 
                                                             $Row['RAGIONE_SOCIALE'], 
                                                             $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'], 
                                                             $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . ' (' . $Row['TARGA_PROVINCIA_FATTURAZIONE'] . ')',
                                                             'P.IVA: ' . $Row['PARTITA_IVA'] . ' C.F. ' . $Row['CODICE_FISCALE']);
     
                    $DatiIntestazione->DestinatarioPresente = !is_null($Row['INDIRIZZO_DESTINAZIONE']) && trim($Row['INDIRIZZO_DESTINAZIONE']) != '' &&
                                                              !is_null($Row['DESTINAZIONE']) && trim($Row['DESTINAZIONE']) != '';;

                    $DatiIntestazione->DESTINATARIO = array($Row['DESTINAZIONE'],
                                                            $Row['INDIRIZZO_DESTINAZIONE'] . ' ' . $Row['NR_CIVICO_DESTINAZIONE'],
                                                            $Row['CAP_DESTINAZIONE'] . ' ' . $Row['COMUNE_DESTINAZIONE'] . ' (' . $Row['TARGA_PROVINCIA_DESTINAZIONE'] . ')' );
                
                    $DatiIntestazione->LB_CONDIZIONI_PAGAMENTO = $Row['COND_PAGAMENTO_DESCRIZIONE'];

                    if($Row['ID_CONTO_CORRENTE'])
                    {
                      $DatiIntestazione->LB_BANCA = $Row['BANCA_CONTO_CORRENTE'];
                      $DatiIntestazione->LB_NS_IBAN = $Row['IBAN_CONTO_CORRENTE'];
                      $IBAN = $Row['IBAN_CONTO_CORRENTE'];
                    }
                    else $DatiIntestazione->LB_BANCA = $Row['IBAN'];

                    if($Row['NUMERO'])
                      $DatiIntestazione->LB_NUM_DOCUMENTO = $Row['NUMERO'];
                    else $DatiIntestazione->LB_NUM_DOCUMENTO = 'Avviso nota';
                    $CambioFormatoData = date("d/m/Y", strtotime($Row['DATA']));  
                    $DatiIntestazione->LB_DATA_DOCUMENTO = $CambioFormatoData;
                    $DatiIntestazione->NOTE = $Row['NOTE'];
                    $DatiIntestazione->LB_CAUSALE = isset($Row['DESCRIZIONE_CAUSALE'])? $Row['DESCRIZIONE_CAUSALE'] : '';
				          }
			        	}

				$Result->HEADER_BAND_PRODOTTI = array();
				$DatiHeader = new TDatiHeaderNota();
				$DatiHeader->QRLabel4 = ' DESCRIZIONE';
				array_push($Result->HEADER_BAND_PRODOTTI,$DatiHeader);
				
				$Result->QRSubDetail1 = array();

				$SQLBody = "SELECT voci_note_di_credito.*, 
                           unita_di_misura.DESCRIZIONE AS DESCRIZIONE_UNITA_DI_MISURA
                      FROM voci_note_di_credito
                           LEFT OUTER JOIN unita_di_misura ON unita_di_misura.CHIAVE = voci_note_di_credito.UNITA_DI_MISURA
                     WHERE voci_note_di_credito.ID_NOTA_DI_CREDITO = $ChiaveNota
                     ORDER BY voci_note_di_credito.ORDINAMENTO";


                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {                     
                    $DatiVoci = new TDatiVociNota();

                    if(str_contains($Row['DESCRIZIONE'], "\n"))
                    {
                      $DatiVoci->DESCRIZIONE = array();
                      $VettoreStringa = explode("\n", $Row['DESCRIZIONE']);
                      foreach($VettoreStringa as $Stringa)
                        array_push($DatiVoci->DESCRIZIONE, $Stringa);
                    }
                    else $DatiVoci->DESCRIZIONE = array("DESCRIZIONE" => $Row['DESCRIZIONE']);

                    if($Row['QUANTITA'] == 0)
                    {
                      $InfoStyle = new TInfoStyleNota();      
                      $InfoStyle->FontStyle = '[fsBold]';
                      $DatiVoci->__InfoStyle = array();
                      $DatiVoci->__InfoStyle['DESCRIZIONE'] = $InfoStyle;
                      $DatiVoci->LB_QUANTITA = "";
                      $DatiVoci->LB_PREZZO = "";
                      $DatiVoci->LB_IMPORTO = "";
                      $DatiVoci->LB_IVA = "";
                      $DatiVoci->LB_UNITA = "";
                      $DatiVoci->LB_SCONTO = ""; 
                    }  
                    else
                    {
                      $DatiVoci->LB_QUANTITA = EuropeanCurrencyFormat(($Row['QUANTITA'])/ 100, false);
                      if($Row['IMPORTO_IVATO'] == 'T')
                        $DatiVoci->LB_PREZZO = EuropeanCurrencyFormat((($Row['IMPORTO'])/100)/(100 + ($Row['IVA'])/100));
                      else $DatiVoci->LB_PREZZO = EuropeanCurrencyFormat(($Row['IMPORTO'])/ 100);
                      if($Row['IMPORTO_IVATO'] == 'T')
                        $DatiVoci->LB_IMPORTO = EuropeanCurrencyFormat(($Row['IMPORTO'])/ 100);
                      else $DatiVoci->LB_IMPORTO = EuropeanCurrencyFormat((($Row['IMPORTO'])/ 100)+(((($Row['IMPORTO'])/ 100)*($Row['IVA'])/100)/100));
                      $DatiVoci->LB_IVA = EuropeanCurrencyFormat(($Row['IVA'])/ 100, false);
                      $DatiVoci->LB_UNITA = ($Row['DESCRIZIONE_UNITA_DI_MISURA']);
                      $DatiVoci->LB_SCONTO = EuropeanCurrencyFormat((($Row['SCONTO']) / 100), false); 
                    }
                    
                  
					          array_push($Result->QRSubDetail1,$DatiVoci);
                  }
                } 
                else throw new Exception('Impossibile trovare le voci della nota');
				
               $TotaliNota = new stdClass();
               $TotaliNota = TSystemInformation::GetTotaliNota($PDODBase,$ChiaveNota);

        				
               $Result->BAND_SUMMARY = array();
               $DatiSummary = new TDatiSummaryNota();
    
               $DatiSummary->LB_TOT_IMPONIBILE = EuropeanCurrencyFormat($TotaliNota[0]->SommaImponibile);
               $DatiSummary->LB_TOT_IMPOSTA = EuropeanCurrencyFormat($TotaliNota[0]->SommaIva);
               $DatiSummary->LB_TOTALE = EuropeanCurrencyFormat($TotaliNota[0]->Totale);
               $DatiSummary->LB_PERC_RIT_ACCONTO = EuropeanCurrencyFormat($TotaliNota[0]->Ritenuta, false);
               if($TotaliNota[0]->Ritenuta != 0)
                $DatiSummary->LB_IMPONIBILE_RIT_ACCONTO = EuropeanCurrencyFormat($TotaliNota[0]->SommaImponibile);
               else
               {
                 $DatiSummary->LB_IMPONIBILE_RIT_ACCONTO = EuropeanCurrencyFormat(0);
               } 
               $DatiSummary->LB_RIT_ACCONTO = EuropeanCurrencyFormat($TotaliNota[0]->TotaleRitenuta);
               $DatiSummary->LB_NETTO_A_PAGARE = EuropeanCurrencyFormat($TotaliNota[0]->NettoAPagare);
               
               
               for($i = 0; $i < count($TotaliNota[0]->LsIva); $i++)
               {
                  if($i == 0)
                  {
                    $DatiSummary->LB_IMPONIBILE1 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->SommaImponibile);
                    $DatiSummary->LB_IVA1 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->IVA, false);
                    $DatiSummary->LB_IMPOSTA1 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->SommaImponibile * $TotaliNota[0]->LsIva[$i]->IVA / 100);

                    $DatiSummary->LB_IMPONIBILE2 = '';
                    $DatiSummary->LB_IVA2 = '';
                    $DatiSummary->LB_IMPOSTA2 = '';

                    $DatiSummary->LB_IMPONIBILE3 = '';
                    $DatiSummary->LB_IVA3 = '';
                    $DatiSummary->LB_IMPOSTA3 = '';
                  }
                  if($i == 1)
                  {
                    if($TotaliNota[0]->LsIva[$i]->SommaImponibile != 0)
                    { 
                      $DatiSummary->LB_IMPONIBILE2 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->SommaImponibile);
                      $DatiSummary->LB_IVA2 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->IVA);
                      $DatiSummary->LB_IMPOSTA2 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->SommaImponibile * $TotaliNota[0]->LsIva[$i]->IVA / 100);
                    }
                  }
                  if($i == 2)
                  {
                    if($TotaliNota[0]->LsIva[$i]->SommaImponibile != 0)
                    { 
                      $DatiSummary->LB_IMPONIBILE3 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->SommaImponibile);
                      $DatiSummary->LB_IVA3 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->IVA);
                      $DatiSummary->LB_IMPOSTA3 = EuropeanCurrencyFormat($TotaliNota[0]->LsIva[$i]->SommaImponibile * $TotaliNota[0]->LsIva[$i]->IVA / 100);
                    }
                  }
               }
				       array_push($Result->BAND_SUMMARY,$DatiSummary);
       
               if($NomeLogo != '')
                  $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

               array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

               return $Result;
            }
            
            private function FGetDatiStampa($ChiaviNote,$PDODBase,&$JSONAnswer,$NomeLogo)
            {
              $Result = array();
              foreach($ChiaviNote as $Chiave)
                     array_push($Result,$this->FGetDatiStampaSingolaNota($Chiave,$PDODBase,$JSONAnswer,$NomeLogo));
              return $Result;
            }
            
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
               //$ChiaveNota = json_decode($_POST['Params'])->Chiave;    
               $Parametri     = $this->Parametri;
               $InvioEmail    = isset($this->InvioEmail)? 
                                $this->InvioEmail : 
                                (isset($Parametri->InvioEmail)? $Parametri->InvioEmail : false);
               $ChiaviNote = array();
               if(isset($Parametri->Chiave))        
                  array_push($ChiaviNote,$Parametri->Chiave);
               else 
               {
                 $QueryPart = explode('FROM',$this->FGetQueryCompiled($PDODBase,
                                                                      'NoteDiCredito', 
                                                                      'FiltroNote',
                                                                      'FiltroNote', 
                                                                      get_object_vars($Parametri)));

                 $QueryChiavi = 'SELECT note_di_credito.CHIAVE FROM' . $QueryPart[count($QueryPart) - 1];
                 $QueryChiavi = explode('LIMIT', $QueryChiavi)[0];
                 if($Query = $PDODBase->query($QueryChiavi))
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                          array_push($ChiaviNote, $Row['CHIAVE']);
               }

               if(count($ChiaviNote) > 100)
                  throw new Exception('Impossibile gestire più di 100 note di credito');

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


               $Report = new TReportNotaDiCredito();
               $Report->LoadFromFile('ModelliStampe/QrStampaNota.json');

               if($InvioEmail)
               {
                 $NomeCartella = 'Nota_' . gmdate('d_m_Y_H_i_s',time());
                 ForceDirectory($this->FNomeCartellaAttacMail . '/' . $NomeCartella);
                 $NomeNota = 'Nota_' . gmdate('m_Y',time()) . '.pdf';
                 file_put_contents( $this->FNomeCartellaAttacMail . '/' . $NomeCartella . '/' . $NomeNota, 
                                   $Report->GetPDF($this->FGetDatiStampa($ChiaviNote, $PDODBase, $JSONAnswer,$NomeLogo)));

                 $JSONAnswer->Messaggio = $this->FSendMailNotaAllegato($Parametri, $NomeNota, $NomeCartella, $PDODBase, $JSONAnswer, $ChiaviNote);

                 DeleteFullDir( $this->FNomeCartellaAttacMail . '/' . $NomeCartella, '/' );
               }
               else $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaviNote, $PDODBase, $JSONAnswer,$NomeLogo))); 

               if($NomeLogo !='')
                  unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');            
            }

            protected function FSendMailNotaAllegato($Parametri, $NomeNota, $NomeCartella, $PDODBase, &$JSONAnswer, $ChiaviNote)
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
                                  FROM note_di_credito
                                 WHERE CHIAVE = " . $ChiaviNote[0];

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
                            foreach($ChiaviNote as $Chiave)
                              $StringaListaChiavi .= $Chiave . ',';

                            if($StringaListaChiavi != '')
                            {
                              $StringaListaChiavi = substr($StringaListaChiavi, 0, -1);

                              $SQLBody = "UPDATE note_di_credito 
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
                      foreach($ChiaviNote as $Chiave)
                        $StringaListaChiavi .= $Chiave . ',';

                      if($StringaListaChiavi != '')
                      {
                        $StringaListaChiavi = substr($StringaListaChiavi, 0, -1);

                        $SQLBody = "UPDATE note_di_credito 
                                       SET INVIATA_TRAMITE_EMAIL = 'T'
                                     WHERE CHIAVE IN ($StringaListaChiavi)";
                        
                        $PDODBase->query($SQLBody);
                      }
                    }
                  }
                }
                else $JSONAnswer->Risposta = 'MAIL_INVIATA';
            }

            private function FGetTestoMail($Testo)
            {
              $TestoMail = "";
              $TestoMail = file_get_contents('../SendMailDocumento.html');
              $TestoMail = str_replace('_TOKEN_CORPO_EMAIL_', $Testo, $TestoMail);

              return $TestoMail;
            }
      }