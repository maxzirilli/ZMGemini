<template>
 <VUEConfirm :Popup="PopupContentOpzioni" :Richiesta="'Annullare le modifiche effettuate?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
 </VUEConfirm>
 <div class="col-md-12">
  <div v-if="ModificheDaApplicare" style="text-align:right;padding-top:2px;padding-bottom:2px">
      <a class="btn btn-s-md btn-success" style="margin-right:20px" @click="Registra()">Conferma</a>
      <a class="btn btn-s-md btn-danger" @click="OnClickAnnulla();">Annulla</a>
  </div>
  <div class="ZMCorpoSchedeDati">
   <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}</a>
         </li>
       </ul>
   </header>
   <div style="height:5px;"></div>
   <div  v-if="Tabs.ActiveTab == 'DatiFiscali'">   
   <div class="col-md-8" style="float:left">
    <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">IVA suggerita [%]
          <input type="number" class="form-control" v-model="Dati.IVASuggerita" step="0.1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Ritenuta d'acconto amministratori
          <input type="number" class="form-control" v-model="Dati.RitenutadAccontoSuggerita" step="0.1"/>
       </label>
      </div>
    </div>
   </div>
   <div class="col-md-8" style="float:left">
    <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Esigibilità IVA
        <VUEInputEsigibilitaIVA v-model="Dati.EsigibilitaIvaSuggerita"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Unità di misura suggerita
            <VUEInputUdm v-model="Dati.UnitaDiMisura" class="form-control" style="cursor:default"/>
       </label>
      </div>
    </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Spese di trasporto suggerite
          <input type="number" class="form-control" v-model="Dati.SpeseDiTrasporto" step="0.01"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Costo del bollo
          <input type="number" class="form-control" v-model="Dati.CostoBollo" step="0.01"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:86%">
       <label style="font-size:14px;">Articolo di legge imposta di bollo
          <input type="text" class="form-control" v-model="Dati.ImpostaDiBollo"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:86%">
       <label style="font-size:14px;">Nota in fattura per superamento soglia per esenzione IVA
          <input type="text" class="form-control" v-model="Dati.NotaFatturaSuperamentoSoglia"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">      
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Giorni del mese in cui la nuova numerazione di una fattura viene impostata alla fine del mese precedente
          <input type="number" class="form-control" step="1" v-model="Dati.NumerazionePrimiGiorni" min="0"/>
       </label>
      </div>
   </div>
  </div>

  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero di mesi che devono passare per ritenere una fattura insoluta per i condomini
          <input type="number" class="form-control" step="1" v-model="Dati.NumeroMesiPerFattureInsolutePerCondomini" min="0"/>
       </label>
      </div>
   </div>

    <div style="float:left; margin-right: 5%;width:40%">
      <label style="font-size:14px;">Numero di mesi che devono passare per ritenere una fattura insoluta per i privati
        <input type="number" class="form-control" step="1" v-model="Dati.NumeroMesiPerFattureInsolutePerPrivati" min="0"/>
      </label>
    </div>
  </div>

   <div style="clear:both"></div>
 </div>



 <div  v-if="Tabs.ActiveTab == 'PrimaNumerazioni'">   
   <div class="col-md-8" style="float:left">
      <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
         <div style="float:left; margin-right: 5%;width:40%">
         <label style="font-size:14px;">Anno messa in opera
            <input type="number" class="form-control" v-model="Dati.AnnoMessaInOpera" min="0"/>
         </label>
         </div>
         <div style="float:left;width:1%">&nbsp;</div>
         <div style="float:left; margin-right: 5%;width:40%">
         <!-- <label style="font-size:14px;">Ritenuta d'acconto amministratori
            <input type="number" class="form-control" v-model="Dati.RitenutadAccontoSuggerita" step="0.1"/>
         </label> -->
         </div>
      </div>
   </div>
   <div class="col-md-8" style="float:left">
    <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno primo doc scarico prodotti
          <input type="number" class="form-control" v-model="Dati.AnnoPrimoDocScaricoProd" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero primo doc scarico prodotti
          <input type="number" class="form-control" v-model="Dati.NumeroPrimoDocScaricoProd" step="1"/>
       </label>
      </div>
   </div>
  </div>
        <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno prima fattura
          <input type="number" class="form-control" v-model="Dati.AnnoPrimaFattura" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero prima fattura
          <input type="number" class="form-control" v-model="Dati.NumeroPrimaFattura" step="1"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno prima fattura banco
          <input type="number" class="form-control" v-model="Dati.AnnoPrimaFatturaBanco" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero prima fattura banco
          <input type="number" class="form-control" v-model="Dati.NumeroPrimaFatturaBanco" step="1"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno prima nota di credito
          <input type="number" class="form-control" v-model="Dati.AnnoPrimaNotaDiCredito" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero prima nota di credito
          <input type="number" class="form-control" v-model="Dati.NumeroPrimaNotaDiCredito" step="1"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno prima conferma d'ordine
          <input type="number" class="form-control" v-model="Dati.AnnoPrimoPreventivo" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero prima conferma d'ordine
          <input type="number" class="form-control" v-model="Dati.NumeroPrimoPreventivo" step="1"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno primo DDT
          <input type="number" class="form-control" v-model="Dati.AnnoPrimoDDT" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero primo DDT
          <input type="number" class="form-control" v-model="Dati.NumeroPrimoDDT" step="1"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno primo preventivo
          <input type="number" class="form-control" v-model="Dati.AnnoPrimoPrevMulti" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero primo preventivo
          <input type="number" class="form-control" v-model="Dati.NumeroPrimoPrevMulti" step="1"/>
       </label>
      </div>
   </div>
  </div>
  <div class="col-md-8" style="float:left" v-if="VisibilitaNotaDiDebito">
   <div style="padding-top: 25px;" class="ZMNuovaRigaScheda">
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Anno prima nota di debito
          <input type="number" class="form-control" v-model="Dati.AnnoPrimaNotaDebito" maxlength="4" min="1900" max="2100" step="1"/>
       </label>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left; margin-right: 5%;width:40%">
       <label style="font-size:14px;">Numero prima nota di debito
          <input type="number" class="form-control" v-model="Dati.NumeroPrimaNotaDebito" step="1"/>
       </label>
      </div>
   </div>
  </div>
  <div style="clear:both"></div>
 </div>

   <div  v-if="Tabs.ActiveTab == 'LogoAzienda'" style="height:300px">   
      <div style="clear:both; height:50px"></div>
      <div style="float:left;margin-left:30px;margin-top:70px">
         <label class="btn" type="button" style="cursor:pointer;width:100%;background-color:white; width:200px">Inserisci immagine         
               <input style="display:none;" type="file" id="inputLogoAzienda" @change="LoadImmagineFromFile(elemento)" accept="image/jpeg">
         </label>                     
      </div>
      <div href="javascript:;" v-if="Dati.LogoAzienda != ''" style="float:left;margin-left:50px">
         <img style="height:300; max-height:300; max-width:300; min-height:300; min-width:300" :src="Dati.LogoAzienda" class="image">
      </div>
   </div>
 </div>
 </div>
</template>

<script>
import { SystemInformation } from '@/SystemInformation';
import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
import VUEInputUdm from '@/components/InputComponents/VUEInputUdm.vue';
import { TZImageFunct } from '@/../../../../../../Librerie/VUE/ZImageFunct.js'
import VUEConfirm from '@/components/VUEConfirm.vue';

export default 
{
  props : ['Altezza'],
  data()
  {
    return {
             Dati : {
                      IVASuggerita                             : 22,
                      RitenutadAccontoSuggerita                : 0,
                      EsigibilitaIvaSuggerita                  : -1,
                      SpeseDiTrasporto                         : 0,
                      CostoBollo                               : 0,
                      ImpostaDiBollo                           : '',
                      NotaFatturaSuperamentoSoglia             : '',
                      UnitaDiMisura                            : -1,
                      LogoAzienda                              : '',
                      NumerazionePrimiGiorni                   : 1,
                      NumeroMesiPerFattureInsolutePerPrivati   : 0,
                      NumeroMesiPerFattureInsolutePerCondomini : 0,
                      AnnoMessaInOpera                         : (new Date()).getFullYear(),
                      AnnoPrimaFattura                         : '',
                      NumeroPrimaFattura                       : '',
                      AnnoPrimaFatturaBanco                    : '',
                      AnnoPrimoDocScaricoProd                  : '',
                      NumeroPrimoDocScaricoProd                : '',
                      NumeroPrimaFatturaBanco                  : '',
                      AnnoPrimoPreventivo                      : '',
                      NumeroPrimoPreventivo                    : '',
                      AnnoPrimoDDT                             : '',
                      NumeroPrimoDDT                           : '',
                      AnnoPrimaNotaDiCredito                   : '',
                      NumeroPrimaNotaDiCredito                 : '',
                      AnnoPrimoPrevMulti                       : '',
                      NumeroPrimoPrevMulti                     : '',
                      AnnoPrimaNotaDebito                      : '',
                      NumeroPrimaNotaDebito                    : '',
                    },
             Snapshot : '',
             Tabs            : {
                                    ActiveTab : 'DatiFiscali',
                                    Tabs      : [
                                                  {
                                                    Caption : 'Dati fiscali',
                                                    Id      : 'DatiFiscali'
                                                  },
                                                  {
                                                    Caption : 'Prime numerazioni',
                                                    Id      : 'PrimaNumerazioni'
                                                  },
                                                  {
                                                    Caption : 'Logo azienda',
                                                    Id      : 'LogoAzienda'
                                                  }
                                                ]
                                 },
                         

             PopupContentOpzioni    : false,
             VisibilitaNotaDiDebito : SystemInformation.AccessRights.VisibilitaNotaDiDebito(),
           }
  },
  computed :
  {
     ModificheDaApplicare : 
     {
        get()
        {
          if(this.Snapshot != '')
            return this.Snapshot != JSON.stringify(this.Dati)
          return false
        }
     },

  },
  components : 
  {
    VUEInputEsigibilitaIVA, VUEInputUdm, VUEConfirm
  },
  methods:
  {
    Registra()
    {
      var Self = this;
      var ObjQuery = { 
                         Operazioni : [
                                        {
                                            Query     : "Update",
                                            Parametri : {
                                                             IVA_SUGGERITA                                   : Math.round(Self.Dati.IVASuggerita * 10),
                                                             RITENUTA_ACCONTO                                : Math.round(Self.Dati.RitenutadAccontoSuggerita * 10),
                                                             ESIGIBILITA_IVA                                 : Self.Dati.EsigibilitaIvaSuggerita,
                                                             ANNO_MESSA_IN_OPERA                             : Self.Dati.AnnoMessaInOpera,
                                                             SPESE_DI_TRASPORTO_SUGGERITE                    : Self.Dati.SpeseDiTrasporto * 100,
                                                             COSTO_BOLLO_SUGGERITO                           : Self.Dati.CostoBollo * 100,
                                                             IMPOSTA_DI_BOLLO_IMPOSTAZIONI                   : Self.Dati.ImpostaDiBollo,
                                                             NOTA_PER_SUPERAMENTO_SOGLIA_ESENZIONE_IVA       : Self.Dati.NotaFatturaSuperamentoSoglia,
                                                             UNITA_DI_MISURA_SUGGERITA                       : Self.Dati.UnitaDiMisura,
                                                             LOGO_AZIENDA                                    : Self.Dati.LogoAzienda,
                                                             NUMERAZIONE_PRIMI_GIORNI                        : Self.Dati.NumerazionePrimiGiorni,
                                                             NUMERO_MESI_PER_FATTURE_INSOLUTE_PER_CONDOMINI  : Self.Dati.NumeroMesiPerFattureInsolutePerCondomini,
                                                             NUMERO_MESI_PER_FATTURE_INSOLUTE_PER_PRIVATI    : Self.Dati.NumeroMesiPerFattureInsolutePerPrivati,
                                                             ANNO_PRIMA_FATTURA                              : Self.Dati.AnnoPrimaFattura,
                                                             NUMERO_PRIMA_FATTURA                            : Self.Dati.NumeroPrimaFattura,
                                                             ANNO_PRIMA_NOTA_DI_CREDITO                      : Self.Dati.AnnoPrimaNotaDiCredito,
                                                             NUMERO_PRIMA_NOTA_DI_CREDITO                    : Self.Dati.NumeroPrimaNotaDiCredito,
                                                             ANNO_PRIMA_FATTURA_BANCO                        : Self.Dati.AnnoPrimaFatturaBanco,
                                                             NUMERO_PRIMA_FATTURA_BANCO                      : Self.Dati.NumeroPrimaFatturaBanco,
                                                             ANNO_PRIMO_PREVENTIVO                           : Self.Dati.AnnoPrimoPreventivo,
                                                             NUMERO_PRIMO_PREVENTIVO                         : Self.Dati.NumeroPrimoPreventivo,
                                                             ANNO_PRIMO_DDT                                  : Self.Dati.AnnoPrimoDDT,
                                                             NUMERO_PRIMO_DDT                                : Self.Dati.NumeroPrimoDDT,
                                                             ANNO_PRIMO_PREVENTIVO_MULTI                     : Self.Dati.AnnoPrimoPrevMulti,
                                                             NUMERO_PRIMO_PREVENTIVO_MULTI                   : Self.Dati.NumeroPrimoPrevMulti,
                                                             ANNO_PRIMA_NOTA_DEBITO                          : Self.Dati.AnnoPrimaNotaDebito,
                                                             NUMERO_PRIMA_NOTA_DEBITO                        : Self.Dati.NumeroPrimaNotaDebito,
                                                             NUMERO_PRIMO_DOC                                : Self.Dati.NumeroPrimoDocScaricoProd,
                                                             ANNO_PRIMO_DOC                                  : Self.Dati.AnnoPrimoDocScaricoProd
                                                       }
                                        }
                                      ] 
                     };
      SystemInformation.AdvQuery.PostSQL('Impostazioni',ObjQuery,function()
      {
        SystemInformation.GetConfigurazioni(function()
        {
          ObjQuery = {};
          Self.Annulla();
        })
      },
      function(HTTPError,SubHTTPError,Response)
      {
        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
      });
    },

    LoadImmagineFromFile()
    {
      var Self    = this;
      var Reader  = new FileReader()
      var ImgFile = document.getElementById('inputLogoAzienda')
      
      var TypeImg = ImgFile.files[0].type
      Reader.readAsDataURL(ImgFile.files[0])

      Reader.onload = function (e) 
      {
         var Img = new Image()
         Img.onload = function () 
         {
            Img.onload = undefined;
            TZImageFunct.ResizeImage(Img,300,300,TypeImg) 
            Self.Dati.LogoAzienda = Img.src 
         }
         Img.src = e.target.result
      }        
    },

    ConfermaElimina()
    {
       this.Annulla()
       this.AnnullaElimina()
    },
    AnnullaElimina()
    {
       this.PopupContentOpzioni = false
    },
    OnClickAnnulla()
    {
       this.PopupContentOpzioni = true
    },
    Annulla()
    {
       var Self = this;
       SystemInformation.AdvQuery.GetSQL("Impostazioni",{},
                                         function(Results)
                                         {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Impostazioni");                                           
                                           Self.Dati.IVASuggerita                             = parseInt(ArrayInfo[0].IVA_SUGGERITA) / 10;
                                           Self.Dati.RitenutadAccontoSuggerita                = parseInt(ArrayInfo[0].RITENUTA_ACCONTO) / 10
                                           Self.Dati.EsigibilitaIvaSuggerita                  = ArrayInfo[0].ESIGIBILITA_IVA
                                           Self.Dati.AnnoMessaInOpera                         = ArrayInfo[0].ANNO_MESSA_IN_OPERA,
                                           Self.Dati.AnnoPrimaFattura                         = ArrayInfo[0].ANNO_PRIMA_FATTURA
                                           Self.Dati.NumeroPrimaFattura                       = ArrayInfo[0].NUMERO_PRIMA_FATTURA,
                                           Self.Dati.AnnoPrimaNotaDiCredito                   = ArrayInfo[0].ANNO_PRIMA_NOTA_DI_CREDITO
                                           Self.Dati.NumeroPrimaNotaDiCredito                 = ArrayInfo[0].NUMERO_PRIMA_NOTA_DI_CREDITO
                                           Self.Dati.SpeseDiTrasporto                         = parseInt(ArrayInfo[0].SPESE_DI_TRASPORTO_SUGGERITE) / 100
                                           Self.Dati.CostoBollo                               = parseInt(ArrayInfo[0].COSTO_BOLLO_SUGGERITO) / 100
                                           Self.Dati.ImpostaDiBollo                           = ArrayInfo[0].IMPOSTA_DI_BOLLO_IMPOSTAZIONI
                                           Self.Dati.NotaFatturaSuperamentoSoglia             = ArrayInfo[0].NOTA_PER_SUPERAMENTO_SOGLIA_ESENZIONE_IVA,
                                           Self.Dati.UnitaDiMisura                            = ArrayInfo[0].UNITA_DI_MISURA_SUGGERITA,
                                           Self.Dati.LogoAzienda                              = ArrayInfo[0].LOGO_AZIENDA
                                           Self.Dati.NumerazionePrimiGiorni                   = ArrayInfo[0].NUMERAZIONE_PRIMI_GIORNI,
                                           Self.Dati.AnnoPrimaFatturaBanco                    = ArrayInfo[0].ANNO_PRIMA_FATTURA_BANCO,
                                           Self.Dati.NumeroPrimaFatturaBanco                  = ArrayInfo[0].NUMERO_PRIMA_FATTURA_BANCO,
                                           Self.Dati.NumeroMesiPerFattureInsolutePerCondomini = ArrayInfo[0].NUMERO_MESI_PER_FATTURE_INSOLUTE_PER_CONDOMINI
                                           Self.Dati.NumeroMesiPerFattureInsolutePerPrivati   = ArrayInfo[0].NUMERO_MESI_PER_FATTURE_INSOLUTE_PER_PRIVATI
                                           Self.Dati.AnnoPrimoDDT                             = ArrayInfo[0].ANNO_PRIMO_DDT,
                                           Self.Dati.NumeroPrimoDDT                           = ArrayInfo[0].NUMERO_PRIMO_DDT,
                                           Self.Dati.AnnoPrimoPreventivo                      = ArrayInfo[0].ANNO_PRIMO_PREVENTIVO,
                                           Self.Dati.NumeroPrimoPreventivo                    = ArrayInfo[0].NUMERO_PRIMO_PREVENTIVO,
                                           Self.Dati.AnnoPrimoPrevMulti                       = ArrayInfo[0].ANNO_PRIMO_PREVENTIVO_MULTI,
                                           Self.Dati.NumeroPrimoPrevMulti                     = ArrayInfo[0].NUMERO_PRIMO_PREVENTIVO_MULTI,
                                           Self.Dati.AnnoPrimaNotaDebito                      = ArrayInfo[0].ANNO_PRIMA_NOTA_DEBITO;
                                           Self.Dati.NumeroPrimaNotaDebito                    = ArrayInfo[0].NUMERO_PRIMA_NOTA_DEBITO,
                                           Self.Dati.NumeroPrimoDocScaricoProd                = ArrayInfo[0].NUMERO_PRIMO_DOC,
                                           Self.Dati.AnnoPrimoDocScaricoProd                  = ArrayInfo[0].ANNO_PRIMO_DOC

                                           Self.Snapshot = JSON.stringify(Self.Dati);
                                          },
                                         function(HTTPError,SubHTTPError,Response)
                                         {
                                           SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                         });
    },

  },
  beforeMount() 
  {
     this.Annulla()
  },
   
}
</script>
