<template>
 <div style="float:left; width: 100%">
   <Autocomplete v-model="ChiaveContropartita"
                 placeholder="Contropartita"
                 FieldChiave = "CHIAVE"
                 FieldDescrizione = "DESCRIZIONE"
                 FieldCodice = "CODICE"
                 InvalidValue = "-1"
                 InputClasses="form-control"
                 Input="Attivi"
                 :items="ListaContropartiteModificata"
                 :disabled="disabled"
                 :minInputLength="1"
                 @change="OnChangeEmit"
                 />
 </div>
</template>

<script>
import { SystemInformation } from '@/SystemInformation';
import Autocomplete from '../../../../../../../../Librerie/VUE/ZAutocomplete.vue'

 export default 
 {
    data()
    {
       return {
                 ChiaveContropartita       : -1,
                 Contropartite             : SystemInformation.Configurazioni.Contropartite,
              }
    },
    props : [ 'modelValue' ,'disabled'],
    emits : [ 'onChangeInputContropartita', 'update:modelValue'],
    components: 
    {
      Autocomplete
    },
    mounted()
    {
      this.ChiaveContropartita = this.modelValue;
    },
    computed : 
    {
      ListaContropartiteModificata:
      {
        get()
        {
          var Result = [] 
          this.Contropartite.forEach(function(Contropartita)
          { 
            Result.push({
                          DESCRIZIONE : Contropartita.CODICE + ' - ' + Contropartita.DESCRIZIONE,
                          CHIAVE      : Contropartita.CHIAVE
                        })
          })   

          return Result       
        }
      },
    },
  

    methods :
    {
      OnChangeEmit()
      {
        this.$emit('onChangeInputContropartita')
      },
    },
    watch :
    {
      ChiaveContropartita :
      {
        handler(NewValue)
        {
           if(NewValue != undefined)
              this.$emit('update:modelValue',NewValue)
        }
      },

      modelValue :
      {
        handler(NewValue)
        {
          this.ChiaveContropartita = NewValue;
        }
      }
    }
 }
 
</script>
