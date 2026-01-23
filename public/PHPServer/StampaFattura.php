<?php 
      include_once 'ClassStampaFattura.php';
  
      $OpzioneInvioEmail = null;

      $AConnection = new TExtraStampaFattura(null, $OpzioneInvioEmail);
      $AConnection->ServerSideScript();