<?php       
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';
  include_once PATH_LIBRERIE . 'ZGenericFunct.php';
  include_once PATH_LIBRERIE . 'ZReport.php';
  include_once PATH_LIBRERIE . 'ZEconomicFunct.php';

  header("Content-Type: application/json;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  


  class TRiga 
  {
    public $LB_CODICE_CLIENTE        = null;
    public $LB_DESCRIZIONE_CLIENTE   = '';
    public $LB_SALDO                 = 0;

    function __construct($CodiceCliente, $RagioneSociale)
    {
      $this->LB_CODICE_CLIENTE      = $CodiceCliente;
      $this->LB_DESCRIZIONE_CLIENTE = $RagioneSociale;
    }
  }

  class TStampaSaldiClienti
  {
    public $BAND_INTESTAZIONE = array();
    public $BAND_SUMMARY      = array();
    public $BAND_FOOTER       = array();
  }

  class TDatiIntestazione
  {
    public $DENOMINAZIONE_SOCIETA       = null;
    public $INTESTAZIONE_SOCIETA        = null;
    public $LB_NOME_CAUSALE             = null;
    public $QR_LOGO                     = null;
  }

  
  class TDatiFooter
  {
    public $LB_TOTALE_SALDO = 0;
  }

  class TReportSaldoClienti extends TZReport
  {
      private $FReportEnded           = false;
      private $FPrimoPrint            = 0;
      private $FCambioColore          = false;

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
        if($ABand->Name == 'BAND_ELENCO_CLIENTI')
        {
          $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
          $ABand->Color = $Colore;
          $this->FCambioColore = !$this->FCambioColore;
        }
      }
  }

  class TExtraStampaSaldiClienti extends TAdvQuery
  { 
    private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
    {
      $DataDal        = "2000-01-01";
      $DataAl         = $Parametri->DataAl;
      $Ordinamento    = $Parametri->Ordinamento;

      $Result = new TStampaSaldiClienti();

      $DatiIntestazione = new TDatiIntestazione();
      $DatiIntestazione->LB_NOME_CAUSALE = 'Fino al ' . date('d/m/Y', strtotime($DataAl));

      TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
      
      if($NomeLogo != '')
        $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

      array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);
      
      
      $DatiFooter = new TDatiFooter();
      $DatiFooter->LB_TOTALE_SALDO = 0;

      $this->FPrepareParameterValue($DataDal,'#');
      $this->FPrepareParameterValue($DataAl,'#');
      
      try
      {
        $SQLBody = '';
        if($Ordinamento == 'A')
        {
          $SQLBody = "SELECT fornitori.*
                        FROM fornitori
                    ORDER BY fornitori.RAGIONE_SOCIALE";
        }
        else 
          {
          $SQLBody = "SELECT fornitori.*
                        FROM fornitori
                       ORDER BY SUBSTRING(fornitori.CODICE_FORNITORE, 2) + 0";
          }

        if($Query = $PDODBase->query($SQLBody))
        { 
          while($Row = $Query->fetch(PDO::FETCH_ASSOC))
          {
            $Fornitore           = new TRiga($Row['CODICE_FORNITORE'], $Row['RAGIONE_SOCIALE']);
            
            $Fornitore->LB_SALDO = $this->FCalcoloSaldo($Row['CHIAVE'], $DataDal, $DataAl, $PDODBase);
            

            if(abs($Fornitore->LB_SALDO) > 0.015)
            {
              $DatiFooter->LB_TOTALE_SALDO += $Fornitore->LB_SALDO;

              $Fornitore->LB_SALDO = EuropeanCurrencyFormat($Fornitore->LB_SALDO, false);
              
              if($Fornitore->LB_SALDO != '0,00')
              {
                $Fornitore->LB_SALDO .= ' €';
                array_push($Result->BAND_SUMMARY, $Fornitore);
              }
            }
          }
        }

        $DatiFooter->LB_TOTALE_SALDO = number_format($DatiFooter->LB_TOTALE_SALDO, 2, ',', '.') . '€';
        array_push($Result->BAND_FOOTER, $DatiFooter);

        return $Result;
      }
      catch(Exception $e)
      {
        error_log($SQLBody);
        throw $e;
      }
    }

    private function FCalcoloSaldo($ChiaveFornitore, $DataDal, $DataAl, $PDODBase)
    {
      $TotaleSaldoAnno = 0;
 
      // MOVIMENTI ASSOCIATI AL FORNITORE
      $SQLBodyMovimenti = "SELECT movimenti.*,
                                  cat_movimenti.DESCRIZIONE AS DESCR_CATEGORIA
                             FROM movimenti LEFT OUTER JOIN cat_movimenti ON (cat_movimenti.CHIAVE = movimenti.ID_CATEGORIA_MOVIMENTO)
                            WHERE ID_FORNITORE = $ChiaveFornitore
                              AND (movimenti.DATA >= '$DataDal'  AND movimenti.DATA <= '$DataAl' OR 
                                   movimenti.DATA_CHIUSURA >= '$DataDal' AND movimenti.DATA_CHIUSURA <= '$DataAl')";
      
      try
      {
        $Query = $PDODBase->query($SQLBodyMovimenti);

        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
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
            {
              $TotaleSaldoAnno += $Row['IMPORTO'] / 100;

              $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
            }
            else 
            {
              $TotaleSaldoAnno -= $Row['IMPORTO'] / 100;
              
              $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
            }

          if(!is_null($Row['DATA_CHIUSURA']))
            if($Row['DATA_CHIUSURA'] >= $DataDal && $Row['DATA_CHIUSURA'] <= $DataAl)
              if(!$IsDare)
              {
                $TotaleSaldoAnno += $Row['IMPORTO'] / 100;

                $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
              }
              else 
              {
                $TotaleSaldoAnno -= $Row['IMPORTO'] / 100;

                $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
              }
        }
      }
      catch(Exception $e)
      {
        error_log($SQLBodyMovimenti);
        throw $e;
      }


      // FATTURE E NOTE PASSIVE DEL FORNITORE
      $SQLBody = "SELECT fatture_passive.CHIAVE,
                         fatture_passive.IS_FATTURA,
                         fatture_passive.TOTALE_FATTURA
                    FROM fatture_passive
                   WHERE fatture_passive.ID_FORNITORE = $ChiaveFornitore 
                     AND fatture_passive.NUMERO IS NOT NULL
                     AND fatture_passive.DATA >= '$DataDal' AND fatture_passive.DATA <= '$DataAl'";
      
      if($Query = $PDODBase->query($SQLBody))
      { 
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        { 
          if($Row['IS_FATTURA'] == 'T')
            $TotaleSaldoAnno -= $Row['TOTALE_FATTURA'] / 100;
          else $TotaleSaldoAnno += $Row['TOTALE_FATTURA'] / 100;

          $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
        }
      }  
      else throw new Exception('Impossibile leggere le fatture attive dal database...');




      // PAGAMENTI FATTURE E NOTE PASSIVE
      $SQLBody = "SELECT  fatture_passive.CHIAVE,
                          fatture_passive.IS_FATTURA,
                          rate_fatture_passive.IMPORTO / 1000 AS IMPORTO_RATA
                    FROM  fatture_passive
                          LEFT OUTER JOIN rate_fatture_passive  ON fatture_passive.CHIAVE      = rate_fatture_passive.ID_FATTURA_PASSIVA
                          LEFT OUTER JOIN conti_correnti_casse  ON conti_correnti_casse.CHIAVE = rate_fatture_passive.ID_CONTO_CASSA
                          LEFT OUTER JOIN fornitori             ON fornitori.CHIAVE            = fatture_passive.ID_FORNITORE
                   WHERE  rate_fatture_passive.DATA_PAGAMENTO IS NOT NULL
                     AND  rate_fatture_passive.ID_MOVIMENTO IS NULL
                     AND  fatture_passive.ID_FORNITORE = $ChiaveFornitore
                     AND  fatture_passive.NUMERO IS NOT NULL
                     AND  rate_fatture_passive.DATA_PAGAMENTO >= '$DataDal' AND rate_fatture_passive.DATA_PAGAMENTO <= '$DataAl'";

      if($Query = $PDODBase->query($SQLBody))
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          if($Row['IS_FATTURA'] == 'T')
            $TotaleSaldoAnno += $Row['IMPORTO_RATA'];
          else $TotaleSaldoAnno -= $Row['IMPORTO_RATA'];
          
          $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
        }
      else throw new Exception('Impossibile leggere le rate delle fatture attive dal database');


      // MOVIMENTI ASSOCIATI AD UNA FATTURA O FATTURA PREGRESSA DEL FORNITORE
      $SQLBody = "SELECT  movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                          movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                     FROM fatture_passive
                          JOIN rate_fatture_passive ON fatture_passive.CHIAVE = rate_fatture_passive.ID_FATTURA_PASSIVA
                          JOIN movimenti            ON movimenti.CHIAVE       = rate_fatture_passive.ID_MOVIMENTO
                    WHERE fatture_passive.ID_FORNITORE = $ChiaveFornitore
                      AND fatture_passive.NUMERO IS NOT NULL
                      AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'
                UNION 
                   SELECT movimenti.CHIAVE                      AS CHIAVE_MOVIMENTO,
                          movimenti.IMPORTO / 100               AS IMPORTO_MOVIMENTO
                     FROM movimenti             
                          JOIN fatture_insolute_pregresse_fornitori ON fatture_insolute_pregresse_fornitori.ID_MOVIMENTO = movimenti.CHIAVE
                    WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveFornitore
                      AND movimenti.DATA >= '$DataDal' AND movimenti.DATA <= '$DataAl'";

      if($Query = $PDODBase->query($SQLBody))
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $TotaleSaldoAnno += $Row['IMPORTO_MOVIMENTO'];

          $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
        }
      else throw new Exception('Impossibile leggere le rate delle fatture passive dal database');




      // FATTURE INSOLUTE PREGRESSE
      $SQLBody = "SELECT CHIAVE          AS CHIAVE_FATTURA, 
                         NUMERO          AS NUMERO_FATTURA, 
                         IMPORTO / 100   AS IMPORTO_PREGRESSA,
                         DATA            AS DATA_FATTURA
                    FROM fatture_insolute_pregresse_fornitori 
                   WHERE ID_FORNITORE = $ChiaveFornitore
                     AND DATA >= '$DataDal' AND DATA <= '$DataAl'";
      
      if($Query = $PDODBase->query($SQLBody))
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $TotaleSaldoAnno -= $Row['IMPORTO_PREGRESSA'];

          $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
        }
      else throw new Exception('Impossibile leggere le fatture (passive) insolute pregresse');




      // PAGAMENTI FATTURE PREGRESSE
      $SQLBody = "SELECT  fatture_insolute_pregresse_fornitori.CHIAVE          AS CHIAVE_FATTURA, 
                          fatture_insolute_pregresse_fornitori.NUMERO          AS NUMERO_FATTURA, 
                          fatture_insolute_pregresse_fornitori.IMPORTO / 100   AS IMPORTO_PREGRESSA, 
                          fatture_insolute_pregresse_fornitori.DATA            AS DATA_FATTURA,
                          fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO,
                          fatture_insolute_pregresse_fornitori.NOTE
                    FROM  fatture_insolute_pregresse_fornitori
                    WHERE fatture_insolute_pregresse_fornitori.ID_FORNITORE = $ChiaveFornitore
                      AND fatture_insolute_pregresse_fornitori.ID_MOVIMENTO   IS NULL
                      AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO IS NOT NULL
                      AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO >= '$DataDal' AND fatture_insolute_pregresse_fornitori.DATA_PAGAMENTO <= '$DataAl'";


      if($Query = $PDODBase->query($SQLBody))
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $TotaleSaldoAnno += $Row['IMPORTO_PREGRESSA'];

          $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento
        }
      else throw new Exception('Impossibile leggere i pagamenti delle fatture (passive) insolute pregresse');


      $TotaleSaldoAnno = round($TotaleSaldoAnno, 2); //Arrotondamento

      return $TotaleSaldoAnno;
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
      $Report = new TReportSaldoClienti();
      $Report->LoadFromFile('ModelliStampe/QrStampaSaldiContiFornitori.json');
      $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

      if($NomeLogo != '')
          unlink(NOME_CARTELLA_FILE_TEMP. '/' . $NomeLogo . '.jpg');  
    }
  }

  $AConnection = new TExtraStampaSaldiClienti();
  $AConnection->ServerSideScript();