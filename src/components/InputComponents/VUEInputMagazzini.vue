<template>
 <div style="float:left; width: 100%" >
   <Autocomplete v-model="ChiaveMagazzino"
                 placeholder="Magazzino"
                 FieldChiave = "CHIAVE"
                 FieldDescrizione = "DESCRIZIONE"
                 FieldStato = "STATO"
                 InvalidValue = "-1"
                 InputClasses="form-control"
                 Input="Attivi"
                 :items="ListaMagazziniModificata"
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
                 ChiaveMagazzino    : -1,
                 Magazzini          : SystemInformation.Configurazioni.Magazzini
              }
    },
    props : [ 'modelValue' ,'disabled'],
    emits : [ 'onChangeInputMagazzino', 'onUpdate'],
    components: 
    {
      Autocomplete
    },
    beforeMount()
    {

    },
    mounted()
    {
      this.ChiaveMagazzino = this.modelValue;

    },
    computed : 
    {
      ListaMagazziniModificata:
      {
        get()
        {
          var Result = [] 
          
          this.Magazzini.forEach(function(Magazzini)
          {
            Result.push({
                          DESCRIZIONE : Magazzini.DESCRIZIONE,
                          CHIAVE      : Magazzini.CHIAVE
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
        this.$emit('onChangeInputMagazzino')
      }
    
    },
    watch :
    {
      ChiaveMagazzino :
      {
        handler(NewValue)
        {
           if(NewValue != undefined)
              this.$emit('onUpdate',NewValue)
        }
      },

      modelValue :
      {
        handler(NewValue)
        {
          this.ChiaveMagazzino = NewValue;
        }
      }
    }
 }
 
</script>

<style lang="scss" scoped>

</style>