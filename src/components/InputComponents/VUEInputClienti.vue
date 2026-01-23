<template>
 <div style="float:left;" :style="{ width : VisualizzaImmagine ? '85%' : '100%'}">
   <Autocomplete v-model="ChiaveCliente"
                 placeholder="Cliente"
                 FieldChiave = "CHIAVE"
                 FieldDescrizione = "IDENTIFICATIVO"
                 FieldStato = "STATO"
                 InvalidValue = "-1"
                 InputClasses="form-control"
                 Input="Attivi"
                 :items="ListaClientiModificata"
                 :disabled="disabled"
                 :minInputLength="1"
                 @change="OnChangeEmit"
                 />
 </div>
 <div v-if="VisualizzaImmagine" style="width:1%;float:left;">&nbsp;</div>
 <div v-if="VisualizzaImmagine" style="width:14%;float:left;">
  <img src="@/assets/images/IconeAlbero/Cliente2.png" style="float:left;margin-top:-2px;cursor:pointer;width:30px;height:30px;" 
      v-if="StatoCliente==0"
      @click="OnClickImmagineAttiva()" 
      title="Clienti attivi"/>
  <img src="@/assets/images/IconeAlbero/ClienteNonAttivo2.png" style="float:left;margin-top:-2px;cursor:pointer;width:30px;height:30px;" 
      v-if="StatoCliente==1"
      @click="OnClickImmagineAttiva()"
      title="Clienti non attivi" />
  <img src="@/assets/images/IconeAlbero/tuttiiclienti.png" style="float:left;margin-top:-2px;cursor:pointer;width:30px;height:30px;" 
      v-if="StatoCliente==2"
      @click="OnClickImmagineAttiva()"
      title="Tutti i clienti" />
 </div>

</template>


<script>
import { SystemInformation } from '@/SystemInformation';
import Autocomplete from '../../../../../../../../Librerie/VUE/ZAutocomplete.vue'

const CLIENTI_ATTIVI     = 0;
const CLIENTI_NON_ATTIVI = 1;
const CLIENTI_TUTTI      = 2;
 export default 
 {
    data()
    {
       return {
                 StatoCliente  : CLIENTI_ATTIVI,
                 ChiaveCliente : -1,
                 Clienti       : SystemInformation.Configurazioni.Clienti,
                 ClientiNonAttivi : SystemInformation.Configurazioni.ClientiNonAttivi,
                 VisualizzaImmagine : false,
              }
    },
    props : [ 'modelValue' ,'disabled', 'ShowImage', 'OggettoClienteInattivo', 'TakeAllClienti'],
    emits : [ 'onChangeInputCliente', 'onUpdate'],
    components: 
    {
      Autocomplete
    },
    beforeMount()
    {
      if(this.ShowImage != null && this.ShowImage)
        this.VisualizzaImmagine = true
    },
    mounted()
    {
      this.ChiaveCliente = this.modelValue;

    },
    computed : 
    {
      ListaClientiModificata:
      {
        get()
        {
          var Result = [] 
          var Self = this;
          this.Clienti.forEach(function(Cliente)
          { 
            if(Self.StatoCliente == CLIENTI_TUTTI ||
               (Cliente.ATTIVO == 'T' && Self.StatoCliente == CLIENTI_ATTIVI) ||
               (Cliente.ATTIVO != 'T' && Self.StatoCliente == CLIENTI_NON_ATTIVI))
            {
              Result.push({
                            IDENTIFICATIVO : Cliente.CODICE_CLIENTE + ' - ' + Cliente.RAGIONE_SOCIALE,
                            CHIAVE         : Cliente.CHIAVE
                          })
            }
          })   

          if (Self.OggettoClienteInattivo != null) 
          {
            let GiaPresente = Result.some(item => item.CHIAVE == Self.OggettoClienteInattivo.CHIAVE);
            if (!GiaPresente) 
            {
              Result.push({
                            IDENTIFICATIVO: Self.OggettoClienteInattivo.CODICE + ' - ' + Self.OggettoClienteInattivo.RAGIONE_SOCIALE + ' - NON ATTIVO',
                            CHIAVE: Self.OggettoClienteInattivo.CHIAVE
                          });
            }
          }

          let ControlloNonAttivi = this.ClientiNonAttivi.find(item => item.CHIAVE == this.modelValue);
          if (ControlloNonAttivi != undefined) 
          {
            Result.push({
                          IDENTIFICATIVO: ControlloNonAttivi.CODICE_CLIENTE + ' - ' + ControlloNonAttivi.RAGIONE_SOCIALE + ' - NON ATTIVO',
                          CHIAVE: ControlloNonAttivi.CHIAVE
                        });
          }

          if(this.TakeAllClienti)
          {
            Result = [] 
            this.Clienti.forEach(function(Cliente)
            {
              Result.push({
                            IDENTIFICATIVO : Cliente.CODICE_CLIENTE + ' - ' + Cliente.RAGIONE_SOCIALE,
                            CHIAVE         : Cliente.CHIAVE
                          })
            })
          }

          return Result       
        }
      },
    },
  

    methods :
    {
      OnChangeEmit()
      {
        this.$emit('onChangeInputCliente')
      },

      OnClickImmagineAttiva()
      {
        switch(this.StatoCliente)
        {
          case CLIENTI_ATTIVI:
                this.StatoCliente = CLIENTI_NON_ATTIVI;
                break;
          case CLIENTI_NON_ATTIVI:
                this.StatoCliente = CLIENTI_TUTTI;
                break;
          case CLIENTI_TUTTI:
                this.StatoCliente = CLIENTI_ATTIVI;
                break;
        }
      },
    },
    watch :
    {
      ChiaveCliente :
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
          this.ChiaveCliente = NewValue;
        }
      }
    }
 }
 
</script>

<style lang="scss" scoped>

</style>