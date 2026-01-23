<?php
    // Configurazione coordinate server SQL
    define('MYSQLSERVER','localhost');
    define('MYSQLDBASE','gemini');
    define('MYSQLACCOUNT', 'root');
    define('MYSQLPASSWORD', '');
    define('FOLDER_BLOB','/ppp/Blobs/');
    define('MULTISERVER_ENABLED', false);
    define('ADV_LOG_MODIFICHE_IN_DBASE',true);

    // Configurazione
    define('PATH_CARTELLA_DOCX','/ppp/Docx/');
    define('PAR_SMTP_DEBUGLEVEL',0);
    
    define('NOME_PROGRAMMA','GEMINI');
    define('INVIO_MAIL_ABILITATO', true);
    // Controllo useragent e continente su token remember me
    define('TOKEN_REMEMBERME_CHECK_UA', false);

    // Abilitazione invio dati già zippati
    define('HTTP_ZIP_ENABLED', true);    
    
    define('DEVELOPER_MODE', true);
    define('MAIL_DEVELOPER', 'misere8000@rencr.com');
    // Path librerie
    define('PATH_LIBRERIE', "/GoogleDrive/Lavoro/Librerie/PHP/");
    
    //mySond
    define('MYSOND_DEBUG', true);
    define('MYSOND_SANDBOX', false);
    define('MYSOND_INVIA_IMMEDIATAMENTE', false);
    
    define('PATH_ACCESSORIE', "/ppp/GeminiVarie/");
    define('PATH_LOG', "/ppp/LogGemini.txt");
    
    define('ADV_LOG_ALL_QUERYS',false);

    // ZMSOFTWARE  - (PUO' TUTTO)
    // GENERICO    - STANDARD
    define('NOTE_INSTALLAZIONE', '{"Cliente" : "ZMSOFTWARE"}');

    // Configurazione access controll
    define('ACCESS_CONTROLL_SHARED', 'Access-Control-Allow-Origin: *');

?>