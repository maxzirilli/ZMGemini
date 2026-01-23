<?php 
include_once 'Configurations.php';
include_once 'Definitions.php';
include_once 'SystemInformation.php';
include_once PATH_LIBRERIE . 'ZAdvQuery.php';

header("Content-Type: application/json;charset=UTF-8");
header(ACCESS_CONTROLL_SHARED);
header("Access-Control-Allow-Methods:POST, OPTIONS");

class TAppClientiListaFatture extends TAdvQuery
{
  protected function FExtraScriptIgnoreLogin()
  {
    return true;
  }

  protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
  {
    $Input = JSON_decode($_POST['Params']);
    $Parametri = [];
    $JSONAnswer->ListaFatture = [];

    $Parametri['ID_CLIENTE'] = $Input->ID_CLIENTE;

    $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                        'AppClientiFatture',
                                        'Fatture',
                                        'DatiFattura',
                                        $Parametri, []);
    $Query = $PDODBase->query($SQLBody);

    $MappaFatture = [];
    $ListaChiavi = '';

    while ($Row = $Query->fetch(PDO::FETCH_ASSOC))
    {
      $Chiave = $Row['CHIAVE'];
      $Oggetto = new stdClass();

      foreach ($Row as $key => $value)
      {
        $Oggetto->$key = $value;
      }

      $MappaFatture[$Chiave] = $Oggetto;
      $ListaChiavi .= $Chiave . ',';
    }

    $ListaChiavi = rtrim($ListaChiavi, ',');

    if ($ListaChiavi !== '')
    {
      $Totali = TSystemInformation::GetTotaliFattura($PDODBase, $ListaChiavi, true); 

      if (is_array($Totali))
      {
        foreach ($Totali as $TotaleObj)
        {
          $Chiave = $TotaleObj->IdFattura ?? $TotaleObj->Chiave ?? $TotaleObj->CHIAVE ?? $TotaleObj->CHIAVE_FATTURA ?? null;
          $NettoAPagare = $TotaleObj->NettoAPagare ?? 0;
          $TotaleDaIncassare = $TotaleObj->TotaliDaIncassare ?? 0;

          if ($Chiave !== null && isset($MappaFatture[$Chiave]))
          {
            $MappaFatture[$Chiave]->TOTALE = number_format($NettoAPagare, 2, ',', '');
            $MappaFatture[$Chiave]->TOTALE_DA_INCASSARE = number_format($TotaleDaIncassare, 2, ',', '');
          }
        }
      }
    }

    $JSONAnswer->ListaFatture = array_values($MappaFatture);
    $JSONAnswer->Response = 0;
  }
}

$Connection = new TAppClientiListaFatture();
$Connection->ServerSideScript();