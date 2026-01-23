<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once 'ClassForMail.php';

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

           private function FGetTestoMail($Parametri)
           {
             $TestoMail = "";
             $TestoMail = file_get_contents('../SendMailTemplateIscrizione.html');
             $TestoMail = str_replace('_TOKEN_NOME_',$Parametri->Nome,$TestoMail);
             $TestoMail = str_replace('_TOKEN_COGNOME_',$Parametri->Cognome,$TestoMail);
             $TestoMail = str_replace('_TOKEN_EMAIL_',$Parametri->Email,$TestoMail);
             $TestoMail = str_replace('_TOKEN_TELEFONO_',$Parametri->Telefono,$TestoMail);
             
             return $TestoMail;
           }


            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {            
              $JSONAnswer->MessaggioUtente = false;  
              $Parametri            = json_decode($_POST['Params']);
              $OggettoMail          = NOME_PROGRAMMA . ' - Iscrizione al programma';

              $Errore = '';
              
              if(INVIO_MAIL_ABILITATO)
              {
                 $this->GetParameterEmail($PDODBase, MAIL_ACCOUNT_AVVISI);

                 $Destinatario = $this->FMailSystemAdministrator;
                 if($this->FSendMail('','',$Destinatario,$OggettoMail,$this->FGetTestoMail($Parametri), $this->FSmtpFromName, $this->FSmtpFromMail,$Errore,'',true, $PDODBase))
                 {
                    $JSONAnswer->Risposta = 'MAIL_INVIATA';
                 }
                 else 
                 {
                   $JSONAnswer->ErroreMail    = $Errore;
                   $JSONAnswer->Risposta      = 'ERRORE_INVIO_MAIL';
                   $TestoAdmin = "Tentativo di iscrizione su" . NOME_PROGRAMMA ." non andato a buon fine.";
                   
                   $this->FSendMail('','',$Destinatario,'ERRORE INVIO MAIL [ ARTAX ]',$TestoAdmin, $this->FSmtpFromName, $this->FSmtpFromMail,$Errore,'',false, $PDODBase);
                 }
              }
              else $JSONAnswer->Risposta = 'MAIL_INVIATA';
            }
      }

      $AConnection = new TExtraSendMail();
      $AConnection->ServerSideScript();