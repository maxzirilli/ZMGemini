
<?php       
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once 'ClassStampaContoCliente.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';
  include_once PATH_LIBRERIE . 'ZGenericFunct.php';
  include_once PATH_LIBRERIE . 'ZReport.php';
  include_once PATH_LIBRERIE . 'ZEconomicFunct.php';

  header("Content-Type: application/json;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST, OPTIONS");  


  class TRigaSaldo 
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

  class TDatiIntestazioneSaldi
  {
    public $DENOMINAZIONE_SOCIETA       = null;
    public $INTESTAZIONE_SOCIETA        = null;
    public $LB_NOME_CAUSALE             = null;
    public $QR_LOGO                     = null;
  }
  
  class TDatiFooterSaldi
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
            $this->FPrimoPrint++;
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

      $DatiIntestazione = new TDatiIntestazioneSaldi();
      $DatiIntestazione->LB_NOME_CAUSALE = 'Fino al ' . date('d/m/Y', strtotime($DataAl));

      TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
      
      if($NomeLogo != '')
        $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

      array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);
      
      
      $DatiFooter = new TDatiFooterSaldi();
      $DatiFooter->LB_TOTALE_SALDO = 0;

      $this->FPrepareParameterValue($DataDal,'#');
      $this->FPrepareParameterValue($DataAl,'#');
      
      $SQLBody = '';
                      //  AND CHIAVE = 482550
      if($Ordinamento == 'A')
        $SQLBody = "SELECT clienti.*
                      FROM clienti
                     WHERE clienti.ATTIVO = 'T'
                  ORDER BY clienti.RAGIONE_SOCIALE";
      else 
        $SQLBody = "SELECT clienti.*
                      FROM clienti
                     WHERE clienti.ATTIVO = 'T'
                     ORDER BY clienti.CODICE_CLIENTE + 0";

      if($Query = $PDODBase->query($SQLBody))
      { 
        while($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $Cliente                  = new TRigaSaldo($Row['CODICE_CLIENTE'],$Row['RAGIONE_SOCIALE']);

          $Parametri                = new stdClass();
          $Parametri->ChiaveCliente = $Row['CHIAVE'];
          $Parametri->DataDal       = $DataDal;
          $Parametri->DataAl        = $DataAl;
          $Parametri->StatoConti    = true;
          $Parametri->VelocizzaXStampaSaldiClienti = true;
          $AConnection              = new TExtraStampaContoCliente($Parametri, true);
          $ResultStampaContoCliente = $AConnection->ServerSideScript(false);

          if(isset($ResultStampaContoCliente->LsConti->TotaleSaldoAttualePerSchedaCliente))
            $Cliente->LB_SALDO = $ResultStampaContoCliente->LsConti->TotaleSaldoAttualePerSchedaCliente;
          
          if(abs($Cliente->LB_SALDO) > 0.015)
          {
            $DatiFooter->LB_TOTALE_SALDO += $Cliente->LB_SALDO;

            $Cliente->LB_SALDO = EuropeanCurrencyFormat($Cliente->LB_SALDO, false);
            
            if($Cliente->LB_SALDO != '0,00')
            {
              $Cliente->LB_SALDO .= ' €';
              array_push($Result->BAND_SUMMARY, $Cliente);
            }
          }
        }
      }

      $DatiFooter->LB_TOTALE_SALDO = number_format($DatiFooter->LB_TOTALE_SALDO, 2, ',', '.') . '€';
      array_push($Result->BAND_FOOTER, $DatiFooter);

      return $Result;
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
      $Report->LoadFromFile('ModelliStampe/QrStampaSaldiContiClienti.json');
      $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

      if($NomeLogo != '')
          unlink(NOME_CARTELLA_FILE_TEMP. '/' . $NomeLogo . '.jpg');  
    }
  }

  $AConnection = new TExtraStampaSaldiClienti();
  $AConnection->ServerSideScript();