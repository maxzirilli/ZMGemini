<template>
  <VUEModal v-if="Popup" :Titolo="'Conferma'" :Altezza="'100px'" :Larghezza="'500px'" 
            @onClickChiudiModal="ChiudiPopup" @onClickConfermaModal="ConfermaPopup">
    <template v-slot:Body>
      <p style="white-space: pre-line">{{Richiesta}}</p>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="ChiudiPopup" data-dismiss="modal">No</button>
      <button type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="ConfermaPopup" data-dismiss="modal">Sì</button>
    </template>
  </VUEModal>
</template>

<script>

import VUEModal from '@/components/Slots/VUEModal.vue';
export default 
{
  name : "VUEConfirm",
  data()
  {
   return {}
  },
  props : ['Richiesta', 'Popup', 'Row'],
  emits : ['onClickChiudiPopup', 'onClickConfermaPopup'],
  components: { VUEModal },
  methods :
  {
    ConfermaPopup()
    {
        this.$emit('onClickConfermaPopup', this.Row)
    },
    ChiudiPopup()
    {
        this.$emit('onClickChiudiPopup')
    }
  }
}
</script>
