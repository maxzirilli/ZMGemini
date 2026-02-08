<?php       
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';
  include_once PATH_LIBRERIE . 'ZGenericFunct.php';
  include_once PATH_LIBRERIE . 'ZReport.php';

  header("Content-Type: application/json;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

  // Generica riga (in dare o in avere) del conto Cliente

  const TYP_MOVIMENTI         = 'M';
  const TYP_FATTURE_ATTIVE    = 'F';
  const TYP_NOTE_DI_CREDITO   = 'N';
  const TYP_FATTURE_PASSIVE         = 'P';
  const TYP_RATE_FATTURE_PASSIVE    = 'RF';
  const TYP_NOTE_DI_CREDITO_SPLIT = 'SN';
  const TYP_RATA_FATTURA      = 'R';
  const TYP_FATTURE_PREGRESSE = '1';
  const TYP_APERTURA_ANNO     = 'A';

  class TRiga 
  {
    public $Chiave          = -1;
    public $ChiaveSecondaria= -1;
    public $Descrizione     = '';
    public $NumeroDocumento = '';
    public $Data            = '1970-01-01';
    public $Importo         = 0;
    public $Tipo            = '';
    public $IsDare          = false; // se true, la riga va registrata in DARE; se false, va registrata in AVERE.

    function __construct($Chiave,$Tipo,$Descrizione, $NumeroDocumento, $Data, $Importo, $IsDare, $ChiaveSecondaria = null)
    {
      $this->Chiave           = $Chiave; 
      $this->ChiaveSecondaria = isset($ChiaveSecondaria)? $ChiaveSecondaria : $Chiave; 
      $this->Descrizione      = $Descrizione;
      $this->NumeroDocumento  = $NumeroDocumento;
      $this->Data             = $Data;       
      $this->Importo          = $Importo;
      $this->IsDare           = $IsDare;       
      $this->Tipo             = $Tipo;
    }
  }

  class TStampaConto
  {
    public $BAND_INTESTAZIONE = array();
    public $BAND_FOOTER       = array();
    public $BAND_SUMMARY      = array();

    public $TotaleSaldoAttualePerSchedaClienteStringa = '0€';
    public $TotaleSaldoAttualePerSchedaCliente        = 0;
  }

  class TDatiIntestazione
  {
    public $DENOMINAZIONE_SOCIETA       = null;
    public $INTESTAZIONE_SOCIETA        = null;
    public $LB_NOME_CAUSALE             = null;
    public $DESTINATARIO                = array();
    public $MM_CONTATTI_DESTINATARIO    = array();
    public $MM_AMMINISTRATORE           = array();
    public $MM_CONTATTI_AMMINISTRATORE  = array();
    public $AmministratorePresente      = false;
    public $QR_LOGO                     = null;
  }

  class TDatiFooter
  {
    public $LB_TOTALE_DARE  = 0;
    public $LB_TOTALE_AVERE = 0;
    public $LB_TOTALE_SALDO = 0;
    public $TOTALE_SALDO    = 0;
    public $QRLabel10       = "Saldo attuale: ";
  }

  class TDatiDocumento
  {
    public $MM_DESCRIZIONE_DOCUMENTO = null;
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
      $this->MM_DESCRIZIONE_DOCUMENTO = '';
      $this->LB_DATA_DOCUMENTO        = '';
      $this->LB_NUMERO_DOCUMENTO      = ''; 
      $this->LB_AVERE                 = 0;
      $this->LB_DARE                  = 0;
      $this->LB_SALDO                 = 0;
      $this->Tipologia                = '';
      $this->ChiaveDocumento          = -1;
    }
  }

  class TReportContoCliente extends TZReport
  {
      private $FReportEnded           = false;
      private $FPrimoPrint            = 0;
      private $FCambioColore          = false;
      private $AmministratorePresente = false;

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
        if($ABand->Name == 'BAND_INTESTAZIONE')
        {
          $this->FSetVisibilita($ABand,"LB_AMM", !$ASingleRecord->AmministratorePresente);
        }
      }
  }

  class TExtraStampaConto extends TAdvQuery
  {
    private $Parametri            = null;
    private $FRigheProspetto      = array();
    private $DareDiPartenza       = 0;
    private $AvereDiPartenza      = 0;
    private $SaldoContabileCliente = 0;
    public $TotaleDareCliente                         = 0;
    public $TotaleAvereCliente                        = 0;

    public function __construct($Parametri, $ScriptEsterno = false)
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
      $ChiaveCliente  = $Parametri->ChiaveCliente;
      $DataDal        = $Parametri->DataDal;
      $DataAl         = $Parametri->DataAl;
      $VelocizzaXStampaSaldiClienti = isset($Parametri->VelocizzaXStampaSaldiClienti)? $Parametri->VelocizzaXStampaSaldiClienti : false;

      $Result = new TStampaConto();
      $DatiIntestazione = new TDatiIntestazione();
      $DatiIntestazione->LB_NOME_CAUSALE = 'Dal ' . date('d/m/Y', strtotime($DataDal)) . ' al ' . date('d/m/Y', strtotime($DataAl));

      if(!$VelocizzaXStampaSaldiClienti)
        TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

      $this->FGestioneChiusureAnnuali($PDODBase, $DataDal, $DataAl, $ChiaveCliente, $VelocizzaXStampaSaldiClienti);

      if(!$VelocizzaXStampaSaldiClienti)
        $this->FGestioneSaldoIniziale($PDODBase, $DataDal, $DataAl, $ChiaveCliente);

      $this->FCalcolaSaldoContabileCliente($PDODBase, $ChiaveCliente);

      $DatiFooter = new TDatiFooter();
      $DatiFooter->LB_TOTALE_DARE  = 0;
      $DatiFooter->LB_TOTALE_AVERE = 0;
      $DatiFooter->LB_TOTALE_SALDO = 0;

      $this->FPrepareParameterValue($ChiaveCliente, ':');
      $this->FPrepareParameterValue($DataDal,'#');
      $this->FPrepareParameterValue($DataAl,'#');

      if(!$VelocizzaXStampaSaldiClienti)
      {
      
        $SQLBody = "SELECT anagrafiche.*
                      FROM anagrafiche
                     WHERE anagrafiche.CHIAVE = $ChiaveCliente";

        if($Query = $PDODBase->query($SQLBody))
        {
          while($Row = $Query->fetch(PDO::FETCH_ASSOC))
          {  
            $DatiIntestazione->DESTINATARIO = array( 'CODICE CLIENTE: ' . $Row['CODICE'], 
                                                      $Row['RAGIONE_SOCIALE'], 
                                                      $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'] . ' ' .
                                                      $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] );
          }
        }

        $SQLBody = "SELECT  clienti_telefono.EMAIL,
                            clienti_telefono.TELEFONO,
                            clienti_telefono.DESCRIZIONE
                    FROM   		anagrafiche
                    LEFT JOIN clienti_telefono ON clienti_telefono.ID_CLIENTE = anagrafiche.CHIAVE
                    WHERE anagrafiche.CHIAVE = $ChiaveCliente";

        if($Query = $PDODBase->query($SQLBody))
        {
          while($Row = $Query->fetch(PDO::FETCH_ASSOC))
          {  
            $RigaContattoDestinatario = $Row['EMAIL'] . ' ' . $Row['TELEFONO'] . ' ' . $Row['DESCRIZIONE'];

            array_push($DatiIntestazione->MM_CONTATTI_DESTINATARIO, $RigaContattoDestinatario);
          }
        }
      }

      if($VelocizzaXStampaSaldiClienti)
      {
        $DataAlDateTime = new DateTime($DataAl);

        $DataDal = $DataAlDateTime->format('y') . '-01-01';
      }

      // MOVIMENTI ASSOCIATI AL CLIENTE
      $SQLBodyMovimenti = "SELECT movimenti.*,
                                  cat_movimenti.DESCRIZIONE AS DESCR_CATEGORIA
                             FROM movimenti LEFT OUTER JOIN cat_movimenti ON (cat_movimenti.CHIAVE = movimenti.ID_CATEGORIA_MOVIMENTO)
                            WHERE ID_ANAGRAFICA = $ChiaveCliente
                              AND (movimenti.DATA >= '$DataDal'  AND movimenti.DATA <= '$DataAl' OR 
                                   movimenti.DATA_CHIUSURA >= '$DataDal' AND movimenti.DATA_CHIUSURA <= '$DataAl')";
       
      try
      {
        // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***

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
            else continue; 
          }
          
          if($Row['DATA'] >= $DataDal && $Row['DATA'] <= $DataAl)
             $this->FAddNewRiga($Row['CHIAVE'],
                                TYP_MOVIMENTI,
                                $Descrizione,
                                ' - ',
                                $Row['DATA'],
                                $Row['IMPORTO'] / 100,
                                $IsDare);

          if(!is_null($Row['DATA_CHIUSURA']))
             if($Row['DATA_CHIUSURA'] >= $DataDal && $Row['DATA_CHIUSURA'] <= $DataAl)
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


      // FATTURE ATTIVE DEL CLIENTE
      $SQLBody = "SELECT fatture.CHIAVE		AS CHIAVE_FATTURA,
                         fatture.NUMERO		AS NUMERO_FATTURA,
                         fatture.DATA   	AS DATA_FATTURA,
                         fatture.NOTE_IN_FATTURA,
                         fatture.ESIGIBILITA_IVA,
                         fatture.NUMERO_ANTICIPO
                    FROM fatture 
                   WHERE fatture.ID_CLIENTE = $ChiaveCliente 
                     AND fatture.NUMERO IS NOT NULL
                     AND fatture.DATA >= '$DataDal' AND fatture.DATA <= '$DataAl'";
      
      if($Query = $PDODBase->query($SQLBody))
      { 
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***
         
          $Descrizione      = 'Fattura n. ' . $Row['NUMERO_FATTURA'];

          if($Row['NOTE_IN_FATTURA'] != null && trim($Row['NOTE_IN_FATTURA']) != '')
            $Descrizione .= ' - ' . $Row['NOTE_IN_FATTURA'];
          
          $TotaliFattura    = TSystemInformation::GetTotaliFattura($PDODBase, $Row['CHIAVE_FATTURA']);

          $this->FAddNewRiga($Row['CHIAVE_FATTURA'],
                              TYP_FATTURE_ATTIVE,
                              $Descrizione, 
                              $Row['NUMERO_FATTURA'], 
                              $Row['DATA_FATTURA'], 
                              $TotaliFattura[0]->Totale, 
                              true);
                             
          if($Row['ESIGIBILITA_IVA'] == ESIGIBILITA_SPLIT_PAYMENT)
            $this->FAddNewRiga(-$Row['CHIAVE_FATTURA'],
                               TYP_FATTURE_ATTIVE,
                               "Split payment fattura n. " . $Row['NUMERO_FATTURA'], 
                               '', 
                               $Row['DATA_FATTURA'], 
                               $TotaliFattura[0]->SommaIva, 
                               false);

        }
      }  
      else throw new Exception('Impossibile leggere le fatture attive dal database...');


       // FATTURE PASSIVE FORNITORE
            $SQLBody = "SELECT 
                        fatture_passive.CHIAVE				        AS CHIAVE_FATTURA,
                        fatture_passive.NUMERO							    	AS NUMERO_FATTURA,
                        fatture_passive.DATA  						   	 	AS DATA_FATTURA,
                        fatture_passive.OGGETTO					      AS OGGETTO_FATTURA,
                        fatture_passive.IS_FATTURA,
                        fatture_passive.TOTALE_FATTURA    AS TOTALE_FATTURA
                  FROM  fatture_passive 
                 WHERE  fatture_passive.ID_FORNITORE = $ChiaveCliente 
                   AND  fatture_passive.DATA >= '$DataDal' AND fatture_passive.DATA <= '$DataAl'";
            
            if($Query = $PDODBase->query($SQLBody))
            { 
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                $Descrizione      = $Row['IS_FATTURA'] == 'T' ? "Fattura passiva n. " : "Nota passiva n. ";
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
                                JOIN anagrafiche                ON anagrafiche.CHIAVE            = fatture_passive.ID_FORNITORE
                                LEFT OUTER JOIN mod_pagamento ON mod_pagamento.CHIAVE        = rate_fatture_passive.ID_MOD_PAGAMENTO
                         WHERE  (rate_fatture_passive.DATA_PAGAMENTO IS NOT NULL OR rate_fatture_passive.ID_MOVIMENTO IS NOT NULL)
                           AND  fatture_passive.ID_FORNITORE = $ChiaveCliente
                           AND  rate_fatture_passive.ID_MOVIMENTO IS NULL
                           AND  fatture_passive.NUMERO IS NOT NULL
                           AND  rate_fatture_passive.DATA_PAGAMENTO >= '$DataDal' AND rate_fatture_passive.DATA_PAGAMENTO <= '$DataAl'";

            try 
            {
              if($Query = $PDODBase->query($SQLBody))
              { 
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                { 
                  $FatturaONota     = $Row['IS_FATTURA'] == 'T' ? 'fattura passiva' : 'nota passiva';
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


      // NOTE DI CREDITO DEL CLIENTE
      $SQLBody = "SELECT  note_di_credito.CHIAVE          AS CHIAVE_NOTA,
                          note_di_credito.NUMERO          AS NUMERO_NOTA,
                          note_di_credito.DATA            AS DATA_NOTA,
                          note_di_credito.NOTE,
                          note_di_credito.ESIGIBILITA_IVA
                     FROM note_di_credito
                    WHERE note_di_credito.ID_CLIENTE = $ChiaveCliente
                      AND note_di_credito.NUMERO IS NOT NULL
                      AND note_di_credito.DATA >= '$DataDal'  AND note_di_credito.DATA <= '$DataAl'";

          if($Query = $PDODBase->query($SQLBody))
          {
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            { 
              // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***
              
              $Descrizione      = 'Nota n. ' . $Row['NUMERO_NOTA'];
              if($Row['NOTE'] != null && trim($Row['NOTE']) != '')
                 $Descrizione .= ' - ' . $Row['NOTE'];

              $TotaliNota       = TSystemInformation::GetTotaliNota($PDODBase, $Row['CHIAVE_NOTA']);
              $this->FAddNewRiga($Row['CHIAVE_NOTA'],
                                 TYP_NOTE_DI_CREDITO,
                                 $Descrizione, 
                                 $Row['NUMERO_NOTA'], 
                                 $Row['DATA_NOTA'], 
                                 $TotaliNota[0]->Totale, 
                                 false);

              if($Row['ESIGIBILITA_IVA'] == ESIGIBILITA_SPLIT_PAYMENT)
                $this->FAddNewRiga($Row['CHIAVE_NOTA'],
                                  TYP_NOTE_DI_CREDITO_SPLIT,
                                  "Split payment nota n. " . $Row['NUMERO_NOTA'], 
                                  '', 
                                  $Row['DATA_NOTA'], 
                                  $TotaliNota[0]->SommaIva, 
                                  true);
            }
          }  
          else throw new Exception('Impossibile leggere le note del cliente dal database');

            // MOVIMENTI ASSOCIATI AD UNA FATTURA PASSIVA O FATTURA PREGRESSA DEL FORNITORE
           $SQLBody = "SELECT  movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                               movimenti.DESCRIZIONE                 AS DESCRIZIONE_MOVIMENTO,
                               movimenti.DATA                        AS DATA_MOVIMENTO,
                               movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                          FROM fatture_passive
                               JOIN rate_fatture_passive ON fatture_passive.CHIAVE   = rate_fatture_passive.ID_FATTURA_PASSIVA
                               JOIN movimenti            ON movimenti.CHIAVE = rate_fatture_passive.ID_MOVIMENTO
                         WHERE fatture_passive.ID_FORNITORE = $ChiaveCliente
                           AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'
                     UNION 
                        SELECT movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                               movimenti.DESCRIZIONE                 AS DESCRIZIONE_MOVIMENTO,
                               movimenti.DATA                        AS DATA_MOVIMENTO,
                               movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                          FROM movimenti             
                               JOIN fatture_insolute_pregresse_fornitori ON fatture_insolute_pregresse_fornitori.ID_MOVIMENTO = movimenti.CHIAVE
                         WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveCliente
                           AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'";

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
                         WHERE ID_FORNITORE = $ChiaveCliente
                           AND DATA >= '$DataDal' AND DATA <= '$DataAl'";
            
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
                         WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveCliente
                           AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO >= '$DataDal' AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO <= '$DataAl'";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                 $this->FAddNewRiga($Row['CHIAVE_FATTURA'],TYP_FATTURE_PREGRESSE,"Rata Fatt. Pregr. n. " . $Row['NUMERO_FATTURA'], $Row['NUMERO_FATTURA'], $Row['DATA_PAGAMENTO'], $Row['IMPORTO_PREGRESSA'], true);
            }  
            else throw new Exception('Impossibile trovare i pagamenti delle fatture insolute pregresse del Fornitore');

      
      // PAGAMENTI NOTE DI CREDITO DEL CLIENTE
      $SQLBody = "SELECT rate_note.CHIAVE AS CHIAVE_RATA,
                         note_di_credito.NUMERO  AS NUMERO_NOTA,
			                   rate_note.ID_NOTA AS ID_NOTA,
                         rate_note.`DATA` AS DATA_RATA,
                         rate_note.DATA_PAGAMENTO AS DATA_PAGAMENTO_RATA,
                         rate_note.IMPORTO AS IMPORTO,
                         rate_note.NOTE AS NOTE,
                         rate_note.ID_CONTO_CASSE AS ID_CONTO_CASSE,
                         rate_note.SCALATA_IN_FATTURA AS SCALATA_IN_FATTURA,
                         rate_note.ID_FATTURA AS ID_FATTURA,
                         fatture.NUMERO AS NUMERO_FATTURA

                    FROM rate_note
                    LEFT JOIN fatture ON fatture.CHIAVE = rate_note.ID_FATTURA
                    JOIN note_di_credito ON note_di_credito.CHIAVE = rate_note.ID_NOTA

                    WHERE note_di_credito.ID_CLIENTE = $ChiaveCliente
                    AND note_di_credito.NUMERO IS NOT NULL
                    AND(
                          (rate_note.DATA_PAGAMENTO IS NOT NULL
                          AND rate_note.DATA_PAGAMENTO >= '$DataDal'
                          AND rate_note.DATA_PAGAMENTO <= '$DataAl')
                          
                          OR
                          
                          (rate_note.ID_FATTURA IS NOT NULL
                          AND rate_note.`DATA`>= '$DataDal'
                          AND rate_note.`DATA` <= '$DataAl')
                       )";

      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***
          
          $TotaliNota       = TSystemInformation::GetRitenutaNotaXRate($PDODBase, $Row['ID_NOTA']);
          $ArrayRate = $TotaliNota[0]->Rate;
          
          if($Row['DATA_PAGAMENTO_RATA'] != null && $Row['ID_FATTURA'] == null)
          {
            $Descrizione      = 'Pagamento Rata della Nota n. ' . $Row['NUMERO_NOTA'];

            if($Row['NOTE'] != null && trim($Row['NOTE']) != '')
               $Descrizione .= ' - ' . $Row['NOTE'];

            
            $this->FAddNewRiga(-$Row['CHIAVE_RATA'],
                                TYP_NOTE_DI_CREDITO,
                                $Descrizione, 
                                $Row['NUMERO_NOTA'], 
                                $Row['DATA_PAGAMENTO_RATA'], 
                                $Row['IMPORTO']/100, 
                                true);

            if (($TotaliNota[0]->TotaleRitenuta) != 0 && count($ArrayRate) > 0)
            {
              for($i = 0 ; $i < count($ArrayRate); $i++)
              {
                if($ArrayRate[$i]->Pagata && $Row['CHIAVE_RATA'] == $ArrayRate[$i]->IdRata)
                {
                   $Descrizione      = 'Ritenuta della nota n.' . $Row['NUMERO_NOTA'];

                   $this->FAddNewRiga( $Row['CHIAVE_RATA'],
                                      '',
                                      $Descrizione, 
                                      '', 
                                      $Row['DATA_PAGAMENTO_RATA'], 
                                      $ArrayRate[$i]->Ritenuta, 
                                      true);
                }
              }
             
            }
          }
          else
          {
            if(isset($Row['ID_FATTURA']) && $Row['ID_FATTURA'] != null)
            {
              $Descrizione      = 'Nota n. ' . $Row['NUMERO_NOTA'] . ' - scalata dalla fattura n. ' . $Row['NUMERO_FATTURA'];

              $this->FAddNewRiga(-$Row['CHIAVE_RATA'],
                                  TYP_NOTE_DI_CREDITO,
                                  $Descrizione, 
                                  $Row['NUMERO_NOTA'], 
                                  $Row['DATA_RATA'], 
                                  $Row['IMPORTO']/100, 
                                  true);

              if (($TotaliNota[0]->TotaleRitenuta) != 0 && count(($TotaliNota[0]->Rate)) > 0)
              {
                for($i = 0 ; $i < count($ArrayRate); $i++)
                {
                  if(!$ArrayRate[$i]->Pagata && $Row['CHIAVE_RATA'] == $ArrayRate[$i]->IdRata)
                  {
                    $Descrizione      = 'Ritenuta della nota n.' . $Row['NUMERO_NOTA'];

                    $this->FAddNewRiga( $Row['CHIAVE_RATA'],
                                        '',
                                        $Descrizione, 
                                        '', 
                                        $Row['DATA_RATA'], 
                                        $ArrayRate[$i]->Ritenuta, 
                                        true);
                  }
                };

                
              }

              if(isset($Row['ID_FATTURA']))
              {
                $Descrizione      = 'Incasso ' . (isset($Row['NUMERO_FATTURA'])? 'fatt n. ' . $Row['NUMERO_FATTURA'] : ' avv. fatt. n. ' . $Row['ID_FATTURA']);

                $this->FAddNewRiga($Row['ID_FATTURA'],
                                    TYP_NOTE_DI_CREDITO,
                                    $Descrizione, 
                                    isset($Row['NUMERO_FATTURA'])? $Row['NUMERO_FATTURA'] : '//', 
                                    $Row['DATA_RATA'], 
                                    $Row['IMPORTO']/100, 
                                    false);

                $TotaliFattura = TSystemInformation::GetRitenutaFatturaXRate($PDODBase, $Row['ID_FATTURA']);
                $Descrizione      = 'Ritenuta ' . (isset($Row['NUMERO_FATTURA'])? ' della fattura n. ' . $Row['NUMERO_FATTURA'] : ' avv. fatt. n. ' . $Row['ID_FATTURA']);

                if($TotaliFattura[0]->TotaleRitenuta != 0)
                  $this->FAddNewRiga(-$Row['ID_FATTURA'],
                                      TYP_NOTE_DI_CREDITO,
                                      $Descrizione, 
                                      isset($Row['NUMERO_FATTURA'])? $Row['NUMERO_FATTURA'] : '//', 
                                      $Row['DATA_RATA'], 
                                      $this->FGetTotaleRitenutaRata($TotaliFattura[0], $Row['CHIAVE_RATA']), 
                                      false);
              }
            }
          }

        }
      }  
      else throw new Exception('Impossibile leggere le note del cliente dal database');

      // PAGAMENTI FATTURE ATTIVE
      $SQLBody = "SELECT  fatture.CHIAVE							     AS CHIAVE_FATTURA,
                          fatture.NUMERO							     AS NUMERO_FATTURA,
                          fatture.DATA  							     AS DATA_FATTURA,
                          fatture.NOTE_IN_FATTURA,
                          
                          rate_fattura.CHIAVE              AS CHIAVE_RATA,
                          rate_fattura.DATA_PAGAMENTO      AS DATA_PAGAMENTO_RATA,
                          rate_fattura.IMPORTO / 100       AS IMPORTO_RATA,
                          rate_fattura.NOTE                AS NOTE_RATA,
                          rate_fattura.IS_RITENUTA_ACCONTO,
                          rate_fattura.IS_NON_SCARICATA,

                          conti_correnti_casse.BANCA,
                          conti_correnti_casse.DESCRIZIONE      AS DESCRIZIONE_CONTO
                    FROM  fatture
                          LEFT OUTER JOIN rate_fattura          ON fatture.CHIAVE              = rate_fattura.ID_FATTURA
                          LEFT OUTER JOIN conti_correnti_casse  ON conti_correnti_casse.CHIAVE = rate_fattura.ID_CONTO_CASSE
                          LEFT OUTER JOIN anagrafiche               ON anagrafiche.CHIAVE              = fatture.ID_CLIENTE
                   WHERE  rate_fattura.DATA_PAGAMENTO IS NOT NULL
                     AND  rate_fattura.ID_MOVIMENTO IS NULL
                     AND  fatture.ID_CLIENTE = $ChiaveCliente
                     AND  fatture.NUMERO IS NOT NULL
                     AND  rate_fattura.DATA_PAGAMENTO >= '$DataDal' AND rate_fattura.DATA_PAGAMENTO <= '$DataAl'";

      if($Query = $PDODBase->query($SQLBody))
      { 
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***
          // SEZIONE: SEZIONE_RATE_FATTURE_PAGATE (CERCA QUESTO PER TROVARE LA SEZIONE CORRISPONDENTE DA MODIFICARE)

          $Descrizione      = "Rata fattura n. " . $Row['NUMERO_FATTURA'];
          if($Row['NOTE_RATA'] != null && trim($Row['NOTE_RATA']) != '')
            $Descrizione .= ' - Note: ' . $Row['NOTE_RATA'];

          $TotaliFattura = TSystemInformation::GetRitenutaFatturaXRate($PDODBase, $Row['CHIAVE_FATTURA']);
         
          if(isset($Row['IS_RITENUTA_ACCONTO']) && $Row['IS_RITENUTA_ACCONTO'] == 'T')
          {
            if(isset($Row['IS_NON_SCARICATA']) && $Row['IS_NON_SCARICATA'] != 'T')
            {
              $Descrizione      = 'Ritenuta della fattura n. ' . $Row['NUMERO_FATTURA'];
              if($Row['NOTE_RATA'] != null && trim($Row['NOTE_RATA']) != '')
                $Descrizione .= ' - Note: ' . $Row['NOTE_RATA'];

                $this->FAddNewRiga($Row['CHIAVE_RATA'],
                                   TYP_RATA_FATTURA,
                                   $Descrizione, 
                                   $Row['NUMERO_FATTURA'], 
                                   $Row['DATA_PAGAMENTO_RATA'], 
                                   TSystemInformation::GestioneArrotondamentoRateDerivatoDaErroreArrotondamento($Row['CHIAVE_RATA'], $TotaliFattura[0]),
                                   false,
                                   $Row['CHIAVE_FATTURA']);
            }
          }
          else
          {
            $this->FAddNewRiga($Row['CHIAVE_RATA'],
                               TYP_RATA_FATTURA,
                               $Descrizione, 
                               $Row['NUMERO_FATTURA'], 
                               $Row['DATA_PAGAMENTO_RATA'], 
                               TSystemInformation::GestioneArrotondamentoRateDerivatoDaErroreArrotondamento($Row['CHIAVE_RATA'], $TotaliFattura[0]),
                               false,
                               $Row['CHIAVE_FATTURA']);
          }
          


          if($TotaliFattura[0]->TotaleRitenuta != 0 && !$TotaliFattura[0]->ContieneAlmenoUnaRataConRdA)
             $this->FAddNewRiga(-$Row['CHIAVE_RATA'],
                                TYP_FATTURE_ATTIVE,
                                'Ritenuta della fattura n. ' . $Row['NUMERO_FATTURA'], 
                                '', 
                                $Row['DATA_PAGAMENTO_RATA'], 
                                $this->FGetTotaleRitenutaRata($TotaliFattura[0], $Row['CHIAVE_RATA']), 
                                false);
        }
      }  
      else throw new Exception('Impossibile leggere le rate delle fatture attive dal database');

      // MOVIMENTI ASSOCIATI AD UNA FATTURA O FATTURA PREGRESSA DEL CLIENTE
      $SQLBody = "SELECT  movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                          movimenti.DESCRIZIONE                 AS DESCRIZIONE_MOVIMENTO,
                          movimenti.DATA                        AS DATA_MOVIMENTO,
                          movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO,
                          fatture.CHIAVE AS CHIAVE_FATTURA,
                          rate_fattura.CHIAVE AS CHIAVE_RATA,
                          fatture.NUMERO AS NUMERO_FATTURA,
                          'F' AS TIPO_MOVIMENTO
                     FROM fatture
                          JOIN rate_fattura ON fatture.CHIAVE   = rate_fattura.ID_FATTURA
                          JOIN movimenti    ON movimenti.CHIAVE = rate_fattura.ID_MOVIMENTO
                    WHERE fatture.ID_CLIENTE = $ChiaveCliente
                      AND fatture.NUMERO IS NOT NULL
                      AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'
                UNION 
                   SELECT movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                          movimenti.DESCRIZIONE                 AS DESCRIZIONE_MOVIMENTO,
                          movimenti.DATA                        AS DATA_MOVIMENTO,
                          movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO,
                          1 AS CHIAVE_FATTURA,
                          1 AS CHIAVE_RATA,
                          1 AS NUMERO_FATTURA,
                          'P' AS TIPO_MOVIMENTO
                     FROM movimenti             
                          JOIN fatture_insolute_pregresse ON fatture_insolute_pregresse.ID_MOVIMENTO = movimenti.CHIAVE
                    WHERE fatture_insolute_pregresse.ID_CLIENTE = $ChiaveCliente
                      AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'";

      if($Query = $PDODBase->query($SQLBody))
      { 
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***
          
            $this->FAddNewRiga($Row['CHIAVE_MOVIMENTO'],
                                TYP_MOVIMENTI,
                                'Movimento di Conto: ' . $Row['DESCRIZIONE_MOVIMENTO'], 
                                '', 
                                $Row['DATA_MOVIMENTO'], 
                                $Row['IMPORTO_MOVIMENTO'], 
                                false);

          if($Row['TIPO_MOVIMENTO'] == 'F')
          {
            $TotaliFattura = TSystemInformation::GetRitenutaFatturaXRate($PDODBase, $Row['CHIAVE_FATTURA']);

            $this->FGestioneArrotondamentoMovimentiDerivatoDaErroreArrotondamento($Row['CHIAVE_MOVIMENTO'], $Row['CHIAVE_RATA'], $TotaliFattura[0]);

            if($TotaliFattura[0]->TotaleRitenuta != 0)
              $this->FAddNewRiga(-$Row['CHIAVE_RATA'],
                                  TYP_FATTURE_ATTIVE,
                                  'Ritenuta della fattura n. ' . $Row['NUMERO_FATTURA'], 
                                  '', 
                                  $Row['DATA_MOVIMENTO'], 
                                  $this->FGetTotaleRitenutaRata($TotaliFattura[0], $Row['CHIAVE_RATA']), 
                                  false);
          }
        }       
      }  
      else throw new Exception('Impossibile leggere le rate delle fatture attive dal database');

      // FATTURE INSOLUTE PREGRESSE
      $SQLBody = "SELECT CHIAVE          AS CHIAVE_FATTURA, 
                         NUMERO          AS NUMERO_FATTURA, 
                         IMPORTO / 100   AS IMPORTO_PREGRESSA,
                         DATA            AS DATA_FATTURA
                    FROM fatture_insolute_pregresse 
                   WHERE ID_CLIENTE = $ChiaveCliente
                     AND DATA >= '$DataDal' AND DATA <= '$DataAl'";
      
      if($Query = $PDODBase->query($SQLBody))
      {
        // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***
        
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
           $this->FAddNewRiga($Row['CHIAVE_FATTURA'],
                              TYP_FATTURE_PREGRESSE,
                              'F.P. n. ' . $Row['NUMERO_FATTURA'], 
                              $Row['NUMERO_FATTURA'], 
                              $Row['DATA_FATTURA'], 
                              $Row['IMPORTO_PREGRESSA'], 
                              true);
      }  
      else throw new Exception('Impossibile leggere le fatture (attive) insolute pregresse'); 


      // PAGAMENTI FATTURE PREGRESSE
      $SQLBody = "SELECT  fatture_insolute_pregresse.CHIAVE          AS CHIAVE_FATTURA, 
                          fatture_insolute_pregresse.NUMERO          AS NUMERO_FATTURA, 
                          fatture_insolute_pregresse.IMPORTO / 100   AS IMPORTO_PREGRESSA, 
                          fatture_insolute_pregresse.DATA            AS DATA_FATTURA,
                          fatture_insolute_pregresse.DATA_PAGAMENTO,
                          fatture_insolute_pregresse.NOTE
                    FROM  fatture_insolute_pregresse
                    WHERE fatture_insolute_pregresse.ID_CLIENTE = $ChiaveCliente
                      AND fatture_insolute_pregresse.ID_MOVIMENTO   IS NULL
                      AND fatture_insolute_pregresse.DATA_PAGAMENTO IS NOT NULL
                      AND fatture_insolute_pregresse.DATA_PAGAMENTO >= '$DataDal' AND fatture_insolute_pregresse.DATA_PAGAMENTO <= '$DataAl'";


      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          // ***SE CAMBI QUI, CAMBIARE ANCHE LA FUNZIONE CHE CALCOLA I SALDI DELLE CHIUSURE ANNUALI FGestioneChiusureAnnuali***

          $Descrizione      = "Rata Fatt. Pregr. n. " . $Row['NUMERO_FATTURA'];
          if($Row['NOTE'] != null && trim($Row['NOTE']) != '')
             $Descrizione .= ' - ' . $Row['NOTE'];

          $this->FAddNewRiga($Row['CHIAVE_FATTURA'],
                             TYP_FATTURE_PREGRESSE,
                             $Descrizione, 
                             $Row['NUMERO_FATTURA'], 
                             $Row['DATA_PAGAMENTO'], 
                             $Row['IMPORTO_PREGRESSA'], 
                             false);
        }
      }  
      else throw new Exception('Impossibile leggere i pagamenti delle fatture (attive) insolute pregresse');
      
      if(!$VelocizzaXStampaSaldiClienti)
      {
        // Ordino le righe prima caricarle nel PDF
        usort($this->FRigheProspetto, 
              function($a, $b) 
              { 
                return strcmp($a->Data, $b->Data);
              });
      }

      $PrimoElemento  = true;
      $ConteggioSaldo = 0;

      if(!$VelocizzaXStampaSaldiClienti)
      {
        // Carico tutte le righe nel PDF
        foreach($this->FRigheProspetto as $Riga)
        {
          $Fattura = new TDatiDocumento();
          
          $Fattura->MM_DESCRIZIONE_DOCUMENTO = $Riga->Descrizione;
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
              $ConteggioSaldo             += $Riga->Importo;
              $ConteggioSaldo              = round($ConteggioSaldo, 2); //Arrotondamento
              $Fattura->LB_SALDO           = $ConteggioSaldo;
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
              $Fattura->LB_DARE               = $Riga->Importo;
              $Fattura->LB_AVERE              = '';
              $DatiFooter->LB_TOTALE_DARE    += $Riga->Importo;
              $ConteggioSaldo                += $Riga->Importo;
            }
            else
            {
              $Fattura->LB_DARE               = '';
              $Fattura->LB_AVERE              = $Riga->Importo;
              $DatiFooter->LB_TOTALE_AVERE   += $Riga->Importo;
              $ConteggioSaldo                -= $Riga->Importo;
            }

            $ConteggioSaldo                    = round($ConteggioSaldo, 2); //Arrotondamento
            
            $Fattura->LB_SALDO                 = $ConteggioSaldo;
          }

          $PrimoElemento = false;
          array_push($Result->BAND_SUMMARY, $Fattura);
        }
      }

      if(!$VelocizzaXStampaSaldiClienti)
      {
        // FORMATTAZIONI TIPOGRAFICHE
        $this->FFormattazioneDelleDateETotali($Result->BAND_SUMMARY);
      }

      if($NomeLogo != '')
        $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

      array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

      $DatiFooterVuoto              = new TDatiFooter();
      $DatiFooterVuoto->QRLabel10       = '';
      $DatiFooterVuoto->LB_TOTALE_DARE  = '';
      $DatiFooterVuoto->LB_TOTALE_AVERE = '';
      $DatiFooterVuoto->LB_TOTALE_SALDO = '';
      array_push($Result->BAND_FOOTER, $DatiFooterVuoto);

      $DatiFooter->LB_TOTALE_DARE  = round($DatiFooter->LB_TOTALE_DARE, 2);
      $DatiFooter->LB_TOTALE_AVERE = round($DatiFooter->LB_TOTALE_AVERE, 2);

      $DatiFooter->TOTALE_SALDO    = $this->SaldoContabileCliente;
      $DatiFooter->LB_TOTALE_SALDO = number_format($this->SaldoContabileCliente, 2, ',', '.') . '€';
      $Result->TotaleSaldoAttualePerSchedaClienteStringa = $DatiFooter->LB_TOTALE_SALDO;
      $Result->TotaleSaldoAttualePerSchedaCliente        = $DatiFooter->TOTALE_SALDO;

      $DatiFooter->LB_TOTALE_DARE  = number_format($this->TotaleDareCliente,  2, ',', '.') . '€';
      $DatiFooter->LB_TOTALE_AVERE = number_format($this->TotaleAvereCliente, 2, ',', '.') . '€';

      array_push($Result->BAND_FOOTER, $DatiFooter);

      return $Result;

    }

    private function FFormattazioneDelleDateETotali(&$BAND_SUMMARY)
    {
      $ConteggioSaldo = $this->DareDiPartenza - $this->AvereDiPartenza;
      $ConteggioSaldo = round($ConteggioSaldo, 2); //Arrotondamento

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

            $ConteggioSaldo = round($ConteggioSaldo, 2); //Arrotondamento
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

    private function FGestioneChiusureAnnuali($PDODBase, $DataDal, $DataAl, $ChiaveCliente, $VelocizzaXStampaSaldiClienti)
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
                      WHERE saldi_chiusure_annuali.ID_ANAGRAFICA = $ChiaveCliente
                        AND saldi_chiusure_annuali.ANNO IN ($StringaAnni)";


        if($Query = $PDODBase->query($SQLBody))
        {
          while($Row = $Query->fetch(PDO::FETCH_ASSOC))
          { 
            for($i = 0; $i < count($ArrayPrimoMeseAnniIntervallo); $i++)
            {
              if($Row['ANNO'] == $ArrayPrimoMeseAnniIntervallo[$i]->Anno)
              {
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

            if($PrimoDareAvereImpostato || $VelocizzaXStampaSaldiClienti)
            {
              $this->DareDiPartenza    = $OggettoAnno->CifraChiusuraDare;
              $this->AvereDiPartenza   = $OggettoAnno->CifraChiusuraAvere;
              $PrimoDareAvereImpostato = false;
            }
          }
          else 
          {
            $CifraChiusuraAnnuale = $this->FCalcoloTotaleSaldoCliente("2000-01-01", $OggettoAnno->Anno . "-12-31", $OggettoAnno->Anno, $ChiaveCliente, true, $PDODBase);

            $SaldoChiusuraAnnuale = $CifraChiusuraAnnuale->TotaleDareAnno - $CifraChiusuraAnnuale->TotaleAvereAnno; 

            $this->FAddNewRiga('Chiave' . $OggettoAnno->Anno,
                               TYP_APERTURA_ANNO,
                               'Apertura conto ' . $OggettoAnno->Anno + 1, 
                               '', 
                               $OggettoAnno->Anno + 1 . '-01-01', 
                               $SaldoChiusuraAnnuale >= 0? $SaldoChiusuraAnnuale : -1 * $SaldoChiusuraAnnuale, 
                               $SaldoChiusuraAnnuale >= 0? true : false);
            
            if($PrimoDareAvereImpostato || $VelocizzaXStampaSaldiClienti)
            {
              $this->DareDiPartenza    = $CifraChiusuraAnnuale->TotaleDareAnno;
              $this->AvereDiPartenza   = $CifraChiusuraAnnuale->TotaleAvereAnno;
              $PrimoDareAvereImpostato = false;
            }
          }
        }
      }
    }

    // cambiato nome funzione, prima veniva utilizzata per il saldo annuale, invece adesso calcola il saldo dalla data, alla data
    private function FCalcoloTotaleSaldoCliente($DataDalParam, $DataAlParam, $OggettoAnno, $ChiaveCliente, $SalvareSaldoNellaTabella, $PDODBase)
    {
      $DataDal = $DataDalParam;
      $DataAl  = $DataAlParam;

      $VisualizzaSaldoNelLog = false;
      
      $this->FPrepareParameterValue($DataDal,'#');
      $this->FPrepareParameterValue($DataAl,'#');
      $this->FPrepareParameterValue($ChiaveCliente,':');
      $this->FPrepareParameterValue($OggettoAnno, ':');

      $TotaleDareAnno       = 0;
      $TotaleAvereAnno      = 0;
      $MovimentiConteggiati = array();
            
      if($VisualizzaSaldoNelLog)
        error_log($OggettoAnno);


      // MOVIMENTI ASSOCIATI AL CLIENTE
      $SQLBodyMovimenti = "SELECT movimenti.*,
                                  cat_movimenti.DESCRIZIONE AS DESCR_CATEGORIA
                             FROM movimenti LEFT OUTER JOIN cat_movimenti ON (cat_movimenti.CHIAVE = movimenti.ID_CATEGORIA_MOVIMENTO)
                            WHERE ID_ANAGRAFICA = $ChiaveCliente
                              AND (movimenti.DATA >= '$DataDal'  AND movimenti.DATA <= '$DataAl' OR 
                                   movimenti.DATA_CHIUSURA >= '$DataDal' AND movimenti.DATA_CHIUSURA <= '$DataAl')";

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
          
          if($Row['DATA'] >= $DataDal && $Row['DATA'] <= $DataAl)
            if($IsDare)
              $TotaleDareAnno += $Row['IMPORTO'] / 100;
            else $TotaleAvereAnno += $Row['IMPORTO'] / 100;

          if(!is_null($Row['DATA_CHIUSURA']))
             if($Row['DATA_CHIUSURA'] >= $DataDal && $Row['DATA_CHIUSURA'] <= $DataAl)
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

      if($VisualizzaSaldoNelLog)
      {
        error_log("MOVIMENTI ASSOCIATI AL CLIENTE: " . $TotaleDareAnno);
        error_log("MOVIMENTI ASSOCIATI AL CLIENTE: " . $TotaleAvereAnno);
      }


      // FATTURE ATTIVE DEL CLIENTE
      $SQLBody = "SELECT fatture.CHIAVE				    AS CHIAVE_FATTURA,
                         fatture.NUMERO					  AS NUMERO_FATTURA,
                         fatture.DATA  						AS DATA_FATTURA,
                         fatture.NOTE_IN_FATTURA,
                         fatture.NUMERO_ANTICIPO
                    FROM fatture 
                   WHERE fatture.ID_CLIENTE = $ChiaveCliente 
                     AND fatture.NUMERO IS NOT NULL
                     AND fatture.DATA >= '$DataDal' AND fatture.DATA <= '$DataAl'";
      
      if($Query = $PDODBase->query($SQLBody))
      { 
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          $TotaliFattura    = TSystemInformation::GetTotaliFattura($PDODBase, $Row['CHIAVE_FATTURA']);
          
          $TotaleDareAnno += $TotaliFattura[0]->Totale;
        }
      }  
      else throw new Exception('Impossibile leggere le fatture attive dal database...');
      

        // FATTURE PASSIVE FORNITORE
            $SQLBody = "SELECT 
                        fatture_passive.IS_FATTURA,
                        fatture_passive.TOTALE_FATTURA    AS TOTALE_FATTURA
                  FROM  fatture_passive 
                 WHERE  fatture_passive.ID_FORNITORE = $ChiaveCliente 
                   AND  fatture_passive.DATA >= '$DataDal' AND fatture_passive.DATA <= '$DataAl'";
            
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

      if($VisualizzaSaldoNelLog)
      {
        error_log("FATTURE ATTIVE DEL CLIENTE: " . $TotaleDareAnno);
        error_log("FATTURE ATTIVE DEL CLIENTE: " . $TotaleAvereAnno);
      }

      // NOTE DI CREDITO DEL CLIENTE
      $SQLBody = "SELECT  note_di_credito.CHIAVE          AS CHIAVE_NOTA
                     FROM note_di_credito
                    WHERE note_di_credito.ID_CLIENTE = $ChiaveCliente
                      AND note_di_credito.NUMERO IS NOT NULL
                      AND note_di_credito.DATA >= '$DataDal' AND note_di_credito.DATA <= '$DataAl'";

      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          $TotaliNota       = TSystemInformation::GetTotaliNota($PDODBase, $Row['CHIAVE_NOTA']);
          
          $TotaleAvereAnno += $TotaliNota[0]->Totale;
        }
      }  
      else throw new Exception('Impossibile leggere le note del cliente dal database');

        // PAGAMENTI FATTURE PASSIVE PAGATE DIRETTAMENTE
            $SQLBody = "SELECT  fatture_passive.CHIAVE						       		 AS CHIAVE_FATTURA,
                                fatture_passive.IS_FATTURA,
                                rate_fatture_passive.IMPORTO / 1000   AS IMPORTO_RATA
                          FROM  fatture_passive
                                JOIN rate_fatture_passive     ON fatture_passive.CHIAVE      = rate_fatture_passive.ID_FATTURA_PASSIVA
                         WHERE  (rate_fatture_passive.DATA_PAGAMENTO IS NOT NULL OR rate_fatture_passive.ID_MOVIMENTO IS NOT NULL)
                           AND  fatture_passive.ID_FORNITORE = $ChiaveCliente
                           AND  rate_fatture_passive.ID_MOVIMENTO IS NULL
                           AND  fatture_passive.NUMERO IS NOT NULL
                           AND  rate_fatture_passive.DATA_PAGAMENTO >= '$DataDal' AND rate_fatture_passive.DATA_PAGAMENTO <= '$DataAl'";

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

      if($VisualizzaSaldoNelLog)
      {
        error_log("NOTE DI CREDITO DEL CLIENTE: " . $TotaleDareAnno);
        error_log("NOTE DI CREDITO DEL CLIENTE: " . $TotaleAvereAnno);
      }

       // AGGIUNTA AL SALDO ANNUALE LA SOMMA DELLE RITENUTE DELLE FATTURE PASSIVE
            $SQLBody = "SELECT (COUNT(rate_fatture_passive.ID_FATTURA_PASSIVA) = COUNT(rate_fatture_passive.DATA_PAGAMENTO)) AS PAGATE_TUTTE,
                               SUM(ritenute_fatture_passive.IMPORTO) AS IMPORTO,
                               fatture_passive.CHIAVE
                          FROM fatture_passive
                               LEFT JOIN rate_fatture_passive ON fatture_passive.CHIAVE = rate_fatture_passive.ID_FATTURA_PASSIVA
                               LEFT JOIN ritenute_fatture_passive ON ritenute_fatture_passive.ID_FATTURA_PASSIVA = fatture_passive.CHIAVE
                         WHERE fatture_passive.ID_FORNITORE = $ChiaveCliente
                           AND rate_fatture_passive.DATA_SCADENZA >= '$DataDal' 
                           AND rate_fatture_passive.DATA_SCADENZA <= '$DataAl'
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
                          WHERE fatture_passive.ID_FORNITORE = $ChiaveCliente
                            AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'
                      UNION 
                          SELECT movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                                movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                            FROM movimenti             
                                JOIN fatture_insolute_pregresse_fornitori ON fatture_insolute_pregresse_fornitori.ID_MOVIMENTO = movimenti.CHIAVE
                          WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveCliente
                            AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'";

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
                         WHERE ID_FORNITORE = $ChiaveCliente
                           AND DATA >= '$DataDal' AND DATA <= '$DataAl'";
            
            if($Query = $PDODBase->query($SQLBody))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                $TotaleAvereAnno += $Row['IMPORTO_PREGRESSA'];
            else throw new Exception('Impossibile trovare le fatture insolute pregresse del Fornitore'); 


            // PAGAMENTI DIRETTI FATTURE INSOLUTE PREGRESSE FORNITORI
            $SQLBody = "SELECT fatture_insolute_pregresse_fornitori.CHIAVE          AS CHIAVE_FATTURA, 
                               fatture_insolute_pregresse_fornitori.IMPORTO / 100   AS IMPORTO_PREGRESSA
                          FROM fatture_insolute_pregresse_fornitori
                         WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveCliente
                           AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO >= '$DataDal' AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO <= '$DataAl'";

            if($Query = $PDODBase->query($SQLBody))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                $TotaleDareAnno += $Row['IMPORTO_PREGRESSA'];
            else throw new Exception('Impossibile trovare i pagamenti delle fatture insolute pregresse del Fornitore');


            if($SalvareSaldoNellaTabella)
            {
              $SQLBody = "INSERT INTO saldi_chiusure_annuali (ID_ANAGRAFICA, ANNO, DARE_CHIUSURA, AVERE_CHIUSURA)
                                                      VALUES ($ChiaveCliente, $OggettoAnno, $TotaleDareAnno * 100, $TotaleAvereAnno * 100)";
        
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

      // PAGAMENTI NOTE DI CREDITO DEL CLIENTE
      $SQLBody = "SELECT rate_note.CHIAVE AS CHIAVE_RATA,
                         rate_note.`DATA` AS DATA_RATA,
                         rate_note.ID_NOTA AS ID_NOTA,
                         rate_note.DATA_PAGAMENTO AS DATA_PAGAMENTO_RATA,
                         rate_note.IMPORTO AS IMPORTO,
                         rate_note.ID_FATTURA AS ID_FATTURA

                    FROM rate_note
                    JOIN note_di_credito ON note_di_credito.CHIAVE = rate_note.ID_NOTA

                    WHERE note_di_credito.ID_CLIENTE = $ChiaveCliente
                    AND note_di_credito.NUMERO IS NOT NULL
                    AND(
                          (rate_note.DATA_PAGAMENTO IS NOT NULL
                          AND rate_note.DATA_PAGAMENTO >= '$DataDal'
                          AND rate_note.DATA_PAGAMENTO <= '$DataAl')
                          
                          OR
                          
                          (rate_note.ID_FATTURA IS NOT NULL
                          AND rate_note.`DATA`>= '$DataDal'
                          AND rate_note.`DATA` <= '$DataAl')
                       )";

      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          $TotaliNota       = TSystemInformation::GetRitenutaNotaXRate($PDODBase, $Row['ID_NOTA']);
          $ArrayRate = $TotaliNota[0]->Rate;
          
          if(isset($Row['DATA_PAGAMENTO_RATA']))
          {
            $TotaleDareAnno += $Row['IMPORTO']/100;
            
            if (($TotaliNota[0]->TotaleRitenuta) != 0 && count($ArrayRate) > 0)
            {
              for($i = 0 ; $i < count($ArrayRate); $i++)
              {
                if($Row['CHIAVE_RATA'] == $ArrayRate[$i]->IdRata)
                {
                   $TotaleDareAnno += $ArrayRate[$i]->Ritenuta;
                }
              }
            }
          }
          else
          {
            if(isset($Row['ID_FATTURA']))
            {
              $TotaleDareAnno += $Row['IMPORTO'] / 100;
              
              if (($TotaliNota[0]->TotaleRitenuta) != 0 && count($ArrayRate) > 0)
              {
                for($i = 0 ; $i < count($ArrayRate); $i++)
                {
                  if($Row['CHIAVE_RATA'] == $ArrayRate[$i]->IdRata)
                  {
                    $TotaleDareAnno += $ArrayRate[$i]->Ritenuta;
                  }
                }
              }
                
              $TotaleAvereAnno += $Row['IMPORTO'] / 100; // AGGIUNGO L'INCASSO DELLA FATTURA
              
              $TotaliFattura = TSystemInformation::GetRitenutaFatturaXRate($PDODBase, $Row['ID_FATTURA']);
              if($TotaliFattura[0]->TotaleRitenuta != 0)
                $TotaleAvereAnno += $this->FGetTotaleRitenutaRata($TotaliFattura[0], $Row['CHIAVE_RATA']);

            }
          }
        }
      }  
      else throw new Exception('Impossibile leggere le note del cliente dal database');

      if($VisualizzaSaldoNelLog)
      {
        error_log("PAGAMENTI NOTE DI CREDITO DEL CLIENTE: " . $TotaleDareAnno);
        error_log("PAGAMENTI NOTE DI CREDITO DEL CLIENTE: " . $TotaleAvereAnno);
      }
      
      // PAGAMENTI FATTURE ATTIVE
      $SQLBody = "SELECT  fatture.CHIAVE								AS CHIAVE_FATTURA,
                          rate_fattura.CHIAVE           AS CHIAVE_RATA,
                          rate_fattura.IMPORTO / 100    AS IMPORTO_RATA,
                          rate_fattura.IS_RITENUTA_ACCONTO,
                          rate_fattura.IS_NON_SCARICATA
                    FROM  fatture
                          LEFT OUTER JOIN rate_fattura          ON fatture.CHIAVE              = rate_fattura.ID_FATTURA
                          LEFT OUTER JOIN conti_correnti_casse  ON conti_correnti_casse.CHIAVE = rate_fattura.ID_CONTO_CASSE
                          LEFT OUTER JOIN anagrafiche               ON anagrafiche.CHIAVE              = fatture.ID_CLIENTE
                   WHERE  rate_fattura.DATA_PAGAMENTO IS NOT NULL
                     AND  rate_fattura.ID_MOVIMENTO IS NULL
                     AND  fatture.ID_CLIENTE = $ChiaveCliente
                     AND  fatture.NUMERO IS NOT NULL
                     AND  rate_fattura.DATA_PAGAMENTO >= '$DataDal' AND rate_fattura.DATA_PAGAMENTO <= '$DataAl'";
                     
      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          // SEZIONE: SEZIONE_RATE_FATTURE_PAGATE (CERCA QUESTO PER TROVARE LA SEZIONE CORRISPONDENTE DA MODIFICARE)
          $TotaliFattura = TSystemInformation::GetRitenutaFatturaXRate($PDODBase, $Row['CHIAVE_FATTURA']);
         
          if(isset($Row['IS_RITENUTA_ACCONTO']) && $Row['IS_RITENUTA_ACCONTO'] == 'T')
          {
            if(isset($Row['IS_NON_SCARICATA']) && $Row['IS_NON_SCARICATA'] != 'T') //SE LA RATA è UNA RDA BISOGNA CONTROLLARE SE DEVE ESSERE SCALATA DALLA FATTURA (PROBLEMA POSTE)
              $TotaleAvereAnno += TSystemInformation::GestioneArrotondamentoRateDerivatoDaErroreArrotondamento($Row['CHIAVE_RATA'], $TotaliFattura[0]);
          }
          else $TotaleAvereAnno += TSystemInformation::GestioneArrotondamentoRateDerivatoDaErroreArrotondamento($Row['CHIAVE_RATA'], $TotaliFattura[0]);

          if($TotaliFattura[0]->TotaleRitenuta != 0 && !$TotaliFattura[0]->ContieneAlmenoUnaRataConRdA)
            $TotaleAvereAnno += $this->FGetTotaleRitenutaRata($TotaliFattura[0], $Row['CHIAVE_RATA']);
        }
      }
      else throw new Exception('Impossibile leggere le rate delle fatture attive dal database');

      if($VisualizzaSaldoNelLog)
      {
        error_log( "PAGAMENTI FATTURE ATTIVE: Dare " . $TotaleDareAnno);
        error_log( "PAGAMENTI FATTURE ATTIVE: Avere " . $TotaleAvereAnno);
      }

      // MOVIMENTI ASSOCIATI AD UNA FATTURA O FATTURA PREGRESSA DEL CLIENTE
      $SQLBody = "  SELECT  movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                            movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO,
                            movimenti.ID_ANAGRAFICA,
                            fatture.CHIAVE AS CHIAVE_FATTURA,
                            rate_fattura.CHIAVE AS CHIAVE_RATA,
                            fatture.NUMERO AS NUMERO_FATTURA,
                            'F' AS TIPO_MOVIMENTO
                      FROM fatture
                            JOIN rate_fattura ON fatture.CHIAVE   = rate_fattura.ID_FATTURA
                            JOIN movimenti    ON movimenti.CHIAVE = rate_fattura.ID_MOVIMENTO
                      WHERE fatture.ID_CLIENTE = $ChiaveCliente
                        AND fatture.NUMERO IS NOT NULL
                        AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'
                  UNION 
                    SELECT movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                            movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO,
                            movimenti.ID_ANAGRAFICA,
                            1 AS CHIAVE_FATTURA,
                            1 AS CHIAVE_RATA,
                            1 AS NUMERO_FATTURA,
                            'P' AS TIPO_MOVIMENTO
                      FROM movimenti             
                            JOIN fatture_insolute_pregresse ON fatture_insolute_pregresse.ID_MOVIMENTO = movimenti.CHIAVE
                      WHERE fatture_insolute_pregresse.ID_CLIENTE = $ChiaveCliente
                        AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'
                    
                    ORDER BY CHIAVE_MOVIMENTO";

      $LastMovimento = -1; 
      if($Query = $PDODBase->query($SQLBody))
      {
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          if(!in_array($Row['CHIAVE_MOVIMENTO'], $MovimentiConteggiati))
          {
            if($LastMovimento != $Row['CHIAVE_MOVIMENTO'])
            {
              $LastMovimento    = $Row['CHIAVE_MOVIMENTO'];
              $TotaleAvereAnno += $Row['IMPORTO_MOVIMENTO'];
            }
          }

          if($Row['TIPO_MOVIMENTO'] == 'F')
          {
            $TotaliFattura = TSystemInformation::GetRitenutaFatturaXRate($PDODBase, $Row['CHIAVE_FATTURA']);

            TSystemInformation::GestioneArrotondamentoSaldiAnnualiDerivatoDaErroreArrotondamento($TotaleDareAnno,  $Row['CHIAVE_RATA'], $TotaliFattura[0]);
            TSystemInformation::GestioneArrotondamentoSaldiAnnualiDerivatoDaErroreArrotondamento($TotaleAvereAnno, $Row['CHIAVE_RATA'], $TotaliFattura[0]);

            if($TotaliFattura[0]->TotaleRitenuta != 0)
              $TotaleAvereAnno += $this->FGetTotaleRitenutaRata($TotaliFattura[0], $Row['CHIAVE_RATA']);
            }
          
        }
      }
      else throw new Exception('Impossibile leggere le rate delle fatture attive dal database');

      if($VisualizzaSaldoNelLog)
      {
        error_log("MOVIMENTI ASSOCIATI AD UNA FATTURA O FATTURA PREGRESSA DEL CLIENTE: " . $TotaleDareAnno);
        error_log("MOVIMENTI ASSOCIATI AD UNA FATTURA O FATTURA PREGRESSA DEL CLIENTE: " . $TotaleAvereAnno);
      }

      // FATTURE INSOLUTE PREGRESSE
      $SQLBody = "SELECT CHIAVE          AS CHIAVE_FATTURA, 
                         NUMERO          AS NUMERO_FATTURA, 
                         IMPORTO / 100   AS IMPORTO_PREGRESSA,
                         DATA            AS DATA_FATTURA
                    FROM fatture_insolute_pregresse 
                   WHERE ID_CLIENTE = $ChiaveCliente
                     AND DATA >= '$DataDal' AND DATA <= '$DataAl'";
      
      if($Query = $PDODBase->query($SQLBody))
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
          $TotaleDareAnno += $Row['IMPORTO_PREGRESSA'];
      else throw new Exception('Impossibile leggere le fatture (attive) insolute pregresse');

      if($VisualizzaSaldoNelLog)
      {
        error_log("FATTURE INSOLUTE PREGRESSE: Dare " . $TotaleDareAnno);
        error_log("FATTURE INSOLUTE PREGRESSE: Avere " . $TotaleAvereAnno);
      }

      // PAGAMENTI FATTURE PREGRESSE
      $SQLBody = "SELECT  fatture_insolute_pregresse.CHIAVE          AS CHIAVE_FATTURA, 
                          fatture_insolute_pregresse.NUMERO          AS NUMERO_FATTURA, 
                          fatture_insolute_pregresse.IMPORTO / 100   AS IMPORTO_PREGRESSA, 
                          fatture_insolute_pregresse.DATA            AS DATA_FATTURA,
                          fatture_insolute_pregresse.DATA_PAGAMENTO,
                          fatture_insolute_pregresse.NOTE
                    FROM  fatture_insolute_pregresse
                    WHERE fatture_insolute_pregresse.ID_CLIENTE = $ChiaveCliente
                      AND fatture_insolute_pregresse.ID_MOVIMENTO   IS NULL
                      AND fatture_insolute_pregresse.DATA_PAGAMENTO IS NOT NULL
                      AND fatture_insolute_pregresse.DATA_PAGAMENTO >= '$DataDal' AND fatture_insolute_pregresse.DATA_PAGAMENTO <= '$DataAl'";


      if($Query = $PDODBase->query($SQLBody))
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
          $TotaleAvereAnno += $Row['IMPORTO_PREGRESSA'];
      else throw new Exception('Impossibile leggere i pagamenti delle fatture (attive) insolute pregresse');

      if($VisualizzaSaldoNelLog)
      {
        error_log("PAGAMENTI FATTURE PREGRESSE: Dare " . $TotaleDareAnno);
        error_log("PAGAMENTI FATTURE PREGRESSE: Avere " . $TotaleAvereAnno);
      }

      if($SalvareSaldoNellaTabella)
      {
        $SQLBody = "INSERT INTO saldi_chiusure_annuali (ID_ANAGRAFICA, ANNO, DARE_CHIUSURA, AVERE_CHIUSURA)
                         VALUES ($ChiaveCliente, $OggettoAnno, $TotaleDareAnno * 100, $TotaleAvereAnno * 100)";
      
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

    private function FGestioneArrotondamentoMovimentiDerivatoDaErroreArrotondamento($ChiaveMovimento, $ChiaveRata, $Totale)
    {
      $TotaleRate  = 0;
      $ImportoRata = 0;
      $PrimaRata   = false;
      $NumeroRate  = 0;
      foreach ($Totale->Rate as $Rata) 
      {
        $TotaleRate += $Rata->Importo;
        $NumeroRate++;

        if($ChiaveRata == $Rata->IdRata)
          if($NumeroRate == 1)
            $PrimaRata = true;
      }

      $TotaleRate     = round($TotaleRate, 2);
      if($TotaleRate != $Totale->NettoAPagare && $PrimaRata)
      {
        if(abs(round(($Totale->NettoAPagare - $TotaleRate),2)) <= 0.011)
        {
          if($Totale->NettoAPagare < $TotaleRate)
            for($i = 0; $i < count($this->FRigheProspetto); $i++)
              if($this->FRigheProspetto[$i]->Chiave == $ChiaveMovimento)
                $this->FRigheProspetto[$i]->Importo += 0.01;
          else 
            for($i = 0; $i < count($this->FRigheProspetto); $i++)
              if($this->FRigheProspetto[$i]->Chiave == $ChiaveMovimento)
                $this->FRigheProspetto[$i]->Importo -= 0.01;
        }
      }
    }

    private function FGestioneSaldoIniziale($PDODBase, $DataDal, $DataAl, $ChiaveCliente)
    {
      $DataDalDateTime = new DateTime($DataDal);

      if($DataDalDateTime->format('d-m') == '01-01') 
        return;

      $Anno      = $DataDalDateTime->format('Y');
      $PrimoAnno = "$Anno-01-01";

      $CifraDare  = 0;
      $CifraAvere = 0;
      $OggettoReturn = null;

      $SQLBody = "SELECT saldi_chiusure_annuali.*
                    FROM saldi_chiusure_annuali
                   WHERE saldi_chiusure_annuali.ID_ANAGRAFICA = $ChiaveCliente
                     AND saldi_chiusure_annuali.ANNO = " . ($Anno - 1);

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
        $OggettoReturn = $this->FCalcoloTotaleSaldoCliente("2000-01-01", 
                                                           $PrimoAnno, 
                                                           $Anno - 1, 
                                                           $ChiaveCliente, 
                                                           true, 
                                                           $PDODBase);
        $CifraDare  = $OggettoReturn->TotaleDareAnno;
        $CifraAvere = $OggettoReturn->TotaleAvereAnno;
      }

      $OggettoReturn = $this->FCalcoloTotaleSaldoCliente($PrimoAnno, 
                                                         $DataDal, 
                                                         $Anno - 1, 
                                                         $ChiaveCliente, 
                                                         false, 
                                                         $PDODBase);
      
      $CifraDare  = $CifraDare  + $OggettoReturn->TotaleDareAnno;
      $CifraAvere = $CifraAvere + $OggettoReturn->TotaleAvereAnno;

      $this->DareDiPartenza  = round($CifraDare, 2);
      $this->AvereDiPartenza = round($CifraAvere, 2);

    }

    private function FCalcolaSaldoContabileCliente($PDODBase, $ChiaveCliente)
    {
       $OggettoSaldo = $this->FCalcoloTotaleSaldoCliente(
                                                          "2000-01-01",      
                                                          date('Y-m-d'),         
                                                          date('Y'),
                                                          $ChiaveCliente,
                                                          true,
                                                          $PDODBase);

     $this->TotaleDareCliente  = round($OggettoSaldo->TotaleDareAnno,  2);
     $this->TotaleAvereCliente = round($OggettoSaldo->TotaleAvereAnno, 2);

     $this->SaldoContabileCliente = round($OggettoSaldo->TotaleDareAnno - $OggettoSaldo->TotaleAvereAnno,2);
    }

    protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
    { 
      $Parametri = $this->Parametri;
      $VelocizzaXStampaSaldiClienti = isset($Parametri->VelocizzaXStampaSaldiClienti)? $Parametri->VelocizzaXStampaSaldiClienti : false;
      
      $NomeLogo = '';
      if(!$VelocizzaXStampaSaldiClienti)
      {
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
      }

      if(isset($Parametri->StatoConti) && $Parametri->StatoConti == true )
      {
        $JSONAnswer->LsConti = $this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo);
      }
      else
      {
        $Report = new TReportContoCliente();
        $Report->LoadFromFile('ModelliStampe/QrStampaContoCliente.json');
        $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 
        if($NomeLogo != '')
            unlink(NOME_CARTELLA_FILE_TEMP. '/' . $NomeLogo . '.jpg');
      }
    }
  }