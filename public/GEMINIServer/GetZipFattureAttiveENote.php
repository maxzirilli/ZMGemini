<?php
  include_once 'Configurations.php';
  include_once 'Definitions.php';
  include_once 'SystemInformation.php';
  include_once PATH_LIBRERIE . 'ZAdvQuery.php';
  include_once PATH_LIBRERIE . 'ZStringFunct.php';
  include_once PATH_LIBRERIE . 'ZFileFunct.php';
        
  header("Content-Type: application/json;charset=UTF-8");
  header(ACCESS_CONTROLL_SHARED);
  header("Access-Control-Allow-Methods:POST, OPTIONS"); 

  class GetZipFattureAttiveENote extends TAdvQuery
  {
    protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
    {
      $Parametri             = json_decode($_POST['Params']);

      $PercorsoZip           = tempnam(sys_get_temp_dir(), 'fatture_') . '.zip';
      $FileZip               = new ZipArchive();
      $ListaFileDaCancellare = [];
      $ChiusuraZipAvvenuta   = false;
      $ContenutoFileLetto    = null;
      $PercorsoEFile         = '';

      $SQLBody               = $this->FGetQueryCompiled($PDODBase, 'EsportaDatiPerGDPR', 'SelectPerZip', 'SelectXMLFattureInviateAlloSDI', (array) $Parametri);
      
      $FileZip->open($PercorsoZip, ZipArchive::CREATE);

      // FATTURE
      if ($Query = $PDODBase->query($SQLBody))
        while ($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $File = tempnam(sys_get_temp_dir(), 'ft_');
          file_put_contents($File, $Row['XML_BODY']);
          $FileZip->addFile($File, 'Fatture/' . $Row['NOME_FILE']);
          
          $ListaFileDaCancellare[] = $File;
        }

      // NOTE DI CREDITO
      $SQLBody               = $this->FGetQueryCompiled($PDODBase, 'EsportaDatiPerGDPR', 'SelectPerZip', 'SelectXMLNoteDiCreditoInviateAlloSDI', (array) $Parametri);

      if ($Query = $PDODBase->query($SQLBody))
        while ($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $File = tempnam(sys_get_temp_dir(), 'nc_');
          file_put_contents($File, $Row['XML_BODY']);
          $FileZip->addFile($File, 'NoteDiCredito/' . $Row['NOME_FILE']);
          
          $ListaFileDaCancellare[] = $File;
        }

      // FATTURE PASSIVE
      $SQLBody                  = $this->FGetQueryCompiled($PDODBase, 'EsportaDatiPerGDPR', 'SelectPerZip', 'SelectFatturePassive', (array) $Parametri);

      if ($Query = $PDODBase->query($SQLBody))
        while ($Row = $Query->fetch(PDO::FETCH_ASSOC))
        {
          $REGEX_MIME                          = '/^data:[^;]+;\/?/';
          $REGEX_P7M                           = '/\.p7m$/i';

          $File                                = tempnam(sys_get_temp_dir(), 'fp_');
          $AllegatoXMLSenzaMime                = preg_replace($REGEX_MIME, '', $Row['ALLEGATO_XML']);
          $NomeFileSenzaEstensioneP7M          = preg_replace($REGEX_P7M, '', $Row['NOME_FILE']);
          $PercorsoEFile                       = FOLDER_BLOB . $AllegatoXMLSenzaMime;

          $ContenutoFileLetto                  = file_get_contents($PercorsoEFile);

          if ($ContenutoFileLetto !== false)
            file_put_contents($File, $ContenutoFileLetto);
          else
            error_log('Impossibile leggere il file ' . $PercorsoEFile);

          $FileZip->addFile($File, 'FatturePassive/' . $NomeFileSenzaEstensioneP7M);
          
          $ListaFileDaCancellare[] = $File;
        }

      $ChiusuraZipAvvenuta = $FileZip->close();

      if ($ChiusuraZipAvvenuta !== true)
        throw new Exception("Errore nella chiusura dello ZIP");

      foreach ($ListaFileDaCancellare as $FileDaCancellare)
        unlink($FileDaCancellare);

      $JSONAnswer->FileZipInBase64 = base64_encode(file_get_contents($PercorsoZip));

      unlink($PercorsoZip);
    }
  }

  $Result = new GetZipFattureAttiveENote();
  $Result->ServerSideScript();
?>