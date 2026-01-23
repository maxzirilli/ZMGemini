<?php
    // Variabile di sessione che contiene l'utente connesso
    define('SESSION_USERKEY','GEMINIUser');
    define('SESSION_DBASE_SERVER','GEMINIDBaseServer');
    define('SESSION_DBASE_PASSWORD','GEMINIDBasePassword');
    define('SESSION_DBASE_ACCOUNT','GEMINIDBaseAccount');
    define('SESSION_DBASE_DATABASE','GEMINIDBaseDatabase');
    define('SESSION_DBASE_CARTELLA_ALLEGATI','GEMINIDBaseCartellaAllegati');
    define('SESSION_RUOLO_ACCOUNT','GEMINIRuoloAccount');
    define('SESSION_ULTIMO_ACCESSO','GEMINIUltimoAccesso');
    define('DEFAULT_PASSWORD','Gemini');
    define('TABLE_UTENTI','utenti');
    define('FIELD_UTENTI_ACCOUNT','USERNAME');
    define('FIELD_UTENTI_PASSWORD','PASSWORD');
    define('FIELD_UTENTI_CHIAVE','CHIAVE');
    define('TABELLA_LOG_ACCESSI','advquery_access');
    define('FIELD_RUOLO_ACCOUNT','ROLE');
    define('CHECK_WHITE_BLACK_LIST', true);

    define('PAR_TOKEN_REMEMBERME',"TokenRememberMe");
    define('PAR_REMEMBER_ME_ENABLED',"GetTokenRememberMe");
    define('PAR_USERNAME','Username');
    define('PAR_PASSWORD','Password');
    define('PAR_NOME_FILE','NomeFile');
    define('PAR_LISTA_FILE_DA_ELIMINARE','LsFileDaEliminare');
    define('PAR_NEW_PASSWORD','NewPassword');
    define('PAR_PARTITA_IVA','PartitaIVA');
    define('PAR_SQL_QUERY','SelectQuery');
    define('PAR_MODEL','Model');
    define('PAR_PARAMETERS','EditParams');
    define('GEN_CHIAVI','gen_key'); 
    define('FIELD_GEN_CHIAVI','CHIAVE');
    define('FIELD_UTENTI_CAMPO_ELIMINATO','ELIMINATO');
    define('PAR_MAIL_BODY','Testo');
    define('PAR_MAIL_SUBJECT','Oggetto');
    define('PAR_SESSION_ID','SessionId');

    define('MAIL_ACCOUNT_AVVISI',0);
    define('MAIL_ACCOUNT_FATTURE',1);
    define('MAIL_ACCOUNT_SOLLECITI',2);


    function Decifra($Cifrato)
    {
       $ChiaveSimmetricaCifrata = file_get_contents('OtherInfos.cyp');
       $ChiavePubblica = file_get_contents('Utilties.bin');
       openssl_public_decrypt($ChiaveSimmetricaCifrata, $ChiaveSimmetricaDecifrata, $ChiavePubblica);

       // Decifrare JSON con la chiave simmetrica
       $ConfigurazioniCrittate = $Cifrato;
       
       $ConfigurazioniDecrittate = openssl_decrypt($ConfigurazioniCrittate, 'aes-256-cbc', $ChiaveSimmetricaDecifrata, OPENSSL_RAW_DATA, 'KenIlGuerriero75');
       return $ConfigurazioniDecrittate;
    }