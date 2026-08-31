<template>
  <td style="width:34%;">{{ CurrentRiga.Dati.NOME_PRODOTTO.Valore }}</td>
  <td style="width:18%;">{{ CurrentRiga.Dati.BARCODE.Valore }}</td>
  <td style="width:12%;">
    <input type="number"
           class="form-control"
           min="1"
           step="1"
           v-model.number="CurrentRiga.Dati.Quantita.Valore"
           @input="OnChangeQuantita">
  </td>
  <td style="width:18%;">{{ CurrentRiga.Dati.QuantitaAttuale.Valore }}</td>
  <td style="width:18%;">{{ CurrentRiga.Dati.QuantitaDopo.Valore }}</td>
  <td>&nbsp;</td>
</template>

<script>
export default
{
  name  : 'VUEDataRowMovimentoBarcode',
  props : ['Riga'],
  emits : ['onChangeQuantita'],

  data()
  {
    return {
             CurrentRiga : this.Riga
           }
  },

  methods :
  {
    OnChangeQuantita()
    {
      this.CurrentRiga.OnChange('Quantita');
      this.$emit('onChangeQuantita', this.CurrentRiga);
    }
  }
}
</script>
