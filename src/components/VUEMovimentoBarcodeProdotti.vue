<template>
  <div>
    <VUEModalCaricamentoDati v-if="PopupAttesaCalcolo" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>

    <VUEModal v-if="PopupBarcodeProdotto" :Titolo="'Movimento barcode'" :Altezza="'520px'" :Larghezza="'950px'"
              @onClickChiudiModal="ChiudiPopupBarcodeProdotto"
              :PathLogo="require('../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma">
        <template v-slot:Body>
          <div class="form-row">
            <div class="col-md-5">
              <label style="font-weight:bold;">Magazzino</label>
              <select class="form-control" v-model="MovimentoBarcode.ID_MAGAZZINO" @change="OnChangeMagazzinoMovimentoBarcode">
                <option :value="-1">-</option>
                <option v-for="Magazzino in LsMagazzini" :key="Magazzino.CHIAVE" :value="Magazzino.CHIAVE">
                  {{ Magazzino.DESCRIZIONE }}
                </option>
              </select>
            </div>
            <div class="col-md-3">
              <label style="font-weight:bold;">Movimento</label>
              <select class="form-control" v-model="MovimentoBarcode.Tipo" @change="AggiornaRigheMovimentoBarcode">
                <option value="E">Entrata</option>
                <option value="U">Uscita</option>
              </select>
            </div>
            <div class="col-md-4">
              <label style="font-weight:bold;">Ultimo barcode</label>
              <label class="form-control">{{ BarcodeProdottoLetto }}</label>
            </div>
            <div class="col-md-12" style="margin-top:15px;">
              <table class="table table-striped b-t b-light" style="width:100%; margin-bottom:0;">
                <thead style="background-color:white;">
                  <tr>
                    <template v-for="Colonna in DataTableMovimentiBarcode.Configurazione.Colonne" :key="Colonna.Id">
                      <th v-if="!Colonna.Nascosta"
                          :style="{ width : Colonna.Larghezza, 'text-align' : Colonna.GetTitleAlignment() }">
                        {{ Colonna.Titolo }}
                      </th>
                    </template>
                    <th></th>
                  </tr>
                </thead>
              </table>
              <VUEDataTable :DataTable="DataTableMovimentiBarcode" :NumeroRighe="6" :AltezzaTabella="'260px'"
                            :NomeProgramma="NomeProgramma" :PathLogo="require('../assets/images/LogoGemini2.png')">
                <template v-slot:RowAlternativa="{ Riga }">
                  <VUEDataRowMovimentoBarcode :Riga="Riga" @onChangeQuantita="AggiornaRigaMovimentoBarcode"/>
                </template>
              </VUEDataTable>
              <label v-if="DataTableMovimentiBarcode.Righe.length == 0" style="margin-top:8px;">Spara un barcode per aggiungere prodotti al movimento.</label>
            </div>
            <div v-if="MessaggioBarcodeProdotto != ''" class="col-md-12" style="margin-top:10px;">
              <label>{{ MessaggioBarcodeProdotto }}</label>
            </div>
          </div>
        </template>
        <template v-slot:Footer>
          <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="ChiudiPopupBarcodeProdotto" data-dismiss="modal">Chiudi</button>
          <button v-if="DataTableMovimentiBarcode.Righe.length != 0" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickConfermaMovimentoBarcode">Conferma</button>
        </template>
    </VUEModal>
  </div>
</template>

<script>
import { SystemInformation } from '@/SystemInformation.js';
import VUEDataRowMovimentoBarcode from '@/components/DataRows/VUEDataRowMovimentoBarcode.vue';
import VUEModal from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import VUEModalCaricamentoDati from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';
import VUEDataTable from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2.vue';
import { TZDataTable, TZDTableColumnType } from '../../../../../../../Librerie/VUE/ZDataTable2.js';
import { TZDateFunct } from '../../../../../../../Librerie/VUE/ZDateFunct';
import { TSchedaGenerica } from '../../../../../../../Librerie/VUE/ZSchedaGenerica.js';

export default
{
  name       : 'VUEMovimentoBarcodeProdotti',
  props      : ['Attivo', 'LsMagazzini', 'NomeProgramma'],
  emits      : ['onMovimentoRegistrato'],
  components : {
                 VUEModal,
                 VUEModalCaricamentoDati,
                 VUEDataTable,
                 VUEDataRowMovimentoBarcode
               },

  data()
  {
    let DataTableMovimentiBarcode = new TZDataTable('ID_PRODOTTO');

    DataTableMovimentiBarcode.ClearColumns();
    DataTableMovimentiBarcode.TastoAggiungiVisibile = false;
    DataTableMovimentiBarcode.EditRicercaVisibile = false;
    DataTableMovimentiBarcode.DatiEliminabili = false;
    DataTableMovimentiBarcode.Configurazione.AltezzaTutteLeRighe = '44px';
    DataTableMovimentiBarcode.AddColumn('Prodotto',
                                        TZDTableColumnType.typString,
                                        'NOME_PRODOTTO',
                                        '34%');
    DataTableMovimentiBarcode.AddColumn('Barcode',
                                        TZDTableColumnType.typString,
                                        'BARCODE',
                                        '18%');
    DataTableMovimentiBarcode.AddColumn('Quantita',
                                        TZDTableColumnType.typInteger,
                                        'Quantita',
                                        '12%');
    DataTableMovimentiBarcode.AddColumn('Qnt attuale',
                                        TZDTableColumnType.typString,
                                        'QuantitaAttuale',
                                        '18%');
    DataTableMovimentiBarcode.AddColumn('Qnt dopo',
                                        TZDTableColumnType.typString,
                                        'QuantitaDopo',
                                        '18%');

    return {
             PopupAttesaCalcolo         : false,
             PopupBarcodeProdotto       : false,
              BarcodeProdottoLetto       : '',
              MessaggioBarcodeProdotto   : '',
              MovimentoBarcode           : {
                                             ID_MAGAZZINO : SystemInformation.Configurazioni.Impostazioni.MAGAZZINO,
                                             Tipo         : 'U'
                                           },
             DataTableMovimentiBarcode  : DataTableMovimentiBarcode,
             BarcodeLetturaBuffer       : '',
             BarcodeUltimaPressione     : 0,
             BarcodePrimaPressione      : 0,
             BarcodeTimer               : null,
             KeyDownHandler             : null
           };
  },

  methods :
  {
    KeyDownBarcode(event)
    {
      if(this.IsInputAttivoBarcode(event))
      {
        this.ResetLetturaBarcodeProdotto();
        return;
      }

      this.GestioneLetturaBarcodeProdotto(event);
    },

    IsInputAttivoBarcode(event)
    {
      let Elemento = event.target;

      if(Elemento == null) return false;

      if(Elemento.isContentEditable) return true;

      let NomeTag = '';

      if(Elemento.tagName != null)
        NomeTag = Elemento.tagName.toUpperCase();

      if(NomeTag == 'INPUT') return true;
      if(NomeTag == 'TEXTAREA') return true;
      if(NomeTag == 'SELECT') return true;

      return false;
    },

    FermaTimerBarcode()
    {
      if(this.BarcodeTimer != null)
      {
        clearTimeout(this.BarcodeTimer);
        this.BarcodeTimer = null;
      }
    },

    ResetLetturaBarcodeProdotto()
    {
      this.BarcodeLetturaBuffer   = '';
      this.BarcodeUltimaPressione = 0;
      this.BarcodePrimaPressione  = 0;
      this.FermaTimerBarcode();
    },

    GestioneLetturaBarcodeProdotto(event)
    {
      if(!this.Attivo) return;
      if(event.ctrlKey || event.altKey || event.metaKey) return;

      let Tasto = event.key;

      if(Tasto == 'Enter')
      {
        this.ConfermaLetturaBarcodeProdotto();
        return;
      }

      if(!/^\d$/.test(Tasto))
      {
        this.ResetLetturaBarcodeProdotto();
        return;
      }

      let Ora = Date.now();

      if(this.BarcodeUltimaPressione != 0)
        if(Ora - this.BarcodeUltimaPressione > 80)
          this.ResetLetturaBarcodeProdotto();

      if(this.BarcodeLetturaBuffer == '')
        this.BarcodePrimaPressione = Ora;

      this.BarcodeLetturaBuffer += Tasto;
      this.BarcodeUltimaPressione = Ora;

      this.FermaTimerBarcode();

      this.BarcodeTimer = setTimeout(() =>
                                    {
                                      this.ConfermaLetturaBarcodeProdotto();
                                    }, 90);

      if(this.BarcodeLetturaBuffer.length >= 13)
        this.ConfermaLetturaBarcodeProdotto();
    },

    ConfermaLetturaBarcodeProdotto()
    {
      let BarcodeFiltro = SystemInformation.NormalizzaBarcode(this.BarcodeLetturaBuffer);
      let DurataLettura = this.BarcodeUltimaPressione - this.BarcodePrimaPressione;

      this.ResetLetturaBarcodeProdotto();

      if(BarcodeFiltro.length < 8) return;
      if(DurataLettura > 700) return;

      this.CercaProdottoDaBarcode(BarcodeFiltro);
    },

    CercaProdottoDaBarcode(Barcode)
    {
      var Self = this;
      let Parametri = {
                        Barcode      : Barcode,
                        LimitStart   : 0,
                        LimitNumRows : 2
                      };

      this.ApriPopupMovimentoBarcode();
      this.PopupAttesaCalcolo = true;
      this.BarcodeProdottoLetto = Barcode;
      this.MessaggioBarcodeProdotto = 'Ricerca prodotto in corso...';

      SystemInformation.AdvQuery.GetSQL('Magazzino', Parametri,
                                        function(Results)
                                        {
                                          Self.PopupAttesaCalcolo = false;

                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, 'ListaProdotti');

                                          if(ArrayInfo != undefined)
                                          {
                                            if(ArrayInfo.length == 1)
                                            {
                                              Self.AggiungiRigaMovimentoBarcode(ArrayInfo[0], Barcode);
                                            }
                                            else
                                            {
                                              if(ArrayInfo.length > 1)
                                                Self.MessaggioBarcodeProdotto = 'Trovati piu prodotti con questo barcode.';
                                              else
                                                Self.MessaggioBarcodeProdotto = 'Nessun prodotto trovato con questo barcode.';
                                            }
                                          }
                                          else
                                          {
                                            Self.MessaggioBarcodeProdotto = 'Impossibile cercare il prodotto.';
                                          }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          Self.PopupAttesaCalcolo = false;
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'ListaProdotti')
    },

    ChiudiPopupBarcodeProdotto()
    {
      this.PopupBarcodeProdotto = false;
      this.DataTableMovimentiBarcode.AssignDati([]);
      this.BarcodeProdottoLetto = '';
      this.MessaggioBarcodeProdotto = '';
    },

    ApriPopupMovimentoBarcode()
    {
      this.PopupBarcodeProdotto = true;
    },

    AggiungiRigaMovimentoBarcode(Prodotto, Barcode)
    {
      if(this.MovimentoBarcode.ID_MAGAZZINO == -1)
      {
        this.MessaggioBarcodeProdotto = 'Selezionare un magazzino.';
        return;
      }

      let Riga = this.GetRigaMovimentoBarcode(Prodotto.CHIAVE);

      if(Riga != null)
      {
        Riga.Dati.Quantita.Valore++;
        this.AggiornaRigaMovimentoBarcode(Riga);
        this.MessaggioBarcodeProdotto = 'Prodotto gia presente, quantita aggiornata.';
        return;
      }

      this.DataTableMovimentiBarcode.AssignDati([{
                                                  ID_PRODOTTO      : Prodotto.CHIAVE,
                                                  NOME_PRODOTTO    : Prodotto.NOME_PRODOTTO,
                                                  BARCODE          : Prodotto.BARCODE != null ? Prodotto.BARCODE : Barcode,
                                                  Quantita         : 1,
                                                  QuantitaAttuale  : 0,
                                                  QuantitaDopo     : 0
                                                }], true);

      Riga = this.GetRigaMovimentoBarcode(Prodotto.CHIAVE);
      this.MessaggioBarcodeProdotto = '';
      this.CaricaQntRigaMovimentoBarcode(Riga);
    },

    OnChangeMagazzinoMovimentoBarcode()
    {
      for(let i = 0; i < this.DataTableMovimentiBarcode.Righe.length; i++)
        this.CaricaQntRigaMovimentoBarcode(this.DataTableMovimentiBarcode.Righe[i]);
    },

    CaricaQntRigaMovimentoBarcode(Riga)
    {
      if(this.MovimentoBarcode.ID_MAGAZZINO == -1) return;
      if(Riga == null) return;

      var Self = this;
      let Parametri = {
                        ID_PRODOTTO  : TSchedaGenerica.PrepareForRecordInteger(Riga.Dati.ID_PRODOTTO),
                        ID_MAGAZZINO : TSchedaGenerica.PrepareForRecordInteger(this.MovimentoBarcode.ID_MAGAZZINO)
                      };

      SystemInformation.AdvQuery.GetSQL('Magazzino', Parametri,
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, 'SelectQntProdottoMagazzino');
                                          let QuantitaAttuale = 0;

                                          if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            QuantitaAttuale = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].QUANTITA_MAGAZZINO) / 100;

                                          Riga.Dati.QuantitaAttuale.Valore = QuantitaAttuale;
                                          Self.AggiornaRigaMovimentoBarcode(Riga);
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectQntProdottoMagazzino')
    },

    AggiornaRigheMovimentoBarcode()
    {
      for(let i = 0; i < this.DataTableMovimentiBarcode.Righe.length; i++)
        this.AggiornaRigaMovimentoBarcode(this.DataTableMovimentiBarcode.Righe[i]);
    },

    AggiornaRigaMovimentoBarcode(Riga)
    {
      let Segno = -1;
      let Quantita = Riga.Dati.Quantita.Valore;
      let QuantitaAttuale = Riga.Dati.QuantitaAttuale.Valore;

      if(this.MovimentoBarcode.Tipo == 'E')
        Segno = 1;

      if(Quantita == null || Quantita == '')
        Quantita = 0;

      Riga.Dati.QuantitaDopo.Valore = QuantitaAttuale + (Segno * Quantita);
    },

    GetRigaMovimentoBarcode(IdProdotto)
    {
      for(let i = 0; i < this.DataTableMovimentiBarcode.Righe.length; i++)
      {
        if(this.DataTableMovimentiBarcode.Righe[i].Dati.ID_PRODOTTO == IdProdotto)
          return this.DataTableMovimentiBarcode.Righe[i];
      }

      return null;
    },

    OnClickConfermaMovimentoBarcode()
    {
      if(this.MovimentoBarcode.ID_MAGAZZINO == -1)
      {
        this.MessaggioBarcodeProdotto = 'Selezionare un magazzino.';
        return;
      }

      if(this.DataTableMovimentiBarcode.Righe.length == 0)
      {
        this.MessaggioBarcodeProdotto = 'Sparare almeno un barcode.';
        return;
      }

      var Self = this;
      let Data = new Date();
      let Segno = -1;
      let Descrizione = 'Movimento barcode uscita';
      var ObjQuery = { Operazioni : [] };
      let ListaProdottiMovimentati = [];

      if(this.MovimentoBarcode.Tipo == 'E')
      {
        Segno = 1;
        Descrizione = 'Movimento barcode entrata';
      }

      for(let i = 0; i < this.DataTableMovimentiBarcode.Righe.length; i++)
      {
        let Riga = this.DataTableMovimentiBarcode.Righe[i];

        if(Riga.Dati.Quantita.Valore == null || Riga.Dati.Quantita.Valore <= 0)
        {
          this.MessaggioBarcodeProdotto = 'Inserire quantita maggiori di zero.';
          return;
        }

        let Quantita = TSchedaGenerica.PrepareForRecordInteger(Riga.Dati.Quantita.Valore * 100 * Segno);
        if(!ListaProdottiMovimentati.includes(Riga.Dati.ID_PRODOTTO))
          ListaProdottiMovimentati.push(Riga.Dati.ID_PRODOTTO);

        ObjQuery.Operazioni.push({
                                  Query     : 'UpdateQntProdottoBarcode',
                                  Parametri : {
                                                ID_PRODOTTO  : Riga.Dati.ID_PRODOTTO,
                                                ID_MAGAZZINO : this.MovimentoBarcode.ID_MAGAZZINO,
                                                QUANTITA     : Quantita
                                              }
                                });
        ObjQuery.Operazioni.push({
                                  Query     : 'InsertQntProdottoBarcode',
                                  Parametri : {
                                                ID_PRODOTTO  : Riga.Dati.ID_PRODOTTO,
                                                ID_MAGAZZINO : this.MovimentoBarcode.ID_MAGAZZINO,
                                                QUANTITA     : Quantita
                                              }
                                });
        ObjQuery.Operazioni.push({
                                  Query     : 'InsertLogMovimentoBarcode',
                                  Parametri : {
                                                CHIAVE       : undefined,
                                                DATA         : TZDateFunct.FormatDateTime('yyyy-mm-dd hh:nn:ss', Data),
                                                ID_PRODOTTO  : Riga.Dati.ID_PRODOTTO,
                                                ID_MAGAZZINO : this.MovimentoBarcode.ID_MAGAZZINO,
                                                QUANTITA     : Quantita,
                                                DESCRIZIONE  : Descrizione
                                              },
                                  ResetKeys : [2]
                                });
      }

      this.PopupAttesaCalcolo = true;
      SystemInformation.AdvQuery.PostSQL('Magazzino', ObjQuery,
                                        function()
                                        {
                                          Self.PopupAttesaCalcolo = false;
                                          Self.$emit('onMovimentoRegistrato', ListaProdottiMovimentati);
                                          Self.PopupBarcodeProdotto = false;
                                          Self.DataTableMovimentiBarcode.AssignDati([]);
                                          Self.BarcodeProdottoLetto = '';
                                          Self.MessaggioBarcodeProdotto = 'Movimento registrato.';
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          Self.PopupAttesaCalcolo = false;
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        })
    }
  },

  mounted()
  {
    this.KeyDownHandler = (event) =>
                          {
                            this.KeyDownBarcode(event);
                          };

    window.addEventListener('keydown', this.KeyDownHandler);
  },

  beforeUnmount()
  {
    if(this.KeyDownHandler != null)
      window.removeEventListener('keydown', this.KeyDownHandler);
  }
}
</script>
