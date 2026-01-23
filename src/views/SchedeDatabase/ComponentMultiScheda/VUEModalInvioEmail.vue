<template>
 <VUEModal v-if="AttivazionePopup" :Titolo="Titolo" :Altezza="'500px'" :Larghezza="'60%'"
          @onClickChiudiModal="OnClickChiudiModal">
    <template v-slot:Body>
        <div class="ZMNuovaRigaScheda">
          <div style="float:left;width:100%;">
            <div v-if="VisualizzaLivelliSolleciti">
              <i title="Primo Livello" class="fa fa-circle" style="cursor:pointer;font-size:25px;padding-top:2px;border:0px; color:green;margin-left: 20px;background-color:" @click="onClickPrimoLivello"></i>
              <i title="Secondo Livello" class="fa fa-circle" style="cursor:pointer;font-size:25px;padding-top:2px;border:0px; color:yellow;margin-left: 20px;" @click="onClickSecondoLivello"></i>
              <i title="Terzo Livello"  class="fa fa-circle" style="cursor:pointer;font-size:25px;padding-top:2px;border:0px; color:red;margin-left: 20px;" @click="onClickTerzoLivello"></i>
            </div>

            <div v-if="ListaEmailCliente != null || ListaEmailAmministratore != null">
              <button v-if="ListaEmailCliente != null && ListaEmailCliente != ''" class="btn btn-info" style="width:25%;float:right;margin-left:5px;" @click="OnClickCaricaEmailCliente">Carica email cliente</button>
              <button v-if="ListaEmailAmministratore != null && ListaEmailAmministratore != ''"  class="btn btn-info" style="width:25%;float:right;margin-left:5px;" @click="OnClickCaricaEmailAmministratore">Carica email amm.</button>
            </div>

            <div v-if="EmailPEC != null || EmailPECAmministratore != null">
              <button v-if="EmailPEC != null && EmailPEC != ''" class="btn btn-info" style="width:25%;float:right;margin-left:5px;" @click="OnClickCaricaPECCliente">Carica PEC cliente</button>
              <button v-if="EmailPECAmministratore != null && EmailPECAmministratore != ''" class="btn btn-info"  style="width:25%;float:right;margin-left:5px;" @click="OnClickCaricaPECAmministratore">Carica PEC Amministratore</button>
            </div>

          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Destinatario</text>
            <text style="float:right;font-size:10px;margin-top:4px">(separare le varie email con un ;) &nbsp;&nbsp;   </text>
            <input type="text" style="text-transform: none!important;" placeholder="Destinatario" class="form-control" v-model="OggettoEmail.Destinatario"/>
          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Cc</text>
            <text style="float:right;font-size:10px;margin-top:4px">(separare le varie email con un ;) &nbsp;&nbsp;   </text>
            <input type="text" placeholder="Cc" style="text-transform: none!important;" class="form-control" v-model="OggettoEmail.Cc"/>
          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Ccn</text>
            <text style="float:right;font-size:10px;margin-top:4px">(separare le varie email con un ;) &nbsp;&nbsp;   </text>
            <input type="text" style="text-transform: none!important;" placeholder="Ccn" class="form-control" v-model="OggettoEmail.Ccn"/>
          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Oggetto</text>
            <input type="text" placeholder="Oggetto" style="text-transform: none!important;" class="form-control" v-model="OggettoEmail.Oggetto"/>
          </div>
          <div style="float:left;width:100%">
            <text style="font-weight: bold">Corpo email</text>
            <textarea type="text" style="text-transform:none!important;resize:none; height:200px" class="form-control" v-model="OggettoEmail.CorpoEmail"/>
          </div>
        </div>
    </template>
    <template v-slot:Footer>
      <button class="btn btn-danger" @click="OnClickChiudiModal" style="float:right;margin-right:20px;width:20%">Annulla</button>
      <button v-if="OggettoEmail.CorpoEmail.trim() != '' && OggettoEmail.Oggetto.trim() != '' && OggettoEmail.Destinatario.trim() != ''" class="btn btn-success" @click="OnClickConfermaModal" style="float:right;margin-right:20px;width:20%">Conferma</button>
    </template>
 </VUEModal>
</template>

<script>
import VUEModal from '@/components/Slots/VUEModal.vue';

export default 
{
  emits: ['onClickChiudiModal', 'onClickConfermaModal', 'onClickPrimoLivello','onClickSecondoLivello','onClickTerzoLivello'],

  components : 
  {
    VUEModal
  },

  data()
  {
    return { 
           }
  },

  props : ['OggettoEmail', 'AttivazionePopup', 'ListaEmailCliente', 'EmailPEC', 'ListaEmailAmministratore','VisualizzaLivelliSolleciti','Titolo','EmailPECAmministratore'],

  computed :
  {
    CurrentEmails:
    {
      get()
      {
        return this.ListaEmailCliente + this.EmailPEC + this.ListaEmailAmministratore;
      }
    },
  },
  
  methods : 
  {
    OnClickChiudiModal()
    {
      this.$emit('onClickChiudiModal')
    },

    OnClickConfermaModal()
    {
      this.$emit('onClickConfermaModal')
    },

    onClickPrimoLivello()
    {
      this.$emit('onClickPrimoLivello')
    },

    onClickSecondoLivello()
    {
      this.$emit('onClickSecondoLivello')
    },

    onClickTerzoLivello()
    {
      this.$emit('onClickTerzoLivello')
    },

    OnClickCaricaEmailAmministratore()
    {
      if(!this.OggettoEmail.Destinatario.includes(this.ListaEmailAmministratore))
        this.OggettoEmail.Destinatario += this.OggettoEmail.Destinatario != ''? '; ' + this.ListaEmailAmministratore : this.ListaEmailAmministratore
    },

    OnClickCaricaEmailCliente()
    {
      if(!this.OggettoEmail.Destinatario.includes(this.ListaEmailCliente))
        this.OggettoEmail.Destinatario += this.OggettoEmail.Destinatario != ''? '; ' + this.ListaEmailCliente : this.ListaEmailCliente
    },
    OnClickCaricaPECCliente()
    {
      if(!this.OggettoEmail.Destinatario.includes(this.EmailPEC))
        this.OggettoEmail.Destinatario += this.OggettoEmail.Destinatario != ''? '; ' + this.EmailPEC : this.EmailPEC
    },
    OnClickCaricaPECAmministratore()
    {
      if(!this.OggettoEmail.Destinatario.includes(this.EmailPECAmministratore))
        this.OggettoEmail.Destinatario += this.OggettoEmail.Destinatario != ''? '; ' + this.EmailPECAmministratore : this.EmailPECAmministratore
    }
  }
}
</script>
