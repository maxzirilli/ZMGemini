<template>
  <div>
   <Autocomplete v-model="ChiaveProdotto"
                 placeholder="Prodotto"
                 FieldChiave = "CHIAVE"
                 FieldDescrizione = "IDENTIFICATIVO"
                 InvalidValue = "-1"
                 InputClasses="form-control"
                 :items="ListaProdottiModificata"
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
                 ChiaveProdotto : -1,
                 Prodotti       : SystemInformation.GetProdottiSemplici()
              }
    },
    props : [ 'modelValue' ,'disabled'],
    emits : [ 'onChangeInputProdotto', 'onUpdate'],
    components: 
    {
     Autocomplete
    },
    mounted()
    {
      this.ChiaveProdotto = this.modelValue;
    },
    computed :
    {
      ListaProdottiModificata:
      {
        get()
        {
          var Result = []
          this.Prodotti.forEach(function(Prodotto)
          {
            if(Prodotto.DESCRIZIONE != '')
            {
              Result.push({
                            IDENTIFICATIVO : Prodotto.DESCRIZIONE,
                            CHIAVE         : Prodotto.CHIAVE
                          })
            }
          })
          return Result
        }
      },
    },

    methods :
    {
      OnChangeEmit()
      {
        this.$emit('onChangeInputProdotto')
      }
    },

    watch :
    {
      ChiaveProdotto :
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
          this.ChiaveProdotto = NewValue;
        }
      },
    }
  
 }
</script>

<style lang="scss" scoped>

</style>