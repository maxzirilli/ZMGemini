<template>
  <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
    <option v-if="NessunaSelezione" selected value="-1">-</option>
    <option v-else-if="NessunaSelezione == false || NessunaSelezione == undefined"></option>
    <option v-for="SelectOption in Mensilita" 
            :Key="SelectOption.Id"
            :value="SelectOption.Id"
            :selected="SelectOption.Id == modelValue">
            {{SelectOption.Descrizione}}
     </option>
  </select>
</template>

<script>
 import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'

 export default {
   data()
   {
      return {
                Mensilita : TZDateFunct.GetListaOggettiMensilita()
             }
   },
   props : ['modelValue', 'NessunaSelezione'],
           

 }
</script>
