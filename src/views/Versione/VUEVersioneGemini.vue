<template>
  <div>
     <div class="ZMSeparatoreScheda" 
            style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px;background-color:#2596be">
            Versione software
        </div>
        <div>
          <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.007.000</b>
                </div>
                <div class="col-md-3">
                  16 Luglio 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>NEW: Aggiunta gestione CBI/Ri.Ba. con ABI, CAB, numero conto, configurazioni CBI aziendali ed esportazione file home banking.</p>
                </div>
           </div>
          <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.006.000</b>
                </div>
                <div class="col-md-3">
                  16 Luglio 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>NEW: Aggiunta gestione configurabile dell'invio manuale delle fatture verso SdI con esportazione ZIP.</p>
                </div>
           </div>
          <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.005.000</b>
                </div>
                <div class="col-md-3">
                  10 Luglio 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>NEW: Aggiunta NO PRIMA NOTA nelle rate delle note di credito</p>
                </div>
           </div>
          <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.004.000</b>
                </div>
                <div class="col-md-3">
                  10 Luglio 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>NEW: Aggiunti Sconto 2 e Sconto 3 sulle voci dei documenti.</p>
                </div>
           </div>
             <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.003.000</b>
                </div>
                <div class="col-md-3">
                  10 Luglio 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>NEW: Inserita creazione e gestione delle CONTROPARTITE all'interno del software</p>
                </div>
           </div>
          
          <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.002.000</b>
                </div>
                <div class="col-md-3">
                  22 Giugno 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>CHG: Tolta getquerycompiled e aggiunta la getqueryresult</p>
                     <p>CHG: Cambiata config per il debug mode</p>
                </div>
           </div>
          <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.001.000</b>
                </div>
                <div class="col-md-3">
                  03 Aprile 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>NEW:Aggiunte configurazioni per ip di accesso</p>
                </div>
           </div>
           <div class="col-md-12" style="background-color: #b3dbff;">
                <hr>
                <div class="col-md-3">
                    <b style="color:#2596be; font-size: 20px; font:bold; margin-left:10px">Versione 1.000.000</b>
                </div>
                <div class="col-md-3">
                  23 Febbraio 2026
                </div>
                <div class="col-md-6">
                    <strong>Martina Cabras</strong>
                    <p>Prima versione</p>

                    <strong>Dario Scianna</strong>
                    <p>Fix: Adattamento alla Datable e DataRow nuove</p>
                </div>
            </div>
        </div>
  </div>
</template>
<script>
import { NUMERO_VERSIONE_GEMINI } from '@/SystemInformation.js'
export default 
{
  name  : "VUEVersioneGemini",
  props : ['Altezza'],
  data()
  {
    return {
             Versione : NUMERO_VERSIONE_GEMINI
    }
  },

  methods :
  {

  }
}
</script>
