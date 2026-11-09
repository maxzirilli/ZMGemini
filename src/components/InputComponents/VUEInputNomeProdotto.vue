<template>
  <div>
    <VUEModalCaricamentoDati v-if="PrezzoInCaricamento" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>
    <textarea ref="InputDescrizione"
              type="text"
              wrap="off"
              class="form-control"
              style="resize:none;overflow-y:hidden;"
              :style="{height: AltezzaTextArea ? AltezzaTextArea : '34px'}"
              :value="modelValue"
              :readonly="disabled"
              @input="DigitazioneDescrizione"
              @focus="ApriSuggerimenti"
              @blur="ChiudiSuggerimenti"
              @keydown.down="FrecciaGiu"
              @keydown.up="FrecciaSu"
              @keydown.enter="PressioneInvio"
              @keydown.esc="ChiudiSuggerimenti"/>
    <Teleport to="body">
      <div v-if="!disabled && SuggerimentiVisibili && ProdottiSuggeriti.length > 0"
           ref="ListaSuggerimenti"
           style="position:fixed;z-index:100000;max-height:240px;overflow-y:auto;background:rgba(255,255,255,0.94);border:1px solid #cbd5db;box-shadow:0 3px 10px rgba(0,0,0,0.12);font-size:13px"
           :style="PosizioneSuggerimenti">
        <div v-for="(Prodotto, i) in ProdottiSuggeriti"
             :key="Prodotto.CHIAVE"
             style="padding:5px 8px;cursor:pointer"
             :style="{background: i == IndiceSuggerimento ? 'rgba(208,233,255,0.8)' : ''}"
             @mouseenter="IndiceSuggerimento = i"
             @mousedown.prevent="SelezionaProdotto(Prodotto)">
          <strong>{{ Prodotto.NOME_PRODOTTO }}</strong>
          <small v-if="Prodotto.SETTORE || Prodotto.PART_NUMBER" style="display:block;color:#687680">
            {{ Prodotto.SETTORE }}{{ Prodotto.SETTORE && Prodotto.PART_NUMBER ? ' - ' : '' }}{{ Prodotto.PART_NUMBER }}
          </small>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script>
import { SystemInformation, TIPO_SCONTO } from '@/SystemInformation';
import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';

export default
{
  components: { VUEModalCaricamentoDati },
  props: ['modelValue', 'disabled', 'IdCliente', 'AltezzaTextArea', 'TipoProdotti', 'IdMagazzino'],
  emits: ['update:modelValue', 'onChangeDescrizione', 'onSelectProdotto'],
  data()
  {
    return {
             SuggerimentiVisibili  : false,
             IndiceSuggerimento    : -1,
             PrezzoInCaricamento   : false,
             Prodotti              : this.TipoProdotti == 'DDTEntrante' ? SystemInformation.GetProdottiSemplici() :
                                     this.TipoProdotti == 'DDT' || this.TipoProdotti == 'Scarico' ? [] :
                                     SystemInformation.Configurazioni.Prodotti || [],
             ProdottiInCaricamento : false,
             MagazzinoCaricato     : null,
             PosizioneSuggerimenti : {}
           }
  },
  computed:
  {
    ProdottiSuggeriti()
    {
      let Ricerca = (this.modelValue || '').trim().toUpperCase()
      if(Ricerca == '')
        return []

      let Parole = Ricerca.split(/\s+/)
      let Risultato = []
      for(let i = 0; i < this.Prodotti.length; i++)
      {
        let Prodotto = this.Prodotti[i]
        if((Prodotto.NOME_PRODOTTO || '').toUpperCase() == Ricerca)
          Risultato.push(Prodotto)
        if(Risultato.length == 8)
          return Risultato
      }

      for(let i = 0; i < this.Prodotti.length; i++)
      {
        let Prodotto = this.Prodotti[i]
        let Nome = (Prodotto.NOME_PRODOTTO || '').toUpperCase()
        if(Nome.startsWith(Ricerca) && Nome != Ricerca)
          Risultato.push(Prodotto)
        if(Risultato.length == 8)
          return Risultato
      }

      for(let i = 0; i < this.Prodotti.length; i++)
      {
        let Prodotto = this.Prodotti[i]
        let Nome = (Prodotto.NOME_PRODOTTO || '').toUpperCase()
        if(!Nome.startsWith(Ricerca) && Parole.every(Parola => Nome.includes(Parola)))
          Risultato.push(Prodotto)
        if(Risultato.length == 8)
          return Risultato
      }
      return Risultato
    }
  },
  beforeUnmount()
  {
    window.removeEventListener('scroll', this.Scorrimento, true)
    window.removeEventListener('resize', this.ChiudiSuggerimenti)
  },
  methods:
  {
    CaricaProdottiDisponibili()
    {
      if(this.TipoProdotti != 'DDT' && this.TipoProdotti != 'Scarico')
        return
      if(!this.IdMagazzino || this.ProdottiInCaricamento)
        return

      let Magazzino = this.TipoProdotti + ':' + this.IdMagazzino + ':' + this.IdCliente
      if(this.MagazzinoCaricato == Magazzino)
        return

      this.Prodotti = []
      this.ProdottiInCaricamento = true
      let NomeQuery = this.TipoProdotti == 'DDT' ? 'ListaProdottiVariazionePrezzo' : 'ProdottiCompostiMagazzino'
      let NomeRisultato = this.TipoProdotti == 'DDT' ? 'ListaProdottiVariazionePrezzo' : 'ListaProdottiComposti'
      let Parametri = this.TipoProdotti == 'DDT' ?
                     { CHIAVE: this.IdCliente, ID_MAGAZZINO: this.IdMagazzino } :
                     { ID_MAGAZZINO: this.IdMagazzino }

      SystemInformation.AdvQuery.GetSQL('Magazzino', Parametri,
                                        (Results) =>
                                        {
                                          this.ProdottiInCaricamento = false
                                          if(Magazzino != this.TipoProdotti + ':' + this.IdMagazzino + ':' + this.IdCliente)
                                          {
                                            if(this.$refs.InputDescrizione && document.activeElement == this.$refs.InputDescrizione)
                                              this.CaricaProdottiDisponibili()
                                            return
                                          }

                                          let Prodotti = SystemInformation.AdvQuery.FindResults(Results, NomeRisultato)
                                          if(!Prodotti)
                                          {
                                            SystemInformation.HandleError('Impossibile ottenere la lista dei prodotti')
                                            return
                                          }
                                          this.Prodotti = Prodotti
                                          this.MagazzinoCaricato = Magazzino
                                        },
                                        (HTTPError, SubHTTPError, Response) =>
                                        {
                                          this.ProdottiInCaricamento = false
                                          SystemInformation.HandleError(HTTPError, SubHTTPError, Response)
                                        },
                                        NomeQuery)
    },

    DigitazioneDescrizione(Evento)
    {
      this.$emit('update:modelValue', Evento.target.value)
      this.$emit('onChangeDescrizione')
      this.IndiceSuggerimento = -1
      this.ApriSuggerimenti()
      this.$nextTick(() =>
      {
        if(this.$refs.ListaSuggerimenti)
          this.$refs.ListaSuggerimenti.scrollTop = 0
      })
    },

    ApriSuggerimenti()
    {
      if(this.disabled)
        return
      this.CaricaProdottiDisponibili()
      this.$nextTick(() =>
      {
        if(this.disabled || !this.$refs.InputDescrizione || document.activeElement != this.$refs.InputDescrizione)
          return

        let Rettangolo = this.$refs.InputDescrizione.getBoundingClientRect()
        this.PosizioneSuggerimenti = {
                                        left: Rettangolo.left + 'px',
                                        top: Rettangolo.bottom + 'px',
                                        width: Math.max(Rettangolo.width, 300) + 'px'
                                      }
        this.SuggerimentiVisibili = true
        window.addEventListener('scroll', this.Scorrimento, true)
        window.addEventListener('resize', this.ChiudiSuggerimenti)
      })
    },

    ChiudiSuggerimenti()
    {
      this.SuggerimentiVisibili = false
      this.IndiceSuggerimento = -1
      window.removeEventListener('scroll', this.Scorrimento, true)
      window.removeEventListener('resize', this.ChiudiSuggerimenti)
    },

    Scorrimento(Evento)
    {
      if(Evento.target != this.$refs.ListaSuggerimenti)
        this.ChiudiSuggerimenti()
    },

    FrecciaGiu(Evento)
    {
      if(this.disabled || this.ProdottiSuggeriti.length == 0)
        return
      Evento.preventDefault()
      if(!this.SuggerimentiVisibili)
        this.ApriSuggerimenti()
      if(this.IndiceSuggerimento < this.ProdottiSuggeriti.length - 1)
      {
        this.IndiceSuggerimento++
        this.MostraSuggerimentoSelezionato()
      }
    },

    FrecciaSu(Evento)
    {
      if(!this.SuggerimentiVisibili || this.ProdottiSuggeriti.length == 0)
        return
      Evento.preventDefault()
      if(this.IndiceSuggerimento > 0)
      {
        this.IndiceSuggerimento--
        this.MostraSuggerimentoSelezionato()
      }
    },

    MostraSuggerimentoSelezionato()
    {
      this.$nextTick(() =>
      {
        let Lista = this.$refs.ListaSuggerimenti
        if(!Lista)
          return
        let Suggerimento = Lista.children[this.IndiceSuggerimento]
        if(!Suggerimento)
          return
        let BordiLista = Lista.getBoundingClientRect()
        let BordiSuggerimento = Suggerimento.getBoundingClientRect()
        if(BordiSuggerimento.bottom > BordiLista.bottom)
          Lista.scrollTop += BordiSuggerimento.bottom - BordiLista.bottom
        else
        {
          if(BordiSuggerimento.top < BordiLista.top)
            Lista.scrollTop -= BordiLista.top - BordiSuggerimento.top
        }
      })
    },

    PressioneInvio(Evento)
    {
      if(this.disabled || !this.SuggerimentiVisibili || this.IndiceSuggerimento < 0 ||
         this.IndiceSuggerimento >= this.ProdottiSuggeriti.length)
        return

      Evento.preventDefault()
      this.SelezionaProdotto(this.ProdottiSuggeriti[this.IndiceSuggerimento])
    },

    SelezionaProdotto(Prodotto)
    {
      if(this.disabled || this.PrezzoInCaricamento)
        return
      this.ChiudiSuggerimenti()

      if(this.TipoProdotti == 'DDT' || this.TipoProdotti == 'DDTEntrante' || this.TipoProdotti == 'Scarico')
      {
        this.ConfermaProdotto(Prodotto)
        return
      }

      let IdCliente = this.IdCliente
      let Descrizione = this.modelValue
      this.PrezzoInCaricamento = true
      SystemInformation.AdvQuery.GetSQL('Magazzino', { CHIAVE: IdCliente },
                                        (Results) =>
                                        {
                                          this.PrezzoInCaricamento = false
                                          let Prodotti = SystemInformation.AdvQuery.FindResults(Results, 'ListaProdottiVariazionePrezzo')
                                          if(!Prodotti)
                                          {
                                            SystemInformation.HandleError('Impossibile ottenere il prezzo del prodotto')
                                            return
                                          }

                                          if(this.disabled || this.IdCliente != IdCliente || this.modelValue != Descrizione)
                                            return

                                          let ProdottoConPrezzo = Prodotti.find(ProdottoLista => ProdottoLista.CHIAVE == Prodotto.CHIAVE)
                                          if(!ProdottoConPrezzo)
                                          {
                                            SystemInformation.HandleError('Impossibile trovare il prodotto selezionato')
                                            return
                                          }

                                          this.ConfermaProdotto(ProdottoConPrezzo)
                                        },
                                        (HTTPError, SubHTTPError, Response) =>
                                        {
                                          this.PrezzoInCaricamento = false
                                          SystemInformation.HandleError(HTTPError, SubHTTPError, Response)
                                        },
                                        'ListaProdottiVariazionePrezzo')
    },

    ConfermaProdotto(Prodotto)
    {
      let Prezzo = Prodotto.PREZZO_IMPONIBILE / 100
      let Sconto = 0
      if(Prodotto.TIPO_SCONTO == TIPO_SCONTO.Prezzo)
        Prezzo = parseFloat((Prodotto.VALORE / 100).toFixed(2))
      else
      {
        if(Prodotto.TIPO_SCONTO == TIPO_SCONTO.Sconto)
          Sconto = parseFloat((Prodotto.VALORE / 100).toFixed(2))
        else
        {
          if(Prodotto.SCONTO_GENERALE != null && Prodotto.SCONTO_GENERALE != 0)
            Sconto = parseFloat((Prodotto.SCONTO_GENERALE / 100).toFixed(2))
        }
      }
      this.$emit('update:modelValue', Prodotto.NOME_PRODOTTO)
      this.$emit('onSelectProdotto', Object.assign({}, Prodotto, { PREZZO_DOCUMENTO: Prezzo, SCONTO_DOCUMENTO: Sconto }))
    }
  }
}
</script>
