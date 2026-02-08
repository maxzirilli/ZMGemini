import { SystemInformation, 
         DASHBOARD_FILTER_TYPES,
         NUMERO_LETTERE_PER_RICERCA, 
         STATO_PREVENTIVO, 
         STATO_DDT, 
         LIMITA_RIGHE_CARICATE,
         CAPTION_FILTERS,
         } from '@/SystemInformation';
import { TSchedaVuotaIniziale, TSchedaVuotaInizialeFornitore, TSchedaVuotaMagazzino } from '@/NodiVuoti.js'
import { TZDateFunct } from '../../../../../../Librerie/VUE/ZDateFunct';
import { TSchedaDocumentoDiTrasportoEntrante } from '@/views/SchedeDatabase/VUESchedaDocumentoDiTrasportoEntrante.vue'
import { TSchedaCliente,TModEspansioneCliente } from '@/views/SchedeDatabase/VUESchedaCliente.vue'
import { TSchedaFattura } from '@/views/SchedeDatabase/VUESchedaFattura.vue';
import { TSchedaAutoFattura } from '@/views/SchedeDatabase/VUESchedaAutoFattura.vue';
import { TSchedaNotaDiCredito } from '@/views/SchedeDatabase/VUESchedaNotaDiCredito.vue';
import { TSchedaPreventivo } from '@/views/SchedeDatabase/VUESchedaPreventivo.vue';
import { TSchedaFatturaPassiva } from '@/views/SchedeDatabase/VUESchedaFatturaPassiva.vue';
import { TSchedaMovimento } from '@/views/SchedeDatabase/VUESchedaMovimento.vue';
import { TSchedaMovimentiMagazzini } from '@/views/SchedeDatabase/VUESchedaMovimentiMagazzini.vue';
import { TSchedaDocumentoDiTrasporto } from '@/views/SchedeDatabase/VUESchedaDocumentoDiTrasporto.vue';
import { TSchedaDocScaricoProdottiComposti } from '@/views/SchedeDatabase/VUESchedaDocScaricoProdottiComposti.vue';
import { TZFilter } from '../../../../../../Librerie/VUE/ZFilters.js';
import { TSchedaFornitore } from '@/views/SchedeDatabase/VUESchedaFornitore.vue';
import { TSchedaProdotto } from '@/views/SchedeDatabase/VUESchedaProdotto.vue';
import { TSchedaContoCorrente } from '@/views/SchedeDatabase/VUESchedaContoCorrente.vue';
import { TSchedaPreventivoMultiparametrico } from '@/views/SchedeDatabase/VUESchedaPreventivoMultiparametrico.vue';

export class TFilterCliente extends TZFilter
{
    constructor()
    {
      super()
      this.Cliente                = '',
      this.StatoAttivita          = 'Attivi',
      this.Ritenute               = 'Qualsiasi',
      this.PartitaIva             = '',
      this.CodiceFiscale          = '',
      this.StatoFatture           = 'Qualsiasi',
      this.AnnoRitenuta           = (new Date()).getFullYear(),
      this.IndirizzoFatturazione  = '',
      this.IndirizzoFiliale       = '',
      this.Regione                = -1,
      this.Provincia              = -1,
      this.CodiceCliente          = ''
      this.ZonaFiliale            = -1
    }

    GetFilterId()
    {
      return DASHBOARD_FILTER_TYPES.Clienti
    }

    GetDescrizione()
    {
      return 'Clienti'
    }

    GetParametriXCliente()
    {
    var Parametri = {} 
    if(this.Cliente.trim() != '')
    {
      let Result = this.Cliente.split(" ")
      Parametri.SottoStringa1 = '%' + Result[0] + '%';
      if(Result[1] && Result[1] != '')
        Parametri.SottoStringa2 = '%' + Result[1] + '%';
      if(Result[2] && Result[2] != '')
        Parametri.SottoStringa3 = '%' + Result[2] + '%';
      if(Result[3] && Result[3] != '')
        Parametri.SottoStringa4 = '%' + Result[3] + '%';
      if(Result[4] && Result[4] != '')
        Parametri.SottoStringa5 = '%' + Result[4] + '%';
    }
    if(this.Cliente.trim().length > NUMERO_LETTERE_PER_RICERCA)
      Parametri.LunghezzaSottostringa = -1
    if(this.StatoAttivita == 'Attivi')
        Parametri.Attivo = 'T';
    if(this.StatoAttivita == 'NonAttivi')
        Parametri.Attivo = 'F';


    if(this.Ritenute != 'Qualsiasi')
    {
      if(this.Ritenute == 'Pagate')
        Parametri.CercaRitenutePagate = true;
      if(this.Ritenute == 'NonPagate')
        Parametri.CercaRitenutePagate = false;

      Parametri.AnnoRitenuta = this.AnnoRitenuta == ''? -1 : this.AnnoRitenuta
    }
    if(this.PartitaIva.trim() != '')
      Parametri.PartitaIva = '%' + this.PartitaIva + '%' 
    if(this.CodiceFiscale.trim() != '')
      Parametri.CodiceFiscale = '%' + this.CodiceFiscale + '%'
    if(this.StatoFatture != 'Qualsiasi')
      Parametri.FattureInRitardo = -1
    if(this.IndirizzoFatturazione.trim() != '')
      Parametri.IndirizzoFatturazione = '%' + this.IndirizzoFatturazione + '%'

    if(this.IndirizzoFiliale.trim() != '')
      Parametri.IndirizzoFiliale = '%' + (this.IndirizzoFiliale.trim()).toUpperCase() + '%'
    if(this.IndirizzoFiliale.trim().length > NUMERO_LETTERE_PER_RICERCA)
      Parametri.LunghezzaSottostringa = -1

    if(this.CodiceCliente.trim() != '')
      Parametri.CodiceCliente = this.CodiceCliente.replace(/\s/g, '');
    if(this.Regione != -1)
      Parametri.Regione = this.Regione
    if(this.Provincia != -1)
      Parametri.Provincia = this.Provincia
    if(this.ZonaFiliale != -1)
      Parametri.ZonaFiliale = this.ZonaFiliale

    return Parametri;
    }

    ClearFilterCliente()
    {
      this.CodiceCliente          = ''
      this.Cliente                = ''
      this.StatoAttivita          = 'Attivi'
      this.Ritenute               = 'Qualsiasi'
      this.PartitaIva             = ''
      this.CodiceFiscale          = ''
      this.StatoFatture           = 'Qualsiasi'
      this.AnnoRitenuta           = (new Date()).getFullYear()
      this.IndirizzoFatturazione  = ''
      this.ZonaFiliale            = -1
    } 

    Apply(Component,OnSuccess,OnError)
    {
      var Self = this
      SystemInformation.AdvQuery.ExecuteExternalScript('SelectClienteFiltro',Self.GetParametriXCliente(),
                                      function(Results)
                                      { 
                                        let ArrayInfo = Results.ListaClienti
                                        if(ArrayInfo != undefined)
                                        {
                                          Component.TreeView.Clear();
                                          if(Results.PrimaLetteraCliente)
                                          {
                                              ArrayInfo.forEach(function(ARecord)
                                              {
                                                let ANode = Component.TreeView.AddChild(ARecord.Iniziale,
                                                                                    new TSchedaVuotaIniziale(SystemInformation.AdvQuery,
                                                                                                              ARecord.Iniziale,
                                                                                                              TModEspansioneCliente.modClContabilita));
                                                ANode.HasChildren = true;
                                                ANode.Data.FilterCliente = Self;
                                              });
                                          }
                                          else
                                          {
                                              ArrayInfo.forEach(function(ARecord)
                                              {
                                                let SchedaCliente = new TSchedaCliente(SystemInformation.AdvQuery);
                                                SchedaCliente.CaricaRiassunto(ARecord);
                                                let ANode = Component.TreeView.AddChild(SchedaCliente.GetDescrizione(), SchedaCliente);
                                                ANode.HasChildren = true;
                                                if(ArrayInfo.length == 1)
                                                  Component.TreeView.ExpandToggle(ANode)

                                                ANode.Data.FilterCliente = Self;
                                              });
                                          }
                                          OnSuccess();
                                        }
                                        else 
                                        {
                                          SystemInformation.HandleError('Impossibile ottenere la lista delle iniziali dei clienti');
                                          if(OnError != undefined)
                                            OnError()
                                        }
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        if(OnError != undefined)
                                          OnError()
                                      })
  }
}

export class TFilterFattura extends TZFilter
{
  constructor()
  {
    super()
    this.OrdinaFatturate       = false,
    this.Chiave                = null,
    this.DalNr                 = '',
    this.AlNr                  = '',
    this.Anno1                 = (new Date()).getFullYear(),
    this.Anno2                 = (new Date()).getFullYear(),
    this.DallaData             = TZDateFunct.FormatDateTime('yyyy-mm-dd',TZDateFunct.SumMonth(new Date(), -3)),
    this.AllaData              = TZDateFunct.FormatDateTime('yyyy-mm-dd',new Date()),
    this.IncassataDal          = '',
    this.IncassataAl           = '',
    this.Intestatario          = '',
    this.Cliente               = -1,
    this.Stato                 = 'Qualsiasi',
    this.Numerazione           = 'NumerazioneAssegnata',
    this.RicevutaBancaria      = false,
    this.RicevutaNonPresentata = false,
    this.MeseGenerazione       = -1,
    this.AnnoGenerazione       = (new Date()).getFullYear(),
    this.PartitaIva            = '',
    this.RaggruppaAmm          = false,
    this.CodiceFiscale         = '',
    this.TipologiaFattura      = 'Tutte',
    this.InviateSDI            = 'Tutte',
    this.Conformita            = 'Tutte',
    this.AvvisiDaFatturare     = false,
    this.FattureConRiba        = false,
    this.FattureDiAcconto      = false
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.Fatture
  }

  GetDescrizione()
  {
    return 'Fatture'
  }

  GetParametriXFattura()
  {
    var Parametri = {}
    if(this.DalNr != '')
    {
      if(this.AlNr == '')
      {
        Parametri.NumeroFattura = this.DalNr;
        Parametri.AnnoFattura   = this.Anno1;
      }
      else
      {
        Parametri.DalNr = this.DalNr;
        Parametri.Anno1 = this.Anno1;
      }
    }
    if(this.Chiave != null && this.Chiave != '')
      Parametri.Chiave = this.Chiave
    if(this.AlNr != '')
    {
      Parametri.AlNr = this.AlNr;
      Parametri.Anno2 = this.Anno2;
    }
    if(this.DallaData != '')
      Parametri.DallaData = this.DallaData
    if(this.AllaData != '')
      Parametri.AllaData = this.AllaData

    if(this.Intestatario.trim() != '')
      Parametri.Intestatario = '%' + this.Intestatario + '%';
    if(this.Cliente != -1)
      Parametri.Cliente = this.Cliente
    if(this.Stato != 'Qualsiasi')
    {
      if(this.Stato == 'Incassata')
        Parametri.Incassata = -1
      if(this.Stato == 'NonIncassata')
        Parametri.NonIncassata = -1
      if(this.IncassataDal != '')
        Parametri.IncassataDal = this.IncassataDal
      if(this.IncassataAl != '')
        Parametri.IncassataAl = this.IncassataAl
    }
    if(this.Numerazione != '')
    {
      if(this.Numerazione == 'NumerazioneDaAssegnare')
        Parametri.DaAssegnare = -1
      if(this.Numerazione == 'NumerazioneAssegnata')
        Parametri.Assegnata = -1
    }
    if(this.MeseGenerazione != -1)
      Parametri.MeseGenerazione = (this.AnnoGenerazione * 100) + (this.MeseGenerazione + 1)

    if(this.PartitaIva.trim() != '')
      Parametri.PartitaIva = '%' + this.PartitaIva + '%';
    if(this.CodiceFiscale.trim() != '')
      Parametri.CodiceFiscale = '%' + this.CodiceFiscale + '%';

    if(this.TipologiaFattura != 'Tutte')
    {
      if(this.TipologiaFattura == 'FattureDaBanco')
        Parametri.SoloFattureDaBanco = -1
      if(this.TipologiaFattura == 'Fatture')
        Parametri.SoloFatture = -1
    }

    if(this.InviateSDI != 'Tutte')
    {
      if(this.InviateSDI == 'NonInviate')
        Parametri.NonInviateSDI = -1
      if(this.InviateSDI == 'Inviate')
        Parametri.InviateSDI = -1
    }

    if(this.AvvisiDaFatturare)
      Parametri.DaFatturare            = -1
    
    if(this.FattureConRiba)
      Parametri.FattureConRiba = -1

    if(this.FattureDiAcconto)
      Parametri.FattureDiAcconto = -1

    if(this.Conformita != 'Tutte')
    {
      Parametri.Conformita    = this.Conformita
      if(this.Conformita == 'NonConforme')
        Parametri.NonConformi = -1
      if(this.Conformita == 'Conforme')
        Parametri.Conformi    = -1 
    }
      
    return Parametri;
  }

  ClearFilterFattura()
  {
    this.Conformita          = 'Tutte'
  } 

  Apply(Component,OnSuccess,OnError,FromPosition = 0)
  {
    var Self = this
    let Parametri            = this.GetParametriXFattura()
    
    Parametri.RicercaFatture = -1

    Parametri.LimitStart     = FromPosition
    Parametri.LimitNumRows   = LIMITA_RIGHE_CARICATE + 1

    this.LastComponent       = Component;

    if(Self.Conformita != 'Tutte' || this.AvvisiDaFatturare)
      Component.PopupAttesaCalcolo = true
    
    SystemInformation.AdvQuery.ExecuteExternalScript('SelectFatture', Parametri,
                                                    function(Results)
                                                    {
                                                        let ArrayInfo = Results.ListaFatture
                                                        if(ArrayInfo != undefined)
                                                        {
                                                          if(FromPosition == 0)
                                                             Component.TreeView.Clear();
                                                          
                                                          // if(Self.Conformita != 'Tutte')
                                                          //   ArrayInfo = Self.GestioneOrdinamentoFatture(ArrayInfo)

                                                          for(let i = 0; i < ArrayInfo.length; i++)
                                                          { 
                                                            var AFattura = ArrayInfo[i]
                                                            var SchedaFattura = new TSchedaFattura(SystemInformation.AdvQuery);
                                                            SchedaFattura.CaricaRiassunto(AFattura);
                                                            if(i >= LIMITA_RIGHE_CARICATE)
                                                            {
                                                              Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                              break
                                                            }
                                                            else Component.TreeView.AddChild(SchedaFattura.GetDescrizione(), SchedaFattura);
                                                          }
                                                          OnSuccess();
                                                        }
                                                        else
                                                        {
                                                          SystemInformation.HandleError('Impossibile ottenere la lista delle fatture');
                                                          if(OnError != undefined)
                                                            OnError()
                                                        }

                                                        Component.PopupAttesaCalcolo = false
                                                    },
                                                    function(HTTPError,SubHTTPError,Response)
                                                    {
                                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                      Component.PopupAttesaCalcolo = false
                                                      if(OnError != undefined)
                                                        OnError()
                                                    })
  }

  GestioneOrdinamentoFatture(ListaFatture)
  {
    let Result     = []
    let LastChiave = -1 

    for(let i = 0; i < ListaFatture.length; i++)
    {
      if(LastChiave != ListaFatture[i].CHIAVE)
      {
        Result.push(ListaFatture[i])
        LastChiave = ListaFatture[i].CHIAVE  
      }
    }

    Result.sort((a, b) =>
    {
      let Data1 = new Date(a.CONCLUSIONE)
      let Data2 = new Date(b.CONCLUSIONE)

      if (Data1.getTime() === Data2.getTime()) 
        return new Date(a.DATA) - new Date(b.DATA);

      return Data1 - Data2;
    });

    return Result
  }
}

export class TFilterAutofattura extends TZFilter
{
  constructor()
  {
    super()
    this.OrdinaAutofatturate   = false,
    this.Chiave                = null,
    this.DalNr                 = '',
    this.AlNr                  = '',
    this.Anno1                 = (new Date()).getFullYear(),
    this.Anno2                 = (new Date()).getFullYear(),
    this.DallaData             = TZDateFunct.FormatDateTime('yyyy-mm-dd',TZDateFunct.SumMonth(new Date(), -3)),
    this.AllaData              = TZDateFunct.FormatDateTime('yyyy-mm-dd',new Date()),
    this.Intestatario          = '',
    this.Fornitore             = -1,
    this.Stato                 = 'Qualsiasi',
    this.Numerazione           = 'NumerazioneAssegnata',
    this.RicevutaBancaria      = false,
    this.RicevutaNonPresentata = false,
    this.MeseGenerazione       = -1,
    this.AnnoGenerazione       = (new Date()).getFullYear(),
    this.PartitaIva            = '',
    this.CodiceFiscale         = '',
    this.InviateSDI            = 'Tutte',
    this.Conformita            = 'Tutte'
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.Autofatture
  }

  GetDescrizione()
  {
    return 'Autofatture'
  }

  GetParametriXAutofattura()
  {
    var Parametri = {}
    if(this.DalNr != '')
    {
      if(this.AlNr == '')
      {
        Parametri.NumeroAutofattura = this.DalNr;
        Parametri.AnnoAutofattura   = this.Anno1;
      }
      else
      {
        Parametri.DalNr = this.DalNr;
        Parametri.Anno1 = this.Anno1;
      }
    }
    if(this.Chiave != null && this.Chiave != '')
      Parametri.Chiave = this.Chiave
    if(this.AlNr != '')
    {
      Parametri.AlNr = this.AlNr;
      Parametri.Anno2 = this.Anno2;
    }
    if(this.DallaData != '')
      Parametri.DallaData = this.DallaData
    if(this.AllaData != '')
      Parametri.AllaData = this.AllaData

    if(this.Intestatario.trim() != '')
      Parametri.Intestatario = '%' + this.Intestatario + '%';
    if(this.Fornitore != -1)
      Parametri.Fornitore = this.Fornitore

    if(this.Numerazione != '')
    {
      if(this.Numerazione == 'NumerazioneDaAssegnare')
        Parametri.DaAssegnare = -1
      if(this.Numerazione == 'NumerazioneAssegnata')
        Parametri.Assegnata = -1
    }
    if(this.MeseGenerazione != -1)
      Parametri.MeseGenerazione = (this.AnnoGenerazione * 100) + (this.MeseGenerazione + 1)

    if(this.PartitaIva.trim() != '')
      Parametri.PartitaIva = '%' + this.PartitaIva + '%';
    if(this.CodiceFiscale.trim() != '')
      Parametri.CodiceFiscale = '%' + this.CodiceFiscale + '%';


    if(this.InviateSDI != 'Tutte')
    {
      if(this.InviateSDI == 'NonInviate')
        Parametri.NonInviateSDI = -1
      if(this.InviateSDI == 'Inviate')
        Parametri.InviateSDI = -1
    }
      
    Parametri.OrdinaXData = -1
    
    return Parametri;
  }

  ClearFilterFattura()
  {
    this.Conformita = 'Tutte'
  } 

  Apply(Component,OnSuccess,OnError, FromPosition = 0)
  {
    var Self               = this
    let Parametri          = this.GetParametriXAutofattura()
    Parametri.LimitStart   = FromPosition
    Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
    this.LastComponent     = Component;

    SystemInformation.AdvQuery.ExecuteExternalScript('GetRiassuntoAutofattura', Parametri,
                                                      function(Result)
                                                      {  
                                                        let ArrayInfo = Result.RiassuntoAutofatture;
                                                        if(Result                               != undefined &&
                                                           Result.RiassuntoAutofatture          != undefined   )
                                                        {
                                                          if(FromPosition == 0)
                                                            Component.TreeView.Clear();

                                                          for(let i = 0; i < ArrayInfo.length; i++)
                                                          {
                                                            var ARecord = ArrayInfo[i]
                                                            var SchedaAutofattura = new TSchedaAutoFattura(SystemInformation.AdvQuery);
                                                            SchedaAutofattura.CaricaRiassunto(ARecord);
                                                            if(i >= LIMITA_RIGHE_CARICATE)
                                                            {
                                                              Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                              break
                                                            }
                                                            else
                                                            Component.TreeView.AddChild(SchedaAutofattura.GetDescrizione(), SchedaAutofattura)
                                                          }

                                                          OnSuccess();
                                                        }
                                                        else console.error('Impossibile ottenere il Riassunto delle Note di credito');
                                                        {
                                                          if(OnError != undefined)
                                                            OnError()
                                                        }
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        if(OnError != undefined)
                                                          OnError()
                                                      })
  }
}

export class TFilterNota extends TZFilter
{
  constructor()
  {
    super()
    this.DalNr                 = '',
    this.AlNr                  = '',
    this.Anno1                 = (new Date()).getFullYear(),
    this.Anno2                 = (new Date()).getFullYear(),
    this.DallaData             = '',
    this.AllaData              = '',
    this.Intestatario          = '',
    this.Cliente               = -1,
    this.PartitaIva            = '',
    this.RicevutaBancaria      = false,
    this.RicevutaNonPresentata = false,
    this.Pagamento             = 'Qualsiasi',
    this.Numerazione           = 'Qualsiasi'
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.NoteDiCredito
  }

  GetDescrizione()
  {
    return 'Nota di credito'
  }

  GetParametriXNote()
  {
    var Parametri = {}
    if(this.DalNr != '')
    {
      if(this.AlNr == '')
      {
        Parametri.NumeroNota = this.DalNr;
        Parametri.AnnoNota   = this.Anno1;
      }
      else
      {
        Parametri.DalNr = this.DalNr;
        Parametri.Anno1 = this.Anno1;
        Parametri.OrdinaXNumero = -1
      }
    }
    if(this.AlNr != '')
    {
      Parametri.AlNr = this.AlNr;
      Parametri.Anno2 = this.Anno2;
      Parametri.OrdinaXNumero = -1
    }
    if(this.DallaData != '')
      Parametri.DallaData = this.DallaData
    if(this.AllaData != '')
      Parametri.AllaData = this.AllaData
    if(this.PartitaIva.trim() != '')
      Parametri.PartitaIva = '%' + this.PartitaIva + '%';
    if(this.Intestatario.trim() != '')
      Parametri.Intestatario = '%' + this.Intestatario + '%';
    if(this.Cliente != -1)
      Parametri.Cliente = this.Cliente
    if(this.Numerazione != 'Qualsiasi')
    {
      if(this.Numerazione == 'NumerazioneDaAssegnare')
        Parametri.DaAssegnare = -1
      if(this.Numerazione == 'NumerazioneAssegnata')
        Parametri.Assegnata = -1
    }
    if(this.Pagamento != 'Qualsiasi')
    {
      if(this.Pagamento == 'Pagata')
        Parametri.Incassata = -1
      if(this.Pagamento == 'DaPagare')
        Parametri.NonIncassata = -1
    }
    
    if(!Parametri.OrdinaXNumero)
      Parametri.OrdinaXData = -1
    return Parametri;
  }

  Apply(Component,OnSuccess,OnError,FromPosition = 0)
  {
    var Self              = this
    let Parametri         = this.GetParametriXNote()
    Parametri.LimitStart   = FromPosition
    Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
    this.LastComponent     = Component;

    SystemInformation.AdvQuery.ExecuteExternalScript('GetRiassuntoNoteDiCredito', Parametri,
                                                      function(Result)
                                                      {  
                                                        let ArrayInfo = Result.RiassuntoNoteDiCredito;
                                                        if(Result                               != undefined &&
                                                            Result.RiassuntoNoteDiCredito        != undefined   )
                                                        {
                                                          if(FromPosition == 0)
                                                            Component.TreeView.Clear();
                                                          for(let i = 0; i < ArrayInfo.length; i++)
                                                          {
                                                            var ANota = ArrayInfo[i]
                                                            var SchedaNotaDiCredito = new TSchedaNotaDiCredito(SystemInformation.AdvQuery);
                                                            SchedaNotaDiCredito.CaricaRiassunto(ANota);
                                                            if(i >= LIMITA_RIGHE_CARICATE)
                                                            {
                                                              Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                              break
                                                            }
                                                            else
                                                            Component.TreeView.AddChild(SchedaNotaDiCredito.GetDescrizione(),SchedaNotaDiCredito)
                                                          }


                                                          OnSuccess();
                                                        }
                                                        else console.error('Impossibile ottenere il Riassunto delle Note di credito');
                                                        {
                                                          if(OnError != undefined)
                                                            OnError()
                                                        }
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        if(OnError != undefined)
                                                          OnError()
                                                      })
  }
}


export class TFilterPreventivo extends TZFilter
{
  constructor()
  {
    super()
    this.DalNr                 = '',
    this.AlNr                  = '',
    this.Anno1                 = (new Date()).getFullYear(),
    this.Anno2                 = (new Date()).getFullYear(),
    this.DallaData             = '',
    this.AllaData              = '',
    this.Intestatario          = '',
    this.Cliente               = -1,
    this.PartitaIva            = '',
    this.StatoPreventivo       = -1
}

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.Preventivi
  }

  GetDescrizione()
  {
    return 'Preventivi'
  }


  GetParametriXPreventivo()
  {
    var Parametri = {}

    if(this.DalNr != '')
    {
      if(this.AlNr == '')
      {
        Parametri.NumeroPreventivo = this.DalNr;
        Parametri.AnnoPreventivo   = this.Anno1;
      }
      else
      {
        Parametri.DalNr = this.DalNr;
        Parametri.Anno1 = this.Anno1;
        Parametri.OrdinaXNumero = -1
      }
    }
    if(this.AlNr != '')
    {
      Parametri.AlNr = this.AlNr;
      Parametri.Anno2 = this.Anno2;
      Parametri.OrdinaXNumero = -1                                  
    }
    if(this.DallaData != '')
      Parametri.DallaData = this.DallaData
    if(this.AllaData != '')
      Parametri.AllaData = this.AllaData
    if(this.PartitaIva.trim() != '')
      Parametri.PartitaIva = '%' + this.PartitaIva + '%';
    if(this.Intestatario.trim() != '')
      Parametri.Intestatario = '%' + this.Intestatario + '%';
    if(this.Cliente != -1)
      Parametri.Cliente = this.Cliente
    if(this.StatoPreventivo != -1)
      {
        switch(this.StatoPreventivo)
        {
          case 'DaGestire'  : Parametri.DaGestire = -1
                              break
          case 'Confermati' : Parametri.Confermati = -1 
                              break
          case 'Rifiutati'  : Parametri.Rifiutati = -1 
                              break
        }
      }

      if(!Parametri.OrdinaXNumero)
        Parametri.OrdinaXData = -1
      return Parametri;
    }
  
  Apply(Component,OnSuccess,OnError,FromPosition = 0)
  {
    var Self              = this
    let Parametri         = this.GetParametriXPreventivo()
    Parametri.LimitStart   = FromPosition
    Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
    this.LastComponent     = Component;

    SystemInformation.AdvQuery.GetSQL('PreventiviMultiparametrici',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FiltroPreventivo");
                                          if(ArrayInfo != undefined)
                                          { 
                                            if(FromPosition == 0) 
                                              Component.TreeView.Clear();
                                            for(let i = 0; i < ArrayInfo.length; i++)
                                            {
                                              var APreventivoMulti = ArrayInfo[i]
                                              var SchedaPreventivoMulti = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
                                              SchedaPreventivoMulti.CaricaRiassunto(APreventivoMulti);
                                              if(i >= LIMITA_RIGHE_CARICATE)
                                              {
                                                Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                break
                                              }
                                              else Component.TreeView.AddChild(SchedaPreventivoMulti.GetDescrizione(), SchedaPreventivoMulti)
                                            }
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista dei preventivi multiparametrici');
                                          {
                                            if(OnError != undefined)
                                              OnError()
                                          }
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        if(OnError != undefined)
                                          OnError()
                                      }, "FiltroPreventivo")
  }
  
}

export class TFilterConferme extends TZFilter
{
    constructor()
    {
      super()
      this.DalNr                 = '',
      this.AlNr                  = '',
      this.Anno1                 = (new Date()).getFullYear(),
      this.Anno2                 = (new Date()).getFullYear(),
      this.DallaData             = '',
      this.AllaData              = '',
      this.Intestatario          = '',
      this.Cliente               = -1,
      this.PartitaIva            = '',
      this.RicevutaBancaria      = false,
      this.RicevutaNonPresentata = false,
      this.Pagamento             = 'Qualsiasi'
      this.StatoPreventivo       = -1,
      this.StatoConfermaDOrdine  = -1
    }

    GetFilterId()
    {
      return DASHBOARD_FILTER_TYPES.Conferme
    }

    GetDescrizione()
    {
      return 'Conferme'
    }


    GetParametriXConferme()
    {
      var Parametri = {}

      const GESTITE = 'G';
      const NON_GESTITE = 'NG';

      if(this.DalNr != '')
      {
        if(this.AlNr == '')
        {
          Parametri.NumeroPreventivo = this.DalNr;
          Parametri.AnnoPreventivo   = this.Anno1;
        }
        else
        {
          Parametri.DalNr = this.DalNr;
          Parametri.Anno1 = this.Anno1;
          Parametri.OrdinaXNumero = -1
        }
      }
      if(this.AlNr != '')
      {
        Parametri.AlNr = this.AlNr;
        Parametri.Anno2 = this.Anno2;
        Parametri.OrdinaXNumero = -1                                  
      }
      if(this.DallaData != '')
        Parametri.DallaData = this.DallaData
      if(this.AllaData != '')
        Parametri.AllaData = this.AllaData
      if(this.PartitaIva.trim() != '')
        Parametri.PartitaIva = '%' + this.PartitaIva + '%';
      if(this.Intestatario.trim() != '')
        Parametri.Intestatario = '%' + this.Intestatario + '%';
      if(this.Cliente != -1)
        Parametri.Cliente = this.Cliente
      if(this.StatoPreventivo != -1)
      {
        switch(this.StatoPreventivo)
        {
          case 'Sospesi'    : Parametri.StatoPreventivo = STATO_PREVENTIVO.Sospeso 
                              break
          case 'Confermati' : Parametri.StatoPreventivo = STATO_PREVENTIVO.Confermato 
                              break
          case 'Rifiutati'  : Parametri.StatoPreventivo = STATO_PREVENTIVO.Rifiutato 
                              break
        }
      }
      if(this.StatoConfermaDOrdine === 'Gestite')
        Parametri.StatoConfermaDOrdineGestite = GESTITE;
      
      if(this.StatoConfermaDOrdine === 'NonGestite')
        Parametri.StatoConfermaDOrdineNonGestite = NON_GESTITE;

      if(!Parametri.OrdinaXNumero)
        Parametri.OrdinaXData = -1
      return Parametri;
    }

    Apply(Component,OnSuccess,OnError,FromPosition = 0)
    {
      var Self              = this
      let Parametri         = this.GetParametriXConferme()
      Parametri.LimitStart   = FromPosition
      Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
      this.LastComponent     = Component;

      SystemInformation.AdvQuery.GetSQL('Preventivi',Parametri,
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FiltroPreventivo");
                                            if(ArrayInfo != undefined)
                                            {
                                              if(FromPosition == 0)  
                                                Component.TreeView.Clear();
                                              for(let i = 0; i < ArrayInfo.length; i++)
                                              {
                                                var APreventivo = ArrayInfo[i]
                                                var SchedaPreventivo = new TSchedaPreventivo(SystemInformation.AdvQuery);
                                                SchedaPreventivo.CaricaRiassunto(APreventivo);
                                                if(i >= LIMITA_RIGHE_CARICATE)
                                                {
                                                  Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                  break  
                                                }
                                                else Component.TreeView.AddChild(SchedaPreventivo.GetDescrizione(),SchedaPreventivo)
                                              }
                                              OnSuccess();
                                            }
                                            else console.error('Impossibile ottenere la lista dei preventivi');
                                            {
                                              if(OnError != undefined)
                                                OnError()
                                            }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          if(OnError != undefined)
                                            OnError()
                                        }, "FiltroPreventivo")
    }
}

export class TFilterFatturaPassiva extends TZFilter
{
  constructor()
  {
    super()
    this.DallaData             = '',
    this.AllaData              = '',
    this.Fornitore             = -1,
    this.Importo               = 0,
    this.ScadenzaDal           = '',
    this.ScadenzaAl            = '',
    this.PagataDal             = '',
    this.PagataAl              = '',
    this.Pagate                = 'Tutte',
    this.RaggruppaPerFornitore = false
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.FatturePassive
  }

  GetDescrizione()
  {
    return 'Fatture Passive'
  }

  GetParametriXFatturaPassiva()
  {
  var Parametri = {}
  if(this.DallaData != '')
    Parametri.DallaData = this.DallaData
  if(this.AllaData != '')
    Parametri.AllaData = this.AllaData
  if(this.ScadenzaDal != '')
    Parametri.ScadenzaDal = this.ScadenzaDal
  if(this.ScadenzaAl != '')
    Parametri.ScadenzaAl = this.ScadenzaAl
    if(this.PagataDal != '')
    Parametri.PagataDal = this.PagataDal
  if(this.PagataAl != '')
    Parametri.PagataAl = this.PagataAl
  if(this.Fornitore != -1)
    Parametri.Fornitore = this.Fornitore
  if(this.Importo != 0)
    Parametri.Importo = this.Importo * 100
  Parametri.Fatture = -1
  return Parametri;
  }

  Apply(Component,OnSuccess,OnError,FromPosition = 0)
  {
    var Self              = this
    let Parametri         = this.GetParametriXFatturaPassiva()
    Parametri.LimitStart   = FromPosition
    Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
    this.LastComponent     = Component;

    SystemInformation.AdvQuery.GetSQL('FatturePassive',Parametri,
                                function(Results)
                                {
                                    let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"DocumentiPassivi");
                                    if(ArrayInfo != undefined)
                                    { 
                                      if(FromPosition == 0) 
                                        Component.TreeView.Clear();
                                      for(let i = 0; i < ArrayInfo.length; i++)
                                      {
                                        var AFatturePassive = ArrayInfo[i]
                                        var SchedaFatturaPassiva = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                        SchedaFatturaPassiva.CaricaRiassunto(AFatturePassive);
                                        if(i >= LIMITA_RIGHE_CARICATE)
                                        {
                                          Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                          break
                                        }
                                        else Component.TreeView.AddChild(SchedaFatturaPassiva.GetDescrizione(),SchedaFatturaPassiva)
                                      }
                                      OnSuccess();
                                    }
                                    else console.error('Impossibile ottenere la lista delle fatture passive');
                                    {
                                      if(OnError != undefined)
                                        OnError()
                                    }
                                },
                                function(HTTPError,SubHTTPError,Response)
                                {
                                  SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                  if(OnError != undefined)
                                    OnError()
                                }, "FiltroDocumentiPassivi")
  }
}

export class TFilterFiliale extends TZFilter
{
   GetFilterId()
   {
    return DASHBOARD_FILTER_TYPES.Filiali
   }

   GetDescrizione()
   {
    return 'Filiali'
   }
}

export class TFilterMagazzini extends TZFilter
{
   GetFilterId()
   {
    return DASHBOARD_FILTER_TYPES.Magazzini
   }

   GetDescrizione()
   {
    return 'Magazzini'
   }
}

export class TFilterMovimentiMagazzini extends TZFilter
  {
    constructor()
    {
      super()
      this.DallaData            ='',
      this.AllaData             ='',
      this.CodiceProdotto       ='',
      this.DescrizioneProdotto  =''
    }

    GetFilterId()
    {
      return DASHBOARD_FILTER_TYPES.MovimentiMagazzini
    }

    GetDescrizione()
    {
      return 'Movimenti Magazzini'
    }

    GetParametriXMovimentiMagazzini()
    {
      var Parametri = {}
      if(this.DallaData != '')
        Parametri.DallaData = this.DallaData
      if(this.AllaData != '')
        Parametri.AllaData = this.AllaData    
      if(this.CodiceProdotto.trim() != '')
        Parametri.CodiceProdotto = '%' + this.CodiceProdotto + '%';
      if(this.DescrizioneProdotto.trim() != '')
        Parametri.DescrizioneProdotto = '%' + this.DescrizioneProdotto + '%';
      return Parametri;
    } 

    Apply(Component,OnSuccess,OnError,FromPosition = 0) 
    {
      var Self              = this
      let Parametri         = this.GetParametriXMovimentiMagazzini()
      Parametri.LimitStart   = FromPosition
      Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
      this.LastComponent     = Component;
      
      SystemInformation.AdvQuery.GetSQL('MovimentiMagazzini',Parametri,
                                        function(Results)
                                        {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FiltroMovimentiMagazzini");
                                          if(ArrayInfo != undefined)
                                          {  
                                            if(FromPosition == 0)
                                              Component.TreeView.Clear();
                                            for(let i = 0; i < ArrayInfo.length; i++)
                                            {
                                              var ARiassunto = ArrayInfo[i]
                                              var SchedaMovimentoMagazzino = new TSchedaMovimentiMagazzini(SystemInformation.AdvQuery); 
                                              SchedaMovimentoMagazzino.CaricaRiassunto(ARiassunto);
                                              if(i >= LIMITA_RIGHE_CARICATE)
                                              {
                                                Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                break
                                              }
                                              else Component.TreeView.AddChild(SchedaMovimentoMagazzino.GetDescrizione(ARiassunto),SchedaMovimentoMagazzino)
                                            }
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista dei movimenti');
                                          {
                                            if(OnError != undefined)
                                              OnError()
                                          }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          if(OnError != undefined)
                                            OnError()
                                        }, "FiltroMovimentiMagazzini")
    }
  }

export class TFilterNotaDiCreditoPassiva extends TZFilter
  {
    constructor()
    {
      super()
      this.DallaData             = '',
      this.AllaData              = '',
      this.Fornitore             = -1,
      this.Importo               = 0,
      this.ScadenzaDal           = '',
      this.ScadenzaAl            = '',
      this.PagataDal             = '',
      this.PagataAl              = '',
      this.Pagate                = 'Tutte',
      this.RaggruppaPerFornitore = false
    }

    GetFilterId()
    {
      return DASHBOARD_FILTER_TYPES.NoteDiCreditoPassive
    }

    GetDescrizione()
    {
      return 'Note di credito passive'
    }

    GetParametriXNotaDiCreditoPassiva()
    {
      var Parametri = {}
      if(this.DallaData != '')
        Parametri.DallaData = this.DallaData
      if(this.AllaData != '')
        Parametri.AllaData = this.AllaData
      if(this.ScadenzaDal != '')
        Parametri.ScadenzaDal = this.ScadenzaDal
      if(this.ScadenzaAl != '')
        Parametri.ScadenzaAl = this.ScadenzaAl
        if(this.PagataDal != '')
        Parametri.PagataDal = this.PagataDal
      if(this.PagataAl != '')
        Parametri.PagataAl = this.PagataAl
      if(this.Fornitore != -1)
        Parametri.Fornitore = this.Fornitore
      if(this.Importo != 0)
        Parametri.Importo = this.Importo * 100
      Parametri.Note = -1
      return Parametri;
    }

    Apply(Component,OnSuccess,OnError,FromPosition = 0)
    {
      var Self              = this
      let Parametri         = this.GetParametriXNotaDiCreditoPassiva()
      Parametri.LimitStart   = FromPosition
      Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
      this.LastComponent     = Component;
  
      SystemInformation.AdvQuery.GetSQL('FatturePassive',Parametri,
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"DocumentiPassivi");
                                            if(ArrayInfo != undefined)
                                            {  
                                              if(FromPosition == 0)
                                                Component.TreeView.Clear();
                                              for(let i = 0; i < ArrayInfo.length; i++)
                                              {
                                                var AFatturePassive = ArrayInfo[i]
                                                var SchedaFatturaPassiva = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                                SchedaFatturaPassiva.CaricaRiassunto(AFatturePassive);
                                                if(i >= LIMITA_RIGHE_CARICATE)
                                                {
                                                  Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                  break
                                                }
                                                else Component.TreeView.AddChild(SchedaFatturaPassiva.GetDescrizione(),SchedaFatturaPassiva)
                                              }
                                              OnSuccess();
                                            }
                                            else console.error('Impossibile ottenere la lista delle note di credito passive');
                                            {
                                              if(OnError != undefined)
                                              OnError()
                                            }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                            if(OnError != undefined)
                                            OnError()
                                        }, "FiltroDocumentiPassivi")
    }
  }

  export class TFilterProdotto extends TZFilter
  {
    constructor()
    {
      super()
      this.Codice      = '',
      this.Descrizione = ''
      this.Settore     = -1
      this.IdMagazzino = -1
      this.SoloSottosoglia = false
    }

    GetFilterId()
    {
      return DASHBOARD_FILTER_TYPES.Prodotti
    }

    GetDescrizione()
    {
      return 'Prodotti'
    }

    GetParametriXProdotto()
    {
      var Parametri = {}
      if(this.Codice.trim() != '')
        Parametri.Codice = '%' + this.Codice + '%';
      if(this.Descrizione.trim() != '')
        Parametri.Descrizione = '%' + this.Descrizione + '%';
      if(this.Settore && this.Settore != -1)
        Parametri.Settore = this.Settore;
      if(this.SoloSottosoglia)
        Parametri.SoloSottosoglia = 'T' 
      if(this.IdMagazzino && this.IdMagazzino != -1)
        Parametri.IdMagazzino = this.IdMagazzino
      
      return Parametri;
    }
    
    Apply(Component, OnSuccess, OnError, FromPosition = 0)
    {
      if(this.Codice != '' || this.Descrizione != '' || this.Settore != -1 || this.SoloSottosoglia || this.IdMagazzino != -1 )
      {
        let Self      = this
        let Parametri = {}
        Parametri     = this.GetParametriXProdotto()
        Parametri.LimitStart   = FromPosition
        Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
        this.LastComponent     = Component;

        SystemInformation.AdvQuery.GetSQL('Magazzino', Parametri,
                                    function(Results)
                                    {
                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ListaProdotti");
                                        if(ArrayInfo != undefined)
                                        {  
                                          if(FromPosition == 0)
                                            Component.TreeView.Clear();
                                          for(let i = 0; i < ArrayInfo.length; i++)
                                          {
                                            var ASchedaProdotto = ArrayInfo[i]
                                            var SchedaProdotto = new TSchedaProdotto(SystemInformation.AdvQuery);
                                            SchedaProdotto.CaricaRiassunto(ASchedaProdotto);
                                            if(i >= LIMITA_RIGHE_CARICATE)
                                            {
                                              Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                              break
                                            }
                                            else Component.TreeView.AddChild(SchedaProdotto.GetDescrizione(), SchedaProdotto);
                                          }

                                          if(OnSuccess != undefined)
                                            OnSuccess()
                                        }
                                        else 
                                        {
                                          console.error('Impossibile ottenere la lista dei prodotti');
                                          if(OnError != undefined)
                                            OnError()
                                        }
                                    },
                                    function(HTTPError,SubHTTPError,Response)
                                    {
                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        if(OnError != undefined)
                                          OnError()
                                    }, 
                                    "ListaProdotti")
        }
        else
        {
          SystemInformation.AdvQuery.GetSQL('Magazzino',{},
                                            function(Results)
                                            {
                                                let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Settori");
                                                if(ArrayInfo != undefined)
                                                {
                                                  Component.TreeView.Clear();
                                                  ArrayInfo.forEach(function(ARecord)
                                                  {
                                                    let ANode = Component.TreeView.AddChild(ARecord.DESCRIZIONE,
                                                                    new TSchedaVuotaMagazzino(SystemInformation.AdvQuery, ARecord.CHIAVE));
                                                    ANode.HasChildren = true;
                                                  });
                                                  if(OnSuccess != undefined)
                                                    OnSuccess()
                                                }
                                                else SystemInformation.HandleError('Impossibile ottenere la lista dei settori');
                                                {
                                                  if(OnError != undefined)
                                                    OnError()
                                                }
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                              if(OnError != undefined)
                                              OnError()
                                            },
                                            'SelectSettori')
        }
    }      
  }

  export class TFilterMovimento extends TZFilter
  {
    constructor()
    {
      super()
      this.DallaData ='',
      this.AllaData  ='',
      this.Tipologia =-1
    }

    GetFilterId()
    {
      return DASHBOARD_FILTER_TYPES.Movimenti
    }

    GetDescrizione()
    {
      return 'Movimenti'
    }

    GetParametriXMovimento()
    {
      var Parametri = {}
      if(this.DallaData != '') 
        Parametri.DallaData = this.DallaData
      if(this.AllaData != '')
        Parametri.AllaData = this.AllaData
      if(this.Tipologia != -1)
        Parametri.Tipologia = this.Tipologia
      return Parametri;
    } 

    Apply(Component,OnSuccess,OnError,FromPosition = 0) 
    {
      var Self              = this
      let Parametri         = this.GetParametriXMovimento()
      Parametri.LimitStart   = FromPosition
      Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
      this.LastComponent     = Component;
      
      SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FiltroMovimenti");
                                            if(ArrayInfo != undefined)
                                            {  
                                              if(FromPosition == 0)
                                                Component.TreeView.Clear();
                                              for(let i = 0; i < ArrayInfo.length; i++)
                                                {
                                                var ARiassunto = ArrayInfo[i]
                                                var SchedaMovimento = new TSchedaMovimento(SystemInformation.AdvQuery);
                                                SchedaMovimento.CaricaRiassunto(ARiassunto);
                                                if(i >= LIMITA_RIGHE_CARICATE)
                                                {
                                                  Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                  break
                                                }
                                                else Component.TreeView.AddChild(SchedaMovimento.GetDescrizione(ARiassunto),SchedaMovimento)
                                              }
                                              OnSuccess();
                                            }
                                            else console.error('Impossibile ottenere la lista dei movimenti');
                                            {
                                              if(OnError != undefined)
                                                OnError()
                                            }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          if(OnError != undefined)
                                            OnError()
                                        }, "FiltroMovimenti")
    }
}
export class TFilterPrimaNota extends TZFilter
{
   GetFilterId()
   {
    return DASHBOARD_FILTER_TYPES.PrimaNota
   }

   GetDescrizione()
   {
    return 'Prima nota'
   }
}

export class TFilterDDT extends TZFilter
{
  constructor()
  {
    super()
    this.DalNr                 = '',
    this.AlNr                  = '',
    this.Anno1                 = (new Date()).getFullYear(),
    this.Anno2                 = (new Date()).getFullYear(),
    this.DallaData             = '',
    this.AllaData              = '',
    this.Intestatario          = '',
    this.PartitaIva            = '',
    this.Cliente               = -1,
    this.StatoDDT              = -1
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.DDT
  }

  GetDescrizione()
  {
    return 'Uscenti'
  }

  GetParametriXDDT()
  {
    var Parametri = {}
    if(this.DalNr != '')
    {
      if(this.AlNr == '')
      {
        Parametri.NumeroDDT = this.DalNr;
        Parametri.AnnoDDT   = this.Anno1;
      }
      else
      {
        Parametri.DalNr = this.DalNr;
        Parametri.Anno1 = this.Anno1;
        Parametri.OrdinaXNumero = -1
      }
    }
    if(this.AlNr != '')
    {
      Parametri.AlNr  = this.AlNr;
      Parametri.Anno2 = this.Anno2;
      Parametri.OrdinaXNumero = -1
    }
    if(this.DallaData != '')
      Parametri.DallaData = this.DallaData
    if(this.AllaData != '')
      Parametri.AllaData = this.AllaData
    if(this.PartitaIva.trim() != '')
      Parametri.PartitaIva = '%' + this.PartitaIva + '%';
    if(this.Intestatario.trim() != '')
      Parametri.Intestatario = '%' + this.Intestatario + '%';
    if(this.Cliente != -1)
      Parametri.Cliente = this.Cliente
    
      if(this.StatoDDT != -1)
    {
      switch(this.StatoDDT)
      {
        case 'Concluso'   : Parametri.StatoDDT = STATO_DDT.Concluso 
                            break
        case 'Annullato'  : Parametri.StatoDDT = STATO_DDT.Annullato
                            break
        case 'InCorso'    : Parametri.StatoDDT = STATO_DDT.InCorso 
                            break
        }
    }
    
    Parametri.OrdinaXNumero = -1
    
    if(!Parametri.OrdinaXNumero)
      Parametri.OrdinaXData = -1
    return Parametri;
    
  }

  Apply(Component,OnSuccess,OnError,FromPosition = 0)
  {
    var Self              = this
    let Parametri         = this.GetParametriXDDT()
    Parametri.LimitStart   = FromPosition
    Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
    this.LastComponent     = Component;

    SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasporto',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FiltroDDT");
                                          if(ArrayInfo != undefined)
                                          { 
                                            if(FromPosition == 0) 
                                              Component.TreeView.Clear();
                                            for(let i = 0; i < ArrayInfo.length; i++)
                                            {
                                              var ARiassunto = ArrayInfo[i]
                                              var SchedaDocumentoDiTrasporto = new TSchedaDocumentoDiTrasporto(SystemInformation.AdvQuery);
                                              SchedaDocumentoDiTrasporto.CaricaRiassunto(ARiassunto);
                                              if(i >= LIMITA_RIGHE_CARICATE)
                                              {
                                                Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                break
                                              }
                                              else Component.TreeView.AddChild(SchedaDocumentoDiTrasporto.GetDescrizione(ARiassunto),SchedaDocumentoDiTrasporto)
                                            }
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle ddt');
                                          {
                                            if(OnError != undefined)
                                              OnError()
                                          }
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        if(OnError != undefined)
                                          OnError()
                                      }, "FiltroDDT")
  }
}

export class TFilterDDTEntranti extends TZFilter
{
  constructor()
  {
    super()
    this.DalNr                 = '',
    this.AlNr                  = '',
    this.Anno1                 = (new Date()).getFullYear(),
    this.Anno2                 = (new Date()).getFullYear(),
    this.DallaData             = '',
    this.AllaData              = '',
    this.Intestatario          = '',
    this.PartitaIva            = '',
    this.Cliente               = -1,
    this.StatoDDT              = -1
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.DDTEntranti
  }

  GetDescrizione()
  {
    return 'Entranti'
  }

  GetParametriXDDT()
  {
    var Parametri = {}
    if(this.DalNr != '')
    {
      if(this.AlNr == '')
      {
        Parametri.NumeroDDT = this.DalNr;
        Parametri.AnnoDDT   = this.Anno1;
      }
      else
      {
        Parametri.DalNr = this.DalNr;
        Parametri.Anno1 = this.Anno1;
        Parametri.OrdinaXNumero = -1
      }
    }
    if(this.AlNr != '')
    {
      Parametri.AlNr  = this.AlNr;
      Parametri.Anno2 = this.Anno2;
      Parametri.OrdinaXNumero = -1
    }
    if(this.DallaData != '')
      Parametri.DallaData = this.DallaData
    if(this.AllaData != '')
      Parametri.AllaData = this.AllaData
    if(this.PartitaIva.trim() != '')
      Parametri.PartitaIva = '%' + this.PartitaIva + '%';
    if(this.Intestatario.trim() != '')
      Parametri.Intestatario = '%' + this.Intestatario + '%';
    if(this.Cliente != -1)
      Parametri.Cliente = this.Cliente
    
      if(this.StatoDDT != -1)
    {
      switch(this.StatoDDT)
      {
        case 'Concluso'    : Parametri.StatoDDT = STATO_DDT.Concluso 
                        break
        case 'Annullato'   : Parametri.StatoDDT = STATO_DDT.Annullato
                        break
        case 'In Corso'    : Parametri.StatoDDT = STATO_DDT.InCorso 
                        break
        }
    }
    
    Parametri.OrdinaXNumero = -1
    
    if(!Parametri.OrdinaXNumero)
      Parametri.OrdinaXData = -1
    return Parametri;
  }

  Apply(Component,OnSuccess,OnError,FromPosition = 0)
  {
    var Self              = this
    let Parametri         = this.GetParametriXDDT()
    Parametri.LimitStart   = FromPosition
    Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
    this.LastComponent     = Component;

    SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasportoEntranti',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FiltroDDT");
                                          if(ArrayInfo != undefined)
                                          {  
                                            if(FromPosition == 0) 
                                              Component.TreeView.Clear();
                                            for(let i = 0; i < ArrayInfo.length; i++)
                                            {
                                              var ARiassunto = ArrayInfo[i]
                                              var SchedaDocumentoDiTrasportoEntrante = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
                                              SchedaDocumentoDiTrasportoEntrante.CaricaRiassunto(ARiassunto);
                                              if(i >= LIMITA_RIGHE_CARICATE)
                                              {
                                                Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                break
                                              }
                                              else Component.TreeView.AddChild(SchedaDocumentoDiTrasportoEntrante.GetDescrizione(ARiassunto),SchedaDocumentoDiTrasportoEntrante)
                                            }
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle ddt');
                                          {
                                            if(OnError != undefined)
                                              OnError()
                                          }
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        if(OnError != undefined)
                                          OnError()
                                      }, "FiltroDDT")
  }
}

export class TFilterDocScaricoProdComposti extends TZFilter
{
  constructor()
  {
    super()
    this.DallaData             = '',
    this.AllaData              = ''
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.DocScaricoProdottiComposti
  }

  GetDescrizione()
  {
    return 'Doc scarico prod composti'
  }

  GetParametriXDocScaricoProdComposti()
  {
    var Parametri = {}
    
    if(this.DallaData != '')
      Parametri.DallaData = this.DallaData
    if(this.AllaData != '')
      Parametri.AllaData = this.AllaData

    Parametri.OrdinaXNumero = -1
    
    if(!Parametri.OrdinaXNumero)
      Parametri.OrdinaXData = -1
    return Parametri;
    
  }

  Apply(Component,OnSuccess,OnError,FromPosition = 0)
  {
    var Self              = this
    let Parametri         = this.GetParametriXDocScaricoProdComposti()
    Parametri.LimitStart   = FromPosition
    Parametri.LimitNumRows = LIMITA_RIGHE_CARICATE + 1
    this.LastComponent     = Component;

    SystemInformation.AdvQuery.GetSQL('DocScaricoProdottiComposti',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"FiltroDoc");
                                          if(ArrayInfo != undefined)
                                          { 
                                            if(FromPosition == 0) 
                                              Component.TreeView.Clear();
                                            for(let i = 0; i < ArrayInfo.length; i++)
                                            {
                                              var ARiassunto = ArrayInfo[i]
                                              var  SchedaDocScaricoProdottiComposti = new  TSchedaDocScaricoProdottiComposti(SystemInformation.AdvQuery);
                                              SchedaDocScaricoProdottiComposti.CaricaRiassunto(ARiassunto);
                                              if(i >= LIMITA_RIGHE_CARICATE)
                                              {
                                                Component.TreeView.AddChild(CAPTION_FILTERS, Self)
                                                break
                                              }
                                              else Component.TreeView.AddChild(SchedaDocScaricoProdottiComposti.GetDescrizione(ARiassunto),SchedaDocScaricoProdottiComposti)
                                            }
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista dei docuementi');
                                          {
                                            if(OnError != undefined)
                                              OnError()
                                          }
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        if(OnError != undefined)
                                          OnError()
                                      }, "FiltroDoc")
  }
}

export class TFilterFornitore extends TZFilter
{
  constructor()
  {
    super()
    this.RagioneSociale  = '',
    this.Indirizzo       = '',
    this.CodiceFornitore = ''
  }

  GetFilterId()
  {
    return DASHBOARD_FILTER_TYPES.Fornitore
  }

  GetDescrizione()
  {
    return 'Fornitori'
  }

  GetParametriXFornitore()
  {
    var Parametri = {}
    if(this.RagioneSociale.trim() != '')
      Parametri.RagioneSociale = '%' + this.RagioneSociale + '%';
    if(this.Indirizzo.trim() != '')
      Parametri.Indirizzo = '%' + this.Indirizzo + '%';
    if(this.CodiceFornitore.trim() != '')
      Parametri.CodiceFornitore = this.CodiceFornitore;
    return Parametri;
  }

  ClearFilterFornitore()
  {
    this.RagioneSociale  = ''
    this.Indirizzo       = ''
    this.CodiceFornitore = ''
  }

  Apply(Component,OnSuccess,OnError)
  {
    var Self = this
    SystemInformation.AdvQuery.GetSQL('Fornitori', this.GetParametriXFornitore(),
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsIniziali");
                                          if(ArrayInfo != undefined)
                                          {
                                            Component.TreeView.Clear();
                                            if(ArrayInfo.length < 11)
                                            {
                                              ArrayInfo.forEach(function(AFornitore)
                                              {
                                                var SchedaFornitore = new TSchedaFornitore(SystemInformation.AdvQuery);
                                                SchedaFornitore.CaricaRiassunto(AFornitore);
                                                var NodeFornitore = Component.TreeView.AddChild(SchedaFornitore.GetDescrizione(),SchedaFornitore)
                                                NodeFornitore.HasChildren = true
                                                if(ArrayInfo.length == 1)
                                                    Component.TreeView.ExpandToggle(NodeFornitore)
                                              });
                                              OnSuccess();
                                            }
                                            else
                                            {
                                              Component.TreeView.Clear();
                                              let $LastIniziale = -1 
                                              ArrayInfo.forEach(function(ARecord)
                                              {
                                                if(ARecord.Iniziale != $LastIniziale)
                                                {
                                                  $LastIniziale = ARecord.Iniziale
                                                  let ANode = Component.TreeView.AddChild(ARecord.Iniziale,
                                                                                      new TSchedaVuotaInizialeFornitore(SystemInformation.AdvQuery,
                                                                                      ARecord.Iniziale));
                                                  ANode.HasChildren = true;
                                                  ANode.Data.FilterFornitore = Self;
                                                }
                                              });
                                              OnSuccess();
                                            }
                                          }
                                          else SystemInformation.HandleError('Impossibile ottenere la lista delle iniziali dei clienti');
                                          {
                                            if(OnError != undefined)
                                              OnError()
                                          }
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        if(OnError != undefined)
                                          OnError()
                                      },
                                      'SelectIniziali')
 }
}

export class TFilterMovimentoConti extends TZFilter
  {
    constructor()
    {
      super()
    }
    GetFilterId()
    {
      return DASHBOARD_FILTER_TYPES.MovimentiConti
    }

    GetDescrizione()
    {
      return 'Movimenti/Conti'
    }

    Apply(Component,OnSuccess,OnError) 
    {
      SystemInformation.AdvQuery.GetSQL('MovimentiConti',{},
        function(Results)
        {
            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectContiCasse");
            if(ArrayInfo != undefined)
            {
              Component.TreeView.Clear();
              ArrayInfo.forEach(function(AMovimentoConto)
              {
                var SchedaContoCorrente = new TSchedaContoCorrente(SystemInformation.AdvQuery);
                SchedaContoCorrente.CaricaRiassunto(AMovimentoConto);
                let ANode = Component.TreeView.AddChild(SchedaContoCorrente.GetDescrizione(), SchedaContoCorrente);
                ANode.HasChildren = true;
              });
              OnSuccess();
            }
            else SystemInformation.HandleError('Impossibile ottenere la lista dei movimenti/conti');
            {
              if(OnError != undefined)
                OnError()
            }
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
          if(OnError != undefined)
            OnError()
        })
    }
}



