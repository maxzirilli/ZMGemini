<template>
    <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
        <option v-for="SelectOption in LsContiCorrentiCasse" 
                :Key="SelectOption.CHIAVE"
                :value="SelectOption.CHIAVE"
                :selected="SelectOption.CHIAVE == modelValue">
                {{SelectOption.DESCRIZIONE}}
         </option>
    </select>
</template>
   
<script>
   
import { SystemInformation } from '@/SystemInformation';
   
export default 
{
  data()
  {
    return {
              LsContiCorrentiCasse : SystemInformation.GetLsContiCasse()
           }
  },
  props : ['modelValue'],
}
</script>