<template>
 <div class="ZMCorpoSchedeDati" style="padding-bottom:90px;">
   <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
              <img v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T' && ATab.Id == 'Generale'" src="@/assets/images/IconeAlbero/ContoCorrente2.png" style="width:40px;height:40px;float:left;margin-top:-9px">  
              <img v-else-if="ATab.Id == 'Generale'" src="@/assets/images/IconeAlbero/Cassa2.png" style="width:40px;height:40px;float:left;margin-top:-9px">               
             </a>
         </li>
       </ul>
   </header>
   <div style="height:5px;"></div>
   <div  v-if="Tabs.ActiveTab == 'Generale'">
   <div class="ZMNuovaRigaScheda">
    <div class="btn-group m-b-sm" style="width:40%;float:right;margin-right:40px" v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'F'">
      <button @click="OnClickContoCassa" type="button" class="btn btn-default" style="width:50%;float:right;">Conto corrente</button>
      <button type="button" class="btn btn-info" style="width:50%;float:right">Cassa</button>
    </div>
    <div class="btn-group m-b-sm" style="width:40%;float:right;margin-right:40px" v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'">
      <button type="button" class="btn btn-info" style="width:50%;float:right">Conto corrente</button>
      <button @click="OnClickContoCassa" type="button" class="btn btn-default" style="width:50%;float:right">Cassa</button>
    </div>
    <br>
   </div>
    <div class="ZMSeparatoreScheda">Descrizione</div>
    <div class="ZMNuovaRigaScheda">
        <input type="text" class="form-control" v-model="SchedaContoCorrente.Dati.DESCRIZIONE" placeholder="Descrizione"/>
        <label v-if="SchedaContoCorrente.Dati.DESCRIZIONE.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
    </div>
    <div class="ZMSeparatoreScheda">Descrizione per stampa prima nota</div>
    <div class="ZMNuovaRigaScheda">
        <input type="text" class="form-control" v-model="SchedaContoCorrente.Dati.DESCRIZIONE_ABBR" placeholder="Descrizione per stampa prima nota"/>
    </div>
      <br>
    <div v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'" class="ZMSeparatoreScheda">Agenzia Bancaria</div>
    <div v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'" class="ZMNuovaRigaScheda">
      <label style="font-weight: bold;">Banca </label>
      <input type="text" class="form-control" v-model="SchedaContoCorrente.Dati.BANCA" placeholder="Banca"/>
    </div>
    <div v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'" class="ZMNuovaRigaScheda">
          <label style="font-weight: bold;">Agenzia </label>
          <input type="text" class="form-control" v-model="SchedaContoCorrente.Dati.AGENZIA" placeholder="Agenzia"/>
    </div>
    <div v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'" class="ZMNuovaRigaScheda">
          <label style="font-weight: bold;">Indirizzo </label>
          <input type="text" class="form-control" v-model="SchedaContoCorrente.Dati.INDIRIZZO" placeholder="Indirizzo"/>
    </div>
    <br v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'">

    <div v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'" class="ZMSeparatoreScheda">Coordinate Bancarie</div>

    <div v-if="SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T'" class="ZMNuovaRigaScheda">
      <div style="float:left;width:25%">
        <label style="font-weight: bold;">Nr. Conto </label>
        <input type="text" maxlength = "12" class="form-control" v-model="SchedaContoCorrente.Dati.NR_CONTO" placeholder="Nr. Conto "/>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left;width:24%">
        <label style="font-weight: bold;">IBAN </label>
        <input type="text" maxlength = "34" class="form-control" v-model="SchedaContoCorrente.Dati.IBAN" placeholder="IBAN"/>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left;width:24%">
        <label style="font-weight: bold;">BIC </label>
        <input type="text"  maxlength = "11" class="form-control" v-model="SchedaContoCorrente.Dati.BIC" placeholder="BIC"/>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left;width:24%">
        <label style="font-weight: bold;">SWIFT </label>
        <input type="text" maxlength = "11" class="form-control" v-model="SchedaContoCorrente.Dati.SWIFT" placeholder="SWIFT"/>
      </div>
      <div style="clear:both;padding-bottom:1%"></div>
    </div>
    <div class="ZMSeparatoreScheda">Disponibilità e Stato Iniziale</div>
    <div class="ZMNuovaRigaScheda">
        <div style="float:left">&nbsp;</div>
          <div style="float:left;width:48%">
          <label style="font-weight: bold;">Data Inizio Sessione </label>
          <input style="width:30%" type="date" class="form-control" v-model="SchedaContoCorrente.Dati.DATA_INIZIO_SESSIONE"/>
          <label v-if="SchedaContoCorrente.Dati.DATA_INIZIO_SESSIONE.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left">&nbsp;</div>
          <div style="float:left;width:48%">
          <label style="font-weight: bold;">Saldo ad inizio Sessione </label>
          <input type="number" step="0.01" style="width:60%" class="form-control" v-model="SchedaContoCorrente.Dati.SALDO_AD_INIZIO_SESSIONE" placeholder="Saldo"/>
          </div>
          </div>
   </div>
 </div>
</template>


<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, DASHBOARD_FILTER_TYPES, RUOLI } from '@/SystemInformation.js'
import { TSchedaAnnoMovimentiVuota } from '@/NodiVuoti'
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'

export class TSchedaContoCorrente extends TSchedaGenerica
 {
  
  OnInitialization()
   {
     
   }

  GetClassName()
   {
     return 'TSchedaContoCorrente';
   }

   CanRecord()
   {
     return this.Dati.DESCRIZIONE.trim() != '' && 
            this.Dati.DATA_INIZIO_SESSIONE.trim() != ''
   }
   
   GetDescrizione()
   {
      if(this.Dati.CONTO_CORRENTE == 'T')
        return  ('Conto Corrente - ' + this.Dati.BANCA)
      else if (this.Dati.CONTO_CORRENTE == 'F')
        return ('Cassa - ' + this.Dati.DESCRIZIONE)
   }

   DatiEliminabili()
   {
      if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
        return false;


      return true;
   }

   Elimina(OnSuccess,OnError)
   {
      this.InEliminazione = true;
      var ObjQuery = { Operazioni : [ 
                                      {
                                        Query     : "Delete",
                                        Parametri : { CHIAVE_CONTO_CORRENTE : this.Chiave }
                                      }
                                    ]};

     this.AdvQuery.PostSQL('MovimentiConti',ObjQuery,function()
     {
        OnSuccess();
     },
     function(HTTPError,SubHTTPError,Response)
     {
       OnError(HTTPError,SubHTTPError,Response);
     });
   }

   GetImageIndex()
   {
     if(this.Dati.CONTO_CORRENTE == 'T') 
       return 'ContoCorrente.png' 
     else if(this.Dati.CONTO_CORRENTE == 'F') 
       return 'Cassa.png'
   }

   CaricaRiassunto(Riassunto)
   {
     if(Riassunto.CONTO_CORRENTE == 'T')
     {
      this.Chiave               = Riassunto.CHIAVE;
      this.Dati.BANCA           = TSchedaGenerica.DisponiFromString(Riassunto.BANCA);
      this.Dati.NR_CONTO        = Riassunto.NR_CONTO;
      this.Dati.CONTO_CORRENTE  = Riassunto.CONTO_CORRENTE
      this.CreateSnapshot();
     }
     else if (Riassunto.CONTO_CORRENTE == 'F')
     {
      this.Chiave               = Riassunto.CHIAVE;
      this.Dati.CONTO_CORRENTE  = Riassunto.CONTO_CORRENTE
      this.Dati.DESCRIZIONE     = TSchedaGenerica.DisponiFromString(Riassunto.DESCRIZIONE)
      this.Dati.DESCRIZIONE_ABBR = TSchedaGenerica.DisponiFromString(Riassunto.DESCRIZIONE_ABBR)
      this.CreateSnapshot();
     }
   }

   Clear()
   {
      this.Dati = { 
                     RAGIONE_SOCIALE           : '',
                     DESCRIZIONE               : '',
                     DESCRIZIONE_ABBR          : '',
                     BANCA                     : '',
                     AGENZIA                   : '',
                     INDIRIZZO                 : '',
                     NR_CONTO                  : '',
                     IBAN                      : '',
                     BIC                       : '',
                     SWIFT                     : '',
                     DATA_INIZIO_SESSIONE      : TZDateFunct.DateInHTMLInputFormat(new Date()),
                     SALDO_AD_INIZIO_SESSIONE  : '',
                     CONTO_CORRENTE            : 'T'                   
      }
      super.Clear();
   }

   Registra(OnSuccess,OnError)
   {
      if(this.CanRecord())
      {
        var ObjQuery = { Operazioni : [] };
        ObjQuery.Operazioni.push({
                                   Query     : this.IsNuovo() ? "Insert" : "Update",
                                   Parametri : {
                                                  CHIAVE                    : this.IsNuovo() ? undefined : this.Chiave, 
                                                  RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(this.Dati.RAGIONE_SOCIALE),
                                                  DESCRIZIONE               : TSchedaGenerica.PrepareForRecordString(this.Dati.DESCRIZIONE),
                                                  DESCRIZIONE_ABBR          : TSchedaGenerica.PrepareForRecordString(this.Dati.DESCRIZIONE_ABBR),
                                                  BANCA                     : TSchedaGenerica.PrepareForRecordString(this.Dati.BANCA),
                                                  AGENZIA                   : TSchedaGenerica.PrepareForRecordString(this.Dati.AGENZIA),
                                                  INDIRIZZO                 : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO),
                                                  NR_CONTO                  : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CONTO),
                                                  IBAN                      : TSchedaGenerica.PrepareForRecordString(this.Dati.IBAN),
                                                  BIC                       : TSchedaGenerica.PrepareForRecordString(this.Dati.BIC),
                                                  SWIFT                     : TSchedaGenerica.PrepareForRecordString(this.Dati.SWIFT),
                                                  DATA_INIZIO_SESSIONE      : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA_INIZIO_SESSIONE),
                                                  SALDO_AD_INIZIO_SESSIONE  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.SALDO_AD_INIZIO_SESSIONE * 100),
                                                  CONTO_CORRENTE            : TSchedaGenerica.PrepareForRecordString(this.Dati.CONTO_CORRENTE),
                                                  
                                               }
                                 });
         var Self = this
         this.AdvQuery.PostSQL('MovimentiConti',ObjQuery,function(Response)
         {
           ObjQuery = {};
           if(Self.Chiave == -1)
              Self.Chiave = Response.NewKey1;
           Self.CreateSnapshot();
           OnSuccess();
         },
         function(HTTPError,SubHTTPError,Response)
         {
           OnError(HTTPError,SubHTTPError,Response);
         });

      }
   }

   Disponi(OnSuccess)
   {
      var Self = this;
      if(this.InEliminazione) return;
      this.AdvQuery.GetSQL('MovimentiConti',{ CHIAVE : Self.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                            if(ArrayInfo != undefined)
                                            {
                                              if(ArrayInfo.length != 0)
                                              {
                                                Self.Dati = { 
                                                              CHIAVE                    : Self.Chiave, 
                                                              RAGIONE_SOCIALE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                                                              DESCRIZIONE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE),
                                                              DESCRIZIONE_ABBR          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE_ABBR) != '' ? TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE_ABBR) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE),
                                                              BANCA                     : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA),
                                                              AGENZIA                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].AGENZIA),
                                                              INDIRIZZO                 : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO),
                                                              NR_CONTO                  : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CONTO),
                                                              IBAN                      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN),
                                                              BIC                       : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC),
                                                              SWIFT                     : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT),
                                                              DATA_INIZIO_SESSIONE      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DATA_INIZIO_SESSIONE),
                                                              SALDO_AD_INIZIO_SESSIONE  : parseFloat((TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].SALDO_AD_INIZIO_SESSIONE) / 100).toFixed(2)),
                                                              CONTO_CORRENTE            : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CONTO_CORRENTE),
                                                               
                                                }
                                                Self.CreateSnapshot();
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Movimento/Conto eliminato')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio del movimento/conto');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectDettaglio')
   }

   BeforeExpand(AnItem,OnSuccess)
   {
      if(this.Chiave != undefined)
      {
        var Self = this
        let Parametri = { ChiaveContoCassa : this.Chiave}
        SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                          function(Results)
                                          {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoMovimenti");
                                              if(ArrayInfo != undefined)
                                              {  
                                                ArrayInfo.forEach(function(AnnoMovimento)
                                                {
                                                    var SchedaAnnoMovimentiVuota = new TSchedaAnnoMovimentiVuota(Self.AdvQuery,Self.Chiave,AnnoMovimento.ANNO);
                                                    var NodeAnno = AnItem.AddChild(SchedaAnnoMovimentiVuota.GetDescrizione(),SchedaAnnoMovimentiVuota)
                                                    NodeAnno.HasChildren = true;
                                                });
                                                OnSuccess();
                                              }
                                              else console.error('Impossibile ottenere la lista delle fatture');
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          },'LsAnniMovimentiXConto')
      }   
   }

   GetFiltriAbilitati(OnSuccess)
   {
      OnSuccess([{
                   Name      : DASHBOARD_FILTER_TYPES.MovimentiConti,
                   Positions : [
                                  this.Chiave
                               ]
              }])
   }

  }

 export default 
 {
    components : 
    {
    },
    data()
    {
      return { 
               Tabs            : {
                                    ActiveTab : 'Generale',
                                    Tabs      : [
                                                  {
                                                    Caption : 'Generale',
                                                    Id      : 'Generale'
                                                  },
                                                ]
                                 }
             };
    },
    props : ['SchedaContoCorrente'],
    computed:
    {
    },
    methods: 
    {
     OnClickContoCassa()
     {
        this.SchedaContoCorrente.Dati.CONTO_CORRENTE = this.SchedaContoCorrente.Dati.CONTO_CORRENTE == 'T' ? 'F' : 'T'
     }
    },
    beforeMount() 
    {
      this.ActiveTab = 'Generale'
    },
 }
</script>

<style scoped>
label {
  margin-bottom: 0px;
}
</style>
