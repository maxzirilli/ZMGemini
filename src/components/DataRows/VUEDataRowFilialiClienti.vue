<template>
<VUEConfirm :Popup="PopupDataRowFilialiClienti" :Richiesta="'Eliminare la riga selezionata?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina" 
            :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma" >
</VUEConfirm>
<VUEConfirm :Popup="PopupDataRowCordinate" :Richiesta="'Vuoi aggiornare le coordinate?'" @onClickConfermaPopup="ConfermaAggiorna" @onClickChiudiPopup="AnnullaAggiorna"
            :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma">
</VUEConfirm>

<VUEModal v-if="LsLogFiliali" 
            :Titolo="'Lista Log Filiale'" 
            :Altezza="'200px'" 
            :Larghezza="'900px'"
            :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma"
            @onClickChiudiModal="OnClickChiudiLista">
 <template v-slot:Body>
  <div class="row wrapper">
        <section class="panel panel-default" style="background-color:white;">
          <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
            <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
              <thead>
                <tr>
                  <th style="width:22%;position: sticky; top: 0;text-align:center;">Data e ora</th>
                  <th style="width:43%;position: sticky; top: 0;text-align:center;">Stato Filiale</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="LogFiliale in ElencoLogFiliali" :Key="LogFiliale.CHIAVE">
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ LogFiliale.DATA_ORA }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ LogFiliale.FILIALE_DISATTIVATA == 'T' ? 'Disattivata' : 'Attivata'  }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>  
 </template>
 <template v-slot:Footer>
      <button class="btn btn-danger"  @click="OnClickChiudiLista"  style="float:right;margin-right:20px; width:20%">Annulla</button>  
 </template>
</VUEModal>
<div style="border-top: 1px solid gray">
   <div>
      <label v-for="(Error,NdxError) in Riga.RowErrors()" :Key="NdxError" style="margin-bottom: 0px; margin-top:0px;" class="ZMFormLabelError">{{Error}}</label>
   </div>
   <div style="padding-top: 5px;padding-bottom: 2px;">
     <div style="float:left;width:11%;padding-top: 8px;">
        <div style="float:left;">
           <label style="font-weight: bold;">Sede riscossione&nbsp;&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;">
          <input @input="CurrentRiga.OnChange()" type="checkbox" v-model="CurrentRiga.Dati['SEDE'].Valore"/>
        </div>
     </div>
     <div style="float:left;width:10%;padding-top: 8px;">
        <div style="float:left;">
           <label style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Disattivata&nbsp;&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;">
          <input @input="CurrentRiga.OnChange()" type="checkbox" v-model="CurrentRiga.Dati['FILIALE_DISATTIVATA'].Valore" @change="OnChangeSede(CurrentRiga)"/>
        </div>
     </div>
     <div style="float:left;width:5%;padding-top: 2px" v-if="CurrentRiga.Dati['FILIALE_DISATTIVATA'].Valore"><br>
      <div style="float:left;;background-color:red;padding-left:4px;padding-right:4px;padding-top:4px;">
           <label style="font-weight: bold;float:left;color:white">DISATTIVATA</label>
        </div>
     </div>
     <div style="float:left;width:5%;padding-top: 8px;" v-else>&nbsp;</div>
     <div style="float:left;width:59%">
        <div style="float:left;padding-top: 8px;width:12%;text-align: right;">
           <label style="font-weight: bold;">Nome&nbsp;&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width: 88%;">
          <input @input="CurrentRiga.OnChange()" type="text" style="width:100%" class="form-control" v-model="CurrentRiga.Dati['NOME'].Valore" />
        </div>
     </div>
     <div style="float:left;width:1%">&nbsp;</div>
     <div style="padding-top:4px;float: left; width: 4%; text-align: center;">
        <i v-if="Collapsed" class="ZMIconButton fa fa-arrow-circle-down text-info" @click="Collapsed = !Collapsed"></i>
        <i v-else class="ZMIconButton fa fa-arrow-circle-up text-info text" @click="Collapsed = !Collapsed"></i>
     </div>
     <div style="float:left;width:1%">&nbsp;</div>
     <div style="padding-top:4px;float: left; width: 4%; text-align: center;">  
               <a v-if="CurrentRiga.Dati['ATTIVAZIONI_PRESENTI'].Valore != 0 && CurrentRiga.Dati['ATTIVAZIONI_PRESENTI'].Valore != null"
                 class="fa fa-list" 
                 style="cursor:pointer;color:black;font-size:15px;margin-right:5px;"
                 @click="ListaLogFiliali()">
              </a>
     </div>
     <div style="float:left;width:1%">&nbsp;</div>
     <div style="padding-top:4px;float: left; width: 4%; text-align: center;">
         <div style="float:left;width:50%;" v-if="CurrentRiga.Modificato()">
          <i v-if="CurrentRiga.RowErrors().length == 0" class="fa fa-check text-success"></i>
          <i v-else class="fa fa-times text-danger text"></i>
         </div>
         <div class="fa-hover" style="float:left;width:50%;" v-if="CurrentRiga.Nuovo">
           &nbsp;&nbsp;<i class="fa fa-file-text text-danger text"></i>
         </div>
     </div>
     <div style="padding-top:4px;float: left; width: 4%; text-align: center;">
       <i class="fa fa-trash-o text-danger ZMIconButton" @click="OnClickEliminaRiga(CurrentRiga)"></i>
     </div>
   </div>
   <div v-if="!Collapsed">
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
      <div class="ZMSeparatoreScheda">Indirizzo</div>
      <div style="clear:both">
          <div style="float:left;width:50%">
              <label style="font-weight: bold;">Indirizzo</label>
              <input @input="CurrentRiga.OnChange()" type="text" class="form-control" v-model="CurrentRiga.Dati['INDIRIZZO'].Valore" placeholder="Indirizzo"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:7%">
               <label style="font-weight: bold;">Civico</label>
               <input @input="CurrentRiga.OnChange()" maxlength="7" type="text" class="form-control" v-model="CurrentRiga.Dati['NR_CIVICO'].Valore" placeholder="Nr. civico"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:7%">
               <label style="font-weight: bold;">CAP</label>
               <VUEInputCAP @input="CurrentRiga.OnChange()" v-model="CurrentRiga.Dati['CAP'].Valore"/>
          </div>
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:13%">
              <label style="font-weight: bold;">Latitudine</label>
              <input @input="CurrentRiga.OnChange()" maxlength="10" type="text" class="form-control" v-model="CurrentRiga.Dati['LAT_IND'].Valore" placeholder="Latitudine"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:13%">
              <label style="font-weight: bold;">Longitudine</label>
              <input @input="CurrentRiga.OnChange()" maxlength="10" type="text" class="form-control" v-model="CurrentRiga.Dati['LONG_IND'].Valore" placeholder="Longitudine"/>
          </div>
          <div style="padding-top:15px;">
                  <img style="float:left;cursor:pointer; margin-left:20px"  
                    src="@/assets/images/coordinate.png" 
                    @click="OnClickPopupModificaCoordinate(Riga)"/>
          </div>
      </div>
      <div style="clear:both">
         <div style="float:left;width:24%">
              <label style="font-weight: bold;">Comune</label>
              <input @input="CurrentRiga.OnChange()" type="text" class="form-control" v-model="CurrentRiga.Dati['COMUNE'].Valore" placeholder="Comune"/>
         </div>
         <div style="float:left;width:1%;">&nbsp;</div>
         <div style="float:left;width:14%">
            <label style="font-weight: bold;">Provincia</label>
            <VUEInputProvince @input="CurrentRiga.OnChange()" v-model="CurrentRiga.Dati['PROVINCIA'].Valore" emptyElement="true"/>
         </div> 
         <div style="float:left;width:1%;">&nbsp;</div>
         <div style="float:left;width:10%">
            <label style="font-weight: bold;">Zona</label>
            <VUEInputZone @input="CurrentRiga.OnChange()" v-model="CurrentRiga.Dati['ZONA'].Valore"/>
         </div> 
         <div style="float:left;width:1%;">&nbsp;</div>
         <div style="float:left;width:15%">
            <label style="font-weight: bold;">Nazione</label>
            <VUEInputNazioni @input="CurrentRiga.OnChange()" v-model="CurrentRiga.Dati['NAZIONE'].Valore" emptyElement="true"/>
         </div>
         
         <div style="float:left;width:1%;">&nbsp;</div>
         <div style="float:left;width:13%">
            <label style="font-weight: bold;">&nbsp;</label>
            <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickCopiaDatiFatturazione(Riga)">Copia dati fatt.</button>
         </div>
         <div style="float:left;width:1%;">&nbsp;</div>
         <div style="float:left;width:13%">
            <label style="font-weight: bold;">&nbsp;</label>
            <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickCopiaDatiDestinazione(Riga)">Copia dati dest.</button>
         </div>
      </div>
      <div style="clear: both;height: 5px;">&nbsp;</div>
      <div style="float:left;width:55%">
         <div class="ZMSeparatoreScheda">Recapiti</div>
      </div>
      <div style="float:left;width:45%">
         <div class="ZMSeparatoreScheda">Orari</div>
      </div>
      <div style="clear: both;height: 5px;">&nbsp;</div>
      
      <div style="float:left;width:10%">
         <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickShowOnlyRecapiti()">Mostra solo recapiti</button>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left;width:10%">
         <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickShowOnlyTimes()">Mostra solo orari</button>
      </div>
      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left;width:10%">
         <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickShowRecapitiAndTimes()">Mostra entrambi</button>
      </div>

      <div v-if="!NascondiRecapiti && !NascondiTimes" style="float:right;width:56%;">&nbsp;</div>
      <div v-else style="float:right;width:1%;">&nbsp;</div>

      <div v-if="!NascondiRecapiti" style="float:right;width:9%">
         <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickCopiaRecapiti()">Copia recapiti</button>
      </div>

      <div style="clear: both;height: 5px;">&nbsp;</div>

      <div v-if="!NascondiRecapiti" :style="{ width : RecapitiWidth + '%' }" style="float:left;">
         <VUERecapiti :SchedaRecapiti="CurrentDataTableRecapiti" @onChange="OnRecapitiChange"></VUERecapiti>
      </div>

      <div v-if="!NascondiRecapiti && !NascondiTimes" style="float:left;width:1%;">&nbsp;</div>

      <div v-if="!NascondiTimes" :style="{ width : TimesWidth + '%' }" style="float:left">
         <VUEDataTableSecondaria :DataTable="CurrentDataTableSecondaria"
                                 :NomeProgramma="NomeProgramma" 
                                 :PathLogo="require('../../assets/images/LogoGemini2.png')"/>
      </div>
      
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

      <!-- <div class="col-md-6" style="padding:0px">
         <div class="ZMSeparatoreScheda">Altre info</div>
         <div class="ZMNuovaRigaScheda" style="padding-top:0px">
            <div style="float:left;width:35%;">
               <label style="font-weight: bold;">Giorni di chiusura</label>
               <input type="text" style="width:99%" class="form-control" v-model="CurrentRiga.Dati['CHIUSURA'].Valore" />
            </div>
         </div>
      </div> -->
      
      <div class="col-md-12" style="padding:0px">
         <div class="ZMSeparatoreScheda">Note</div>
         <div class="ZMNuovaRigaScheda" style="padding-top:0px">
            <!-- <div style="float:left;width:1%;">&nbsp;</div> -->
            <div style="float:left;width:100%;">
               <textarea @input="CurrentRiga.OnChange()" style="resize:none;height:104px" class="form-control" v-model="CurrentRiga.Dati['NOTE'].Valore" />
            </div>
         </div>
      </div>
      
   </div>
   <div style="clear: both;height: 5px;">&nbsp;</div>

</div>
</template>

<script>
import { SystemInformation, NOME_PROGRAMMA } from '@/SystemInformation';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputZone from '@/components/InputComponents/VUEInputZone.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEDataTableSecondaria from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2Secondaria.vue';
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import VUERecapiti from '@/views/SchedeDatabase/ComponentMultiScheda/VUERecapiti.vue';
import { TZOpenStreetMap } from '../../../../../../../../Librerie/VUE/ZOpenStreetMap.js';

export default 
{

  props: ['Riga','DataTable', 'SchedaCliente'],
  components: 
  {
    VUEInputCAP,
    VUEInputProvince,
    VUEInputNazioni,
    VUEDataTableSecondaria,
    VUEConfirm,
    VUEModal,
    VUERecapiti,
    VUEInputZone
  },

  data()
  {    
     return {
              Collapsed                  : true,
              PopupDataRowFilialiClienti : false,
              PopupDataRowCordinate      : false,
              LsLogFiliali               : false,
              NascondiRecapiti           : false,
              NascondiTimes              : false,
              NomeProgramma              : NOME_PROGRAMMA,
              RecapitiWidth              : 44,
              TimesWidth                 : 55,
              RigaPopup                  : null,
              ElencoLogFiliali           : []
            }
  },
  
  watch: 
  {
    CurrentDataTableSecondaria :
    { 
        handler(NewValue,OldValue)
        {
          if(NewValue != OldValue && NewValue != undefined)
          {
            this.CurrentDataTableSecondaria.AssignOnRowChange(() =>
            {
              this.CurrentRiga.OnChange()
            })

            this.CurrentDataTableSecondaria.AssignOnRowDelete(() =>
            {
              this.CurrentRiga.OnChange()
            })
          } 
        },
        immediate : true
    },
  },

  methods: 
  {
    OnRecapitiChange()
    {
      this.CurrentRiga.OnChange()
    },

    OnClickEliminaRiga(Riga)
    {
       this.PopupDataRowFilialiClienti = true
       this.RigaPopup = Riga
    },
    
    ConfermaElimina()
    {
      this.RigaPopup.DataTableOrari.DeleteAllRows()
      this.RigaPopup.SchedaRecapitiFiliali.DataTableTelefono.DeleteAllRows()
      this.DataTable.DeleteRow(this.RigaPopup);
      this.AnnullaElimina()
    },

    AnnullaElimina()
    {
       this.PopupDataRowFilialiClienti = false
    },

    ConfermaAggiorna()
    {
      this.OnClickTrovaCoordinateFiliale(this.Riga)
      this.PopupDataRowCordinate = false 
    },

    AnnullaAggiorna()
    {
      this.PopupDataRowCordinate = false 
    },

    OnClickChiudiLista()
    {
       this.LsLogFiliali = false
    },

    OnChangeSede(CurrentRiga)
    {
     if(CurrentRiga.Dati['SEDE'].Valore)
        this.DataTable.ResetBoolExceptFor(CurrentRiga,'SEDE');
    },

    OnClickCopiaDatiDestinazione(Riga)
    {
      Riga.Dati['INDIRIZZO'].Valore = this.SchedaCliente.Dati.INDIRIZZO_SPEDIZIONE
      Riga.Dati['NR_CIVICO'].Valore = this.SchedaCliente.Dati.NR_CIVICO_SPEDIZIONE
      Riga.Dati['PROVINCIA'].Valore = this.SchedaCliente.Dati.PROVINCIA_SPEDIZIONE
      Riga.Dati['COMUNE'].Valore    = this.SchedaCliente.Dati.COMUNE_SPEDIZIONE
      Riga.Dati['CAP'].Valore       = this.SchedaCliente.Dati.CAP_SPEDIZIONE
      Riga.Dati['NAZIONE'].Valore   = this.SchedaCliente.Dati.NAZIONE_SPEDIZIONE
      Riga.Dati['ZONA'].Valore      = this.SchedaCliente.Dati.ZONA_SPEDIZIONE
    },

    OnClickCopiaDatiFatturazione(Riga)
    {
      Riga.Dati['INDIRIZZO'].Valore = this.SchedaCliente.Dati.INDIRIZZO_FATTURAZIONE
      Riga.Dati['NR_CIVICO'].Valore = this.SchedaCliente.Dati.NR_CIVICO_FATTURAZIONE
      Riga.Dati['PROVINCIA'].Valore = this.SchedaCliente.Dati.PROVINCIA_FATTURAZIONE
      Riga.Dati['COMUNE'].Valore    = this.SchedaCliente.Dati.COMUNE_FATTURAZIONE
      Riga.Dati['CAP'].Valore       = this.SchedaCliente.Dati.CAP_FATTURAZIONE
      Riga.Dati['ZONA'].Valore      = this.SchedaCliente.Dati.ZONA_FATTURAZIONE
      Riga.Dati['NAZIONE'].Valore   = this.SchedaCliente.Dati.NAZIONE_EM_PIVA
    },

    OnClickPopupModificaCoordinate(Riga)
    {
      this.PopupDataRowCordinate = true
      this.RigaPopup = Riga
    },

    OnClickTrovaCoordinateFiliale(Riga)
    { 
      Riga = this.Riga
      var Provincia = SystemInformation.Configurazioni.Province.find(function(Elemento)
                      {
                        return Elemento.CHIAVE == Riga.Dati['PROVINCIA'].Valore
                      });
      let Self = this
      Provincia = Provincia != undefined ? Provincia.NOME : '-'
      TZOpenStreetMap.GetCoordinate(Riga.Dati['NR_CIVICO'].Valore, Riga.Dati['INDIRIZZO'].Valore, Provincia,
                                    function(Latitudine, Longitudine)
                                    {
                                      Riga.Dati['LAT_IND'].Valore  = Latitudine
                                      Riga.Dati['LONG_IND'].Valore = Longitudine
                                      Self.PopupDataRowCordinate = false
                                    },
                                      function(Message)
                                    {
                                      alert(Message)
                                    });
    },

    OnClickCopiaRecapiti()
    {
      var VettoreRecapiti = []
      for(let j = 0; j < this.CurrentDataTableRecapiti.DataTableTelefono.Righe.length; j++)
         this.CurrentDataTableRecapiti.DataTableTelefono.Righe[j].Eliminato = true
      for(let i = 0; i < this.SchedaCliente.SchedaRecapiti.DataTableTelefono.Righe.length; i++)
      {
         var OggettoTemporaneo = {}
         OggettoTemporaneo = {
                                 'CHIAVE'      : undefined,
                                 'PRINCIPALE'  : this.SchedaCliente.SchedaRecapiti.DataTableTelefono.Righe[i].Dati['PRINCIPALE'].Valore? 'T' : 'F',
                                 'EMAIL'       : this.SchedaCliente.SchedaRecapiti.DataTableTelefono.Righe[i].Dati['EMAIL'].Valore,
                                 'DESCRIZIONE' : this.SchedaCliente.SchedaRecapiti.DataTableTelefono.Righe[i].Dati['DESCRIZIONE'].Valore,
                                 'TELEFONO'    : this.SchedaCliente.SchedaRecapiti.DataTableTelefono.Righe[i].Dati['TELEFONO'].Valore
                             }
         VettoreRecapiti.push(OggettoTemporaneo)
      }
      this.CurrentDataTableRecapiti.AssignDati(VettoreRecapiti, true);
    },

    OnClickShowOnlyRecapiti()
    {
      this.NascondiRecapiti = false
      this.NascondiTimes     = true
      this.RecapitiWidth     = 100
    },

    OnClickShowOnlyTimes()
    {
      this.NascondiRecapiti = true
      this.NascondiTimes     = false
      this.TimesWidth        = 100
    },

    OnClickShowRecapitiAndTimes()
    {
      this.NascondiRecapiti = false
      this.NascondiTimes     = false
      this.RecapitiWidth     = 44
      this.TimesWidth        = 55
    },

    ListaLogFiliali()
    {
      this.LsLogFiliali = true
      var Self = this
      SystemInformation.AdvQuery.GetSQL('Clienti',{ CHIAVE_FILIALE : this.CurrentRiga.Dati.CHIAVE },
                                             function(Results)
                                             {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "LogFilialiAttive");
                                              if (ArrayInfo != undefined) 
                                              {
                                                 Self.ElencoLogFiliali = ArrayInfo
                                                 Self.ElencoLogFiliali.forEach(function(Filiale)
                                                 {
                                                  Filiale.DATA_ORA = new Date(Filiale.DATA_ORA)
                                                  Filiale.DATA_ORA = TZDateFunct.FormatDateTime('dd-mm-yyyy hh:nn',Filiale.DATA_ORA)
                                                 })
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere lista dei log');
                                             },
                                             function (HTTPError, SubHTTPError, Response) 
                                             {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                             },
                                              'LogFiliali');
    }
  },

  computed: 
  {
     CurrentRiga : 
     { 
        get() 
        {
          return this.Riga;
        }
     },
     RigaModificata :
     {
        get()
        {
           return JSON.stringify(this.Riga.Dati) != this.Riga.StartData;
        }
     },
     CurrentDataTableSecondaria : 
     { 
        get() 
        {
          return this.Riga.DataTableOrari;
        }
     },
     CurrentDataTableRecapiti : 
     { 
        get() 
        {
          return this.Riga.SchedaRecapitiFiliali;
        }
     },
  },
}
</script>

<style>
label {
  width: 100%;
}
</style>