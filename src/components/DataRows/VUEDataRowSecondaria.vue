<template>
<VUEConfirm :Popup="PopupDataRowSecondaria" :Richiesta="'Eliminare la riga selezionata?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
</VUEConfirm>
  <td v-for="Colonna in DataTableSecondaria.Configurazione.Colonne" :Key="Colonna.Id">
    <div v-if="CurrentRiga.IsString(Colonna.Campo)" style="text-align:center;padding-top:6px;">
     <input type="text" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :disabled="Colonna.Disabilitato"/>
    </div>
    <div v-if="CurrentRiga.IsBoolean(Colonna.Campo)" style="text-align:center;padding-top:6px;">
       <input type="checkbox" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" @change="OnCheckBoxChange(CurrentRiga,Colonna)" :disabled="Colonna.Disabilitato"/>
    </div>
    <div v-if="CurrentRiga.IsNumber(Colonna.Campo)" style="text-align:center;padding-top:6px;">
       <input type="number" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :step="CurrentRiga.StepNumber(Colonna.Tipo)" :disabled="Colonna.Disabilitato"/>
    </div>
    <div v-if="CurrentRiga.IsSelect(Colonna.Campo)" style="text-align:center;padding-top:6px">
         <select class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" style="cursor:default" :disabled="Colonna.Disabilitato">
           <option v-for="SelectOption in Colonna.Lista" 
                   :Key="SelectOption[Colonna.SelCampoChiave]"
                   :value="SelectOption[Colonna.SelCampoChiave]">{{SelectOption[Colonna.SelCampoCaption]}}
            </option>
         </select>
     </div>
     <div v-if="CurrentRiga.IsAButton(Colonna.Campo)" style="text-align:center;padding-top:6px;">
      <button class="btn btn-sm btn-success" @click="EventoOnClick" v-if="!CurrentRiga.Graphics[Colonna.Campo].ButtonContentIsImage">{{ Riga.Graphics[Colonna.Campo].ButtonContent }}</button>
      <img style="height:34px;width:34px;cursor:pointer" :src="CurrentRiga.Graphics[Colonna.Campo].ButtonContent" @click="EventoOnClick" v-if="CurrentRiga.Graphics[Colonna.Campo].ButtonContentIsImage">
     </div>
     <div v-if="CurrentRiga.IsDate(Colonna.Campo)" style="text-align:center;padding-top:6px;">
        <input type="date" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsTime(Colonna.Campo)" style="text-align:center;padding-top:6px;">
        <input type="time" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsDateTime(Colonna.Campo)" style="text-align:center;padding-top:6px;">
        <input type="datetime-local" class="form-control" v-model="CurrentRiga.Dati[Colonna.Campo].Valore" :disabled="Colonna.Disabilitato"/>
     </div>
     <div v-if="CurrentRiga.IsMemo(Colonna.Campo)" style="text-align:center;padding-top:6px;">
      <textarea style="resize:none;overflow-y:hidden" 
               :style="{height : CurrentRiga.AltezzaTextArea? CurrentRiga.AltezzaTextArea : '34px'}" 
                type="text" class="form-control" 
                v-model="CurrentRiga.Dati[Colonna.Campo].Valore" 
               :disabled="Colonna.Disabilitato"
               @input="CheckDimensioniRiga()"/>
     </div>
  </td>
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
      <i class="fa fa-trash-o text-danger ZMIconButton" @click="OnClickEliminaRiga(CurrentRiga)"></i>
    </div>
  </td>
</template>

<script>
import VUEConfirm from '@/components/VUEConfirm.vue';
export default 
{
  components : { VUEConfirm },
  emits: ['onClick'],
  props: ['Riga','DataTableSecondaria'],
  data()
  {
     return {
               PopupDataRowSecondaria : false,
               RigaPopup : null
     }
  },
  methods: 
  {
    OnClickEliminaRiga(Riga)
    {
       this.PopupDataRowSecondaria = true
       this.RigaPopup = Riga
    },
    ConfermaElimina()
    {
       this.DataTableSecondaria.DeleteRow(this.RigaPopup);
       this.AnnullaElimina()
    },
    AnnullaElimina()
    {
       this.PopupDataRowSecondaria = false
    },
    OnCheckBoxChange(CurrentRiga,Colonna)
    {
     if(Colonna.BoolEsclusivo && CurrentRiga.Dati[Colonna.Campo].Valore)
        this.DataTableSecondaria.ResetBoolExceptFor(CurrentRiga,Colonna.Campo);
       
    },

    CheckDimensioniRiga()
    {
      let NrRighe = 0
      var Self = this;
      this.DataTableSecondaria.Configurazione.Colonne.forEach(function(Colonna)
      {
         if(Self.CurrentRiga.IsMemo(Colonna.Campo))
         {
            let NrRigheColonna = Self.CurrentRiga.Dati[Colonna.Campo].Valore.split("\n").length; 
            if(NrRighe < NrRigheColonna)
               NrRighe = NrRigheColonna
         }
      });
      this.CurrentRiga.AltezzaTextArea = NrRighe  <= 1 ? '34px' : (NrRighe * 22 + 10) + 'px'
    },

    EventoOnClick()
    {
      this.$emit('onClick') 
    }
  },
  computed: 
  {
     CurrentRiga : 
     { 
        get() {
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
  },
  mounted()
  {
    this.CheckDimensioniRiga()
  } 

}
</script>