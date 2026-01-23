<template>
 <div class="ZMCorpoSchedeDati">
   <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">
              <img src="@/assets/images/IconeAlbero/Amministratore2.png" style="width:40px;height:40px;float:left;margin-top:-11px">                                
              {{ATab.Caption}}
             </a>
         </li>
       </ul>
   </header>
   <div style="height:5px;"></div>
   <div  v-if="Tabs.ActiveTab == 'SchedaAmministratore'">
    <div class="ZMNuovaRigaScheda">
      <div style="clear:both;padding-top: 6px;">
        <div style="float:left;width:10%">
            <label style="font-weight: bold;">Titolo</label>
            <select class="form-control" v-model="SchedaAmministratore.Dati.ID_TITOLO">
              <option :selected="SchedaAmministratore.Dati.ID_TITOLO == -1" value="-1">-</option>
              <option v-for="SelectOption in LsTitoli" 
                      :Key="SelectOption.CHIAVE"
                      :value="SelectOption.CHIAVE"
                      :selected="SelectOption.CHIAVE == SchedaAmministratore.Dati.ID_TITOLO">
                      {{SelectOption.DESCRIZIONE}}
                </option>
            </select>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:39%">
          <label style="font-weight: bold;">Ragione sociale </label>
          <input type="text" class="form-control" v-model="SchedaAmministratore.Dati.RAGIONE_SOCIALE" placeholder="Ragione sociale"/>
          <label v-if="SchedaAmministratore.Dati.RAGIONE_SOCIALE.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:11%">
        <label style="font-weight: bold;">Fatturazione</label>
        <select class="form-control" v-model="SchedaAmministratore.Dati.TIPO_FATTURAZIONE">
              <option v-for="SelectOption in TipiFatturazione" 
                      :Key="SelectOption.Id"
                      :value="SelectOption.Id"
                      :selected="SelectOption.Id == SchedaAmministratore.Dati.TIPO_FATTURAZIONE">
                      {{SelectOption.Descrizione}} 
              </option>
          </select>
        </div>
        <div style="float:left;width:1%"> &nbsp;</div>
        <div style="float:left;width:6%">
          <label style="font-weight: bold;">Data fatt.</label>
          <input type="number" min="1" step="1" max="31"  class="form-control" style="width:100%" v-model="SchedaAmministratore.Dati.GIORNO_FATTURAZIONE" @change="OnChangeMeseFatturazione">
        </div>
        <div style="float:left;width:1%"> &nbsp;</div>
        <div style="float:left;width:10%">
          <label style="font-weight: bold;">&nbsp;</label>
          <VUEInputMese v-model="SchedaAmministratore.Dati.MESE_FATTURAZIONE" @change="OnChangeMeseFatturazione"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:19%">
            <label style="font-weight: bold;">Zona</label>
            <VUEInputZone v-model="SchedaAmministratore.Dati.ZONA_FATTURAZIONE"/>
        </div> 
      </div>
      <div class="ZMNuovaRigaScheda">
      </div>
      <div style="clear:both">
          <div style="float:left;width:33%">
              <label style="font-weight: bold;">Indirizzo</label>
              <input type="text" class="form-control" v-model="SchedaAmministratore.Dati.INDIRIZZO_FATTURAZIONE" placeholder="Indirizzo"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:8%">
              <label style="font-weight: bold;">Civico</label>
              <input maxlength="7" type="text" class="form-control" v-model="SchedaAmministratore.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:7%">
              <label style="font-weight: bold;">CAP</label>
              <VUEInputCAP v-model="SchedaAmministratore.Dati.CAP_FATTURAZIONE"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:29%">
              <label style="font-weight: bold;">Comune</label>
              <input type="text" class="form-control" v-model="SchedaAmministratore.Dati.COMUNE_FATTURAZIONE" placeholder="Comune"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:19%">
              <label style="font-weight: bold;">Provincia</label>
              <VUEInputProvince v-model="SchedaAmministratore.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true"/>
          </div> 
      </div>

      <div class="ZMNuovaRigaScheda">
          <div style="float:left;width:23%">
            <label style="font-weight: bold;">Nazione</label>
            <VUEInputNazioni v-model="SchedaAmministratore.Dati.NAZIONE_FATTURAZIONE" emptyElement="true"/>
          </div>
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:9%">
              <label style="font-weight: bold;">Codice SDI</label>
              <input type="text" class="form-control" v-model="SchedaAmministratore.Dati.COD_ENTE_SDI" placeholder="Cod. SDI" maxlength="7"/>
              <label v-if="SchedaAmministratore.Dati.COD_ENTE_SDI.trim().length != 7" class="ZMFormLabelError">
               {{(SchedaAmministratore.Dati.COD_ENTE_SDI.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
              </label>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:12%">
              <label style="font-weight: bold;">Cod. uff. dest.</label>
              <input type="text" class="form-control" 
                     v-model="SchedaAmministratore.Dati.COD_UFFICIO_DEST"
                     placeholder="Codice ufficio destinazione"
                     maxlength="6"/>
              <label v-if="SchedaAmministratore.Dati.COD_UFFICIO_DEST.length != 6 && SchedaAmministratore.Dati.COD_UFFICIO_DEST != ''" class="ZMFormLabelError">Campo non valido
              </label>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:53%">
            <label style="font-weight: bold;">PEC</label>
            <input type="email" class="form-control" v-model="SchedaAmministratore.Dati.PEC" placeholder="PEC"/>
          </div>
      </div>
    </div>
    <div class="ZMNuovaRigaScheda">
      <div class="col-md-9" style="padding:0px;float:left">
        <div style="float:left;width:44%">
            <label style="font-weight: bold;">Giorni di apertura</label>
            <input class="form-control" type="text" v-model="SchedaAmministratore.Dati.GIORNO_APERTURA" placeholder="Giorno di apertura"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Dalle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.DALLE_ORE"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Alle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.ALLE_ORE"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Dalle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.DALLE_ORE2"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Alle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.ALLE_ORE2"/>
        </div> 

       <div class="ZMNuovaRigaScheda">
        <div style="float:left;width:44%">
            <label style="font-weight: bold;">Giorni di apertura</label>
            <input class="form-control" type="text" v-model="SchedaAmministratore.Dati.GIORNO_APERTURA2" placeholder="Giorno di apertura"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Dalle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.DALLE_ORE3"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Alle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.ALLE_ORE3"/>
        </div>
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Dalle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.DALLE_ORE4"/>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:13%">
            <label style="font-weight: bold;">Alle ore</label>
            <input class="form-control" type="time" v-model="SchedaAmministratore.Dati.ALLE_ORE4"/>
        </div>        
       </div>
      </div>
      <div class="col-md-3" style="padding:0px;float:left">
        <div style="float:left;width:3%;">&nbsp;</div>
        <div style="float:left;width:97%;height:100%">
          <label style="font-weight: bold;">Avvisi amministratore</label>
          <textarea style="height:80px;resize:none" class="form-control" v-model="SchedaAmministratore.Dati.AVVISO_AMMINISTRATORE" placeholder="Avvisi amministratore"/>
        </div>
      </div>
    </div>
    <div class="ZMNuovaRigaScheda">
      <div style="float:left;width:50%;">
       <div class="col-md-12" style="padding:0px">
        <label style="font-weight: bold;">Note</label>
        <textarea style="resize:none" 
                  class="form-control" 
                  @input="SchedaAmministratore.CheckDimensioniNote" 
                  :style="{height : SchedaAmministratore.AltezzaTextAreaNote? SchedaAmministratore.AltezzaTextAreaNote : '34px'}"
                  v-model="SchedaAmministratore.Dati.NOTE" />
       </div>
     </div>
     <div style="float:left;width:1%;">&nbsp;</div>
     <div style="float:left;width:49%;">
       <div class="col-md-12" style="padding:0px">
        <label style="font-weight: bold;">Note nascoste</label>
        <textarea style="resize:none" 
                  class="form-control" 
                  @input="SchedaAmministratore.CheckDimensioniNote" 
                  :style="{height : SchedaAmministratore.AltezzaTextAreaNoteNascoste? SchedaAmministratore.AltezzaTextAreaNoteNascoste : '34px'}"
                  v-model="SchedaAmministratore.Dati.NOTE_NASCOSTE" />
       </div>
     </div>
    </div>
    <div style="clear:both;height:2%">&nbsp;</div>
    <div class="ZMNuovaRigaScheda">
      <div class="ZMSeparatoreScheda">Recapiti</div>
      <VUERecapiti :SchedaRecapiti="SchedaAmministratore.SchedaRecapiti" @onChange="OnRecapitiChange"></VUERecapiti>
    </div>
   </div>

  
   <footer class="ZMNuovaRigaScheda" style="height: 2px;">&nbsp;</footer>
 </div>
</template>

<script>
 import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
 import { SystemInformation, DASHBOARD_FILTER_TYPES, TIPI_FATTURAZIONE, RUOLI } from '@/SystemInformation.js'
 import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
 import VUEInputZone from '@/components/InputComponents/VUEInputZone.vue';
 import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
 import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
 import VUEInputMese from '@/components/InputComponents/VUEInputMese.vue';
 import VUERecapiti, {TSchedaRecapiti} from '@/views/SchedeDatabase/ComponentMultiScheda/VUERecapiti.vue';
 import { TSchedaCliente } from '@/views/SchedeDatabase/VUESchedaCliente.vue'
 import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'


 export class TSchedaAmministratore extends TSchedaGenerica
 {
   AltezzaTextAreaNote         = null
   AltezzaTextAreaNoteNascoste = null

   OnInitialization()
   {
      this.SchedaRecapiti  = new TSchedaRecapiti(false, true,false,true)
      this.SchedaRecapiti.ClearSchedaRecapiti()
   }

   CheckDimensioniNote()
   {
     let NrRigheNote          = this.Dati.NOTE != null?            this.Dati.NOTE.split("\n").length : 1; 
     let NrRigheNoteInFattura = this.Dati.NOTE_NASCOSTE != null?   this.Dati.NOTE_NASCOSTE.split("\n").length : 1; 
 
     this.AltezzaTextAreaNote          = NrRigheNote          <= 1 ? '34px' : 
                                        (NrRigheNote          > 7 ? (6 * 22 + 10) + 'px' : (NrRigheNote * 22 + 10) + 'px')
     this.AltezzaTextAreaNoteNascoste  = NrRigheNoteInFattura <= 1 ? '34px' : 
                                         (NrRigheNoteInFattura > 7 ? (6 * 22 + 10) + 'px' : (NrRigheNoteInFattura * 22 + 10) + 'px')
   }

   GetClassName()
   {
     return 'TSchedaAmministratore';
   }

   CanRecord()
   {
     return this.Dati.RAGIONE_SOCIALE.trim() != '' && 
            this.Dati.COD_ENTE_SDI.trim().length == 7 &&
            this.SchedaRecapiti.DataTableTelefono.AllDataOk() && 
            (this.Dati.COD_UFFICIO_DEST.length == 6 || this.Dati.COD_UFFICIO_DEST == '')
   }

   GetDescrizione()
   {
      return this.Dati.RAGIONE_SOCIALE;
   }

   GetImageIndex()
   {
      return 'Amministratore.png'
   } 

   Registra(OnSuccess,OnError)
   {
      var Self = this
      if(this.CanRecord())
      {
              var ObjQuery = { Operazioni : [] };
              ObjQuery.Operazioni.push({
                                    Query     : Self.IsNuovo() ? "Insert" : "Update",
                                    Parametri : {
                                                    CHIAVE_AMMINISTRATORE     : Self.IsNuovo() ? undefined : Self.Chiave,
                                                    RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(Self.Dati.RAGIONE_SOCIALE),
                                                    MESE_FATTURAZIONE         : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.MESE_FATTURAZIONE),
                                                    GIORNO_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.GIORNO_FATTURAZIONE),
                                                    TIPO_FATTURAZIONE         : TSchedaGenerica.PrepareForRecordString(Self.Dati.TIPO_FATTURAZIONE),
                                                    COD_ENTE_SDI              : TSchedaGenerica.PrepareForRecordString(Self.Dati.COD_ENTE_SDI),
                                                    COD_UFFICIO_DEST          : TSchedaGenerica.PrepareForRecordString(Self.Dati.COD_UFFICIO_DEST),
                                                    INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.INDIRIZZO_FATTURAZIONE),
                                                    NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.NR_CIVICO_FATTURAZIONE),
                                                    PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PROVINCIA_FATTURAZIONE),
                                                    NAZIONE_FATTURAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NAZIONE_FATTURAZIONE),
                                                    COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.COMUNE_FATTURAZIONE),
                                                    CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAP_FATTURAZIONE),
                                                    PEC                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.PEC),
                                                    NOTE                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.NOTE),
                                                    ZONA_FATTURAZIONE         : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ZONA_FATTURAZIONE),
                                                    DALLE_ORE                 : TSchedaGenerica.PrepareForRecordTime(Self.Dati.DALLE_ORE),
                                                    ALLE_ORE                  : TSchedaGenerica.PrepareForRecordTime(Self.Dati.ALLE_ORE),
                                                    DALLE_ORE2                : TSchedaGenerica.PrepareForRecordTime(Self.Dati.DALLE_ORE2),
                                                    ALLE_ORE2                 : TSchedaGenerica.PrepareForRecordTime(Self.Dati.ALLE_ORE2),
                                                    DALLE_ORE3                : TSchedaGenerica.PrepareForRecordTime(Self.Dati.DALLE_ORE3),
                                                    ALLE_ORE3                 : TSchedaGenerica.PrepareForRecordTime(Self.Dati.ALLE_ORE3),
                                                    DALLE_ORE4                : TSchedaGenerica.PrepareForRecordTime(Self.Dati.DALLE_ORE4),
                                                    ALLE_ORE4                 : TSchedaGenerica.PrepareForRecordTime(Self.Dati.ALLE_ORE4),
                                                    GIORNO_APERTURA           : TSchedaGenerica.PrepareForRecordString(Self.Dati.GIORNO_APERTURA),
                                                    GIORNO_APERTURA2          : TSchedaGenerica.PrepareForRecordString(Self.Dati.GIORNO_APERTURA2),
                                                    AVVISO_AMMINISTRATORE     : TSchedaGenerica.PrepareForRecordString(Self.Dati.AVVISO_AMMINISTRATORE),
                                                    ID_TITOLO                 : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ID_TITOLO),
                                                    NOTE_NASCOSTE             : TSchedaGenerica.PrepareForRecordString(Self.Dati.NOTE_NASCOSTE),
                                                }
                                  });
            Self.SchedaRecapiti.PrepareQueryParametersTelefono(ObjQuery.Operazioni,Self.Chiave,'ID_AMMINISTRATORE')

          Self.AdvQuery.PostSQL('Amministratori',ObjQuery,function(Response)
          {
            if(Self.IsNuovo())
            {
                SystemInformation.GetConfigurazioni(function()
                {
                  ObjQuery = {};
                  if(Self.Chiave == -1)
                      Self.Chiave = Response.NewKey1;
                  Self.Dati.ModificaTabelle        = false;
                  Self.CreateSnapshot();
                  if(OnSuccess != undefined)
                    OnSuccess()
                })
            }
            else 
            {
                ObjQuery = {};
                if(Self.Chiave == -1)
                    Self.Chiave = Response.NewKey1;
                Self.Dati.ModificaTabelle        = false;
                Self.CreateSnapshot();
                if(OnSuccess != undefined)
                  OnSuccess()
            }
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
      this.AdvQuery.GetSQL('Amministratori',{ CHIAVE : this.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo               = Self.AdvQuery.FindResults(Results,"Dettaglio");
                                            let ArrayInfoTelefono       = Self.AdvQuery.FindResults(Results,"DettaglioTelefono");
                                            if(ArrayInfo != undefined && ArrayInfoTelefono != undefined)
                                            {
                                              Self.SchedaRecapiti.AssignDati(ArrayInfoTelefono)
                                              if(ArrayInfo.length != 0)
                                              {
                                                Self.Dati = { 
                                                               RAGIONE_SOCIALE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                                                               MESE_FATTURAZIONE         : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].MESE_FATTURAZIONE),
                                                               GIORNO_FATTURAZIONE       : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].GIORNO_FATTURAZIONE),
                                                               TIPO_FATTURAZIONE         : TSchedaGenerica.DisponiFromString(ArrayInfo[0].TIPO_FATTURAZIONE),
                                                               COD_ENTE_SDI              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_ENTE_SDI),
                                                               COD_UFFICIO_DEST          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_UFFICIO_DEST),
                                                               INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE),
                                                               NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE),
                                                               PROVINCIA_FATTURAZIONE    : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE),
                                                               NAZIONE_FATTURAZIONE      : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_FATTURAZIONE),
                                                               COMUNE_FATTURAZIONE       : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE),
                                                               CAP_FATTURAZIONE          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE),
                                                               PEC                       : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC),
                                                               ZONA_FATTURAZIONE         : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ZONA_FATTURAZIONE),
                                                               NOTE                      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE),
                                                               DALLE_ORE                 : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].DALLE_ORE),
                                                               ALLE_ORE                  : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].ALLE_ORE),
                                                               DALLE_ORE2                : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].DALLE_ORE2),
                                                               ALLE_ORE2                 : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].ALLE_ORE2),
                                                               DALLE_ORE3                : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].DALLE_ORE3),
                                                               ALLE_ORE3                 : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].ALLE_ORE3),
                                                               DALLE_ORE4                : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].DALLE_ORE4),
                                                               ALLE_ORE4                 : TSchedaGenerica.DisponiFromTime(ArrayInfo[0].ALLE_ORE4),
                                                               GIORNO_APERTURA           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].GIORNO_APERTURA),
                                                               GIORNO_APERTURA2          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].GIORNO_APERTURA2),
                                                               AVVISO_AMMINISTRATORE     : TSchedaGenerica.DisponiFromString(ArrayInfo[0].AVVISO_AMMINISTRATORE),
                                                               ID_TITOLO                 : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_TITOLO),
                                                               NOTE_NASCOSTE             : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE_NASCOSTE),
                                                               // Indicazione stato tabelle dati
                                                               ModificaTabelle           : false,
                                                }
                                                Self.CreateSnapshot();

                                                Self.CheckDimensioniNote()
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Ammininstratore eliminato')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio dell\'amministratore');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectDettaglio')
   }     

   CaricaRiassunto(Riassunto)
   {
      this.Chiave                         = TSchedaGenerica.DisponiFromInteger(Riassunto.CHIAVE);
      this.Dati.RAGIONE_SOCIALE           = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE);
      this.GestisceClienti = Riassunto.NUMERO_CLIENTI_GESTITI > 0 ? true : false
      this.CreateSnapshot();
   }  

   Clear()
   {
      this.SchedaRecapiti.ClearSchedaRecapiti()
      this.Dati = { 
                     RAGIONE_SOCIALE           : '',
                     MESE_FATTURAZIONE         : new Date().getMonth() + 1,
                     GIORNO_FATTURAZIONE       : new Date().getDate(),
                     TIPO_FATTURAZIONE         : TIPI_FATTURAZIONE.Annuale,
                     COD_ENTE_SDI              : '0000000',
                     COD_UFFICIO_DEST          : '',
                     INDIRIZZO_FATTURAZIONE    : '',
                     NR_CIVICO_FATTURAZIONE    : '',
                     PROVINCIA_FATTURAZIONE    : -1,
                     NAZIONE_FATTURAZIONE      : SystemInformation.Configurazioni.StatoItaliano,
                     COMUNE_FATTURAZIONE       : '',
                     CAP_FATTURAZIONE          : '',
                     PEC                       : '',
                     NOTE                      : '',
                     ZONA_FATTURAZIONE         : -1,
                     DALLE_ORE                 : '',
                     ALLE_ORE                  : '',
                     DALLE_ORE2                : '',
                     ALLE_ORE2                 : '',
                     DALLE_ORE3                : '',
                     ALLE_ORE3                 : '',
                     DALLE_ORE4                : '',
                     ALLE_ORE4                 : '',
                     GIORNO_APERTURA           : '',
                     GIORNO_APERTURA2          : '',
                     NOTE_NASCOSTE             : '',
                     AVVISO_AMMINISTRATORE     : '',
                     ID_TITOLO                 : -1,
                     // Indicazioni stato tabelle dati
                     ModificaTabelle           : false,
      }
      super.Clear();
   }    

   BeforeExpand(AnItem,OnSuccess)
   {
      if(this.Chiave != undefined)
      {
        SystemInformation.AdvQuery.GetSQL('Clienti',{CHIAVE_AMMINISTRATORE :  this.Chiave},
                                          function(Results)
                                          {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsClientiXAmministratore");
                                              if(ArrayInfo != undefined)
                                              {  
                                                ArrayInfo.forEach(function(ACliente)
                                                {
                                                    var SchedaCliente = new TSchedaCliente(SystemInformation.AdvQuery);
                                                    SchedaCliente.CaricaRiassunto(ACliente);
                                                    var NodeCliente = AnItem.AddChild(SchedaCliente.GetDescrizione(),SchedaCliente)
                                                    NodeCliente.HasChildren = true;
                                                });
                                                OnSuccess();
                                              }
                                              else console.error('Impossibile ottenere la lista dei clienti per amministratore');
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          },
                                          'LsClientiXAmministratore') 
        
      }   
   }  

   DatiEliminabili()
   {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
      return false;

    return !this.GestisceClienti;
   }

    DatiStampabili()
    {
      if(this.GestisceClienti)
        return true
    }

    Stampa(OnSuccess)
    {
      SystemInformation.AdvQuery.ExecuteExternalScript('StampaEstrattoContoAmm', { ChiaveAmministratore : this.Chiave },
                                                      function(Result)
                                                      {  
                                                        if(Result.PDF != undefined)
                                                          OnSuccess('data:application/pdf;base64,' + Result.PDF);
                                                        else SystemInformation.HandleError('Documento non presente','','');
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                      })  
    } 

   Elimina(OnSuccess, OnError) 
   {
      this.InEliminazione = true;
      var Self = this
      var ObjQuery = {Operazioni : []}
      ObjQuery.Operazioni.push ({
                                  Query: "DeleteTelefonoTramiteAmministratore",
                                  Parametri: { CHIAVE_AMMINISTRATORE : Self.Chiave }
                               })
      ObjQuery.Operazioni.push ({
                                    Query: "Delete",
                                    Parametri: { CHIAVE_AMMINISTRATORE : Self.Chiave }
                                }),
      
      this.AdvQuery.PostSQL('Amministratori', ObjQuery, function () 
      {
        OnSuccess();
      },
      function (HTTPError, SubHTTPError, Response) 
      {
        OnError(HTTPError, SubHTTPError, Response);
      });
   } 

   GetFiltriAbilitati(OnSuccess)
   {
      OnSuccess([{
                   Name : DASHBOARD_FILTER_TYPES.Amministratori,
                   Positions : [
                                  this.Chiave
                               ]
               }]);
   } 
 } 

 export default 
 {
    components : 
    {
        VUEInputCAP,
        VUEInputProvince,
        VUEInputNazioni,
        VUERecapiti,
        VUEInputMese,
        VUEInputZone,
    },
    data()
    {
      return { 
               Tabs            : {
                                    ActiveTab : 'SchedaAmministratore',
                                    Tabs      : [
                                                  {
                                                    Caption : 'Scheda amministratore',
                                                    Id      : 'SchedaAmministratore'
                                                  }
                                                ]
                                 },
                DeveloperMode         : SystemInformation.DeveloperMode,
                StatoItaliano         : SystemInformation.Configurazioni.StatoItaliano,
                LsTitoli              : SystemInformation.Configurazioni.Titoli,
                TipiFatturazione      : SystemInformation.GetLsTipiFatturazione(),

             };
    }, 
    props : ['SchedaAmministratore'],
    methods : 
    {
        OnRecapitiChange()
        {
          this.SchedaAmministratore.Dati.ModificaTabelle = true
        },
        OnChangeMeseFatturazione()
        {
          if(this.SchedaAmministratore.Dati.GIORNO_FATTURAZIONE < 1)
            this.SchedaAmministratore.Dati.GIORNO_FATTURAZIONE = 1
          if(TZDateFunct.GetLunghezzaMese(this.SchedaAmministratore.Dati.MESE_FATTURAZIONE) < this.SchedaAmministratore.Dati.GIORNO_FATTURAZIONE)
            this.SchedaAmministratore.Dati.GIORNO_FATTURAZIONE = TZDateFunct.GetLunghezzaMese(this.SchedaAmministratore.Dati.MESE_FATTURAZIONE)
        }
    },
    computed:
    {
    },

    beforeMount() 
    {
      this.ActiveTab = 'DatiFatturazione'
    },
 }
</script>

<style scoped>
label {
  margin-bottom: 0px;
}
</style>        
