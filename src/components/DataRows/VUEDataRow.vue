<template>
  <VUEConfirm :Popup="PopupDataRow" :Richiesta="'Eliminare la riga selezionata?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
  </VUEConfirm>
  <template v-for="Colonna in DataTable.Configurazione.Colonne" :Key="Colonna.Id"> 
  <td v-if="!Colonna.Nascosta">
     <div v-if="CurrentRiga.IsString(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;">
       <input type="text" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :readonly="Colonna.ReadOnly || CurrentRiga.RowReadOnly" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsBoolean(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;">
       <input type="checkbox" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" @change="OnCheckBoxChange(CurrentRiga,Colonna)" :readonly="Colonna.ReadOnly || CurrentRiga.RowReadOnly" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsNumber(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;">
       <input type="number" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :step="CurrentRiga.StepNumber(Colonna.Tipo)" :readonly="Colonna.ReadOnly || CurrentRiga.RowReadOnly" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsSelect(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px">
         <div v-if="Colonna.Autocomplete">
            <Autocomplete v-model="CurrentRiga.Dati[Colonna.Campo].Valore"
                         :placeholder="Colonna.Titolo"
                          FieldChiave ="CHIAVE"
                          FieldDescrizione = "DESCRIZIONE"
                         :InvalidValue ="Colonna.DefaultValue"
                          InputClasses = "form-control"
                         :items ="Colonna.Lista"
                         :minInputLength="1" />
         </div>

         <div v-else>
            <select class="form-control" 
                    v-model="CurrentRiga.Dati[Colonna.Campo].Valore" 
                    style="cursor:default" 
                    :disabled="Colonna.Disabilitato || CurrentRiga.RowReadOnly || (Colonna.IsDisabledWhen != null && Colonna.IsDisabledWhen(CurrentRiga.Dati))"
                    @change="OnSelectChange(CurrentRiga,Colonna)">
               <option v-if="Colonna.DefaultSelect" :value="Colonna.DefaultValue">-</option>
               <option v-for="SelectOption in Colonna.Lista" 
                      :Key="SelectOption[Colonna.SelCampoChiave]"
                      :value="SelectOption[Colonna.SelCampoChiave]">{{SelectOption[Colonna.SelCampoCaption]}}
               </option>
            </select>
         </div>
     </div>
     <div v-if="CurrentRiga.IsAButton(Colonna.Campo) && !Colonna.Nascosta && (CurrentRiga.GetBottoneNascosto(CurrentRiga.Dati[Colonna.CampoDaPassareComeParametro].Valore) || Riga.Graphics[Colonna.Campo].ButtonContentDefault != null)" style="text-align:center;padding-top:6px;">
      <button class="btn btn-sm btn-success" 
              @click="EventoOnClick(CurrentRiga)" 
              v-if="!CurrentRiga.Graphics[Colonna.Campo].ButtonContentIsImage && ( Colonna.CampoDaPassareComeParametro == null || CurrentRiga.Dati[Colonna.CampoDaPassareComeParametro].Valore != null)">
              {{ Riga.Graphics[Colonna.Campo].ButtonContent }}
      </button>
      
      <button class="btn btn-sm btn-info" 
              @click="EventoOnClick(CurrentRiga)" 
              v-if="!CurrentRiga.Graphics[Colonna.Campo].ButtonContentIsImage && (CurrentRiga.Dati[Colonna.CampoDaPassareComeParametro].Valore == null 
              && Riga.Graphics[Colonna.Campo].ButtonContentDefault != null)">
              {{ Riga.Graphics[Colonna.Campo].ButtonContentDefault }}
      </button>

      <img style="height:34px;width:34px;cursor:pointer" 
           :src="CurrentRiga.Graphics[Colonna.Campo].ButtonContent" 
           @click="EventoOnClick(CurrentRiga)" 
           v-if="CurrentRiga.Graphics[Colonna.Campo].ButtonContentIsImage && ( Colonna.CampoDaPassareComeParametro == null || CurrentRiga.Dati[Colonna.CampoDaPassareComeParametro].Valore != -1)">
     
    </div>
     <div v-if="CurrentRiga.IsDate(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;">
        <input v-if="!Colonna.CampoCalcolato()" type="date" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :readonly="Colonna.ReadOnly || CurrentRiga.RowReadOnly" :disabled="Colonna.Disabilitato"/>
        <label class="ZMLabel" v-else style="text-align:center;height:auto;padding:5px;">{{ CalcoloData(Colonna) }}</label>
     </div>
     <div v-if="CurrentRiga.IsTime(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;">
        <input type="time" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :readonly="Colonna.ReadOnly || CurrentRiga.RowReadOnly" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsDateTime(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;">
        <input type="datetime-local" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :readonly="Colonna.ReadOnly || CurrentRiga.RowReadOnly" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsMemo(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;">
      <textarea v-if="!Colonna.CampoCalcolato()"
                style="resize:none;overflow-y:hidden;white-space: pre" 
               :style="{height : CurrentRiga.AltezzaTextArea? CurrentRiga.AltezzaTextArea : '34px'}" 
                type="text" class="form-control" 
                v-model="CurrentRiga.Dati[Colonna.Campo].Valore" 
               :disabled="Colonna.Disabilitato"
               :readonly="Colonna.ReadOnly || CurrentRiga.RowReadOnly"
               @input="CheckDimensioniRiga()"/>
      <label class="ZMLabel" v-else style="text-align:center;height:auto;padding:5px;">{{ CalcoloMemo(Colonna) }}</label>
     </div>

     <div v-if="CurrentRiga.IsAllegato(Colonna.Campo) && !Colonna.Nascosta" style="text-align:center;padding-top:6px;" >
        <!-- CARICO ALLEGATO -->
        <button v-if="!CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato"
          @click="OnClickCaricaAllegato(CurrentRiga, Colonna)" 
          data-toggle="class" 
          style=" font-size:17px; cursor:pointer; background:none; border:none; color:black;" 
          title="Carica allegato">
          <i class="fa fa-upload"></i>
        </button>
        <input id="fileLoadDocument" 
              accept="all"
              class="form-control" 
              height=1px
              type="file" 
              style="display: none" />
      
        <!-- SCARICO ALLEGATO -->
        <button v-if="CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato"
          @click="OnClickScaricaAllegato(CurrentRiga, Colonna)" 
          data-toggle="class" 
          style="font-size:17px; cursor:pointer; background:none; border:none; color:green;" 
          title="Apri allegato">
          <i class="fa fa-download"></i>
        </button>
      
        <!-- ELIMINO ALLEGATO -->
        <button v-if="CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato"
          @click="OnClickEliminaAllegato(CurrentRiga, Colonna)" 
          data-toggle="class" 
          style="font-size:17px; cursor:pointer; background:none; border:none; " 
          title="Elimina allegato">
          <i class="fa fa-times-circle" style="color: red;"></i>
        </button>

     </div>
  </td>
  </template>
  <td style="padding-top:6px;">
      <div style="padding-top:6px;float:left;" v-if="CurrentRiga.Modificato()">
       <i v-if="CurrentRiga.RowErrors().length == 0" class="fa fa-check text-success"></i>
      </div>
      <div style="padding-top:6px;float:left;" v-if="CurrentRiga.RowErrors().length > 1">
        <i class="fa fa-times text-danger text" ></i>
      </div>
      <div class="fa-hover" style="padding-top:6px;float:left;" v-if="CurrentRiga.Nuovo">
       &nbsp;<i class="fa fa-file-text text-info text"></i>
      </div>
      <div v-if="!CurrentRiga.Nuovo && !CurrentRiga.Modificato()">&nbsp;</div>
  </td>
  <td>
    <div style="padding-top:6px;">
      <i v-if="!CurrentRiga.RowReadOnly" class="fa fa-trash-o text-danger ZMIconButton" @click="OnClickEliminaRiga(CurrentRiga)"></i>
    </div>
  </td>
</template>

<script>
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEConfirm from '@/components/VUEConfirm.vue';
import Autocomplete from '../../../../../../../../Librerie/VUE/ZAutocomplete.vue'
import { SystemInformation } from '@/SystemInformation.js'
import { saveAs } from 'file-saver'
import { TZStringConvFunct } from '../../../../../../../../Librerie/VUE/ZStringConvFunct.js'

export default 
{
  name : "VUEDataRow",
  emits: ['onClick'],
  props: ['Riga','DataTable'],
  components: { 
                  VUEConfirm, 
                  Autocomplete
              },

  data()
  {
     return {
              RigaModal         : null,
              PopupDataRow      : false,
              Posizione         : '',
              NomeAllegato      : '',
              AllegatoCaricato  : null,
              File              : ''
     }
  },

  methods: 
  {
    OnClickEliminaRiga(Riga)
    {
      this.RigaModal    = Riga
      this.PopupDataRow = true      
    },
    ConfermaElimina()
    {
       this.DataTable.DeleteRow(this.RigaModal)
       this.AnnullaElimina()
    },
    AnnullaElimina()
    {
       this.PopupDataRow = false
    },
    OnCheckBoxChange(CurrentRiga,Colonna)
    {
      if(Colonna.BoolEsclusivo && CurrentRiga.Dati[Colonna.Campo].Valore)
        this.DataTable.ResetBoolExceptFor(CurrentRiga,Colonna.Campo);
      if(Colonna.FunctionOnChangeValore != null)
        Colonna.FunctionOnChangeValore(CurrentRiga)
    },

    OnSelectChange(CurrentRiga,Colonna)
    {
      if(Colonna.FunctionOnChangeValore != null)
        Colonna.FunctionOnChangeValore(CurrentRiga)
    },

    CheckDimensioniRiga()
    {
      let NrRighe = 0
      var Self = this;
      this.DataTable.Configurazione.Colonne.forEach(function(Colonna)
      {
         if(Self.CurrentRiga.IsMemo(Colonna.Campo))
         {
            if(Self.CurrentRiga.Dati[Colonna.Campo].Valore != null && 
               Self.CurrentRiga.Dati[Colonna.Campo].Valore != undefined &&
               typeof Self.CurrentRiga.Dati[Colonna.Campo].Valore === 'string')
            {
               let NrRigheColonna = Self.CurrentRiga.Dati[Colonna.Campo].Valore.split("\n").length; 
               if(NrRigheColonna == 1)
                  NrRigheColonna = Self.CurrentRiga.Dati[Colonna.Campo].Valore.split("/\n/g").length; 
               if(NrRighe < NrRigheColonna)
                  NrRighe = NrRigheColonna
            }
         }
      });
      this.CurrentRiga.AltezzaTextArea = NrRighe  <= 1 ? '34px' : (NrRighe * 22 + 10) + 'px'
    },

    EventoOnClick(Parametro)
    {
      this.$emit('onClick', Parametro) 
    },

    
    OnClickCaricaAllegato(CurrentRiga, Colonna)
    {
      let FileInput = document.getElementById('fileLoadDocument');
      FileInput.click();
      FileInput.addEventListener('change', (event) => 
                                  {
                                    let FileSelezionato = event.target.files[0]

                                    this.NomeAllegato = event.target.files[0].name

                                    if (FileSelezionato)
                                    {
                                      if (CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato == null || CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato == '')
                                      {
                                        const reader = new FileReader()

                                        reader.onload = (event) => {
                                                                      this.File = event.target.result

                                                                      var FileBase64 = event.target.result.split("base64,")

                                                                      CurrentRiga.Dati[Colonna.Campo].FileBase64 = FileBase64
                                                                      CurrentRiga.Dati[Colonna.Campo].Valore = FileBase64[1]
                                                                      CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato = this.NomeAllegato
                                                                    }
                                        reader.readAsDataURL(FileSelezionato)
                                      }
                                    }
                                    
                                  }, { once: true })

    },

    OnClickScaricaAllegato(CurrentRiga, Colonna)
    {
      if(CurrentRiga.Dati[Colonna.Campo].Valore != null)
      {
        saveAs(TZStringConvFunct.Base64AsBlob(CurrentRiga.Dati[Colonna.Campo].Valore),'Fattura_Pregressa_' + CurrentRiga.Dati.NUMERO.Valore + CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato.slice(-4))
      }
      else
      {
        if(CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato != null && 
          CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato != '')
        {
          let Parametri         = {}
          let Posizione         = CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato.split(";");
          Parametri.Posizione   = Posizione[1]

          this.GetAttachment(Posizione[1],
                              function(Result)
                              {
                                saveAs(TZStringConvFunct.Base64AsBlob(Result),'Fattura_Pregressa_' + CurrentRiga.Dati.NUMERO.Valore + CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato.slice(-4))
                              })
        }
      }
    },

    OnClickEliminaAllegato(CurrentRiga, Colonna)
    {
      if(CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato)
      {
        CurrentRiga.Dati[Colonna.Campo].PosizioneAllegato = null
        CurrentRiga.Dati[Colonna.Campo].Valore = null
      }
    },

    CalcoloData(Colonna)
    {
      var Data = Colonna.OnCalcoloValore(this.Riga.Dati)
      Data = new Date(Data)
      return TZDateFunct.FormatDateTime('dd/mm/yyyy', Data)
    }, 

    CalcoloMemo(Colonna)
    {
      return Colonna.OnCalcoloValore(this.Riga.Dati)
    },

    GetAttachment(Path, OnSuccess)
    {
      var Self = this
      SystemInformation.AdvQuery.GetAttachment(Path,
                                              function(Result)
                                              {
                                                if (Result != undefined)
                                                {
                                                  if(Result != null)
                                                  {
                                                    if(OnSuccess != undefined)
                                                      OnSuccess(Result)
                                                  }
                                                  else Self.HandleError('Allegato non presente'); 
                                                }
                                              },
                                              function(HTTPError,SubHTTPError,Response)
                                              {
                                                Self.HandleError(HTTPError,SubHTTPError,Response);
                                              })
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
     CurrentDataTable : 
     { 
        get() 
        {
          return this.DataTable;
        }
     },
     RigaModificata :
     {
        get()
        {
           return JSON.stringify(this.Riga.Dati) != this.Riga.StartData;
        }
     },
  },

  watch:
  {
      CurrentRiga :
      {
         handler()
         {
            this.CheckDimensioniRiga()
         }
      }
  },

  mounted()
  {
    this.CheckDimensioniRiga()
  } 

}
</script>