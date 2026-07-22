<template>
  <VUEModal :Titolo="'Selezione indirizzo'"
            :Altezza="'420px'"
            :Larghezza="'950px'"
            @onClickChiudiModal="OnClickAnnulla"
            :Programma="Programma"
            :PathLogo="PathLogo">
    <template v-slot:Body>
      <div class="table-responsive" style="max-height:370px;width:100%;">
        <table class="table table-striped b-t b-light" style="width:100%;">
          <thead>
            <tr>
              <th style="background-color:#42586f; color:white;width:5%;position:sticky;top:0;text-align:center;">&nbsp;</th>
              <th style="background-color:#42586f; color:white;width:65%;position:sticky;top:0;text-align:left;">Indirizzo</th>
              <th style="background-color:#42586f; color:white;width:15%;position:sticky;top:0;text-align:center;">Latitudine</th>
              <th style="background-color:#42586f; color:white;width:15%;position:sticky;top:0;text-align:center;">Longitudine</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(Risultato, i) in Risultati" :key="i" @click="RisultatoSelezionato = Risultato" style="cursor:pointer;">
              <td style="text-align:center;vertical-align:middle;">
                <input type="radio" :checked="RisultatoSelezionato === Risultato"/>
              </td>
              <td style="vertical-align:middle;">{{ TestoRisultato(Risultato) }}</td>
              <td style="text-align:center;vertical-align:middle;">{{ Risultato.lat }}</td>
              <td style="text-align:center;vertical-align:middle;">{{ Risultato.lon }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
    <template v-slot:Footer>
      <button class="btn btn-danger" @click="OnClickAnnulla" style="float:right;width:20%">Annulla</button>
      <button class="btn btn-success" @click="OnClickConferma" style="float:right;margin-right:20px;width:20%" :disabled="RisultatoSelezionato == null">Conferma</button>
    </template>
  </VUEModal>
</template>

<script>
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';

export default
{
  props: ['Risultati', 'Programma', 'PathLogo'],
  emits: ['onClickConferma', 'onClickAnnulla'],
  components:
  {
    VUEModal
  },
  data()
  {
    return {
      RisultatoSelezionato : this.Risultati != undefined && this.Risultati.length > 0 ? this.Risultati[0] : null
    }
  },
  methods:
  {
    TestoRisultato(Risultato)
    {
      if(Risultato == undefined || Risultato == null)
        return ''

      if(Risultato.display_name != undefined && Risultato.display_name != null && String(Risultato.display_name).trim() != '')
        return Risultato.display_name

      return this.TestoDaAddress(Risultato.address)
    },

    TestoDaAddress(Address)
    {
      if(Address == undefined || Address == null)
        return ''

      var DatiIndirizzo = []
      this.AccodaDato(DatiIndirizzo, Address.road)
      this.AccodaDato(DatiIndirizzo, Address.house_number)
      this.AccodaDato(DatiIndirizzo, Address.postcode)
      this.AccodaDato(DatiIndirizzo, Address.city)
      this.AccodaDato(DatiIndirizzo, Address.town)
      this.AccodaDato(DatiIndirizzo, Address.village)
      this.AccodaDato(DatiIndirizzo, Address.municipality)
      this.AccodaDato(DatiIndirizzo, Address.county)
      this.AccodaDato(DatiIndirizzo, Address.state)
      this.AccodaDato(DatiIndirizzo, Address.country)

      return DatiIndirizzo.join(', ')
    },

    AccodaDato(DatiIndirizzo, Valore)
    {
      if(Valore != undefined && Valore != null && String(Valore).trim() != '')
        DatiIndirizzo.push(String(Valore).trim())
    },

    OnClickConferma()
    {
      if(this.RisultatoSelezionato != null)
        this.$emit('onClickConferma', this.RisultatoSelezionato)
    },

    OnClickAnnulla()
    {
      this.$emit('onClickAnnulla')
    }
  }
}
</script>
