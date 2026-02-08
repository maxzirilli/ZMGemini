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
        public $HEADER_BAND_PRODOTTI = null;
        public $QRSubDetail1 = null;
      }

      class TInfoStyleFattura
      {
        public $FontStyle = null;
      }

      class TDatiVociFattura
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

      class TDatiIntestazione
      {
        public $INTESTATARIO = null;  
        public $DESTINATARIO = null; 
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null;
        public $LB_CONDIZIONI_PAGAMENTO = null;  
        public $LB_BANCA = null;    
        public $LB_NS_IBAN = null;
        public $LB_NUM_DOCUMENTO = null;
        public $LB_DATA_DOCUMENTO = null;
        public $CAUSALE_FATTURA = null;
        public $DestinatarioPresente = false;
        public $QR_LOGO = null;
      }

      class TDatiHeader
      {
        public $QRLabel4 = null;
      }

      class TDatiSummary
      {
        public $LB_NOTA_FATTURA = null;
        public $LB_TOT_IMPONIBILE = null; 
        public $LB_RIF_ORDINE = null;
        public $LB_DATA_ORDINE = null;
        public $QRLabel31      = null; //il "del" della stampa vicino data ordine
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
        public $RATA_IMPONIBILE1 = null;
        public $RATA_DATA1 = null;
        public $RATA_IMPONIBILE2 = null;
        public $RATA_DATA2 = null;
        public $RATA_IMPONIBILE3 = null;
        public $RATA_DATA3 = null;
        public $LB_CIG = null;
        public $LB_CUP = null;
        public $LB_ANTICIPO = null;
        public $QRLabel50   = null;
        public $StampaOrdine = false;

        public $StampaCIG = false;
        public $StampaCUP = false;
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
              $this->FSetVisibilita($ABand,"LB_RIF_ORDINE",!$ASingleRecord->StampaOrdine);
              $this->FSetVisibilita($ABand,"LB_DATA_ORDINE",!$ASingleRecord->StampaOrdine);
              $this->FSetVisibilita($ABand,"QRLabel31",!$ASingleRecord->StampaOrdine);
              $this->FSetVisibilita($ABand,"QRLabel11",!$ASingleRecord->StampaOrdine);

              $this->FSetVisibilita($ABand,"QRLabelCIG",!$ASingleRecord->StampaCIG);
              $this->FSetVisibilita($ABand,"QRLabelCUP",!$ASingleRecord->StampaCUP);

              $PrintBand = $this->FReportEnded;
           }
           if($ABand->Name == 'BAND_INTESTAZIONE')
           {
             $this->FSetVisibilita($ABand,"DESTINATARIO",!$ASingleRecord->DestinatarioPresente);
             $this->FSetVisibilita($ABand,"QRLabel22",!$ASingleRecord->DestinatarioPresente);
             $this->FSetVisibilita($ABand,"QRShape42",!$ASingleRecord->DestinatarioPresente);
           }
           if($ABand->Name == 'BAND_CAUSALE')
              $PrintBand = !is_null($ASingleRecord->CAUSALE_FATTURA) && trim($ASingleRecord->CAUSALE_FATTURA) != '';
         }

         public function AfterAllRepetitionPrinted($ABand)
         {
           if($ABand->Name == 'QRSubDetail1')
              $this->FReportEnded = true;
         }
      }

      class TExtraStampaFattura extends TAdvQueryClassForMail
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
            $this->TipoDocumento              = TIPO_DOCUMENTO_FATTURA;
          }    

          private function FGetDatiStampaSingolaFattura($ChiaveFattura, $PDODBase, &$JSONAnswer, $NomeLogo)
          {
            $Result = new TStampaFattura();
            $Result->BAND_INTESTAZIONE = array();
            $DatiIntestazione = new TDatiIntestazione();

            $Result->BAND_SUMMARY = array();
            $DatiSummary = new TDatiSummary();

            $ChiaveNaturaPagamento = null;
            
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

            $IBAN = '';
            $this->FPrepareParameterValue($ChiaveFattura, ':');
            $StampaRate = true;
            $SQLBody = "SELECT fatture.*, 
                               province.TARGA AS TARGA_PROVINCIA_FATTURAZIONE, 
                               nazioni.SIGLA AS SIGLIA_NAZIONE_EM_PIVA,
                              (SELECT TARGA FROM province WHERE fatture.PROVINCIA_DESTINAZIONE = province.CHIAVE) AS TARGA_PROVINCIA_DESTINAZIONE,
                               cond_pagamento.DESCRIZIONE AS COND_PAGAMENTO_DESCRIZIONE, 
                               cond_pagamento.PAGAMENTO_OBBLIGATORIO, 
                               conti_correnti_casse.BANCA AS BANCA_CONTO_CORRENTE,
                               conti_correnti_casse.IBAN AS IBAN_CONTO_CORRENTE, 
                               causali.DESCRIZIONE AS DESCRIZIONE_CAUSALE,
                               anagrafiche.CODICE
                          FROM fatture
                               LEFT OUTER JOIN province ON province.CHIAVE = fatture.PROVINCIA_FATTURAZIONE
                               LEFT OUTER JOIN nazioni  ON nazioni.CHIAVE = fatture.NAZIONE_EM_PIVA
                               LEFT OUTER JOIN causali  ON causali.CHIAVE = fatture.CAUSALE
                               LEFT OUTER JOIN anagrafiche  ON anagrafiche.CHIAVE = fatture.ID_CLIENTE
                               LEFT OUTER JOIN conti_correnti_casse ON conti_correnti_casse.CHIAVE = fatture.ID_CONTO_CORRENTE
                               LEFT OUTER JOIN cond_pagamento ON cond_pagamento.CHIAVE = fatture.COND_PAGAMENTO
                         WHERE fatture.CHIAVE = $ChiaveFattura";

            try
            {
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {  
                  $DatiIntestazione->INTESTATARIO = array( 'CODICE CLIENTE: ' . $Row['CODICE'], 
                                                          $Row['RAGIONE_SOCIALE'], 
                                                          $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'], 
                                                          $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . (isset($Row['TARGA_PROVINCIA_DESTINAZIONE'])? ' (' . $Row['TARGA_PROVINCIA_FATTURAZIONE'] . ')' : ''),
                                                          $Row['PARTITA_IVA'] != ''?'P.IVA: ' . $Row['PARTITA_IVA'] : '',
                                                          $Row['CODICE_FISCALE'] != ''? 'C.F. ' . $Row['CODICE_FISCALE'] : '');
                  
                  $DatiIntestazione->DestinatarioPresente = (!is_null($Row['INDIRIZZO_DESTINAZIONE']) && trim($Row['INDIRIZZO_DESTINAZIONE']) != '') ||
                                                            (!is_null($Row['DESTINAZIONE']) && trim($Row['DESTINAZIONE']) != '');
          
                  $DatiIntestazione->DESTINATARIO = array($Row['DESTINAZIONE'],
                                                          $Row['INDIRIZZO_DESTINAZIONE'] . ' ' . $Row['NR_CIVICO_DESTINAZIONE'],
                                                          $Row['CAP_DESTINAZIONE'] . ' ' . $Row['COMUNE_DESTINAZIONE'] . (isset($Row['TARGA_PROVINCIA_DESTINAZIONE'])? ' (' . $Row['TARGA_PROVINCIA_DESTINAZIONE'] . ')' : ''));
                    
                  $DatiIntestazione->LB_CONDIZIONI_PAGAMENTO = $Row['COND_PAGAMENTO_DESCRIZIONE'];
                  if($Row['ID_CONTO_CORRENTE'])
                  {
                    $DatiIntestazione->LB_BANCA = $Row['BANCA_CONTO_CORRENTE'];
                    $DatiIntestazione->LB_NS_IBAN = $Row['IBAN_CONTO_CORRENTE'];
                    $IBAN = $Row['IBAN_CONTO_CORRENTE'];
                  }
                  else 
                  {
                    $DatiIntestazione->LB_BANCA = $Row['BANCA'];
                    $DatiIntestazione->LB_NS_IBAN = $Row['IBAN'];
                  }

                  if(isset($Row['NUMERO']))
                  {
                    if($Row['DA_BANCO'] == 'T')
                      $DatiIntestazione->LB_NUM_DOCUMENTO = 'B' . $Row['NUMERO'];
                    else $DatiIntestazione->LB_NUM_DOCUMENTO = $Row['NUMERO'];  
                  }
                  else $DatiIntestazione->LB_NUM_DOCUMENTO = 'Avviso fatt. - ' . $Row['CHIAVE'];

                  $CambioFormatoData = date("d/m/Y", strtotime($Row['DATA']));  
                  $DatiIntestazione->LB_DATA_DOCUMENTO = $CambioFormatoData;
                  $DatiIntestazione->CAUSALE_FATTURA = $Row['DESCRIZIONE_CAUSALE'];
                  $DatiSummary->LB_NOTA_FATTURA = $Row['NOTE_IN_FATTURA'];
                  $DatiSummary->LB_RIF_ORDINE = $Row['DOCUMENTO_NR'];
                  $DatiSummary->LB_CIG = $Row['CIG'];
                  $DatiSummary->LB_CUP = $Row['CUP'];
                  if(isset($Row['DATA_DOCUMENTO']) && $Row['DATA_DOCUMENTO'] != '')
                    $DatiSummary->LB_DATA_ORDINE = date("d/m/Y", strtotime($Row['DATA_DOCUMENTO']));  
                  else 
                  {
                    $DatiSummary->LB_DATA_ORDINE = '';
                    $DatiSummary->QRLabel31      = '';
                  }
                  $DatiSummary->StampaOrdine = $Row['DOCUMENTO_CORRELATO'] != 'N';

                  $DatiSummary->StampaCIG = trim($Row['CIG']) != '';
                  $DatiSummary->StampaCUP = trim($Row['CUP']) != '';

                  if($Row['PAGAMENTO_OBBLIGATORIO'] == 'T')
                    $StampaRate = false;

                  if(isset($Row['NATURA_PAGAMENTO']))
                    $ChiaveNaturaPagamento = $Row['NATURA_PAGAMENTO'];
                }
              }
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw new Exception($e->getMessage());         
            }

            $Result->HEADER_BAND_PRODOTTI = array();
            $DatiHeader = new TDatiHeader();
            $DatiHeader->QRLabel4 = ' DESCRIZIONE';
            array_push($Result->HEADER_BAND_PRODOTTI,$DatiHeader);
            
            $Result->QRSubDetail1 = array();

            $SQLBody = "SELECT voci_fatture.*, 
                               unita_di_misura.DESCRIZIONE AS DESCRIZIONE_UNITA_DI_MISURA
                          FROM voci_fatture
                          LEFT OUTER JOIN unita_di_misura ON unita_di_misura.CHIAVE = voci_fatture.UNITA_DI_MISURA
                         WHERE voci_fatture.ID_FATTURA = $ChiaveFattura
                         ORDER BY voci_fatture.ORDINAMENTO";

            try
            {
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {                     
                  $DatiVoci = new TDatiVociFattura();
                  
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
                    $InfoStyle = new TInfoStyleFattura();      
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
                      $DatiVoci->LB_QUANTITA = number_format(($Row['QUANTITA'])/ 100,2, ',', '.');
                      
                      if($Row['IMPORTO_IVATO'] == 'T')
                        $DatiVoci->LB_PREZZO = number_format(GetImponibileFromIvato($Row['IMPORTO'] / 100, $Row['IVA'] / 100), 2, ',', '.');
                      else $DatiVoci->LB_PREZZO = number_format(($Row['IMPORTO']) / 100, 2, ',', '.');

                      if($Row['IMPORTO_IVATO'] == 'T')
                        $ImportoRiga = GetImponibileFromIvato(($Row['IMPORTO'] * $Row['QUANTITA']) / 10000, $Row['IVA'] / 100);
                      else $ImportoRiga = ($Row['IMPORTO'] * $Row['QUANTITA']) /  10000;

                      if($Row['SCONTO']  != 0)
                        $ImportoRiga *= 1 - $Row['SCONTO'] / 10000;
                      $DatiVoci->LB_IMPORTO = number_format($ImportoRiga,2, ',', '.');
                      $DatiVoci->LB_IVA = number_format(($Row['IVA']) / 100, 2, ',', '.');
                      $DatiVoci->LB_UNITA = ($Row['DESCRIZIONE_UNITA_DI_MISURA']);
                      $DatiVoci->LB_SCONTO = number_format((($Row['SCONTO']) / 100),2, ',', '.'); 
                  }
                  array_push($Result->QRSubDetail1,$DatiVoci);
                }
              } 
              else throw new Exception('Impossibile trovare le voci del Fattura');
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw new Exception($e->getMessage());         
            }
            

            $TotaliFattura = new stdClass();
            $TotaliFattura = TSystemInformation::GetTotaliFattura($PDODBase,$ChiaveFattura);

            $Result->BAND_SUMMARY = array();

            $DatiSummary->LB_TOT_IMPONIBILE = number_format($TotaliFattura[0]->SommaImponibile,2, ',', '.');
            $DatiSummary->LB_TOT_IMPOSTA    = number_format($TotaliFattura[0]->SommaIva,2, ',', '.');
            $DatiSummary->LB_TOTALE         = number_format($TotaliFattura[0]->Totale,2, ',', '.');
            $DatiSummary->LB_ANTICIPO       = number_format($TotaliFattura[0]->NumeroAnticipo,2, ',', '.');

            if($DatiSummary->LB_ANTICIPO == "0,00")
            {
              $DatiSummary->LB_ANTICIPO = "";
              $DatiSummary->QRLabel50   = "";
            }

            $DatiSummary->LB_PERC_RIT_ACCONTO = number_format($TotaliFattura[0]->Ritenuta,2, ',', '.');
            if($TotaliFattura[0]->Ritenuta != 0)
               $DatiSummary->LB_IMPONIBILE_RIT_ACCONTO = number_format($TotaliFattura[0]->SommaImponibile,2, ',', '.');
            else $DatiSummary->LB_IMPONIBILE_RIT_ACCONTO = number_format(0,2, ',', '.');
            $DatiSummary->LB_RIT_ACCONTO = number_format($TotaliFattura[0]->TotaleRitenuta,2, ',', '.');

            if(isset($TotaliFattura[0]->NettoAPagareSenzaAnticipo))
              $DatiSummary->LB_NETTO_A_PAGARE    = number_format($TotaliFattura[0]->NettoAPagareSenzaAnticipo,2, ',', '.');
            else $DatiSummary->LB_NETTO_A_PAGARE = number_format($TotaliFattura[0]->NettoAPagare,2, ',', '.');

            if($IBAN)
             $DatiSummary->LB_NS_IBAN = $IBAN;
            for($i = 0; $i < count($TotaliFattura[0]->LsIva); $i++)
            {
              if($i == 0)
              {
                $DatiSummary->LB_IMPONIBILE1 = number_format($TotaliFattura[0]->LsIva[$i]->SommaImponibile,2, ',', '.');
                if($TotaliFattura[0]->LsIva[$i]->IVA != 0)
                  $DatiSummary->LB_IVA1 = number_format($TotaliFattura[0]->LsIva[$i]->IVA,2, ',', '.');
                else $DatiSummary->LB_IVA1 = TSystemInformation::GetDescrizioneNaturaPagamento($ChiaveNaturaPagamento);
                $DatiSummary->LB_IMPOSTA1 = number_format($TotaliFattura[0]->LsIva[$i]->SommaImponibile * $TotaliFattura[0]->LsIva[$i]->IVA / 100,2, ',', '.');
                              
                $DatiSummary->LB_IMPONIBILE2 = '';
                $DatiSummary->LB_IVA2 = '';
                $DatiSummary->LB_IMPOSTA2 = '';

                $DatiSummary->LB_IMPONIBILE3 = '';
                $DatiSummary->LB_IVA3 = '';
                $DatiSummary->LB_IMPOSTA3 = '';
              }
              if($i == 1)
              {
                if($TotaliFattura[0]->LsIva[$i]->SommaImponibile != 0)
                { 
                  $DatiSummary->LB_IMPONIBILE2 = number_format($TotaliFattura[0]->LsIva[$i]->SommaImponibile,2, ',', '.');
                  if($TotaliFattura[0]->LsIva[$i]->IVA != 0)
                    $DatiSummary->LB_IVA2 = number_format($TotaliFattura[0]->LsIva[$i]->IVA,2, ',', '.');
                  else $DatiSummary->LB_IVA2 = TSystemInformation::GetDescrizioneNaturaPagamento($ChiaveNaturaPagamento);
                  $DatiSummary->LB_IMPOSTA2 = number_format($TotaliFattura[0]->LsIva[$i]->SommaImponibile * $TotaliFattura[0]->LsIva[$i]->IVA / 100,2, ',', '.');
                }
              }
              if($i == 2)
              {
                if($TotaliFattura[0]->LsIva[$i]->SommaImponibile != 0)
                {
                  $DatiSummary->LB_IMPONIBILE3 = number_format($TotaliFattura[0]->LsIva[$i]->SommaImponibile,2, ',', '.');
                  if($TotaliFattura[0]->LsIva[$i]->IVA != 0)
                    $DatiSummary->LB_IVA3 = number_format($TotaliFattura[0]->LsIva[$i]->IVA,2, ',', '.');
                  else $DatiSummary->LB_IVA3 = TSystemInformation::GetDescrizioneNaturaPagamento($ChiaveNaturaPagamento);
                  $DatiSummary->LB_IMPOSTA3 = number_format($TotaliFattura[0]->LsIva[$i]->SommaImponibile * $TotaliFattura[0]->LsIva[$i]->IVA / 100,2, ',', '.');
                }
              }
            }
            array_push($Result->BAND_SUMMARY,$DatiSummary);

           
            $this->FGestioneRate($PDODBase, $ChiaveFattura, $DatiSummary, $StampaRate);


            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

            return $Result;
          }

          private function FGestioneRate($PDODBase, $ChiaveFattura, $DatiSummary, $StampaRate)
          {
            if($StampaRate)
            {
              $SQLBody = "SELECT rate_fattura.*
                            FROM rate_fattura
                          WHERE rate_fattura.ID_FATTURA = $ChiaveFattura";
              
              $j = 0;

              try
              {
                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  { 
                    if($j == 0)
                    {
                      $DatiSummary->RATA_IMPONIBILE1 = number_format($Row['IMPORTO']/100,2, ',', '.');
                      $DatiSummary->RATA_DATA1 = date("d/m/Y", strtotime($Row['DATA']));

                      $DatiSummary->RATA_IMPONIBILE2 = '';
                      $DatiSummary->RATA_DATA2 = '';

                      $DatiSummary->RATA_IMPONIBILE3 = '';
                      $DatiSummary->RATA_DATA3 = '';
                    }
                    if($j == 1)
                    {
                      $DatiSummary->RATA_IMPONIBILE2 = number_format($Row['IMPORTO']/100,2, ',', '.');
                      $DatiSummary->RATA_DATA2 = date("d/m/Y", strtotime($Row['DATA']));
                    }
                    if($j == 2)
                    {
                      $DatiSummary->RATA_IMPONIBILE3 = number_format($Row['IMPORTO']/100,2, ',', '.');
                      $DatiSummary->RATA_DATA3 = date("d/m/Y", strtotime($Row['DATA']));
                    }
                    $j++;
                  }
                }
              }
              catch(Exception $e)
              {
                error_log($SQLBody);
                throw new Exception($e->getMessage());         
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
            
          private function FGetDatiStampa($ChiaviFatture,$PDODBase,&$JSONAnswer,$NomeLogo)
          {
             $Result = array();
             foreach($ChiaviFatture as $Chiave)
                array_push($Result,$this->FGetDatiStampaSingolaFattura($Chiave,$PDODBase,$JSONAnswer,$NomeLogo));
             return $Result;
          }

          protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
          { 
            $Parametri     = $this->Parametri;
            $InvioEmail    = isset($this->InvioEmail)? 
                             $this->InvioEmail : 
                             (isset($Parametri->InvioEmail)? $Parametri->InvioEmail : false);
            $ChiaviFatture = array();
            if(isset($Parametri->Chiave))
            {
              if(is_array($Parametri->Chiave))
                 $ChiaviFatture = $Parametri->Chiave;
              else array_push($ChiaviFatture, $Parametri->Chiave);
            }
            else 
            {
               $QueryPart = explode('FROM fatture',$this->FGetQueryCompiled($PDODBase,
                                                                            'Fatture', 
                                                                            'SelectSQL',
                                                                            'SelectFatture', 
                                                                            get_object_vars($Parametri)));

               $QueryChiavi = 'SELECT fatture.CHIAVE FROM fatture ' . $QueryPart[count($QueryPart) - 1];
               $QueryChiavi = explode('LIMIT', $QueryChiavi)[0];

               try
               {
                if($Query = $PDODBase->query($QueryChiavi))
                   while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                         array_push($ChiaviFatture, $Row['CHIAVE']);
               }
               catch(Exception $e)
               {
                  error_log($QueryChiavi);
                  throw new Exception($e->getMessage());         
               }
            }    
            if(count($ChiaviFatture) > 100)
               throw new Exception('Impossibile gestire più di 100 fatture');
               
            $NomeLogo = '';
            $SQLBody = "SELECT impostazioni.LOGO_AZIENDA
                          FROM impostazioni";
            try
            {
              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  if(isset($Row['LOGO_AZIENDA']))
                  {
                      $NomeLogo = date("Y-m-d_H_i_s_U");
                      ForceDirectory(NOME_CARTELLA_FILE_TEMP);
                      file_put_contents( NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg', base64_decode(explode(',',$Row['LOGO_AZIENDA'])[1]));
                  }
                }
              }
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw new Exception($e->getMessage());         
            }

            $Report = new TReportFattura();
            $Report->LoadFromFile('ModelliStampe/QrStampaFattura.json');
            if($InvioEmail)
            {
              $NomeCartella = 'Fattura_' . gmdate('d_m_Y_H_i_s',time());
              ForceDirectory($this->FNomeCartellaAttacMail . '/' . $NomeCartella);
              $NomeFattura = 'Fattura_' . gmdate('m_Y',time()) . '.pdf';
              file_put_contents( $this->FNomeCartellaAttacMail . '/' . $NomeCartella . '/' . $NomeFattura, 
                                 $Report->GetPDF($this->FGetDatiStampa($ChiaviFatture, $PDODBase, $JSONAnswer,$NomeLogo)));
              $JSONAnswer->Messaggio = $this->FSendMailFatturaAllegata($Parametri, $NomeFattura, $NomeCartella, $PDODBase, $JSONAnswer, $ChiaviFatture);

              DeleteFullDir( $this->FNomeCartellaAttacMail . '/' . $NomeCartella, '/' );
            }
            else $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaviFatture, $PDODBase, $JSONAnswer,$NomeLogo))); 


            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');            
          }

          protected function FSendMailFatturaAllegata($Parametri, $NomeFattura, $NomeCartella, $PDODBase, &$JSONAnswer, $ChiaviFatture)
          { 
              $Oggetto      = $Parametri->Oggetto;
              $Destinatario = $Parametri->Destinatario;
              $Cc           = $Parametri->Cc;
              $Ccn          = $Parametri->Ccn;
              $CorpoEmail   = $Parametri->CorpoEmail;

              if(isset($Parametri->IdCliente))
                $this->IdCliente = $Parametri->IdCliente;

              $Errore = '';
              $JSONAnswer->Risposta = 'MAIL_INVIATA';
              
              if(INVIO_MAIL_ABILITATO)
              {
                if(isset($Parametri->FromSolleciti))
                  $this->GetParameterEmail($PDODBase, MAIL_ACCOUNT_SOLLECITI);
                else $this->GetParameterEmail($PDODBase, MAIL_ACCOUNT_FATTURE);

                if(isset($this->ControlloEmailGiaInviata) && $this->ControlloEmailGiaInviata)
                {
                  $SQLBody = "SELECT INVIATA_TRAMITE_EMAIL
                                FROM fatture
                               WHERE CHIAVE = " . $ChiaviFatture[0];

                  try
                  {
                    if($Query = $PDODBase->query($SQLBody))
                    {
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
                            foreach($ChiaviFatture as $Chiave)
                              $StringaListaChiavi .= $Chiave . ',';

                            if($StringaListaChiavi != '')
                            {
                              $StringaListaChiavi = substr($StringaListaChiavi, 0, -1);

                              $SQLBody = "UPDATE fatture 
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
                  }
                  catch(Exception $e)
                  {
                      error_log($SQLBody);
                      throw new Exception($e->getMessage());         
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
                    foreach($ChiaviFatture as $Chiave)
                      $StringaListaChiavi .= $Chiave . ',';

                    if($StringaListaChiavi != '')
                    {
                      $StringaListaChiavi = substr($StringaListaChiavi, 0, -1);

                      $SQLBody = "UPDATE fatture 
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