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

      const TYP_MOVIMENTI               = 'M';
      const TYP_FATTURE_PASSIVE         = 'P';
      const TYP_RATE_FATTURE_PASSIVE    = 'R';
      const TYP_FATTURE_PREGRESSE       = '1';
      const TYP_APERTURA_ANNO           = 'A';
      
      class TRiga 
      {
        public $Descrizione     = '';
        public $NumeroDocumento = '';
        public $Data            = '1970-01-01';
        public $Importo         = 0;
        public $IsDare          = false; 
        public $Chiave          = -1;
        public $ChiaveSecondaria= -1;
        public $Tipo            = '';

        function __construct($Chiave,$Tipo,$Descrizione, $NumeroDocumento, $Data, $Importo, $IsDare, $ChiaveSecondaria = null)
        {
          $this->Chiave           = $Chiave;
          $this->ChiaveSecondaria = isset($ChiaveSecondaria)? $ChiaveSecondaria : $Chiave; 
          $this->Tipo             = $Tipo;
          $this->Descrizione      = $Descrizione;
          $this->NumeroDocumento  = $NumeroDocumento;
          $this->Data             = $Data;       
          $this->Importo          = $Importo;
          $this->IsDare           = $IsDare;       
        }
      }

      class TStampaContoFornitore
      {
        public $BAND_INTESTAZIONE = array();
        public $BAND_FOOTER       = array();
        public $BAND_SUMMARY      = array();

        public $TotaleSaldoAttualePerSchedaFornitoreStringa = '0€';
        public $TotaleSaldoAttualePerSchedaFornitore        = 0;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null;
        public $LB_NOME_CAUSALE       = null;
        public $DESTINATARIO          = array();
        public $QR_LOGO               = null;
      }

      class TDatiFooter
      {
        public $LB_TOTALE_DARE  = 0;
        public $LB_TOTALE_AVERE = 0;
        public $LB_TOTALE_SALDO = 0;

        public $QRLabel10 = "Totali: ";
      }

      class TDatiDocumento
      {
        public $LB_DESCRIZIONE_DOCUMENTO = null;
        public $LB_DATA_DOCUMENTO        = null;
        public $LB_NUMERO_DOCUMENTO      = null;
        public $LB_AVERE                 = null;
        public $LB_DARE                  = null;
        public $LB_SALDO                 = null;
        public $NonConteggiare           = false;
        public $Tipologia                = null;
        public $ChiaveDocumento          = null;

        function __construct()
        {
          $this->LB_DESCRIZIONE_DOCUMENTO = '';
          $this->LB_DATA_DOCUMENTO        = '';
          $this->LB_NUMERO_DOCUMENTO      = ''; 
          $this->LB_AVERE                 = 0;
          $this->LB_DARE                  = 0;
          $this->LB_SALDO                 = 0;
          $this->Tipologia                = '';
          $this->ChiaveDocumento          = -1;
        }
      }

      class TReportContoFornitore extends TZReport
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
      }

      class TExtraStampaContoFornitore extends TAdvQuery
      {            
          private $Parametri        = null;
          private $FRigheProspetto = array();
          private $DareDiPartenza   = 0;
          private $AvereDiPartenza  = 0;

          public function __construct($Parametri , $ScriptEsterno = false)
          {
            parent::__construct($ScriptEsterno); 

            if(isset($Parametri))
              $this->Parametri = $Parametri;
            else $this->Parametri = json_decode($_POST['Params']);
          }

          private function FAddNewRiga($Chiave,$Tipo,$Descrizione, $NumeroDocumento, $DataChiusura, $Importo, $IsDare, $ChiaveSecondaria = null)
          {
             for($i = 0; $i < count($this->FRigheProspetto); $i++)
                if($this->FRigheProspetto[$i]->Chiave == $Chiave && 
                   $this->FRigheProspetto[$i]->IsDare == $IsDare &&
                   $this->FRigheProspetto[$i]->Tipo == $Tipo)
                  return;
             array_push($this->FRigheProspetto, new TRiga($Chiave,$Tipo,$Descrizione, $NumeroDocumento, $DataChiusura, $Importo, $IsDare, $ChiaveSecondaria));   
          }

          private function FGetTotaleRitenutaRata($Totale, $IdRata)
          {
            foreach ($Totale->Rate as $Rata) 
              if($IdRata == $Rata->IdRata)
                return $Rata->Ritenuta;
          }

          private function GetRitenutaFatturaPassiva($PDODBase, $IdFatturaPassiva)
          {
            $SQLBody = "SELECT ritenute_fatture_passive.IMPORTO
                          FROM ritenute_fatture_passive
                         WHERE ID_FATTURA_PASSIVA = $IdFatturaPassiva";

            if($Query = $PDODBase->query($SQLBody))
            {
              $Row = $Query->fetch(PDO::FETCH_ASSOC);
              return $Row;
            }
          }

          private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
          {

            $ChiaveFornitore  = $Parametri->ChiaveFornitore;
            $DallaData        = $Parametri->DataDal;
            $AllaData         = $Parametri->DataAl;

            $Result = new TStampaContoFornitore();
            $DatiIntestazione = new TDatiIntestazione();
            $DatiIntestazione->LB_NOME_CAUSALE = 'Dal ' . date('d/m/Y', strtotime($DallaData)) . ' al ' . date('d/m/Y', strtotime($AllaData));

            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

            $this->FGestioneChiusureAnnuali($PDODBase, $DallaData, $AllaData, $ChiaveFornitore);

            $this->FGestioneSaldoIniziale($PDODBase, $DallaData, $AllaData, $ChiaveFornitore);

            $DatiFooter = new TDatiFooter();
            $DatiFooter->LB_TOTALE_DARE  = 0;
            $DatiFooter->LB_TOTALE_AVERE = 0;
            $DatiFooter->LB_TOTALE_SALDO = 0;

            $this->FPrepareParameterValue($ChiaveFornitore, ':');

            $SQLBody = "SELECT fornitori.*
                          FROM fornitori
                         WHERE fornitori.CHIAVE = $ChiaveFornitore";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {  
                $DatiIntestazione->DESTINATARIO = array( 'CODICE FORNITORE: ' . $Row['CODICE_FORNITORE'], 
                                                         $Row['RAGIONE_SOCIALE'], 
                                                         $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'],
                                                         $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] );
              }
            }

        
            // MOVIMENTI ASSOCIATI AL FORNITORE
            $SQLBodyMovimenti = "SELECT movimenti.*,
                                        cat_movimenti.DESCRIZIONE AS DESCR_CATEGORIA  
                                   FROM movimenti LEFT OUTER JOIN cat_movimenti ON (cat_movimenti.CHIAVE = movimenti.ID_CATEGORIA_MOVIMENTO)
                                  WHERE ID_FORNITORE = $ChiaveFornitore
                                    AND (movimenti.DATA >= '$DallaData'  AND movimenti.DATA <= '$AllaData' OR movimenti.DATA_CHIUSURA >= '$DallaData' AND movimenti.DATA_CHIUSURA <= '$AllaData')";
             
            try
            {
              $Query = $PDODBase->query($SQLBodyMovimenti);
      
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                $Descrizione      = $Row['DESCRIZIONE'];
                if(!is_null($Row['DESCR_CATEGORIA']))
                {
                   if(trim($Descrizione) != '')
                      $Descrizione .= ' - ';
                   $Descrizione .= $Row['DESCR_CATEGORIA'];
                }
                $IsDare = false;
      
                if(isset($Row['CONTO_CORRENTE_VERSAMENTO']) && !isset($Row['CONTO_CORRENTE_PRELIEVO']))
                  $IsDare = false; 
                else
                {
                  if(!isset($Row['CONTO_CORRENTE_VERSAMENTO']) && isset($Row['CONTO_CORRENTE_PRELIEVO']))
                    $IsDare = true; 
                  else
                    continue; 
                }
                

               if($Row['DATA'] >= $DallaData && $Row['DATA'] <= $AllaData)
                  $this->FAddNewRiga($Row['CHIAVE'],
                                     TYP_MOVIMENTI,
                                     $Descrizione, 
                                     ' - ', 
                                     $Row['DATA'], 
                                     $Row['IMPORTO'] / 100, 
                                     $IsDare);

               if(!is_null($Row['DATA_CHIUSURA']))
                  if($Row['DATA_CHIUSURA'] >= $DallaData && $Row['DATA_CHIUSURA'] <= $AllaData)
                     $this->FAddNewRiga($Row['CHIAVE'],
                                        TYP_MOVIMENTI,
                                        $Descrizione, 
                                        ' - ', 
                                        $Row['DATA_CHIUSURA'], 
                                        $Row['IMPORTO'] / 100, 
                                        !$IsDare);
              }
            }
            catch(Exception $e)
            {
              error_log($SQLBodyMovimenti);
              throw $e;
            }
      
            // FATTURE PASSIVE FORNITORE
            $SQLBody = "SELECT 
                        fatture_passive.CHIAVE				        AS CHIAVE_FATTURA,
                        fatture_passive.NUMERO							    	AS NUMERO_FATTURA,
                        fatture_passive.DATA  						   	 	AS DATA_FATTURA,
                        fatture_passive.OGGETTO					      AS OGGETTO_FATTURA,
                        fatture_passive.IS_FATTURA,
                        fatture_passive.TOTALE_FATTURA    AS TOTALE_FATTURA
                  FROM  fatture_passive 
                 WHERE  fatture_passive.ID_FORNITORE = $ChiaveFornitore 
                   AND  fatture_passive.DATA >= '$DallaData' AND fatture_passive.DATA <= '$AllaData'";
            
            if($Query = $PDODBase->query($SQLBody))
            { 
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                $Descrizione      = $Row['IS_FATTURA'] == 'T' ? "Fattura n. " : "Nota n. ";
                $Descrizione     .= $Row['NUMERO_FATTURA'];
                $this->FAddNewRiga($Row['CHIAVE_FATTURA'],
                                    TYP_FATTURE_PASSIVE,
                                    $Descrizione, 
                                    $Row['NUMERO_FATTURA'], 
                                    $Row['DATA_FATTURA'], 
                                    $Row['TOTALE_FATTURA'] / 100, 
                                    $Row['IS_FATTURA'] == 'F');
              }
            }  
            else throw new Exception('Impossibile trovare le fatture Fornitore...');

            // PAGAMENTI FATTURE PASSIVE PAGATE DIRETTAMENTE
            $SQLBody = "SELECT  fatture_passive.CHIAVE						       		 AS CHIAVE_FATTURA,
                                fatture_passive.NUMERO								        AS NUMERO_FATTURA,
                                fatture_passive.DATA  								        AS DATA_FATTURA,
                                fatture_passive.OGGETTO							        AS OGGETTO_FATTURA,
                                fatture_passive.IS_FATTURA,
                                
                                rate_fatture_passive.CHIAVE           AS CHIAVE_RATA,
                                rate_fatture_passive.DATA_PAGAMENTO   AS DATA_PAGAMENTO_RATA,
                                rate_fatture_passive.IMPORTO / 1000   AS IMPORTO_RATA,
                                rate_fatture_passive.NOTE             AS NOTE_RATA,
                                mod_pagamento.DESCRIZIONE             AS MODALITA_PAGAMENTO
                          FROM  fatture_passive
                                JOIN rate_fatture_passive     ON fatture_passive.CHIAVE      = rate_fatture_passive.ID_FATTURA_PASSIVA
                                JOIN fornitori                ON fornitori.CHIAVE            = fatture_passive.ID_FORNITORE
                                LEFT OUTER JOIN mod_pagamento ON mod_pagamento.CHIAVE        = rate_fatture_passive.ID_MOD_PAGAMENTO
                         WHERE  (rate_fatture_passive.DATA_PAGAMENTO IS NOT NULL OR rate_fatture_passive.ID_MOVIMENTO IS NOT NULL)
                           AND  fatture_passive.ID_FORNITORE = $ChiaveFornitore
                           AND  rate_fatture_passive.ID_MOVIMENTO IS NULL
                           AND  fatture_passive.NUMERO IS NOT NULL
                           AND  rate_fatture_passive.DATA_PAGAMENTO >= '$DallaData' AND rate_fatture_passive.DATA_PAGAMENTO <= '$AllaData'";

            try 
            {
              if($Query = $PDODBase->query($SQLBody))
              { 
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  $FatturaONota     = $Row['IS_FATTURA'] == 'T' ? 'fattura' : 'nota';
                  $Descrizione      = "Rata $FatturaONota n. " . $Row['NUMERO_FATTURA'];
                  if(isset($Row['MODALITA_PAGAMENTO']))
                     $Descrizione .= ' - ' . $Row['MODALITA_PAGAMENTO'];                                                    
                  if(isset($Row['NOTE_RATA']) && trim($Row['NOTE_RATA']) != '')
                     $Descrizione .= ' - ' . $Row['NOTE_RATA'];  

                  $TotaleFatturaPassiva = $this->GetRitenutaFatturaPassiva($PDODBase, $Row['CHIAVE_FATTURA']);

                  $this->FAddNewRiga($Row['CHIAVE_RATA'],
                                    TYP_RATE_FATTURE_PASSIVE,
                                    $Descrizione, 
                                    $Row['NUMERO_FATTURA'], 
                                    $Row['DATA_PAGAMENTO_RATA'],
                                    $Row['IMPORTO_RATA'], 
                                    $Row['IS_FATTURA'] == 'T',
                                    $Row['CHIAVE_FATTURA']);

                  if (is_array($TotaleFatturaPassiva) && $TotaleFatturaPassiva['IMPORTO'] != 0)
                    $this->FAddNewRiga(-$Row['CHIAVE_RATA'],
                                      TYP_RATE_FATTURE_PASSIVE,
                                      'Ritenuta della fattura n. ' . $Row['NUMERO_FATTURA'], 
                                      '', 
                                      $Row['DATA_PAGAMENTO_RATA'],
                                      $TotaleFatturaPassiva['IMPORTO']/ 100,
                                      $Row['IS_FATTURA'] == 'T',
                                      );
                }
              } 
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw $e;
            }

           // MOVIMENTI ASSOCIATI AD UNA FATTURA PASSIVA O FATTURA PREGRESSA DEL FORNITORE
           $SQLBody = "SELECT  movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                               movimenti.DESCRIZIONE                 AS DESCRIZIONE_MOVIMENTO,
                               movimenti.DATA                        AS DATA_MOVIMENTO,
                               movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                          FROM fatture_passive
                               JOIN rate_fatture_passive ON fatture_passive.CHIAVE   = rate_fatture_passive.ID_FATTURA_PASSIVA
                               JOIN movimenti            ON movimenti.CHIAVE = rate_fatture_passive.ID_MOVIMENTO
                         WHERE fatture_passive.ID_FORNITORE = $ChiaveFornitore
                           AND movimenti.DATA >= '$DallaData' AND movimenti.DATA <= '$AllaData'
                     UNION 
                        SELECT movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                               movimenti.DESCRIZIONE                 AS DESCRIZIONE_MOVIMENTO,
                               movimenti.DATA                        AS DATA_MOVIMENTO,
                               movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                          FROM movimenti             
                               JOIN fatture_insolute_pregresse_fornitori ON fatture_insolute_pregresse_fornitori.ID_MOVIMENTO = movimenti.CHIAVE
                         WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveFornitore
                           AND movimenti.DATA >= '$DallaData' AND movimenti.DATA <= '$AllaData'";

           if($Query = $PDODBase->query($SQLBody))
           { 
             while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                 $this->FAddNewRiga($Row['CHIAVE_MOVIMENTO'],TYP_MOVIMENTI,'Movimento di Conto: ' . $Row['DESCRIZIONE_MOVIMENTO'], '', $Row['DATA_MOVIMENTO'], $Row['IMPORTO_MOVIMENTO'], true);
           }  
           else throw new Exception('Impossibile leggere le rate delle fatture attive dal database');

            // FATTURE PREGRESSE DEL FORNITORE
            $SQLBody = "SELECT CHIAVE          AS CHIAVE_FATTURA, 
                               NUMERO          AS NUMERO_FATTURA, 
                               ID_FORNITORE, 
                               IMPORTO / 100   AS IMPORTO_PREGRESSA,
                               DATA            AS DATA_FATTURA
                          FROM fatture_insolute_pregresse_fornitori 
                         WHERE ID_FORNITORE = $ChiaveFornitore
                           AND DATA >= '$DallaData' AND DATA <= '$AllaData'";
            
            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  $this->FAddNewRiga($Row['CHIAVE_FATTURA'],TYP_FATTURE_PREGRESSE,'Fatt. Pregr. n. ' . $Row['NUMERO_FATTURA'], $Row['NUMERO_FATTURA'], $Row['DATA_FATTURA'], $Row['IMPORTO_PREGRESSA'], false);
            }  
            else throw new Exception('Impossibile trovare le fatture insolute pregresse del Fornitore'); 

            // PAGAMENTI DIRETTI FATTURE INSOLUTE PREGRESSE FORNITORI
            $SQLBody = "SELECT fatture_insolute_pregresse_fornitori.CHIAVE          AS CHIAVE_FATTURA, 
                               fatture_insolute_pregresse_fornitori.NUMERO          AS NUMERO_FATTURA, 
                               fatture_insolute_pregresse_fornitori.ID_FORNITORE, 
                               fatture_insolute_pregresse_fornitori.IMPORTO / 100   AS IMPORTO_PREGRESSA, 
                               fatture_insolute_pregresse_fornitori.DATA            AS DATA_FATTURA,
                               fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO
                          FROM fatture_insolute_pregresse_fornitori
                         WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveFornitore
                           AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO >= '$DallaData' AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO <= '$AllaData'";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                 $this->FAddNewRiga($Row['CHIAVE_FATTURA'],TYP_FATTURE_PREGRESSE,"Rata Fatt. Pregr. n. " . $Row['NUMERO_FATTURA'], $Row['NUMERO_FATTURA'], $Row['DATA_PAGAMENTO'], $Row['IMPORTO_PREGRESSA'], true);
            }  
            else throw new Exception('Impossibile trovare i pagamenti delle fatture insolute pregresse del Fornitore');

            // Ordino le righe prima caricarle nel PDF
            usort($this->FRigheProspetto, 
                  function($a, $b) 
                  { 
                    return strcmp($a->Data, $b->Data); 
                  });

          
            // Carico tutte le righe nel PDF
            $LastMovimento  = null;
            $PrimoElemento  = true;
            $ConteggioSaldo = 0;
            
            foreach($this->FRigheProspetto as $Riga)
            {
              $Fattura = new TDatiDocumento();
              
              $Fattura->LB_DESCRIZIONE_DOCUMENTO = $Riga->Descrizione;
              $Fattura->LB_DATA_DOCUMENTO        = $Riga->Data;
              $Fattura->LB_NUMERO_DOCUMENTO      = $Riga->NumeroDocumento;

              $Fattura->Tipologia                = $Riga->Tipo;
              $Fattura->ChiaveDocumento          = $Riga->ChiaveSecondaria;

              if($Riga->Tipo == TYP_APERTURA_ANNO)
              {
                $Fattura->LB_DARE                 = $Riga->Importo;
                $Fattura->LB_AVERE                = '';
                
                if($PrimoElemento)
                {
                  $ConteggioSaldo    += $Riga->Importo;
                  $ConteggioSaldo     = round($ConteggioSaldo, 2); //Arrotondamento
                  $Fattura->LB_SALDO  = $ConteggioSaldo;
                }
                else
                {
                  $Fattura->NonConteggiare        = true;
                }
              }
              else
              {
                if($Riga->IsDare)
                {
                  $Fattura->LB_DARE                  = $Riga->Importo;
                  $Fattura->LB_AVERE                 = '';
                  $DatiFooter->LB_TOTALE_DARE       += $Riga->Importo;
                  $ConteggioSaldo                += $Riga->Importo;
                }
                else
                {
                  $Fattura->LB_DARE                  = '';
                  $Fattura->LB_AVERE                 = $Riga->Importo;
                  $DatiFooter->LB_TOTALE_AVERE      += $Riga->Importo;
                  $ConteggioSaldo                -= $Riga->Importo;
                }

                $ConteggioSaldo                    = round($ConteggioSaldo, 2); //Arrotondamento
                $Fattura->LB_SALDO                 = $ConteggioSaldo;
              }

              $PrimoElemento = false;
              array_push($Result->BAND_SUMMARY, $Fattura);
            }


            // FORMATTAZIONI TIPOGRAFICHE
            $this->FFormattazioneDelleDateETotali($Result->BAND_SUMMARY);

            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

            
            // GESTIONE FOOTER
            $DatiFooterVuoto              = new TDatiFooter();
            $DatiFooterVuoto->QRLabel10       = '';
            $DatiFooterVuoto->LB_TOTALE_DARE  = '';
            $DatiFooterVuoto->LB_TOTALE_AVERE = '';
            $DatiFooterVuoto->LB_TOTALE_DARE  = '';
            $DatiFooterVuoto->LB_TOTALE_AVERE = '';
            $DatiFooterVuoto->LB_TOTALE_SALDO = '';
            array_push($Result->BAND_FOOTER, $DatiFooterVuoto);


            $DatiFooterPeriodoSelezionato = new TDatiFooter();
            $DatiFooterPeriodoSelezionato->QRLabel10       = "Tot.period:";
            $DatiFooterPeriodoSelezionato->LB_TOTALE_DARE  = $DatiFooter->LB_TOTALE_DARE;
            $DatiFooterPeriodoSelezionato->LB_TOTALE_AVERE = $DatiFooter->LB_TOTALE_AVERE;
            $DatiFooterPeriodoSelezionato->LB_TOTALE_DARE  = round($DatiFooter->LB_TOTALE_DARE, 2);
            $DatiFooterPeriodoSelezionato->LB_TOTALE_AVERE = round($DatiFooter->LB_TOTALE_AVERE, 2);

            $DatiFooterPeriodoSelezionato->LB_TOTALE_SALDO = number_format($DatiFooter->LB_TOTALE_DARE - $DatiFooter->LB_TOTALE_AVERE, 2, ',', '.') . '€';
            
            $DatiFooterPeriodoSelezionato->LB_TOTALE_AVERE = number_format($DatiFooter->LB_TOTALE_AVERE, 2, ',', '.') . '€';
            $DatiFooterPeriodoSelezionato->LB_TOTALE_DARE  = number_format($DatiFooter->LB_TOTALE_DARE,  2, ',', '.') . '€';


            $DatiFooter->LB_TOTALE_DARE  = $DatiFooter->LB_TOTALE_DARE  + $this->DareDiPartenza;
            $DatiFooter->LB_TOTALE_AVERE = $DatiFooter->LB_TOTALE_AVERE + $this->AvereDiPartenza;
            $DatiFooter->LB_TOTALE_DARE  = round($DatiFooter->LB_TOTALE_DARE, 2);
            $DatiFooter->LB_TOTALE_AVERE = round($DatiFooter->LB_TOTALE_AVERE, 2);

            $DatiFooter->LB_TOTALE_SALDO = number_format($DatiFooter->LB_TOTALE_DARE - $DatiFooter->LB_TOTALE_AVERE, 2, ',', '.') . '€';
            $Result->TotaleSaldoAttualePerSchedaFornitoreStringa = $DatiFooter->LB_TOTALE_SALDO;
            $Result->TotaleSaldoAttualePerSchedaFornitore        = round($DatiFooter->LB_TOTALE_DARE - $DatiFooter->LB_TOTALE_AVERE);

            $DatiFooter->LB_TOTALE_AVERE = number_format($DatiFooter->LB_TOTALE_AVERE, 2, ',', '.') . '€';
            $DatiFooter->LB_TOTALE_DARE  = number_format($DatiFooter->LB_TOTALE_DARE,  2, ',', '.') . '€';

            array_push($Result->BAND_FOOTER, $DatiFooter);
            array_push($Result->BAND_FOOTER, $DatiFooterPeriodoSelezionato);


            return $Result;
          }

          private function FFormattazioneDelleDateETotali(&$BAND_SUMMARY)
          {
            $ConteggioSaldo = 0;
            if(count($BAND_SUMMARY) != 0)
            {
              foreach ($BAND_SUMMARY as $Fattura) 
              {
                $Fattura->LB_DATA_DOCUMENTO = date("d/m/Y", strtotime($Fattura->LB_DATA_DOCUMENTO));

                if(!$Fattura->NonConteggiare)
                {
                  if($Fattura->LB_AVERE != '')
                    $ConteggioSaldo -= $Fattura->LB_AVERE;
                  else $ConteggioSaldo += $Fattura->LB_DARE;
                }

                $Fattura->LB_AVERE          = $Fattura->LB_AVERE != ''?
                                              number_format($Fattura->LB_AVERE, 2, ',', '.') . '€' :
                                              '';
                $Fattura->LB_DARE           = $Fattura->LB_DARE != ''?
                                              number_format($Fattura->LB_DARE,  2, ',', '.') . '€' :
                                              '';
                
                $Fattura->LB_SALDO          = number_format($ConteggioSaldo,    2, ',', '.') . '€';
              }
            }
          }

          private function FGestioneChiusureAnnuali($PDODBase, $DataDal, $DataAl, $ChiaveFornitore)
          {
            $ArrayPrimoMeseAnniIntervallo = $this->FControlloPrimoAnnoNellIntervallo($DataDal, $DataAl);
            
            if(count($ArrayPrimoMeseAnniIntervallo) != 0)
            {
              $StringaAnni = '';
              foreach($ArrayPrimoMeseAnniIntervallo as $OggettoAnno)
                $StringaAnni .= $OggettoAnno->Anno . ',';
              $StringaAnni = substr_replace($StringaAnni, '', -1);

              //Vado a vedere se sono presenti nel database
              $SQLBody = "SELECT  saldi_chiusure_annuali.*
                            FROM  saldi_chiusure_annuali
                            WHERE saldi_chiusure_annuali.ID_FORNITORE = $ChiaveFornitore
                              AND saldi_chiusure_annuali.ANNO IN ($StringaAnni)";


              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  for($i = 0; $i < count($ArrayPrimoMeseAnniIntervallo); $i++)
                  {
                    if($Row['ANNO'] == $ArrayPrimoMeseAnniIntervallo[$i]->Anno)
                    {
                      $ArrayPrimoMeseAnniIntervallo[$i]->CifraChiusura = $Row['CIFRA_CHIUSURA'] / 100;
                      $ArrayPrimoMeseAnniIntervallo[$i]->CifraChiusuraDare  = $Row['DARE_CHIUSURA'] / 100;
                      $ArrayPrimoMeseAnniIntervallo[$i]->CifraChiusuraAvere = $Row['AVERE_CHIUSURA'] / 100;
                      break;
                    }
                  }
                }
              }

              $PrimoDareAvereImpostato = true;
              //se non presente vado a calcolarlo
              foreach($ArrayPrimoMeseAnniIntervallo as $OggettoAnno)
              {
                if(isset($OggettoAnno->CifraChiusuraDare) && isset($OggettoAnno->CifraChiusuraAvere))
                {
                  $SaldoChiusuraAnnuale = $OggettoAnno->CifraChiusuraDare - $OggettoAnno->CifraChiusuraAvere; 
                  $this->FAddNewRiga('Chiave' . $OggettoAnno->Anno,
                                    TYP_APERTURA_ANNO,
                                    'Apertura conto ' . $OggettoAnno->Anno + 1, 
                                    '', 
                                    $OggettoAnno->Anno + 1 . '-01-01', 
                                    $SaldoChiusuraAnnuale >= 0? $SaldoChiusuraAnnuale : -1 * $SaldoChiusuraAnnuale, 
                                    $SaldoChiusuraAnnuale >= 0? true : false);

                  if($PrimoDareAvereImpostato)
                  {
                    $this->DareDiPartenza    = $OggettoAnno->CifraChiusuraDare;
                    $this->AvereDiPartenza   = $OggettoAnno->CifraChiusuraAvere;
                    $PrimoDareAvereImpostato = false;
                  }
                }
                else 
                {
                  $CifraChiusuraAnnuale = $this->FCalcoloTotaleSaldoFornitore("2000-01-01", $OggettoAnno->Anno . "-12-31", $OggettoAnno->Anno, $ChiaveFornitore, true, $PDODBase);

                  $SaldoChiusuraAnnuale = $CifraChiusuraAnnuale->TotaleDareAnno - $CifraChiusuraAnnuale->TotaleAvereAnno; 
                  
                  $this->FAddNewRiga('Chiave' . $OggettoAnno->Anno,
                                    TYP_APERTURA_ANNO,
                                    'Apertura conto ' . $OggettoAnno->Anno + 1, 
                                    '', 
                                    $OggettoAnno->Anno + 1 . '-01-01', 
                                    $SaldoChiusuraAnnuale >= 0? $SaldoChiusuraAnnuale : -1 * $SaldoChiusuraAnnuale, 
                                    $SaldoChiusuraAnnuale >= 0? true : false);

                  if($PrimoDareAvereImpostato)
                  {
                    $this->DareDiPartenza    = $CifraChiusuraAnnuale->TotaleDareAnno;
                    $this->AvereDiPartenza   = $CifraChiusuraAnnuale->TotaleAvereAnno;
                    $PrimoDareAvereImpostato = false;
                  }
                }
              }
            }    
          }

          private function FControlloPrimoAnnoNellIntervallo($DataDal, $DataAl)
          {
            $Result       = [];
            
            $AnnoIniziale = date('Y', strtotime($DataDal));
            $AnnoFine     = date('Y', strtotime($DataAl));

            $DataDalConfronto = date('Y/m/d', strtotime($DataDal));
            $DataAlConfronto  = date('Y/m/d', strtotime($DataAl));

            for($i = $AnnoIniziale; $i <= $AnnoFine; $i++)
            {
              if($DataDalConfronto <= "$i/01/01" && $DataAlConfronto >= "$i/01/01")
              {
                $Oggetto                = new stdClass();
                $Oggetto->Anno          = $i - 1;
                $Oggetto->CifraChiusuraDare  = null;
                $Oggetto->CifraChiusuraAvere = null;
                array_push($Result, $Oggetto);
              }
            }

            return $Result;
          }

          // cambiato nome funzione, prima veniva utilizzata per il saldo annuale, invece adesso calcola il saldo dalla data, alla data
          private function FCalcoloTotaleSaldoFornitore($DataDalParam, $DataAlParam, $OggettoAnno, $ChiaveFornitore, $SalvareSaldoNellaTabella, $PDODBase)
          {
            $DallaData = $DataDalParam;
            $AllaData  = $DataAlParam;
            
            $this->FPrepareParameterValue($DallaData,'#');
            $this->FPrepareParameterValue($AllaData,'#');
            $this->FPrepareParameterValue($ChiaveFornitore,':');
            $this->FPrepareParameterValue($OggettoAnno, ':');

            $TotaleDareAnno       = 0;
            $TotaleAvereAnno      = 0;
            $MovimentiConteggiati = array();


            // MOVIMENTI ASSOCIATI AL FORNITORE
            $SQLBodyMovimenti = "SELECT movimenti.*,
                                        cat_movimenti.DESCRIZIONE AS DESCR_CATEGORIA  
                                   FROM movimenti LEFT OUTER JOIN cat_movimenti ON (cat_movimenti.CHIAVE = movimenti.ID_CATEGORIA_MOVIMENTO)
                                  WHERE ID_FORNITORE = $ChiaveFornitore
                                    AND (movimenti.DATA >= '$DallaData'  AND movimenti.DATA <= '$AllaData' OR 
                                         movimenti.DATA_CHIUSURA >= '$DallaData' AND movimenti.DATA_CHIUSURA <= '$AllaData')";
             
            try
            {
              $Query = $PDODBase->query($SQLBodyMovimenti);
      
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                array_push($MovimentiConteggiati, $Row['CHIAVE']);
                $IsDare = false;

                if(isset($Row['CONTO_CORRENTE_VERSAMENTO']) && !isset($Row['CONTO_CORRENTE_PRELIEVO']))
                  $IsDare = false; 
                else
                {
                  if(!isset($Row['CONTO_CORRENTE_VERSAMENTO']) && isset($Row['CONTO_CORRENTE_PRELIEVO']))
                    $IsDare = true;
                  else continue; 
                }
                
                if($Row['DATA'] >= $DallaData && $Row['DATA'] <= $AllaData)
                  if($IsDare)
                    $TotaleDareAnno += $Row['IMPORTO'] / 100;
                  else $TotaleAvereAnno += $Row['IMPORTO'] / 100;

                if(!is_null($Row['DATA_CHIUSURA']))
                  if($Row['DATA_CHIUSURA'] >= $DallaData && $Row['DATA_CHIUSURA'] <= $AllaData)
                      if(!$IsDare)
                        $TotaleDareAnno += $Row['IMPORTO'] / 100;
                      else $TotaleAvereAnno += $Row['IMPORTO'] / 100;
              }
            }
            catch(Exception $e)
            {
              error_log($SQLBodyMovimenti);
              throw $e;
            }


            // FATTURE PASSIVE FORNITORE
            $SQLBody = "SELECT 
                        fatture_passive.IS_FATTURA,
                        fatture_passive.TOTALE_FATTURA    AS TOTALE_FATTURA
                  FROM  fatture_passive 
                 WHERE  fatture_passive.ID_FORNITORE = $ChiaveFornitore 
                   AND  fatture_passive.DATA >= '$DallaData' AND fatture_passive.DATA <= '$AllaData'";
            
            if($Query = $PDODBase->query($SQLBody))
            { 
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                if($Row['IS_FATTURA'] == 'F')
                  $TotaleDareAnno += $Row['TOTALE_FATTURA'] / 100;
                else $TotaleAvereAnno += $Row['TOTALE_FATTURA'] / 100;
              }
            }  
            else throw new Exception('Impossibile trovare le fatture Fornitore...');


            // PAGAMENTI FATTURE PASSIVE PAGATE DIRETTAMENTE
            $SQLBody = "SELECT  fatture_passive.CHIAVE						       		 AS CHIAVE_FATTURA,
                                fatture_passive.IS_FATTURA,
                                rate_fatture_passive.IMPORTO / 1000   AS IMPORTO_RATA
                          FROM  fatture_passive
                                JOIN rate_fatture_passive     ON fatture_passive.CHIAVE      = rate_fatture_passive.ID_FATTURA_PASSIVA
                         WHERE  (rate_fatture_passive.DATA_PAGAMENTO IS NOT NULL OR rate_fatture_passive.ID_MOVIMENTO IS NOT NULL)
                           AND  fatture_passive.ID_FORNITORE = $ChiaveFornitore
                           AND  rate_fatture_passive.ID_MOVIMENTO IS NULL
                           AND  fatture_passive.NUMERO IS NOT NULL
                           AND  rate_fatture_passive.DATA_PAGAMENTO >= '$DallaData' AND rate_fatture_passive.DATA_PAGAMENTO <= '$AllaData'";

            try 
            {
              if($Query = $PDODBase->query($SQLBody))
              { 
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  if($Row['IS_FATTURA'] == 'T')
                    $TotaleDareAnno += $Row['IMPORTO_RATA'];
                  else $TotaleAvereAnno += $Row['IMPORTO_RATA'];
                }
              } 
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw $e;
            }

            // AGGIUNTA AL SALDO ANNUALE LA SOMMA DELLE RITENUTE DELLE FATTURE PASSIVE
            $SQLBody = "SELECT (COUNT(rate_fatture_passive.ID_FATTURA_PASSIVA) = COUNT(rate_fatture_passive.DATA_PAGAMENTO)) AS PAGATE_TUTTE,
                               SUM(ritenute_fatture_passive.IMPORTO) AS IMPORTO,
                               fatture_passive.CHIAVE
                          FROM fatture_passive
                               LEFT JOIN rate_fatture_passive ON fatture_passive.CHIAVE = rate_fatture_passive.ID_FATTURA_PASSIVA
                               LEFT JOIN ritenute_fatture_passive ON ritenute_fatture_passive.ID_FATTURA_PASSIVA = fatture_passive.CHIAVE
                         WHERE fatture_passive.ID_FORNITORE = $ChiaveFornitore
                           AND rate_fatture_passive.DATA_SCADENZA >= '$DallaData' 
                           AND rate_fatture_passive.DATA_SCADENZA <= '$AllaData'
                         GROUP BY fatture_passive.CHIAVE";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                if($Row['PAGATE_TUTTE'] == 1)
                 $TotaleDareAnno += $Row['IMPORTO'] / 100;
            }
            else throw new Exception('Impossibile trovare leggere il risultato del confronto del pagamento delle fatture passive pagate');


            // MOVIMENTI ASSOCIATI AD UNA FATTURA PASSIVA O FATTURA PREGRESSA DEL FORNITORE
            $SQLBody = "SELECT  movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                                movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                            FROM fatture_passive
                                JOIN rate_fatture_passive ON fatture_passive.CHIAVE   = rate_fatture_passive.ID_FATTURA_PASSIVA
                                JOIN movimenti            ON movimenti.CHIAVE = rate_fatture_passive.ID_MOVIMENTO
                          WHERE fatture_passive.ID_FORNITORE = $ChiaveFornitore
                            AND movimenti.DATA >= '$DallaData' AND movimenti.DATA <= '$AllaData'
                      UNION 
                          SELECT movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                                movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                            FROM movimenti             
                                JOIN fatture_insolute_pregresse_fornitori ON fatture_insolute_pregresse_fornitori.ID_MOVIMENTO = movimenti.CHIAVE
                          WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveFornitore
                            AND movimenti.DATA >= '$DallaData' AND movimenti.DATA <= '$AllaData'";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                if(!in_array($Row['CHIAVE_MOVIMENTO'], $MovimentiConteggiati))
                  $TotaleDareAnno += $Row['IMPORTO_MOVIMENTO'];
            }
            else throw new Exception('Impossibile leggere le rate delle fatture attive dal database');


            // FATTURE PREGRESSE DEL FORNITORE
            $SQLBody = "SELECT CHIAVE          AS CHIAVE_FATTURA, 
                               IMPORTO / 100   AS IMPORTO_PREGRESSA
                          FROM fatture_insolute_pregresse_fornitori 
                         WHERE ID_FORNITORE = $ChiaveFornitore
                           AND DATA >= '$DallaData' AND DATA <= '$AllaData'";
            
            if($Query = $PDODBase->query($SQLBody))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                $TotaleAvereAnno += $Row['IMPORTO_PREGRESSA'];
            else throw new Exception('Impossibile trovare le fatture insolute pregresse del Fornitore'); 


            // PAGAMENTI DIRETTI FATTURE INSOLUTE PREGRESSE FORNITORI
            $SQLBody = "SELECT fatture_insolute_pregresse_fornitori.CHIAVE          AS CHIAVE_FATTURA, 
                               fatture_insolute_pregresse_fornitori.IMPORTO / 100   AS IMPORTO_PREGRESSA
                          FROM fatture_insolute_pregresse_fornitori
                         WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveFornitore
                           AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO >= '$DallaData' AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO <= '$AllaData'";

            if($Query = $PDODBase->query($SQLBody))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                $TotaleDareAnno += $Row['IMPORTO_PREGRESSA'];
            else throw new Exception('Impossibile trovare i pagamenti delle fatture insolute pregresse del Fornitore');


            if($SalvareSaldoNellaTabella)
            {
              $SQLBody = "INSERT INTO saldi_chiusure_annuali (ID_FORNITORE, ANNO, DARE_CHIUSURA, AVERE_CHIUSURA)
                                                      VALUES ($ChiaveFornitore, $OggettoAnno, $TotaleDareAnno * 100, $TotaleAvereAnno * 100)";
        
              try
              {
                $PDODBase->query($SQLBody);
              }
              catch(Exception $e)
              {
                error_log($SQLBody);
                throw $e;
              }
            }

            $OggettoReturn = new stdClass();
            $OggettoReturn->TotaleDareAnno  = $TotaleDareAnno;
            $OggettoReturn->TotaleAvereAnno = $TotaleAvereAnno;

            return $OggettoReturn;
          }

          private function FGestioneSaldoIniziale($PDODBase, $DataDal, $DataAl, $ChiaveFornitore)
          {
            $DataDalDateTime = new DateTime($DataDal);

            if ($DataDalDateTime->format('d-m') == '01-01') 
              return;

            $Anno      = $DataDalDateTime->format('Y');
            $PrimoAnno = "$Anno-01-01";

            $CifraDare  = 0;
            $CifraAvere = 0;
            $OggettoReturn = null;

            $SQLBody = "SELECT saldi_chiusure_annuali.*
                          FROM saldi_chiusure_annuali
                        WHERE saldi_chiusure_annuali.ID_CLIENTE = $ChiaveFornitore
                          AND saldi_chiusure_annuali.ANNO = " . $Anno - 1;

            $Trovato = false;

            if($Query = $PDODBase->query($SQLBody))
              if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                if(isset($Row['DARE_CHIUSURA']) && isset($Row['AVERE_CHIUSURA']))
                {
                  $CifraDare  = $Row['DARE_CHIUSURA'] / 100;
                  $CifraAvere = $Row['AVERE_CHIUSURA'] / 100;
                  $Trovato    = true;
                }

            if(!$Trovato)
            {
              $OggettoReturn = $this->FCalcoloTotaleSaldoFornitore("2000-01-01", 
                                                                  $PrimoAnno, 
                                                                  $Anno - 1, 
                                                                  $ChiaveFornitore, 
                                                                  true, 
                                                                  $PDODBase);
              $CifraDare  = $OggettoReturn->TotaleDareAnno;
              $CifraAvere = $OggettoReturn->TotaleAvereAnno;
            }

            $OggettoReturn = $this->FCalcoloTotaleSaldoFornitore($PrimoAnno, 
                                                                  $DataDal, 
                                                                  $Anno - 1, 
                                                                  $ChiaveFornitore, 
                                                                  false, 
                                                                  $PDODBase);
            
            $CifraDare  = $CifraDare  + $OggettoReturn->TotaleDareAnno;
            $CifraAvere = $CifraAvere + $OggettoReturn->TotaleAvereAnno;

            $this->DareDiPartenza  = round($CifraDare, 2);
            $this->AvereDiPartenza = round($CifraAvere, 2);
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
          
            if(isset($Parametri->StatoConti) && $Parametri->StatoConti == true )
            {
              $JSONAnswer->LsConti = $this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo);
            }
            else
            {
              $Report = new TReportContoFornitore();
              $Report->LoadFromFile('ModelliStampe/QrStampaContoFornitore.json');
              $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 
              if($NomeLogo != '')
                unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');
            }
          }
      }