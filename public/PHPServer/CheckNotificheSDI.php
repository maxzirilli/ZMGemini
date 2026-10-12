<?php
include_once 'Configurations.php';
include_once 'Definitions.php';
include_once 'SystemInformation.php';
include_once PATH_LIBRERIE . 'ZAdvQuery.php';
include_once PATH_LIBRERIE . 'ZFileFunct.php';
require_once PATH_LIBRERIE . 'ZAdvQueryClient.php';

$IsCommandLine = (php_sapi_name() === 'cli');

if(!$IsCommandLine)
{
    header("Content-Type: plain/text;charset=ISO-8859-15");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods:POST,GET");
}

class TCheckNotificheSDI extends TAdvQuery
{
    protected function FExtraScriptIgnoreLogin()
    {
        return true;
    }

    function MapMySondStatusToLetter(int $Stato): string
    {
        switch ($Stato)
        {
            case -1 :     return '';
            case 0  :     return ''; 
            case 1  :
            case 6  :     return STATO_XML_RIFIUTATO; 
            case 7  :     return STATO_XML_INVIATO; 
            case 8  :     return STATO_XML_CASSETTO_FISCALE; 
            case 9  :     return STATO_XML_ACCETTATO_PA; 
            case 10 :     return STATO_XML_RIFIUTATO_PA; 
            case 11 :     return STATO_XML_DECORRENZA_TERMINI; 
            case 12 :
            case 13 :
            case 14 :
            case 15 :
            case 16 :
            case 20 :
            case 22 :
            case 23 :
            case 24 :
            case 25 :
            case 26 :
            case 27 :
            case 28 :
            case 99 :     return STATO_XML_ERRORE_GENERICO;

            default:      if ($Stato >= 11 && $Stato <= 99)
                             return STATO_XML_ERRORE_GENERICO; 

            return STATO_XML_ERRORE_GENERICO;
        }
    }

    protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
    {
        $MySond = TSystemInformation::CreazioneMySond($PDODBase);
        if(!$MySond)
        {
            error_log("Polling SDI: MySond non configurato");
            return;
        }

        $Parametri = ['MYSOND_POLL_LIMIT' => MYSOND_POLL_LIMIT];

        $Sources = [
                       [
                           'Name'         => 'FATTURE',
                           'Query'        => 'SelectXMLFattureInAttesa',
                           'UpdateQuery'  => 'UpdateXMLFatturaNotifica'
                       ],
                       [
                           'Name'         => 'NOTE DI CREDITO',
                           'Query'        => 'SelectXMLNoteInAttesa',
                           'UpdateQuery'  => 'UpdateXMLNotaNotifica'
                       ],
                       [
                           'Name'         => 'AUTOFATTURE',
                           'Query'        => 'SelectXMLAutofattureInAttesa',
                           'UpdateQuery'  => 'UpdateXMLAutofatturaNotifica'
                       ]
                   ];
        
        $MySond = TSystemInformation::CreazioneMySond($PDODBase);
        
        foreach ($Sources as $Origine)
        {
            echo "****************************************\n";
            echo $Origine['Name'] . "\n";
            echo "****************************************\n";
            $Result = $this->FGetQueryResult($PDODBase,'MySond','SelectSQL',$Origine['Query'],$Parametri);

            while ($Row = $Result->fetch(PDO::FETCH_ASSOC))
            {
                $Error = "";
                echo "Elaborazione " . $Row['NOME_FILE'] . "\n";
                $Notifica = $MySond->GetNotifica($Row['NOME_FILE'], $Error);

                if ($Notifica) 
                {
                  $StatoNum     = intval($Notifica['Stato']);
                  $StatoLettera = $this->MapMySondStatusToLetter($StatoNum);

                  $DataNotificaISO = $Notifica['Data'] ?? null;
                  $DataNotifica = null;

                  if (isset($DataNotificaISO)) 
                  {
                      try 
                      {
                          $ADate        = new DateTime($DataNotificaISO);
                          $DataNotifica = $ADate->format('Y-m-d H:i:s');
                      } 
                      catch (Exception $e) 
                      {
                          $DataNotifica = null;
                      }
                  }

                  if ($StatoLettera != '') 
                  {
                    $RicevutaSDI = $Notifica['xmlNotifica'] ?? null;

                    $ParamsUpdate = [
                                        'CHIAVE'        => $Row['CHIAVE'],
                                        'STATUS'        => $StatoLettera,
                                        'STATO_MYSOND'  => $StatoNum,
                                        'RICEVUTA_SDI'  => $Notifica['xmlNotifica'] ?? null,
                                        'DATA_NOTIFICA' => $DataNotifica
                                    ];

                    $this->FGetQueryResult($PDODBase,'MySond','EditSQL',$Origine['UpdateQuery'],$ParamsUpdate);
                  }
                }
            }
        }
    }
}

$AConnection = new TCheckNotificheSDI(true); 
$AConnection->ServerSideScript(false);
