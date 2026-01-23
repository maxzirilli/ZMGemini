<template>
  <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
    <option :selected="modelValue == -1" value="-1">-</option>
    <option v-for="SelectOption in Regioni" 
            :Key="SelectOption.CHIAVE"
            :value="SelectOption.CHIAVE"
            :selected="SelectOption.CHIAVE == modelValue">
            {{SelectOption.DESCRIZIONE}}
     </option>
  </select>
</template>

<script>
 import { SystemInformation } from '@/SystemInformation.js';

 export default {
   data()
   {
      return {
                Regioni : SystemInformation.Configurazioni.Regioni   
             }
   },
   props : ['modelValue']
 }
</script>
