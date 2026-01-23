<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once 'ClassForMail.php';
      include_once 'SystemInformation.php';

      require_once PATH_LIBRERIE . 'mailer_PHP8/src/PHPMailer.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/SMTP.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/Exception.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TExtraSendMail extends TAdvQueryClassForMail
      {            
        protected function FExtraScriptIgnoreLogin()
        {
          return true;
        }

        private function FGetTestoMail($Testo)
        {
          $TestoMail = "";
          $TestoMail = file_get_contents('../SendMailTemplate.html');
          $TestoMail = str_replace('_TOKEN_NUOVA_PASSWORD_',$Testo,$TestoMail);
          
          return $TestoMail;
        }


        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {            
          $JSONAnswer->MessaggioUtente = false;  
          $Parametri            = json_decode($_POST['Params']);
          $NomeUtente           = $this->FPrepareParameterValue($Parametri->ParametroRecupero,'#');
          $OggettoMail          = NOME_PROGRAMMA . ' - Recupero password';
          $Destinatario         = '';
          $LsAdmin              = [];
          $NewPassword          = GeneraCodiceAlfaNumerico(10);


          
          $SQLBody = "SELECT EMAIL
                        FROM utenti
                        WHERE " . FIELD_UTENTI_ACCOUNT . "=" . $NomeUtente;
                            
          if($Query = $PDODBase->query($SQLBody))
          {
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            {
                  $Destinatario = $Row['EMAIL'];  
            }
            if ($Destinatario == null || $Destinatario == '')
            {
              $JSONAnswer->MessaggioUtente = true;
            }
          }

          $SQLBody = "SELECT EMAIL 
                        FROM utenti
                        WHERE " . FIELD_RUOLO_ACCOUNT . "='A'";

          if($Query = $PDODBase->query($SQLBody))
          {
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
            {
                array_push($LsAdmin,$Row['EMAIL']);
            }
          }      

          $Errore = '';

          if(INVIO_MAIL_ABILITATO)
          {
              $this->GetParameterEmail($PDODBase, MAIL_ACCOUNT_AVVISI);

              if($this->FSendMail('','',$Destinatario,$OggettoMail,$this->FGetTestoMail($NewPassword), $this->FSmtpFromName, $this->FSmtpFromMail,$Errore,'',true, $PDODBase))
              {
                $JSONAnswer->Risposta = 'MAIL_INVIATA';
                $SQLBody = "UPDATE " . TABLE_UTENTI . 
                          " SET PRIMO_ACCESSO = 'T',
                                PASSWORD = '" .  $this->FCrittaPassword($NewPassword) . "'" .
                        " WHERE USERNAME = " . $NomeUtente ; 

                $PDODBase->query($SQLBody);
              }
              else 
              {
                $JSONAnswer->ErroreMail    = $Errore;
                $JSONAnswer->Risposta      = 'ERRORE_INVIO_MAIL';
                $TestoAdmin = "Utente " . $NomeUtente . " ha tentato di recuperare la password, ma l'invio della mail non ha avuto successo.\n\n OGGETTO : " . $OggettoMail;
                
                $this->FSendMail('','', $this->FMailSystemAdministrator,'ERRORE INVIO MAIL [ GEMINI ]',$TestoAdmin, $this->FSmtpFromName, $this->FSmtpFromMail,$Errore,'',false, $PDODBase);
              }
          }
          else $JSONAnswer->Risposta = 'MAIL_INVIATA';
        }
      }

      $AConnection = new TExtraSendMail();
      $AConnection->ServerSideScript();