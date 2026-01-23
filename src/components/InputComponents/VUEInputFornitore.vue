<template>
 <div>
   <Autocomplete v-model="ChiaveFornitore"
                 placeholder="Fornitore"
                 FieldChiave = "CHIAVE"
                 FieldDescrizione = "IDENTIFICATIVO"
                 InvalidValue = "-1"
                 InputClasses="form-control"
                 :items="ListaFornitoriModificata"
                 id="EditFornitori"
                 :disabled="disabled"
                 :minInputLength="1" />
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
                 ChiaveFornitore : -1,
                 Fornitori       : SystemInformation.Configurazioni.Fornitori,
              }
    },
    props : [ 'modelValue', 'disabled'],
    components: 
    {
     Autocomplete
    },
    mounted()
    {
      this.ChiaveFornitore = this.modelValue;
    },
    methods :
    {
       ciao()
       {
          alert(this.modelValue)   
       }
    },
    computed : 
    {
      ListaFornitoriModificata:
      {
        get()
        {
          this.Fornitori.forEach(function(Fornitore)
          { 
            if(Fornitore.CODICE_FORNITORE != null && Fornitore.CODICE_FORNITORE != '')
              Fornitore.IDENTIFICATIVO = Fornitore.CODICE_FORNITORE + ' - ' + Fornitore.RAGIONE_SOCIALE 
            else Fornitore.IDENTIFICATIVO = Fornitore.RAGIONE_SOCIALE
          })
          return this.Fornitori
        }
      }
    },
    watch :
    {
      ChiaveFornitore :
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
             this.ChiaveFornitore = NewValue;
           }
      }
    }
  
 }
</script>

<style lang="scss" scoped>

</style>