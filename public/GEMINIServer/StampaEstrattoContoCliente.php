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

            $SQLBody = "SELECT clienti.*
                          FROM clienti
                         WHERE clienti.CHIAVE = $ChiaveCliente";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {  
                $DatiIntestazione->DESTINATARIO = array( 'CODICE CLIENTE: ' . $Row['CODICE_CLIENTE'], 
                                                         $Row['RAGIONE_SOCIALE'], 
                                                         $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'],
                                                         $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . ' ' . $Row['ZONA_FATTURAZIONE'] );
              }
            }


            $TutteLeRighe                     = array();

            // $SQLBodyMovimenti = "SELECT *
            //                      FROM   movimenti
            //                      WHERE ID_CLIENTE = $ChiaveCliente";
                                 

            // try
            // {
            //   $Query = $PDODBase->query($SQLBodyMovimenti);

            //   while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            //   {
            //     // error_log('Row assoc mov: ' . json_encode($Row));

            //     $MovimentoAssociato['IS_MOVIMENTO_ASSOCIATO'] = true;

            //     $MovimentoAssociato['CHIAVE']       = $Row['CHIAVE'];
            //     $MovimentoAssociato['DATA']         = $Row['DATA'];
            //     $MovimentoAssociato['IMPORTO']      = $Row['IMPORTO'] / 100;
            //     $MovimentoAssociato['DESCRIZIONE']  = $Row['DESCRIZIONE'];
                
            //     if(isset($Row['CONTO_CORRENTE_VERSAMENTO']) && !isset($Row['CONTO_CORRENTE_PRELIEVO']))
            //     {
            //       $MovimentoAssociato['IS_ENTRANTE'] = true;
            //     }
            //     else
            //     {
            //       if(!isset($Row['CONTO_CORRENTE_VERSAMENTO']) && isset($Row['CONTO_CORRENTE_PRELIEVO']))
            //       {
            //         $MovimentoAssociato['IS_ENTRANTE'] = false;
            //       }
            //       else
            //       {
            //         continue; // non pusho la riga nella remota eventualità che sia un giroconto (non dovrebbe accadere, perché non avrebbe senso associare un giroconto a un cliente, però maniman...)
            //       }
            //     }

            //     array_push($TutteLeRighe, $MovimentoAssociato);
            //   }
            // }
            // catch(Exception $e)
            // {
            //   error_log($SQLBodyMovimenti);
            //   throw $e;
            // }


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
              $DatiFattura = new TDatiFattura();

              // if(isset($Row['IS_MOVIMENTO_ASSOCIATO']) && $Row['IS_MOVIMENTO_ASSOCIATO'] == true)
              // {
              //   $DatiFattura->LB_CAUSALE_DOCUMENTO  = $Row['DESCRIZIONE'];
              //   $DatiFattura->LB_DATA_DOCUMENTO     = date("d/m/Y", strtotime($Row['DATA']));
              //   $DatiFattura->LB_NUMERO_DOCUMENTO   = ' - ';
                
              //   if($Row['IS_ENTRANTE'] == true)
              //   {
              //     // Movimenti ENTRANTI diminuiscono il debito che il cliente ha nei nostri confronti
              //     $DatiFattura->LB_IMPORTO_DOCUMENTO        = '- ' . EuropeanCurrencyFormat($Row['IMPORTO']);
              //     $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE -= $Row['IMPORTO'];
              //   }
              //   else 
              //   {
              //     // Movimenti USCENTI aumentano il debito che il cliente ha nei nostri confronti
              //     $DatiFattura->LB_IMPORTO_DOCUMENTO = EuropeanCurrencyFormat($Row['IMPORTO']);
              //     $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE += $Row['IMPORTO'];
              //   }

              //   $DatiFattura->LB_SCADENZA_DOCUMENTO = ' - ';
              // }
              // else
              // {
                if(isset($Row['IS_NOTA_CREDITO']))
                  $DatiFattura->LB_CAUSALE_DOCUMENTO = isset($Row['NUMERO'])? 'Nota di credito n. ' . $Row['NUMERO'] : 'Avviso nota ' . $Row['CHIAVE'];
                else 
                {
                  if($Row['DA_BANCO'] == 'T')
                    $DatiFattura->LB_CAUSALE_DOCUMENTO = isset($Row['NUMERO'])? 'Fatt.ra B' . $Row['NUMERO'] : 'Avviso fatt.ra ' . $Row['CHIAVE'];
                  else 
                  {
                    if(isset($Row['PREGRESSA']))
                      $DatiFattura->LB_CAUSALE_DOCUMENTO = 'Fatt.ra preg. ' . $Row['NUMERO'];
                    else $DatiFattura->LB_CAUSALE_DOCUMENTO = isset($Row['NUMERO'])? 'Fattura n. ' . $Row['NUMERO'] : 'Avviso fatt.ra ' . $Row['CHIAVE'];
                  }
                }

                $DatiFattura->LB_DATA_DOCUMENTO = date("d/m/Y", strtotime($Row['DATA']));
                $DatiFattura->LB_NUMERO_DOCUMENTO = isset($Row['NUMERO'])? $Row['NUMERO'] : $Row['CHIAVE'];

                if(isset($Row['IS_NOTA_CREDITO']))
                  $DatiFattura->LB_IMPORTO_DOCUMENTO = '- ' . EuropeanCurrencyFormat($Row['TOTALE_NOTA']);
                else $DatiFattura->LB_IMPORTO_DOCUMENTO = EuropeanCurrencyFormat($Row['NETTO_A_PAGARE']);

                if(isset($Row['IS_NOTA_CREDITO']))
                  $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE -= $Row['TOTALE_NOTA'];
                else $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE += $Row['NETTO_A_PAGARE'];

                if(isset($Row['IS_NOTA_CREDITO']))
                  $DatiFattura->LB_SCADENZA_DOCUMENTO = date("d/m/Y", strtotime($Row['DATA_SCADENZA']));
                else
                {
                  if ($Row['DATA_SCADENZA'] != '-')
                    $DatiFattura->LB_SCADENZA_DOCUMENTO = date("d/m/Y", strtotime($Row['DATA_SCADENZA']));
                  else
                    $DatiFattura->LB_SCADENZA_DOCUMENTO = $Row['DATA_SCADENZA'];
                }

                // if(isset($Row['ID_CONTO_CORRENTE']))
                //   if(isset($Row['IBAN_CONTO']))
                //   {
                //     $ContoTrovato = false;
                //     for( $i = 0; $i < count($DatiIntestazione->QR_MEMO_IBAN); $i++ )
                //     {
                //       if($DatiIntestazione->QR_MEMO_IBAN[$i] == $Row['IBAN_CONTO'])
                //       {
                //         $ContoTrovato = true;
                //         break;
                //       }
                //     }
                //     if(!$ContoTrovato)
                //     {
                //       array_push($DatiIntestazione->QR_MEMO_IBAN, $Row['IBAN_CONTO']);
                //       $ContoTrovato = false;
                //     }
                //   }

              // }

              array_push($Result->BAND_SUMMARY, $DatiFattura);

            }


            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);


            $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE = EuropeanCurrencyFormat($DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE);
            array_push($Result->BAND_FOOTER, $DatiFooter);

            return $Result;
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