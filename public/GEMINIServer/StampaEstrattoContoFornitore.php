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

      class TStampaEstrattoContoFornitore
      {
        public $BAND_INTESTAZIONE = array();
        public $BAND_FOOTER = array();
        public $BAND_SUMMARY = array();
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null;
        public $LB_NOME_CAUSALE       = null;
        public $QR_MEMO_IBAN          = null;
        public $DESTINATARIO          = null;
        public $QR_LOGO               = null;
      }

      class TDatiFattura
      {
        public $LB_CAUSALE_DOCUMENTO  = null;
        public $LB_DATA_DOCUMENTO     = null;
        public $LB_NUMERO_DOCUMENTO   = null;
        public $LB_IMPORTO_DOCUMENTO  = null;
        public $LB_SCADENZA_DOCUMENTO = null;
        public $MM_DOCUMENTO          = null;
      }

      class TDatiFooter
      {
        public $LB_IMPORTO_DA_PAGARE_TOTALE = null;
      }

      class TReportEstrattoContoFornitore extends TZReport
      {
         private $FReportEnded   = false;
         private $FPrimoPrint    = 0;
         private $FCambioColore  = false;

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

      class TExtraStampaEstrattoContoFornitore extends TAdvQuery
      {            
          private function FGetDatiStampa($ChiaveFornitore, $PDODBase, &$JSONAnswer, $NomeLogo)
          {
            $Result           = new TStampaEstrattoContoFornitore();
            $DatiIntestazione = new TDatiIntestazione();
            $DatiIntestazione ->LB_NOME_CAUSALE = 'ESTRATTO CONTO SOSPESI SCADUTI AL: ' . date('d/m/Y', time());

            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

            $DatiIntestazione->QR_MEMO_IBAN = array();

            $DatiFooter = new TDatiFooter();
            $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE = 0;

            $this->FPrepareParameterValue($ChiaveFornitore, ':');

            $SQLBody = "SELECT fornitori.*
                          FROM fornitori
                         WHERE fornitori.CHIAVE = $ChiaveFornitore";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {  
                $DatiIntestazione->DESTINATARIO = array( 'Spett.le ',
                                                         $Row['RAGIONE_SOCIALE'], 
                                                         $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'],
                                                         $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE']  );
              }
            }


            $TutteLeRighe = array();

            // movimenti associati al fornitore (ma non a un documento...)

            // $SQLBodyMovimenti = "SELECT *
            //           FROM   movimenti
            //           WHERE ID_FORNITORE = $ChiaveFornitore";
                                 
            // // error_log('StampaEstrattoContoFornitore.php - $SQLBodyMovimenti: ' .  $SQLBodyMovimenti);
            
            // try
            // {
            //   $Query = $PDODBase->query($SQLBodyMovimenti);

            //   while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            //   {
            //     // error_log('Row assoc mov: ' . json_encode($Row));

            //     $MovimentoAssociato['IS_MOVIMENTO_ASSOCIATO'] = true;

            //     $MovimentoAssociato['CHIAVE']       = $Row['CHIAVE'];
            //     $MovimentoAssociato['DATA_FATTURA'] = $Row['DATA'];
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



            // fatture passive
            
            $SQLBody = "SELECT fatture_passive.NUMERO                       AS NUMERO_FATTURA,
                               fatture_passive.DATA                         AS DATA_FATTURA,
                               fatture_passive.OGGETTO                      AS OGGETTO_FATTURA,
                               fatture_passive.IS_FATTURA                   AS IS_FATTURA,
                               'F'                                          AS IS_PREGRESSA,
                               rate_fatture_passive.DATA_SCADENZA           AS DATA_SCADENZA,
                               rate_fatture_passive.IMPORTO / 1000          AS IMPORTO_RIGA,
                               rate_fatture_passive.NOTE                    AS NOTE_RATA
                          FROM fatture_passive
                               INNER JOIN rate_fatture_passive ON fatture_passive.CHIAVE = rate_fatture_passive.ID_FATTURA_PASSIVA
                         WHERE fatture_passive.ID_FORNITORE = $ChiaveFornitore
                           AND rate_fatture_passive.DATA_PAGAMENTO IS NULL
                           AND rate_fatture_passive.ID_MOVIMENTO   IS NULL
                           AND rate_fatture_passive.DATA_SCADENZA <= CURDATE()";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                array_push($TutteLeRighe,$Row);
              }
            }  
            else throw new Exception('Impossibile trovare le fatture del fornitore');
            
            $SQLBody = "SELECT NUMERO                         AS NUMERO_FATTURA, 
                               DATA                           AS DATA_FATTURA,
                       CONCAT('FATT.PASS.PREGR.',' ', NOTE)   AS OGGETTO_FATTURA, 
                               'T'                            AS IS_FATTURA,
                               'T'                            AS IS_PREGRESSA,
                               '-'                            AS DATA_SCADENZA, 
                               IMPORTO / 100                  AS IMPORTO_RIGA 
                          FROM fatture_insolute_pregresse_fornitori
                         WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveFornitore
                           AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO IS NULL
                           AND fatture_insolute_pregresse_fornitori.ID_MOVIMENTO   IS NULL";
              
            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                array_push($TutteLeRighe, $Row);
              }
            }

            usort($TutteLeRighe, 
                  function($a, $b) 
                  {
                    return strcmp($a['DATA_FATTURA'], $b['DATA_FATTURA']);
                  });

            foreach($TutteLeRighe as $Row)
            {
              $DatiFattura = new TDatiFattura();
              
              // if(isset($Row['IS_MOVIMENTO_ASSOCIATO']) && $Row['IS_MOVIMENTO_ASSOCIATO'] == true)
              // {
              //   $DatiFattura->MM_DOCUMENTO          = $Row['DESCRIZIONE'];
              //   $DatiFattura->LB_NUMERO_DOCUMENTO   = ' - ';
              //   $DatiFattura->LB_SCADENZA_DOCUMENTO = ' - ';
              //   $DatiFattura->LB_DATA_DOCUMENTO     = date("d/m/Y", strtotime($Row['DATA_FATTURA']));

              //   if($Row['IS_ENTRANTE'] == false)
              //   {
              //     // Movimenti USCENTI diminuiscono il mio debito nei confronti del fornitore
              //     $DatiFattura->LB_IMPORTO_DOCUMENTO        = '- ' . EuropeanCurrencyFormat($Row['IMPORTO']);
              //     $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE -= $Row['IMPORTO'];
              //   }
              //   else 
              //   {
              //     // Movimenti ENTRANTI aumentano il mio debito nei confronti del fornitore
              //     $DatiFattura->LB_IMPORTO_DOCUMENTO = EuropeanCurrencyFormat($Row['IMPORTO']);
              //     $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE += $Row['IMPORTO'];
              //   }

              // }
              // else
              // {
                $DatiFattura->MM_DOCUMENTO          =   ($Row['IS_FATTURA'] == 'T' ? 'FATTURA N. ' : 'NOTA DI CREDITO N. ')
                                                      .  $Row['NUMERO_FATTURA'] . ($Row['IS_PREGRESSA'] == 'T' ? ' (PREGR.)' : ''); 
                                                      
                if(isset($Row['NOTE_RATA']) && trim($Row['NOTE_RATA']) != '')
                  $DatiFattura->MM_DOCUMENTO       .= ' - ' . $Row['NOTE_RATA'];

                $DatiFattura->LB_NUMERO_DOCUMENTO   = $Row['NUMERO_FATTURA'];
                $DatiFattura->LB_DATA_DOCUMENTO     = date("d/m/Y", strtotime($Row['DATA_FATTURA']));
                $DatiFattura->LB_SCADENZA_DOCUMENTO = $Row['DATA_SCADENZA'] == '-' ? '-' : date("d/m/Y", strtotime($Row['DATA_SCADENZA']));
                
                if($Row['IS_FATTURA'] == 'T') 
                {
                  $DatiFattura->LB_IMPORTO_DOCUMENTO = number_format($Row['IMPORTO_RIGA'], 2, ',', '.') . '€';
                  $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE += $Row['IMPORTO_RIGA'];
                }
                else 
                {
                  $DatiFattura->LB_IMPORTO_DOCUMENTO = '- ' . number_format($Row['IMPORTO_RIGA'], 2, ',', '.') . '€';
                  $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE -= $Row['IMPORTO_RIGA'];
                }
              
              // }

              array_push($Result->BAND_SUMMARY, $DatiFattura);
            }


            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);


            $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE = number_format($DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE, 2, ',', '.') . '€';
            array_push($Result->BAND_FOOTER, $DatiFooter);

            return $Result;
          }
            
          protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
          { 
            $Parametri = json_decode($_POST['Params']);
            $ChiaveFornitore = $Parametri->ChiaveFornitore;
            
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

            $Report = new TReportEstrattoContoFornitore();
            $Report->LoadFromFile('ModelliStampe/QrStampaEstrattoContoFornitore.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaveFornitore, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
              unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');            
          }
      }
      $AConnection = new TExtraStampaEstrattoContoFornitore();
      $AConnection->ServerSideScript();