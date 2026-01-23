<template>
 <select class="form-control" @change="$emit('update:modelValue',$event.target.value)">
   <option :selected="modelValue == -1" value="-1">-</option>
   <option v-for="SelectOption in LsFiliali" 
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
               LsFiliali : [],
            }
  },
  props : ['modelValue','ChiaveCliente', 'BloccoInserimentoAutomatico'],

  watch:
  {
     ChiaveCliente:
     {
        handler(NewValue)
        {
            if(NewValue != -1)
            {
              this.CaricaListaFiliali(NewValue)
            }
        }
     }
  },

  methods:
  {
      CaricaListaFiliali(Chiave)
      {
         var Self = this;
         var Parametri = {}
         if(this.ChiaveCliente != '' && this.ChiaveCliente != undefined && this.ChiaveCliente != null && this.ChiaveCliente != -1)
         {
            Parametri.CHIAVE_CLIENTE = Chiave
            SystemInformation.AdvQuery.GetSQL('Clienti',Parametri,
                                             function(Results)
                                             {
                                                let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Lista");
                                                if(ArrayInfo.length == 1)
                                                {
                                                   if(!Self.BloccoInserimentoAutomatico || Self.modelValue != -1)
                                                      Self.$emit('update:modelValue',parseInt(ArrayInfo[0].CHIAVE))
                                                }
                                                if(ArrayInfo.length == 0)
                                                {
                                                   Self.$emit('update:modelValue',-1)
                                                }
                                                Self.LsFiliali = ArrayInfo

                                                Self.LsFiliali.forEach(function(Filiale)
                                                {
                                                   Filiale.NOME = Filiale.NOME.toUpperCase() 
                                                })
                                             },
                                             function(HTTPError,SubHTTPError,Response)
                                             {
                                                SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                             },
                                             'ListaFiliali');
         }
      }
  },
  
  beforeMount()
  {
      this.CaricaListaFiliali(this.ChiaveCliente)
  }
}
</script>