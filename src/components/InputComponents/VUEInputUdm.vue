<template>
 <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
   <option :selected="modelValue == -1" value="-1">-</option>
   <option v-for="SelectOption in UnitaDiMisura" 
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
               UnitaDiMisura : {}   
            }
  },
  props : ['modelValue'],

  beforeMount()
  {
    var Self = this;
    SystemInformation.AdvQuery.GetSQL('UnitaDiMisura',{},
                                      function(Results)
                                      {
                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"TutteLeUnita");
                                        Self.UnitaDiMisura = ArrayInfo
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      });
  }
}
</script>