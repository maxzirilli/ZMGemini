<?php  
include_once 'Configurations.php';
include_once 'Definitions.php';
include_once 'SystemInformation.php';
include_once PATH_LIBRERIE . 'ZAdvQuery.php';
include_once PATH_LIBRERIE . 'ZGenericFunct.php';
include_once 'ClassForMail.php';

require_once PATH_LIBRERIE . 'mailer_PHP8/src/PHPMailer.php';
require_once PATH_LIBRERIE . 'mailer_PHP8/src/SMTP.php';
require_once PATH_LIBRERIE . 'mailer_PHP8/src/Exception.php';

header("Content-Type: application/json;charset=UTF-8");
header(ACCESS_CONTROLL_SHARED);
header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

class TGeneraPasswordAppCliente extends TAdvQuery
{
  private $Parametri        = null;

  public function __construct($Parametri = null, $ScriptEsterno = false)
  {
    parent::__construct($ScriptEsterno);

    if(isset($Parametri))
      $this->Parametri = $Parametri;
    else $this->Parametri = json_decode($_POST['Params'], true);
  }

  private function FGetTestoMail($Testo)
  {
    $TestoMail = "";
    $TestoMail = file_get_contents('../SendMailTemplateNuovoAccesso.html');
    $TestoMail = str_replace('_TOKEN_TESTO_PASSWORD_',$Testo->TestoPrincipale,$TestoMail);
    $TestoMail = str_replace('_TOKEN_NUOVA_PASSWORD_',$Testo->Password,$TestoMail);
    
    return $TestoMail;
  }

  protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
  {
    
    $LogAppClienti = $this->Parametri;
    
    // $SQLBody = "SELECT utenti.USERNAME
    //               FROM utenti
    //              WHERE utenti.USERNAME = '" . addslashes($LogAppClienti['USERNAME']) . "'";

    // if($Query = $PDODBase->query($SQLBody))
    // {
    //   if ($Query->rowCount() > 0)
    //   {
    //     $JSONAnswer->Risposta = 'USERNAME_GIA_PRESENTE';
    //     return;
    //   }
    // }

    if(!isset($LogAppClienti['PASSWORD']) || trim($LogAppClienti['PASSWORD']) == '')
    {
      $PasswordInChiaro             = $this->GeneraPasswordCasuale(8);
      $JSONAnswer->PasswordGenerata = $PasswordInChiaro;
    }
    else $PasswordInChiaro = $LogAppClienti['PASSWORD'];

    $OggettoMail   = 'Completa la registrazione';

    $TestoMail = new stdClass();
    $TestoMail->TestoPrincipale = 'Accedi con la seguente password per poter proseguire nella registrazione';
    $TestoMail->Password = $PasswordInChiaro;

    if($this->FSendMail('', '', $LogAppClienti['USERNAME'],$OggettoMail, $this->FGetTestoMail($TestoMail), $this->FSmtpFromName, $this->FSmtpFromMail, $Errore, '', true, $PDODBase))
    {
        $JSONAnswer->Risposta = 'MAIL_INVIATA';
    }

    $PasswordCrittata        = $this->FCrittaPassword($PasswordInChiaro);
    $JSONAnswer->Password    = $PasswordCrittata;
  }


  private function GeneraPasswordCasuale($Lunghezza = 8)
  {
    $Caratteri = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $Password  = '';
    $MaxIndex  = strlen($Caratteri) - 1;

    for($i = 0; $i < $Lunghezza; $i++)
      $Password .= $Caratteri[random_int(0, $MaxIndex)];

    return $Password;
  }
}

$Connessione = new TGeneraPasswordAppCliente();
$Connessione->ServerSideScript();
