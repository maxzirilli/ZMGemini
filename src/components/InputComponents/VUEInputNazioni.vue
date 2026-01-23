<template>
  <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
    <option :selected="modelValue == -1" value="-1">-</option>
    <option v-for="SelectOption in Nazioni" 
            :Key="SelectOption.CHIAVE"
            :value="SelectOption.CHIAVE"
            :selected="SelectOption.CHIAVE == modelValue">
            {{SelectOption.NOME}}
     </option>
  </select>
</template>

<script>
 import { SystemInformation } from '@/SystemInformation.js';

 export default {
   data()
   {
      return {
                Nazioni : SystemInformation.Configurazioni.Nazioni   
             }
   },
   props : ['modelValue']
 }
</script>
