<template>
 <div>
   <Autocomplete v-model="ChiaveAmministratore"
                 placeholder="Amministratore"
                 FieldChiave = "CHIAVE"
                 FieldDescrizione = "RAGIONE_SOCIALE"
                 InvalidValue = "-1"
                 InputClasses="form-control"
                 :items="ListaAmministratoriModificata"
                 id="EditAmministratori"
                 :minInputLength="1"
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
                 ChiaveAmministratore : -1,
                 Amministratori       : SystemInformation.Configurazioni.Amministratori,
                 ChiaveDefault        : 0
              }
    },
    props : [ 'modelValue' ,'disabled'],
    components: 
    {
     Autocomplete
    },
    mounted()
    {
      this.ChiaveAmministratore = this.modelValue;
    },
    computed : 
    {
      ListaAmministratoriModificata:
      {
        get()
        {
          // this.Amministratori.forEach(function(Amministratore)
          // { 
          //   if(Amministratore.CODICE_CLIENTE.trim() != '')
          //     Amministratore.IDENTIFICATIVO = Cliente.CODICE_CLIENTE + ' - ' + Cliente.RAGIONE_SOCIALE  
          //   else Cliente.IDENTIFICATIVO = Cliente.RAGIONE_SOCIALE
          // })
          return this.Amministratori
        }
      }
    },
    methods :
    {

    },
    watch :
    {
      ChiaveAmministratore :
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
             this.ChiaveAmministratore = NewValue;
           }
      },
    }
  
 }
</script>

<style lang="scss" scoped>

</style>