<template>
  <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
    <option :selected="modelValue == -1" value="-1">-</option>
    <option v-for="SelectOption in ListaCausali" 
            :Key="SelectOption.CHIAVE"
            :value="SelectOption.CHIAVE"
            :selected="SelectOption.CHIAVE == modelValue">
            {{SelectOption.DESCRIZIONE}}
     </option>
  </select>
</template>

<script>
 import { SystemInformation, RIFERIMENTO_CAUSALI } from '@/SystemInformation.js';

 export default {
   data()
   {
      return {
             }
   },
   props : ['modelValue', 'Riferimento'],

   computed :
   {
      ListaCausali :
      {
        get()
        {
            let Result = []
            let RiferimentoConfronto = null
            if(this.Riferimento != undefined)
              RiferimentoConfronto = this.Riferimento
            else RiferimentoConfronto = RIFERIMENTO_CAUSALI.Cliente
            SystemInformation.Configurazioni.Causali.forEach(function(Elemento)
            {
              if(Elemento.RIFERIMENTO == RiferimentoConfronto)
                Result.push(Elemento)
            })
            return Result
        }
      }
   }
 }
</script>