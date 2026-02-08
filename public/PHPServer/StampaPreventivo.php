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

      class TStampaPreventivoMultiparametrico
      {
        public $BAND_INTESTAZIONE = null;
        public $HEADER_BAND_PRODOTTI = null;
        public $QRSubDetail1 = null;
        public $BAND_SUMMARY = null;
        public $BAND_TOTALE  = null;
      }

      class TDatiSummary
      {
        public $LB_PROPOSTA_IMMEDIATA = null;
      }

      class TDatiTotali
      {
       public $LB_TOT_IMPONIBILE = null; 
       public $LB_TOT_IMPOSTA = null; 
       public $LB_TOTALE = null; 
       public $LB_PERC_RIT_ACCONTO = null;
       public $LB_IMPONIBILE_RIT_ACCONTO = null;
       public $LB_RIT_ACCONTO = null;
       public $LB_NETTO_A_PAGARE = null;
       public $LB_NS_IBAN = null;
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
            

      class TInfoStyle
      {
        public $FontStyle = null;
      }

      class TDatiVoci
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

      class TDatiHeader
      {
        public $QRLabel4 = null;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 		
        public $INTESTATARIO = null;
        public $DESTINATARIO = null;
        public $LB_CONDIZIONI_PAGAMENTO = null;
        public $LB_BANCA = null;
        public $LB_NUM_DOCUMENTO = null;
        public $LB_DATA_DOCUMENTO = null;
        public $NOTE = null;      
        public $QR_LOGO = null;
        public $DestinatarioPresente = false;
        public $LB_TITOLO_DOCUMENTO = null;
        public $LB_NS_IBAN = null;
      }


      class TReportPreventivoMultiparametrico extends TZReport
      {
         private $DestinatarioPresente = false;
         private $FReportEnded = false;
         
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
           {
             $this->FSetVisibilita($ABand,"DESTINATARIO",!$ASingleRecord->DestinatarioPresente);
           }
         }

         public function AfterAllRepetitionPrinted($ABand)
         {
           if($ABand->Name == 'QRSubDetail1')
              $this->FReportEnded = true;
         }
      }

      class TExtraStampaPreventivoMultiparametrico extends TAdvQueryClassForMail
      {            
            public function __construct($ScriptEsterno = false)
            {
              parent::__construct($ScriptEsterno);
              $this->TipoDocumento = TIPO_DOCUMENTO_PREVENTIVO_MULTI;
            }

            private function FGetDatiStampaSingoloPreventivoMultiparametrico($ChiavePreventivo, $PDODBase, &$JSONAnswer, &$NomeLogo)
            {
             $Result = new TStampaPreventivoMultiparametrico();
             $Result->BAND_INTESTAZIONE = array();
             $DatiIntestazione = new TDatiIntestazione();
             $DatiTotali       = null;
        
                TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
                
                  $IBAN = '';
                  $this->FPrepareParameterValue($ChiavePreventivo, ':');
                  $SQLBody = "SELECT preventivi_multiparametrici.*, 
                                     province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE, 
                                     nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
                             (SELECT TARGA FROM province WHERE preventivi_multiparametrici.PROVINCIA_DESTINAZIONE = province.CHIAVE) AS TARGA_PROVINCIA_DESTINAZIONE,
                                     cond_pagamento.DESCRIZIONE AS COND_PAGAMENTO_DESCRIZIONE, 
                                     conti_correnti_casse.BANCA AS BANCA_CONTO_CORRENTE,
                                     conti_correnti_casse.IBAN AS IBAN_CONTO_CORRENTE,
                                     anagrafiche.CODICE
                                FROM preventivi_multiparametrici
                                LEFT OUTER JOIN province ON province.CHIAVE = preventivi_multiparametrici.PROVINCIA_FATTURAZIONE
                                LEFT OUTER JOIN nazioni ON nazioni.CHIAVE = preventivi_multiparametrici.NAZIONE_EM_PIVA
                                LEFT OUTER JOIN anagrafiche  ON anagrafiche.CHIAVE = preventivi_multiparametrici.ID_CLIENTE
                                LEFT OUTER JOIN conti_correnti_casse ON conti_correnti_casse.CHIAVE = preventivi_multiparametrici.ID_CONTO_CORRENTE
                          LEFT OUTER JOIN cond_pagamento ON cond_pagamento.CHIAVE = preventivi_multiparametrici.COND_PAGAMENTO
                               WHERE preventivi_multiparametrici.CHIAVE = $ChiavePreventivo";

                  if($Query = $PDODBase->query($SQLBody))
                  {
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    {  
                      $DatiIntestazione->INTESTATARIO = array( 'CODICE CLIENTE: ' . $Row['CODICE'], 
                                                               $Row['RAGIONE_SOCIALE'], 
                                                               $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'], 
                                                               $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . (isset($Row['TARGA_PROVINCIA_FATTURAZIONE'])? ' (' . $Row['TARGA_PROVINCIA_FATTURAZIONE'] . ')' : ''),
                                                               'P.IVA: ' . $Row['PARTITA_IVA'] . ' C.F. ' . $Row['CODICE_FISCALE']);
                      
                      $DatiIntestazione->DestinatarioPresente = !is_null($Row['INDIRIZZO_DESTINAZIONE']) && trim($Row['INDIRIZZO_DESTINAZIONE']) != '';
                      $DatiIntestazione->DESTINATARIO = array( $Row['DESTINAZIONE'], 
                                                               $Row['INDIRIZZO_DESTINAZIONE'] . ' ' . $Row['NR_CIVICO_DESTINAZIONE'],
                                                               $Row['CAP_DESTINAZIONE'] . ' ' . $Row['COMUNE_DESTINAZIONE'] . (isset($Row['TARGA_PROVINCIA_DESTINAZIONE'])? ' (' . $Row['TARGA_PROVINCIA_DESTINAZIONE'] . ')' : '' ));

                      $DatiIntestazione->LB_TITOLO_DOCUMENTO     = 'PREVENTIVO N° ' . $Row['NUMERO'] . ' - REVISIONE ' . $Row['NUMERO_REVISIONE'];
                     
                      $DatiIntestazione->LB_CONDIZIONI_PAGAMENTO = $Row['COND_PAGAMENTO_DESCRIZIONE'];
                     
                      if($Row['ID_CONTO_CORRENTE'])
                      {
                        $DatiIntestazione->LB_BANCA = $Row['BANCA_CONTO_CORRENTE'];
                        $DatiIntestazione->LB_NS_IBAN = $Row['IBAN_CONTO_CORRENTE'];
                        $IBAN = $Row['IBAN_CONTO_CORRENTE'];
                      }
                      else $DatiIntestazione->LB_BANCA = $Row['IBAN'];

                      $DatiIntestazione->LB_NUM_DOCUMENTO  = $Row['NUMERO'];
                      $CambioFormatoData                   = date("d/m/Y", strtotime($Row['DATA']));  
                      $DatiIntestazione->LB_DATA_DOCUMENTO = $CambioFormatoData;
                      $DatiIntestazione->NOTE              = $Row['NOTE'];

                    }
                  }
      
                  $Result->HEADER_BAND_PRODOTTI = array();
                  $DatiHeader = new TDatiHeader();
                  $DatiHeader->QRLabel4 = ' DESCRIZIONE';
                  array_push($Result->HEADER_BAND_PRODOTTI,$DatiHeader);
                  
                  $Result->QRSubDetail1 = array();


                  $SQLBody = "SELECT preventivi_multi_sezione.CHIAVE AS CHIAVE_SEZIONE,
                                     preventivi_multi_sezione.ID_PREVENTIVO_MULTI,
                                     preventivi_multi_soluzioni.CHIAVE AS CHIAVE_SOLUZIONE,
                                     preventivi_multi_voci_soluzione.CHIAVE AS CHIAVE_VOCE,
                                     preventivi_multi_voci_soluzione.*,
                                     unita_di_misura.DESCRIZIONE AS DESCRIZIONE_UNITA_DI_MISURA
                                FROM preventivi_multi_sezione 
                                     LEFT OUTER JOIN preventivi_multi_soluzioni ON preventivi_multi_soluzioni.ID_SEZIONE = preventivi_multi_sezione.CHIAVE
                                     LEFT OUTER JOIN preventivi_multi_voci_soluzione ON preventivi_multi_voci_soluzione.ID_SOLUZIONE = preventivi_multi_soluzioni.CHIAVE
                                     LEFT OUTER JOIN unita_di_misura ON unita_di_misura.CHIAVE = preventivi_multi_voci_soluzione.UNITA_DI_MISURA
                               WHERE preventivi_multi_sezione.ID_PREVENTIVO_MULTI = $ChiavePreventivo
                            ORDER BY preventivi_multi_sezione.CHIAVE, preventivi_multi_soluzioni.CHIAVE, preventivi_multi_voci_soluzione.ORDINAMENTO ";

                  $LastSezione   = null;
                  $LastSoluzione = null;
                  $NumeroScelte  = 1;
                  $PosizioneInserimentoScelta = 0;

                  if($Query = $PDODBase->query($SQLBody))
                  {
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    {                
                      if(isset($LastSezione) && $LastSezione != $Row['CHIAVE_SEZIONE'])  
                      {
                        $DatiVoci = new TDatiVoci();
                        $DatiVoci->DESCRIZIONE = array("DESCRIZIONE" => '');
                        $DatiVoci->LB_QUANTITA = "";
                        $DatiVoci->LB_PREZZO   = "";
                        $DatiVoci->LB_IMPORTO  = "";
                        $DatiVoci->LB_IVA      = "";
                        $DatiVoci->LB_UNITA    = "";
                        $DatiVoci->LB_SCONTO   = ""; 
                        array_push($Result->QRSubDetail1, $DatiVoci);
                        
                        $PosizioneInserimentoScelta = count($Result->QRSubDetail1);
                      }


                      if($LastSezione == $Row['CHIAVE_SEZIONE'] && isset($LastSoluzione) && $LastSoluzione != $Row['CHIAVE_SOLUZIONE'])  
                      {
                        if($PosizioneInserimentoScelta == 0)
                        {
                          $DatiVoci = new TDatiVoci();
                          $DatiVoci->DESCRIZIONE = array("DESCRIZIONE" => 'Scelta n. 1');
                          $DatiVoci->LB_QUANTITA = "";
                          $DatiVoci->LB_PREZZO   = "";
                          $DatiVoci->LB_IMPORTO  = "";
                          $DatiVoci->LB_IVA      = "";
                          $DatiVoci->LB_UNITA    = "";
                          $DatiVoci->LB_SCONTO   = ""; 
                          $InfoStyle             = new TInfoStyle();      
                          $InfoStyle->FontStyle  = '[fsBold]';
                          $DatiVoci->__InfoStyle = array();
                          $DatiVoci->__InfoStyle['DESCRIZIONE'] = $InfoStyle;
                          $NumeroScelte++;
                          array_splice( $Result->QRSubDetail1, $PosizioneInserimentoScelta, 0, [$DatiVoci] );
                        }
                        else
                        {
                          $DatiVoci = new TDatiVoci();
                          $DatiVoci->DESCRIZIONE = array("DESCRIZIONE" => 'Scelta n. ' . $NumeroScelte);
                          $DatiVoci->LB_QUANTITA = "";
                          $DatiVoci->LB_PREZZO   = "";
                          $DatiVoci->LB_IMPORTO  = "";
                          $DatiVoci->LB_IVA      = "";
                          $DatiVoci->LB_UNITA    = "";
                          $DatiVoci->LB_SCONTO   = ""; 
                          $InfoStyle             = new TInfoStyle();      
                          $InfoStyle->FontStyle  = '[fsBold]';
                          $DatiVoci->__InfoStyle = array();
                          $DatiVoci->__InfoStyle['DESCRIZIONE'] = $InfoStyle;
                          $NumeroScelte++;
                          array_splice($Result->QRSubDetail1, $PosizioneInserimentoScelta, 0, [$DatiVoci]);
                        }

                        $DatiVoci = new TDatiVoci();
                        $DatiVoci->DESCRIZIONE = array("DESCRIZIONE" => 'OPPURE');
                        $DatiVoci->LB_QUANTITA = "";
                        $DatiVoci->LB_PREZZO   = "";
                        $DatiVoci->LB_IMPORTO  = "";
                        $DatiVoci->LB_IVA      = "";
                        $DatiVoci->LB_UNITA    = "";
                        $DatiVoci->LB_SCONTO   = ""; 
                        $InfoStyle             = new TInfoStyle();      
                        $InfoStyle->FontStyle  = '[fsBold]';
                        $DatiVoci->__InfoStyle = array();
                        $DatiVoci->__InfoStyle['DESCRIZIONE'] = $InfoStyle;
                        array_push($Result->QRSubDetail1,$DatiVoci);
                      }




                      $DatiVoci = new TDatiVoci();

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
                        $InfoStyle = new TInfoStyle();      
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
                        $DatiVoci->LB_PREZZO   = EuropeanCurrencyFormat(($Row['IMPORTO']) / 100, false);
                        
                        $ImportoRiga           = ($Row['IMPORTO'] * $Row['QUANTITA']) /  10000;
                        if($Row['SCONTO']  != 0)
                          $ImportoRiga *= 1 - $Row['SCONTO'] / 10000;
                        $DatiVoci->LB_IMPORTO  = EuropeanCurrencyFormat($ImportoRiga, false);
                        
                        $DatiVoci->LB_IVA      = EuropeanCurrencyFormat(($Row['IVA']) / 100, false);
                        $DatiVoci->LB_UNITA    = ($Row['DESCRIZIONE_UNITA_DI_MISURA']);
                        $DatiVoci->LB_SCONTO   = EuropeanCurrencyFormat((($Row['SCONTO']) / 100), false); 
                      }

                      array_push($Result->QRSubDetail1,$DatiVoci);
                      
                      $LastSezione   = $Row['CHIAVE_SEZIONE'];
                      $LastSoluzione = $Row['CHIAVE_SOLUZIONE'];
                    }
                  } 
                  else throw new Exception('Impossibile trovare le voci del preventivo');
      

                 $Result->BAND_SUMMARY = array();
                 
                 if(isset($DatiSummary))
                  array_push($Result->BAND_SUMMARY,$DatiSummary);

                 if($NomeLogo != '')
                  $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 


                 array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);



                $SQLBody = "SELECT     preventivi_multiparametrici.CHIAVE,
                                  COUNT(preventivi_multi_sezione.CHIAVE) AS NUMERO_SEZIONI,
                                  COUNT(preventivi_multi_soluzioni.CHIAVE) AS NUMERO_SOLUZIONI
                             FROM preventivi_multi_sezione
                             JOIN preventivi_multi_soluzioni ON preventivi_multi_soluzioni.ID_SEZIONE = preventivi_multi_sezione.CHIAVE
                             JOIN preventivi_multiparametrici ON preventivi_multi_sezione.ID_PREVENTIVO_MULTI = preventivi_multiparametrici.CHIAVE
                             WHERE preventivi_multiparametrici.CHIAVE = $ChiavePreventivo
                             GROUP BY preventivi_multiparametrici.CHIAVE";

                 $NumeroSezioni                        = 0;
                 $NumeroSoluzioni                      = 0;
                 $TotaliPreventivoMultiparametrico     = new stdClass();
                 $TotaliPreventivoMultiparametrico     = TSystemInformation::GetTotaliPreventivoMultiParametrico($PDODBase, $ChiavePreventivo);
                 $Result->BAND_TOTALE                  = array();
                if($Query = $PDODBase->query($SQLBody))
                 {
                   while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                   {
                    $NumeroSezioni               = $Row['NUMERO_SEZIONI'];
                    $NumeroSoluzioni             = $Row['NUMERO_SOLUZIONI'];
                   }
                   if($NumeroSezioni == 1 && $NumeroSoluzioni == 1)
                   {
                     $TotaliPreventivoMultiparametrico = new stdClass();
                     $TotaliPreventivoMultiparametrico = TSystemInformation::GetTotaliPreventivoMultiparametrico($PDODBase,$ChiavePreventivo);
                     $DatiTotali = new TDatiTotali();
          
                     $DatiTotali->LB_TOT_IMPONIBILE = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->SommaImponibile);
                     $DatiTotali->LB_TOT_IMPOSTA = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->SommaIva, false);
                     $DatiTotali->LB_TOTALE = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->Totale);
                     $DatiTotali->LB_PERC_RIT_ACCONTO = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->Ritenuta, false);
                     if($TotaliPreventivoMultiparametrico[0]->Ritenuta != 0)
                        $DatiTotali->LB_IMPONIBILE_RIT_ACCONTO = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->SommaImponibile);
                     else $DatiTotali->LB_IMPONIBILE_RIT_ACCONTO = EuropeanCurrencyFormat(0);
                     $DatiTotali->LB_RIT_ACCONTO = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->TotaleRitenuta);
                     $DatiTotali->LB_NETTO_A_PAGARE = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->NettoAPagare);
                     if($IBAN)
                       $DatiTotali->LB_NS_IBAN = $IBAN;
                     for($i = 0; $i < count($TotaliPreventivoMultiparametrico[0]->LsIva); $i++)
                     {
                        if($i == 0)
                        {
                          $DatiTotali->LB_IMPONIBILE1 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile);
                          $DatiTotali->LB_IVA1 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->IVA, false);
                          $DatiTotali->LB_IMPOSTA1 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile * $TotaliPreventivoMultiparametrico[0]->LsIva[$i]->IVA / 100);
      
                          $DatiTotali->LB_IMPONIBILE2 = '';
                          $DatiTotali->LB_IVA2 = '';
                          $DatiTotali->LB_IMPOSTA2 = '';
      
                          $DatiTotali->LB_IMPONIBILE3 = '';
                          $DatiTotali->LB_IVA3 = '';
                          $DatiTotali->LB_IMPOSTA3 = '';
                        }
                        if($i == 1)
                        {
                          if($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile != 0)
                          {
                            $DatiTotali->LB_IMPONIBILE2 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile);
                            $DatiTotali->LB_IVA2 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->IVA, false);
                            $DatiTotali->LB_IMPOSTA2 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile * $TotaliPreventivoMultiparametrico[0]->LsIva[$i]->IVA / 100);
                          }
                        }
                        if($i == 2)
                        {
                          if($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile != 0)
                          {
                            $DatiTotali->LB_IMPONIBILE3 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile);
                            $DatiTotali->LB_IVA3 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->IVA, false);
                            $DatiTotali->LB_IMPOSTA3 = EuropeanCurrencyFormat($TotaliPreventivoMultiparametrico[0]->LsIva[$i]->SommaImponibile * $TotaliPreventivoMultiparametrico[0]->LsIva[$i]->IVA / 100);
                          }
                        }
                      }
                      array_push($Result->BAND_TOTALE, $DatiTotali);
                     }
                 }
                return $Result;
                    
            }
            private function FGetDatiStampa($ChiaviPreventivi,$PDODBase,&$JSONAnswer,$NomeLogo)
            {
              $Result = array();
              foreach($ChiaviPreventivi as $Chiave)
                  array_push($Result,$this->FGetDatiStampaSingoloPreventivoMultiparametrico($Chiave,$PDODBase,$JSONAnswer,$NomeLogo));
              return $Result;
            }

            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {       
               $Parametri  = json_decode($_POST['Params']);
               $InvioEmail = isset($Parametri->InvioEmail)? $Parametri->InvioEmail : false;
               $ChiaviPreventivi = array();
               if(isset($Parametri->Chiave))
                  array_push($ChiaviPreventivi,$Parametri->Chiave);
               else 
               {
                  $QueryPart = explode('FROM',$this->FGetQueryCompiled($PDODBase,
                                                                       'PreventiviMultiparametrici', 
                                                                       'FiltroPreventivo',
                                                                       'FiltroPreventivo', 
                                                                        get_object_vars($Parametri)));

                  $QueryChiavi = 'SELECT preventivi_multiparametrici.CHIAVE FROM' . $QueryPart[count($QueryPart) - 1];

                  $QueryChiavi = explode('LIMIT', $QueryChiavi)[0];
                  
                  if($Query = $PDODBase->query($QueryChiavi))
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                      array_push($ChiaviPreventivi, $Row['CHIAVE']);
               }   
               

               if(count($ChiaviPreventivi) > 100)
                  throw new Exception('Impossibile gestire più di 100 preventivi');

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

               $Report = new TReportPreventivoMultiparametrico();
               $Report->LoadFromFile('ModelliStampe/QrStampaPreventivoMultiparametrico.json');
               
               if($InvioEmail)
               {
                 $NomeCartella = 'PreventivoMulti_' . gmdate('d_m_Y_H_i_s',time());
                 ForceDirectory($this->FNomeCartellaAttacMail . '/' . $NomeCartella);
                 $NomePreventivo = 'PreventivoMulti_' . gmdate('m_Y',time()) . '.pdf';
                 file_put_contents( $this->FNomeCartellaAttacMail . '/' . $NomeCartella . '/' . $NomePreventivo, 
                                   $Report->GetPDF($this->FGetDatiStampa($ChiaviPreventivi, $PDODBase, $JSONAnswer,$NomeLogo)));

                 $JSONAnswer->Messaggio = $this->FSendMailPreventivoMultiAllegato($Parametri, $NomePreventivo, $NomeCartella, $PDODBase, $JSONAnswer);
                 
                 DeleteFullDir( $this->FNomeCartellaAttacMail . '/' . $NomeCartella, '/' );
               }
               else $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaviPreventivi, $PDODBase, $JSONAnswer,$NomeLogo))); 

               if($NomeLogo != '')
               {
                  if(file_exists(NOME_CARTELLA_FILE_TEMP. '/' . $NomeLogo . '.jpg'))
                  {
                    unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');
                  }
               }
            }

            protected function FSendMailPreventivoMultiAllegato($Parametri, $NomePreventivo, $NomeCartella, $PDODBase, &$JSONAnswer)
            {            
                $Oggetto                  = $Parametri->Oggetto;
                $Destinatario             = $Parametri->Destinatario;
                $Cc                       = $Parametri->Cc;
                $Ccn                      = $Parametri->Ccn;
                $CorpoEmail               = $Parametri->CorpoEmail;
                $AllegatiMail             = array();
                
                $DestinationFolder = $this->FNomeCartellaAttacMail . '/' . $NomeCartella;
               
                if (isset($Parametri->AllegatiMail) && !empty($Parametri->AllegatiMail))
                {
                  $AllegatiMail = $Parametri->AllegatiMail;
                  $InfoPosizione  = array();
                  $Descrizione    = array();

                  foreach ($AllegatiMail as $Allegato)
                  {
                    array_push($InfoPosizione, explode(';',$Allegato->InfoPosizione)[1]);
                    array_push($Descrizione, $Allegato->Dati->Descrizione);
                  }

                  for ($i = 0; $i < count($InfoPosizione); $i++)
                  {
                      $FilePath = FOLDER_BLOB . $InfoPosizione[$i];
                      
                      if (file_exists($FilePath))
                      {
                        $FileContent = file_get_contents($FilePath);
                        
                        $NomeFile = $Descrizione[$i];

                        $EstraiEstensione = explode(".", $InfoPosizione[$i]);
                        $Estensione = end($EstraiEstensione);
                        
                        $NomeCompleto = $NomeFile . '.' . $Estensione;
                        $Conteggio = 1;
                            
                        while (file_exists($DestinationFolder . '/' . $NomeCompleto))
                        {
                          $NomeCompleto = $NomeFile . '_' . $Conteggio . '.' . $Estensione;
                          $Conteggio++;
                        }
                        file_put_contents($DestinationFolder . '/' . $NomeCompleto, $FileContent);
                      } 
                      else error_log("File not found: " . $FilePath);
                  }
                }

                $Errore = '';
                $JSONAnswer->Risposta = 'MAIL_INVIATA';

                if(INVIO_MAIL_ABILITATO)
                {
                  $this->GetParameterEmail($PDODBase, MAIL_ACCOUNT_AVVISI);

                  if(!$this->FSendMail($Destinatario, $Cc, $Ccn , $Oggetto, $this->FGetTestoMail($CorpoEmail),  $this->FSmtpFromName, $this->FSmtpFromMail, $Errore, $NomeCartella, true, $PDODBase))
                  {
                    $JSONAnswer->ErroreMail    = $Errore;
                    $JSONAnswer->Risposta      = 'ERRORE_INVIO_MAIL';
                  }
                  else 
                  {
                    $JSONAnswer->Risposta = 'MAIL_INVIATA';
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

      $AConnection = new TExtraStampaPreventivoMultiparametrico();
      $AConnection->ServerSideScript();