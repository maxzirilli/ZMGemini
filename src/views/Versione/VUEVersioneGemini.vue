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