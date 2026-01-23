<template>
  <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
    <option :selected="modelValue == -1" value="-1">-</option>
    <option v-for="SelectOption in NaturaPagamenti" 
            :Key="SelectOption.Id"
            :value="SelectOption.Id"
            :selected="SelectOption.Id == modelValue">
            {{SelectOption.Descrizione}}
     </option>
  </select>
</template>

<script>
 import { TZFatturaElettronica } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';

 export default {
   data()
   {
      return {
                NaturaPagamenti : TZFatturaElettronica.GetLsNaturaPagamenti()
             }
   },
   props : ['modelValue']
 }
</script>
