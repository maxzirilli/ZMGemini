<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once PATH_LIBRERIE . 'ZEconomicFunct.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaEstrattoContoCliente
      {
        public $BAND_INTESTAZIONE = array();
        public $BAND_FOOTER = array();
        public $BAND_SUMMARY = array();
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null;
        public $LB_NOME_CAUSALE = null;
        public $QR_MEMO_IBAN    = null;
        public $DESTINATARIO    = null;
        public $QR_LOGO         = null;
      }

      class TDatiFattura
      {
        public $LB_CAUSALE_DOCUMENTO = null;
        public $LB_DATA_DOCUMENTO = null;
        public $LB_NUMERO_DOCUMENTO = null;
        public $LB_IMPORTO_DOCUMENTO = null;
        public $LB_SCADENZA_DOCUMENTO = null;
      }

      class TDatiFooter
      {
        public $LB_IMPORTO_DA_PAGARE_TOTALE = null;
      }

      class TReportEstrattoContoCliente extends TZReport
      {
         private $FReportEnded   = false;
         private $FPrimoPrint    = 0;
         private $FCambioColore  = 0;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'BAND_SUMMARY')
           {
              if($this->FPrimoPrint == 0)
              {
                $this->FPrimoPrint++;
              }
              else $PrintBand = $this->FReportEnded;
           }
           if($ABand->Name == 'BAND_ELENCO_FATTURE')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
           }
         }

         public function AfterAllRepetitionPrinted($ABand)
         {
           if($ABand->Name == 'BAND_SUMMARY')
              $this->FReportEnded = true;
         }
      }

      class TExtraStampaEstrattoContoCliente extends TAdvQuery
      {            
          private function FGetDatiStampa($ChiaveCliente, $PDODBase, &$JSONAnswer, $NomeLogo)
          {
            $Result           = new TStampaEstrattoContoCliente();
            $DatiIntestazione = new TDatiIntestazione();
            $DatiIntestazione ->LB_NOME_CAUSALE = 'ESTRATTO CONTO SOSPESI SCADUTI AL: ' . date('d/m/Y', time());

            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

            $DatiIntestazione->QR_MEMO_IBAN = array();

            $DatiFooter = new TDatiFooter();
            $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE = 0;

            $this->FPrepareParameterValue($ChiaveCliente, ':');

            $SQLBody = "SELECT anagrafiche.*
                          FROM anagrafiche
                         WHERE anagrafiche.CHIAVE = $ChiaveCliente";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {  
                $DatiIntestazione->DESTINATARIO = array( 'CODICE CLIENTE: ' . $Row['CODICE'], 
                                                         $Row['RAGIONE_SOCIALE'], 
                                                         $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'],
                                                         $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . ' ' . $Row['ZONA_FATTURAZIONE'] );
              }
            }


            $TutteLeRighe                     = array();
            $this->FAggiungiDocumentiPassivi($TutteLeRighe, $PDODBase, $ChiaveCliente);


            $SQLBody = "SELECT fatture.*,
                               conti_correnti_casse.IBAN AS IBAN_CONTO,
                               rate_fattura.DATA AS DATA_SCADENZA,
                               rate_fattura.IMPORTO AS IMPORTO_RATA,
                               rate_fattura.DATA_PAGAMENTO AS DATA_PAGAMENTO_RATA,
                               rate_fattura.ID_MOVIMENTO AS ID_MOVIMENTO_RATA
                          FROM fatture
                               LEFT OUTER JOIN conti_correnti_casse ON conti_correnti_casse.CHIAVE = fatture.ID_CONTO_CORRENTE
                               LEFT OUTER JOIN rate_fattura         ON fatture.CHIAVE = rate_fattura.ID_FATTURA
                         WHERE fatture.ID_CLIENTE = $ChiaveCliente
                           AND fatture.NUMERO IS NOT NULL
                           AND fatture.DATA <= CURDATE()
                           AND (SELECT COUNT(*)
                                  FROM rate_fattura
                                 WHERE DATA_PAGAMENTO IS NULL
                                   AND ID_MOVIMENTO IS NULL
                                   AND ID_FATTURA = fatture.CHIAVE) > 0
                         ORDER BY fatture.ID_CLIENTE,fatture.NUMERO, rate_fattura.DATA_PAGAMENTO, rate_fattura.ID_MOVIMENTO";

            $ParagonePerMoltepliciRate = null; 
            $LastChiave = null;

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {  
                if(isset($ParagonePerMoltepliciRate) && $ParagonePerMoltepliciRate['CHIAVE'] == $Row['CHIAVE'])
                  if(isset($Row['ID_MOVIMENTO_RATA']) || isset($Row['DATA_PAGAMENTO_RATA']))
                    $ParagonePerMoltepliciRate['NETTO_A_PAGARE'] -= $Row['IMPORTO_RATA'] / 100;

                if(isset($LastChiave) && $Row['CHIAVE'] != $LastChiave)
                {
                  array_push($TutteLeRighe, $ParagonePerMoltepliciRate);
                  $LastChiave = null;
                  continue;
                }

                if(!isset($LastChiave))
                {
                  $ParagonePerMoltepliciRate = $Row;
                  $TotaleFattura = TSystemInformation::GetTotaliFattura($PDODBase, $Row['CHIAVE'], true);
                  $ParagonePerMoltepliciRate['TOTALE_FATTURA'] = $TotaleFattura[0]->Totale;
                  $ParagonePerMoltepliciRate['NETTO_A_PAGARE'] = $TotaleFattura[0]->NettoAPagare;
                  $LastChiave = $Row['CHIAVE'];
                }
              }
            }  
            else throw new Exception('Impossibile trovare le fatture del cliente');

            if(isset($ParagonePerMoltepliciRate))
              array_push($TutteLeRighe, $ParagonePerMoltepliciRate);


            $SQLBody ="SELECT SUM(rate_note.IMPORTO) / 100 AS IMPORTO,
                              rate_note.ID_NOTA AS CHIAVE,
                              note_di_credito.NUMERO,
                              note_di_credito.DATA,
                              MIN(rate_note.DATA) AS DATA_SCADENZA
                         FROM note_di_credito
                         JOIN rate_note ON (rate_note.ID_NOTA = note_di_credito.CHIAVE)
                        
                        WHERE note_di_credito.ID_CLIENTE = $ChiaveCliente
                          AND note_di_credito.DATA <= CURDATE()
                          AND (rate_note.DATA_PAGAMENTO IS NULL AND rate_note.ID_FATTURA IS NULL)  

                     GROUP BY rate_note.ID_NOTA, note_di_credito.NUMERO, note_di_credito.DATA
                     ORDER BY note_di_credito.ID_CLIENTE";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {    
                $Row['TOTALE_NOTA'] = $Row['IMPORTO'];
                $Row['IS_NOTA_CREDITO'] = 'T';
                array_push($TutteLeRighe,$Row);
              }
            }  
            else throw new Exception('Impossibile trovare le note del cliente');
            
            $SQLBody = "SELECT NUMERO, 
                               DATA, 
                               IMPORTO AS NETTO_A_PAGARE, 
                               ID_CLIENTE, 
                               '-' AS DATA_SCADENZA, 
                               'F' AS DA_BANCO,
                               'T' AS PREGRESSA
                          FROM fatture_insolute_pregresse
                         WHERE fatture_insolute_pregresse.ID_CLIENTE = $ChiaveCliente
                           AND fatture_insolute_pregresse.DATA_PAGAMENTO IS NULL
                           AND fatture_insolute_pregresse.ID_MOVIMENTO IS NULL";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                $Row['NETTO_A_PAGARE'] = $Row['NETTO_A_PAGARE'] / 100;
                array_push($TutteLeRighe, $Row);
              }
            }

            usort($TutteLeRighe, 
                  function($a, $b) 
                  {
                    return strcmp($a['DATA'], $b['DATA']);
                  });
            
            
            
            foreach($TutteLeRighe as $Row)
            {
              $Row['DA_BANCO'] = $Row['DA_BANCO'] ?? 'F';
              $Row['NETTO_A_PAGARE'] = $Row['NETTO_A_PAGARE'] ?? 0;
              $Row['PREGRESSA'] = $Row['PREGRESSA'] ?? 'F';
          
              $DatiFattura = new TDatiFattura();
          
              // Tipo di riga: default a "ATTIVO" se non specificato
              $TipoRiga = $Row['TIPO'] ?? 'ATTIVO';
              $PrefissoDocumento = $Row['PREFISSO'] ?? ($TipoRiga == 'PASSIVO' ? '[PASSIVO]' : '[ATTIVO]');
          
              // Causale
              if(isset($Row['IS_NOTA_CREDITO']) && $Row['IS_NOTA_CREDITO'] == 'T')
              {
                  $DatiFattura->LB_CAUSALE_DOCUMENTO = isset($Row['NUMERO'])
                      ? 'Nota di credito n. ' . $Row['NUMERO']
                      : 'Avviso nota ' . $Row['CHIAVE'];
              }
              else
              {
                  if($TipoRiga == 'PASSIVO')
                  {
                      if($Row['IS_FATTURA'] == 'T')
                          $DatiFattura->LB_CAUSALE_DOCUMENTO = isset($Row['NUMERO'])
                              ? 'Fattura passiva n. ' . $Row['NUMERO']
                              : 'Avviso fattura passiva ' . $Row['CHIAVE'];
                      else
                          $DatiFattura->LB_CAUSALE_DOCUMENTO = isset($Row['NUMERO'])
                              ? 'Nota passiva n. ' . $Row['NUMERO']
                              : 'Avviso nota passiva ' . $Row['CHIAVE'];
                  }
                  else
                  {
                      $DatiFattura->LB_CAUSALE_DOCUMENTO = isset($Row['NUMERO'])
                          ? 'Fattura n. ' . $Row['NUMERO']
                          : 'Avviso fatt.ra ' . $Row['CHIAVE'];
                  }
              }
          
              $DatiFattura->LB_CAUSALE_DOCUMENTO = $PrefissoDocumento . ' ' . $DatiFattura->LB_CAUSALE_DOCUMENTO;
          
              // Data e numero
              $DatiFattura->LB_DATA_DOCUMENTO = date("d/m/Y", strtotime($Row['DATA']));
              $DatiFattura->LB_NUMERO_DOCUMENTO = isset($Row['NUMERO']) ? $Row['NUMERO'] : $Row['CHIAVE'];
          
              // Importo
              if(isset($Row['IS_NOTA_CREDITO']) || (isset($Row['IS_FATTURA']) && $Row['IS_FATTURA'] == 'F'))
              {
                  $DatiFattura->LB_IMPORTO_DOCUMENTO = '- ' . EuropeanCurrencyFormat($Row['NETTO_A_PAGARE']);
                  $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE -= $Row['NETTO_A_PAGARE'];
              }
              else
              {
                  $DatiFattura->LB_IMPORTO_DOCUMENTO = EuropeanCurrencyFormat($Row['NETTO_A_PAGARE']);
                  $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE += $Row['NETTO_A_PAGARE'];
              }
          
              // Scadenza
              if(isset($Row['DATA_SCADENZA']) && $Row['DATA_SCADENZA'] != '-')
                  $DatiFattura->LB_SCADENZA_DOCUMENTO = date("d/m/Y", strtotime($Row['DATA_SCADENZA']));
              else
                  $DatiFattura->LB_SCADENZA_DOCUMENTO = $Row['DATA_SCADENZA'] ?? '-';
          
              array_push($Result->BAND_SUMMARY, $DatiFattura);
            }

            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);


            $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE = EuropeanCurrencyFormat($DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE);
            array_push($Result->BAND_FOOTER, $DatiFooter);

            return $Result;
          }

          private function FAggiungiDocumentiPassivi(&$TutteLeRighe, $PDODBase, $ChiaveCliente)
          {
           // =========================
           // FATTURE PASSIVE E NOTE PASSIVE
           // =========================
           $SQLBody = "
               SELECT 
                   fatture_passive.NUMERO,
                   fatture_passive.DATA,
                   rate_fatture_passive.IMPORTO / 1000 AS IMPORTO_RIGA,
                   rate_fatture_passive.DATA_SCADENZA,
                   rate_fatture_passive.NOTE AS NOTE_RATA,
                   fatture_passive.IS_FATTURA
               FROM fatture_passive
               JOIN rate_fatture_passive
                   ON fatture_passive.CHIAVE = rate_fatture_passive.ID_FATTURA_PASSIVA
               WHERE fatture_passive.ID_FORNITORE = $ChiaveCliente
                 AND rate_fatture_passive.DATA_PAGAMENTO IS NULL
                 AND rate_fatture_passive.ID_MOVIMENTO IS NULL
                 AND rate_fatture_passive.DATA_SCADENZA <= CURDATE()
               ORDER BY fatture_passive.DATA, rate_fatture_passive.DATA_SCADENZA
           ";
       
           if($Query = $PDODBase->query($SQLBody))
           {
               while($Row = $Query->fetch(PDO::FETCH_ASSOC))
               {
                   // Definisce se è fattura o nota
                   if($Row['IS_FATTURA'] == 'T')
                   {
                       $Row['TIPO'] = 'PASSIVO';
                       $Row['NETTO_A_PAGARE'] = $Row['IMPORTO_RIGA'];
                       $Row['PREFISSO'] = '[PASSIVO]';
                       $Row['DATA_SCADENZA'] = $Row['DATA_SCADENZA'];
                   }
                   else // IS_FATTURA = 'F' -> nota passiva
                   {
                       $Row['TIPO'] = 'PASSIVO';
                       $Row['NETTO_A_PAGARE'] = $Row['IMPORTO_RIGA']; // se vuoi mostrarlo negativo: -$Row['IMPORTO_RIGA']
                       $Row['PREFISSO'] = '[NOTA PASSIVA]';
                       $Row['DATA_SCADENZA'] = '-';
                   }
       
                   // Aggiunge la riga all'elenco
                   array_push($TutteLeRighe, $Row);
               }
           }
           else
           {
               throw new Exception('Impossibile trovare fatture o note passive del cliente.');
           }
          }

            
          protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
          { 
            $Parametri = json_decode($_POST['Params']);
            $ChiaveCliente = $Parametri->ChiaveCliente;
               
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

            $Report = new TReportEstrattoContoCliente();
            $Report->LoadFromFile('ModelliStampe/QrStampaEstrattoContoCliente.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaveCliente, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
              unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');            
          }
      }
      $AConnection = new TExtraStampaEstrattoContoCliente();
      $AConnection->ServerSideScript();