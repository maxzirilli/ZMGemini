<?php 
      include_once 'ClassStampaFatturaPassiva.php';
            
      $OpzioneInvioEmail = null;
      $AConnection = new TExtraStampaFatturaPassiva(null, $OpzioneInvioEmail);
      $AConnection->ServerSideScript();