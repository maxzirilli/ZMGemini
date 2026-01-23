import { TSchedaGenerica } from '../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation } from '@/SystemInformation';
import { TSchedaCliente } from '@/views/SchedeDatabase/VUESchedaCliente.vue';
import { TModEspansioneCliente } from '@/views/SchedeDatabase/VUESchedaCliente.vue';
import { TSchedaFattura } from '@/views/SchedeDatabase/VUESchedaFattura.vue'
import { TSchedaFornitore } from '@/views/SchedeDatabase/VUESchedaFornitore.vue'
import { TSchedaNotaDiCredito } from '@/views/SchedeDatabase/VUESchedaNotaDiCredito.vue'
import { TSchedaPreventivo } from '@/views/SchedeDatabase/VUESchedaPreventivo.vue'
import { TSchedaPreventivoMultiparametrico } from '@/views/SchedeDatabase/VUESchedaPreventivoMultiparametrico.vue'
import { TSchedaProdotto } from '@/views/SchedeDatabase/VUESchedaProdotto.vue'
import { TSchedaDocumentoDiTrasporto } from '@/views/SchedeDatabase/VUESchedaDocumentoDiTrasporto.vue'
import { TSchedaDocumentoDiTrasportoEntrante } from '@/views/SchedeDatabase/VUESchedaDocumentoDiTrasportoEntrante.vue'
import { TSchedaMovimento } from '@/views/SchedeDatabase/VUESchedaMovimento.vue'
import { TZDateFunct } from '../../../../../../Librerie/VUE/ZDateFunct.js'
import { TSchedaFatturaPassiva } from '@/views/SchedeDatabase/VUESchedaFatturaPassiva.vue'
import { TSchedaAutoFattura } from '@/views/SchedeDatabase/VUESchedaAutoFattura.vue'
import { TSchedaFattureInsolutePregresse } from '@/views/SchedeDatabase/VUESchedaFattureInsolutePregresse.vue'
import { TSchedaFattureInsolutePregresseFornitori } from '@/views/SchedeDatabase/VUESchedaFattureInsolutePregresseFornitori.vue'

export const ID_NODO_FATTURE                          = 1;
export const ID_NODO_NOTE_DI_CREDITO                  = 2
export const ID_NODO_DDT                              = 3

export const ID_NODO_DDT_ENTRANTE                     = 27

export const ID_NODO_CONFERME_PREVENTIVI              = 4
export const ID_NODO_PREVENTIVI                       = 5
export const ID_NODO_DDT_FORNITORE                    = 6
export const ID_NODO_DDT_ENTRANTE_FORNITORE           = 28
export const ID_NODO_FATTURE_PASSIVE                  = 7
export const ID_NODO_NOTE_DI_CREDITO_PASSIVE          = 8

export const ID_NODO_GRUPPI_PRESS                     = 16
export const ID_NODO_IMPIANTI_IDRANTI                 = 17
export const ID_NODO_RIVELAZIONE_FUMO                 = 18
export const ID_NODO_SPRINKLER                        = 19
export const ID_NODO_AUTOFATTURE                      = 20
export const ID_NODO_EVACUATORI                       = 22
export const ID_NODO_IMPIANTO_EVAC                    = 23
export const ID_NODO_FATTURE_INSOLUTE                 = 24
export const ID_NODO_RIVELAZIONE_FUMO_DETTAGLIATO     = 27
export const ID_NODO_MOV_FORNITORE                    = 28
export const ID_NODO_MOV_CLIENTE                      = 29
export const ID_NODO_PREVENTIVI_MULTI                 = 30


export const CLIENTI = {
  ZMSoftware          : 'ZMSOFTWARE',
  Generico            : 'GENERICO'
}

export class TSchedaAnnoFatturaVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveCliente,Anno)
   {
      super(AdvQuery);
      this.Chiave          = Anno;
      this.ChiaveCliente   = ChiaveCliente;
   }

   GetClassName()
   {
     return 'TSchedaAnnoFatturaVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveCliente : this.ChiaveCliente, AnnoFattura : this.Chiave }

     Parametri.GruppoQuery = 'RiassuntoFatturePerAnno'
     Parametri.Query       = 'Riassunto'
     SystemInformation.AdvQuery.ExecuteExternalScript('SelectFatture',Parametri,
                                                      function(Results)
                                                      {
                                                          let ArrayInfo = Results.ListaFatture
                                                          if(ArrayInfo != undefined)
                                                          {  
                                                            ArrayInfo.forEach(function(AFattura)
                                                            {
                                                                var SchedaFattura = new TSchedaFattura(SystemInformation.AdvQuery);
                                                                SchedaFattura.CaricaRiassunto(AFattura);
                                                                AnItem.AddChild(SchedaFattura.GetDescrizione(AFattura),SchedaFattura)
                                                            });
                                                            OnSuccess();
                                                          }
                                                          else console.error('Impossibile ottenere la lista delle fatture');
                                                      },
                                                      function(HTTPError,SubHTTPError,Response)
                                                      {
                                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                      })
   }
}

export class TSchedaVuotaIniziale extends TSchedaGenerica
{
   constructor (AdvQuery,Iniziale,ModalitaEspansione)
   {
      super(AdvQuery);
      this.Chiave = Iniziale;
      if(ModalitaEspansione == undefined)
         this.__ModalitaEspansione = TModEspansioneCliente.modClContabilita
      else this.__ModalitaEspansione = ModalitaEspansione
   }

   GetClassName()
   {
     return 'TSchedaVuotaIniziale';
   }

   GetImageIndex()
   {
     return 'Iniziale.png'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = this.FilterCliente.GetParametriXCliente();
     Parametri.Iniziale = this.Chiave;
     var Self = this
     SystemInformation.AdvQuery.GetSQL('Clienti',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsClientiXIniziale");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ACliente)
                                             {
                                                var SchedaCliente = new TSchedaCliente(SystemInformation.AdvQuery,Self.__ModalitaEspansione);
                                                SchedaCliente.CaricaRiassunto(ACliente);
                                                var NodeCliente = AnItem.AddChild(SchedaCliente.GetDescrizione(),SchedaCliente)
                                                NodeCliente.HasChildren = true;
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista dei clienti');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       })

   }
}


export class TSchedaVuotaInizialeFornitore extends TSchedaGenerica
{
   constructor (AdvQuery,Iniziale)
   {
      super(AdvQuery);
      this.Chiave = Iniziale;
   }

   GetClassName()
   {
     return 'TSchedaVuotaInizialeFornitore';
   }
   

   GetImageIndex()
   {
     return 'Iniziale.png'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = this.FilterFornitore.GetParametriXFornitore();
     Parametri.Iniziale = this.Chiave;
     SystemInformation.AdvQuery.GetSQL('Fornitori',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsFornitoriXIniziale");
                                           if(ArrayInfo != undefined)
                                           {  
                                              ArrayInfo.forEach(function(AFornitore)
                                              {
                                                var SchedaFornitore = new TSchedaFornitore(SystemInformation.AdvQuery);
                                                SchedaFornitore.CaricaRiassunto(AFornitore);
                                                var NodeFornitore = AnItem.AddChild(SchedaFornitore.GetDescrizione(),SchedaFornitore)
                                                NodeFornitore.HasChildren = true
                                              });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista dei fornitori');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       })

   }
}

export class TSchedaIndiceFatture extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveCliente)
   {
      super(AdvQuery);
      this.ChiaveCliente   = ChiaveCliente;
      this.Chiave          = ID_NODO_FATTURE
   }

   GetImageIndex()
   {
     return 'Fattura.png'
   }

   GetDescrizione()
   {
     return 'Fatture'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveCliente : this.ChiaveCliente}

    SystemInformation.AdvQuery.GetSQL('Fatture',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoFatture");
                                          let ArrayInfoCliente = SystemInformation.AdvQuery.FindResults(Results,"InfoCliente");
                                          if(ArrayInfo != undefined && ArrayInfoCliente != undefined)
                                          {  
                                            var SchedaInsoluti = new TSchedaFattureInsolutePregresse(SystemInformation.AdvQuery, Self.ChiaveCliente, ArrayInfoCliente[0].RAGIONE_SOCIALE);
                                            AnItem.AddChild(SchedaInsoluti.GetDescrizione(), SchedaInsoluti)
  
                                            ArrayInfo.forEach(function(AnnoFattura)
                                            {
                                              var SchedaAnnoFatturaVuota = new TSchedaAnnoFatturaVuota(Self.AdvQuery,Self.ChiaveCliente,AnnoFattura.ANNO_FATTURA);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoFatturaVuota.GetDescrizione(),SchedaAnnoFatturaVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle fatture');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnniFattureXCliente')
  }
}

export class TSchedaIndiceNotaDiCredito extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente)
   {
      super(AdvQuery)
      this.ChiaveCliente = ChiaveCliente;
      this.Chiave        = ID_NODO_NOTE_DI_CREDITO
   }

   GetImageIndex()
   {
     return 'NotaDiCredito.png'
   }

   GetDescrizione()
   {
     return 'Note di Credito'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveCliente : this.ChiaveCliente}
    SystemInformation.AdvQuery.GetSQL('NoteDiCredito',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoNote");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ANotaDiCredito)
                                            {
                                              var SchedaAnnoNotaDiCreditoVuota = new TSchedaAnnoNotaDiCreditoVuota(Self.AdvQuery,Self.ChiaveCliente,ANotaDiCredito.ANNO_NOTA);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoNotaDiCreditoVuota.GetDescrizione(),SchedaAnnoNotaDiCreditoVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle note di credito');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnnoNoteXCliente')
  }
}

export class TSchedaAnnoNotaDiCreditoVuota extends TSchedaGenerica
{
  constructor (AdvQuery,ChiaveCliente,Anno)
  {
    super(AdvQuery);
    this.Chiave = Anno;
    this.ChiaveCliente = ChiaveCliente;
  }

  GetClassName()
  {
    return 'TSchedaAnnoNotaDiCreditoVuota';
  }

  GetImageIndex()
  {
    return 'Calendario.png'
  }

  GetDescrizione()
  {
    return this.Chiave
  }

  BeforeExpand(AnItem,OnSuccess)
  {
      let Parametri = { ChiaveCliente : this.ChiaveCliente, AnnoNotaDiCredito : this.Chiave, RiassuntoPerAnno : true }
      SystemInformation.AdvQuery.ExecuteExternalScript('GetRiassuntoNoteDiCredito', 
                                                        Parametri,
                                                        function(Result)
                                                        {  
                                                          if(Result                               != undefined &&
                                                             Result.RiassuntoNoteDiCredito        != undefined   )
                                                          {
                                                            Result.RiassuntoNoteDiCredito.forEach(function(ASchedaNotaDiCredito)
                                                            {
                                                                var SchedaNotaDiCredito = new TSchedaNotaDiCredito(SystemInformation.AdvQuery);
                                                                SchedaNotaDiCredito.CaricaRiassunto(ASchedaNotaDiCredito);
                                                                AnItem.AddChild(SchedaNotaDiCredito.GetDescrizione(ASchedaNotaDiCredito),SchedaNotaDiCredito)
                                                            });
                                                            OnSuccess();
                                                          }
                                                          else console.error('Impossibile ottenere il Riassunto delle Note di credito');
                                                        },
                                                        function(HTTPError,SubHTTPError,Response)
                                                        {
                                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        })
  }
}

export class TSchedaIndiceDDT extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente)
   {
      super(AdvQuery)
      this.ChiaveCliente = ChiaveCliente;
      this.Chiave        = ID_NODO_DDT
   }

   GetImageIndex()
   {
     return 'DDT.png'
   }

   GetDescrizione()
   {
     return 'Documento di Trasporto'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveCliente : this.ChiaveCliente }
    SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasporto',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoDDT");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ADDT)
                                            {
                                              var SchedaAnnoDDTVuota = new TSchedaAnnoDDTVuota(Self.AdvQuery,Self.ChiaveCliente,ADDT.ANNO_DDT);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoDDTVuota.GetDescrizione(),SchedaAnnoDDTVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle DDT');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      }, 'LsAnnoDDTXCliente')
  }
}

export class TSchedaIndiceDDTEntrante extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente)
   {
      super(AdvQuery)
      this.ChiaveCliente = ChiaveCliente;
      this.Chiave        = ID_NODO_DDT_ENTRANTE
   }

   GetImageIndex()
   {
     return 'DDT_entrante2.png'
   }

   GetDescrizione()
   {
     return 'Documento di Trasporto Entrante'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
      var Self = this
      let Parametri = { ChiaveCliente : this.ChiaveCliente }
      SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasportoEntranti',Parametri,
                                        function(Results)
                                        {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoDDT");
                                            if(ArrayInfo != undefined)
                                            {  
                                              ArrayInfo.forEach(function(ADDT)
                                              {
                                                var SchedaAnnoDDTEntranteVuota = new TSchedaAnnoDDTEntranteVuota(Self.AdvQuery,Self.ChiaveCliente,ADDT.ANNO_DDT);
                                                var NodeAnno = AnItem.AddChild(SchedaAnnoDDTEntranteVuota.GetDescrizione(),SchedaAnnoDDTEntranteVuota)
                                                NodeAnno.HasChildren = true;
                                              });
                                              OnSuccess();
                                            }
                                            else console.error('Impossibile ottenere la lista delle DDT');
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        }, 'LsAnnoDDTXCliente')    
  }
}

export class TSchedaAnnoDDTVuota extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente, Anno)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveCliente = ChiaveCliente;
   }

   GetClassName()
   {
     return 'TSchedaAnnoDDTVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveCliente : this.ChiaveCliente, AnnoDDT : this.Chiave}
     SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasporto',Parametri,
                                       function(Results)
                                       {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoPerCliente");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ADDT)
                                            {
                                              var SchedaDocumentoDiTrasporto = new TSchedaDocumentoDiTrasporto(SystemInformation.AdvQuery);
                                              SchedaDocumentoDiTrasporto.CaricaRiassunto(ADDT);
                                              AnItem.AddChild(SchedaDocumentoDiTrasporto.GetDescrizione(),SchedaDocumentoDiTrasporto)
                                            });
                                            OnSuccess()                                         

                                           }
                                           else console.error('Impossibile ottenere la lista delle DDT');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },"RiassuntoDDTPerAnno")
   }
}

export class TSchedaAnnoDDTEntranteVuota extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente, Anno)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveCliente = ChiaveCliente;
   }

   GetClassName()
   {
     return 'TSchedaAnnoDDTEntranteVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveCliente : this.ChiaveCliente, AnnoDDT : this.Chiave}
     SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasportoEntranti',Parametri,
                                       function(Results)
                                       {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoPerCliente");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ADDT)
                                            {
                                              var SchedaDocumentoDiTrasportoEntrante = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
                                              SchedaDocumentoDiTrasportoEntrante.CaricaRiassunto(ADDT);
                                              AnItem.AddChild(SchedaDocumentoDiTrasportoEntrante.GetDescrizione(),SchedaDocumentoDiTrasportoEntrante)
                                            });
                                            OnSuccess()                                         

                                           }
                                           else console.error('Impossibile ottenere la lista delle DDT');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },"RiassuntoDDTPerAnno")
   }
}

export class TSchedaIndiceDDTFornitore extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveFornitore)
   {
      super(AdvQuery)
      this.ChiaveFornitore = ChiaveFornitore;
      this.Chiave          = ID_NODO_DDT_FORNITORE
   }

   GetImageIndex()
   {
     return 'DDT.png'
   }

   GetDescrizione()
   {
     return 'Documento di Trasporto'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveFornitore : this.ChiaveFornitore }
    SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasporto',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoDDT");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ADDT)
                                            {
                                              var SchedaAnnoDDTVuota = new TSchedaAnnoDDTVuotaFornitore(Self.AdvQuery,Self.ChiaveFornitore,ADDT.ANNO_DDT);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoDDTVuota.GetDescrizione(),SchedaAnnoDDTVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle DDT');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      }, 'LsAnnoDDTXFornitore')
  }
}

export class TSchedaIndiceDDTEntranteFornitore extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveFornitore)
   {
      super(AdvQuery)
      this.ChiaveFornitore = ChiaveFornitore;
      this.Chiave          = ID_NODO_DDT_ENTRANTE_FORNITORE
   }

   GetImageIndex()
   {
     return 'DDT_entrante2.png'
   }

   GetDescrizione()
   {
     return 'Documento di Trasporto Entrante'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveFornitore : this.ChiaveFornitore }
    SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasportoEntranti',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoDDT");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ADDT)
                                            {
                                              var SchedaAnnoDDTEntranteVuota = new TSchedaAnnoDDTEntranteVuotaFornitore(Self.AdvQuery,Self.ChiaveFornitore,ADDT.ANNO_DDT);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoDDTEntranteVuota.GetDescrizione(),SchedaAnnoDDTEntranteVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle DDT');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      }, 'LsAnnoDDTXFornitore')
  }
}

export class TSchedaAnnoDDTVuotaFornitore extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore,Anno)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveFornitore = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaAnnoDDTVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveFornitore : this.ChiaveFornitore, AnnoDDT : this.Chiave}
     SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasporto',Parametri,
                                       function(Results)
                                       {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoPerFornitore");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ADDT)
                                            {
                                              var SchedaDocumentoDiTrasporto = new TSchedaDocumentoDiTrasporto(SystemInformation.AdvQuery);
                                              SchedaDocumentoDiTrasporto.CaricaRiassunto(ADDT);
                                              AnItem.AddChild(SchedaDocumentoDiTrasporto.GetDescrizione(),SchedaDocumentoDiTrasporto)
                                            });
                                            OnSuccess()                                         

                                           }
                                           else console.error('Impossibile ottenere la lista delle DDT');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },"RiassuntoDDTPerAnnoFornitore")
   }
}

export class TSchedaAnnoDDTEntranteVuotaFornitore extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore,Anno)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveFornitore = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaAnnoDDTEntranteVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveFornitore : this.ChiaveFornitore, AnnoDDT : this.Chiave}
     SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasportoEntranti',Parametri,
                                       function(Results)
                                       {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoPerFornitore");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(ADDT)
                                            {
                                              var SchedaDocumentoDiTrasportoEntrante = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
                                              SchedaDocumentoDiTrasportoEntrante.CaricaRiassunto(ADDT);
                                              AnItem.AddChild(SchedaDocumentoDiTrasportoEntrante.GetDescrizione(),SchedaDocumentoDiTrasportoEntrante)
                                            });
                                            OnSuccess()                                         

                                           }
                                           else console.error('Impossibile ottenere la lista delle DDT');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },"RiassuntoDDTPerAnnoFornitore")
   }
}

export class TSchedaIndicePreventivoMultiparametrico extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente)
   {
      super(AdvQuery)
      this.ChiaveCliente = ChiaveCliente;
      this.Chiave        = ID_NODO_PREVENTIVI_MULTI
   }

   GetImageIndex()
   {
     return 'Preventivo.png'
   }

   GetDescrizione()
   {
     return 'Preventivi'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveCliente : this.ChiaveCliente}
    SystemInformation.AdvQuery.GetSQL('PreventiviMultiparametrici',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoPreventivi");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(APreventivo)
                                            {
                                              var SchedaAnnoPreventivoVuota = new TSchedaAnnoPreventivoMultiVuota(Self.AdvQuery,Self.ChiaveCliente,APreventivo.ANNO_PREVENTIVO);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoPreventivoVuota.GetDescrizione(),SchedaAnnoPreventivoVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista dei preventivi');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnnoPreventiviXCliente')
  }
}

export class TSchedaAnnoPreventivoMultiVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveCliente,Anno)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveCliente = ChiaveCliente;
   }

   GetClassName()
   {
     return 'TSchedaAnnoPreventivoVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveCliente : this.ChiaveCliente, AnnoPreventivo : this.Chiave}
     SystemInformation.AdvQuery.GetSQL('PreventiviMultiparametrici',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Riassunto");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaPreventivoMulti)
                                             {
                                                var SchedaPreventivoMulti = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
                                                SchedaPreventivoMulti.CaricaRiassunto(ASchedaPreventivoMulti);
                                                AnItem.AddChild(SchedaPreventivoMulti.GetDescrizione(ASchedaPreventivoMulti),SchedaPreventivoMulti)
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista dei preventivi');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },'RiassuntoPreventiviPerAnno')
   }
}

export class TSchedaIndicePreventivo extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente)
   {
      super(AdvQuery)
      this.ChiaveCliente = ChiaveCliente;
      this.Chiave        = ID_NODO_PREVENTIVI
   }

   GetImageIndex()
   {
     return 'Preventivo.png'
   }

   GetDescrizione()
   {
     return 'Preventivi vecchio stile'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveCliente : this.ChiaveCliente}
    SystemInformation.AdvQuery.GetSQL('Preventivi',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoPreventivi");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(APreventivo)
                                            {
                                              var SchedaAnnoPreventivoVuota = new TSchedaAnnoPreventivoVuota(Self.AdvQuery,Self.ChiaveCliente,APreventivo.ANNO_PREVENTIVO);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoPreventivoVuota.GetDescrizione(),SchedaAnnoPreventivoVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista dei preventivi');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnnoPreventiviXCliente')
  }
}

export class TSchedaAnnoPreventivoVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveCliente,Anno)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveCliente = ChiaveCliente;
   }

   GetClassName()
   {
     return 'TSchedaAnnoPreventivoVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveCliente : this.ChiaveCliente, AnnoPreventivo : this.Chiave}
     SystemInformation.AdvQuery.GetSQL('Preventivi',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Riassunto");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaPreventivo)
                                             {
                                                var SchedaPreventivo = new TSchedaPreventivo(SystemInformation.AdvQuery);
                                                SchedaPreventivo.CaricaRiassunto(ASchedaPreventivo);
                                                AnItem.AddChild(SchedaPreventivo.GetDescrizione(ASchedaPreventivo),SchedaPreventivo)
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista dei preventivi');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },'RiassuntoPreventiviPerAnno')
   }
}

export class TSchedaIndiceConferme extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveCliente)
   {
      super(AdvQuery)
      this.ChiaveCliente = ChiaveCliente;
      this.Chiave        = ID_NODO_CONFERME_PREVENTIVI
   }

   GetImageIndex()
   {
     return 'Conferma.png'
   }

   GetDescrizione()
   {
     return 'Conferme d\' ordine'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveCliente : this.ChiaveCliente}
    SystemInformation.AdvQuery.GetSQL('Preventivi',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoConferme");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(AConferma)
                                            {
                                              var SchedaAnnoConfermaVuota = new TSchedaAnnoConfermaVuota(Self.AdvQuery,Self.ChiaveCliente,AConferma.ANNO);
                                              var NodeAnno = AnItem.AddChild(SchedaAnnoConfermaVuota.GetDescrizione(),SchedaAnnoConfermaVuota)
                                              NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle conferme');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnnoConfermeXCliente')
  }
}

export class TSchedaAnnoConfermaVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveCliente,Anno)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveCliente = ChiaveCliente;
   }

   GetClassName()
   {
     return 'TSchedaAnnoConfermaVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveCliente : this.ChiaveCliente, AnnoConferma : this.Chiave}
     SystemInformation.AdvQuery.GetSQL('Preventivi',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Riassunto");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaConferma)
                                             {
                                                var SchedaConferma = new TSchedaPreventivo(SystemInformation.AdvQuery);
                                                SchedaConferma.CaricaRiassunto(ASchedaConferma);
                                                AnItem.AddChild(SchedaConferma.GetDescrizione(ASchedaConferma),SchedaConferma)
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista delle conferme');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },'RiassuntoConfermePerAnno')
   }
}

export class TSchedaVuotaMagazzino extends TSchedaGenerica
{
   constructor (AdvQuery, ChiaveSettore)
   {
      super(AdvQuery);
      this.Chiave = ChiaveSettore
   }

   GetClassName()
   {
     return 'TSchedaVuotaMagazzino';
   }

   GetImageIndex()
   {
     return 'Settore.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     var Self = this
     let Parametri = { ChiaveSettore : this.Chiave }
     SystemInformation.AdvQuery.GetSQL('Magazzino',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsInizialiProdotti");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaInizialiProdotti)
                                             {
                                                var SchedaInizialiProdottiVuota = new TSchedaInizialiProdottiVuota(Self.AdvQuery,Self.Chiave, ASchedaInizialiProdotti.Iniziale);
                                                var NodeAnno = AnItem.AddChild(SchedaInizialiProdottiVuota.GetDescrizione(),SchedaInizialiProdottiVuota)
                                                NodeAnno.HasChildren = true;
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista delle iniziali dei prodotti');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },'LsInizialiProdotti')
   }
}

export class TSchedaInizialiProdottiVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveSettore,Iniziale)
   {
      super(AdvQuery);
      this.ChiaveSettore = ChiaveSettore
      this.Chiave        = Iniziale;
   }

   GetClassName()
   {
     return 'TSchedaInizialiProdottiVuota';
   }

   GetImageIndex()
   {
     return 'Iniziale.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     var Parametri = {}
     Parametri = {Iniziale : this.Chiave, ChiaveSettore : this.ChiaveSettore}
     SystemInformation.AdvQuery.GetSQL('Magazzino',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsProdottiXIniziale");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaProdotto)
                                             {
                                              var SchedaProdotto = new TSchedaProdotto(SystemInformation.AdvQuery);
                                              SchedaProdotto.CaricaRiassunto(ASchedaProdotto);
                                              AnItem.AddChild(SchedaProdotto.GetDescrizione(ASchedaProdotto),SchedaProdotto)
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista dei prodotti');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       }, "LsProdottiXIniziale")
   }
}

export class TSchedaAnnoMovimentiVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveContoCassa,Anno,ChiaveFornitore)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveContoCassa = ChiaveContoCassa;
      this.ChiaveFornitore  = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaAnnoMovimentiVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     var Self = this
     let Parametri = { ChiaveContoCassa : this.ChiaveContoCassa, AnnoMovimento : this.Chiave, ChiaveFornitore : this.ChiaveFornitore}
     SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ListaMesi");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaMovimento)
                                             {
                                                let MesiInIta = TZDateFunct.GetMesiInItaliano()
                                                ASchedaMovimento.MESE = MesiInIta[ASchedaMovimento.MESE - 1]
                                                var SchedaMesiMovimentiVuota = new TSchedaMesiMovimentiVuota(Self.AdvQuery,Self.ChiaveContoCassa,Self.Chiave,ASchedaMovimento.MESE,Self.ChiaveFornitore);
                                                var NodeIniziale = AnItem.AddChild(SchedaMesiMovimentiVuota.GetDescrizione(),SchedaMesiMovimentiVuota)
                                                NodeIniziale.HasChildren = true;
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista anno dei preventivi');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },this.ChiaveFornitore == undefined ?'LsMesiMovimentiXConto' : 'ListaMesiMovimentiXFornitore')
   }
}

export class TSchedaMesiMovimentiVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveContoCassa,Anno,Mese,ChiaveFornitore)
   {
      super(AdvQuery);
      this.Chiave           = Mese;
      this.ChiaveContoCassa = ChiaveContoCassa;
      this.Anno             = Anno;
      this.ChiaveFornitore  = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaMesiMovimentiVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let MesiInIta = TZDateFunct.GetMesiInItaliano()
       for( let i = 0; i <= MesiInIta.length; i++)
         if(this.Chiave == MesiInIta[i])
           this.Chiave = i + 1
     let Parametri = { ChiaveContoCassa : this.ChiaveContoCassa, AnnoMovimento : this.Anno, MeseMovimento : this.Chiave, ChiaveFornitore: this.ChiaveFornitore}
     SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo               = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoMovimenti");
                                           let ArrayInfoFatture        = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoFatture");
                                           let ArrayInfoFatturePassive = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoFatturePassive");
                                           let ArrayConcatenati        = ArrayInfo.concat(ArrayInfoFatture,ArrayInfoFatturePassive)
                                           let ArrayOrdinato = ArrayConcatenati.sort(function(p1,p2)
                                           {
                                            return ((new Date(p1.DATA) < new Date(p2.DATA)) ? 1 : (new Date(p1.DATA) > new Date(p2.DATA)) ? -1 : 0)
                                           })

                                           if(ArrayInfo != undefined && ArrayInfoFatture != undefined)
                                           {  
                                            ArrayOrdinato.forEach(function(ARiassunto)
                                             {
                                              if(ARiassunto.TIPO_DOCUMENTO == 'F')
                                              {
                                                var SchedaFattura = new TSchedaFattura(SystemInformation.AdvQuery, true);
                                                SchedaFattura.CaricaRiassunto(ARiassunto);
                                                AnItem.AddChild(SchedaFattura.GetDescrizione(ARiassunto),SchedaFattura)
                                              }
                                              else if(ARiassunto.TIPO_DOCUMENTO == 'P')
                                              {
                                                var SchedaFatturaPassiva = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                                SchedaFatturaPassiva.CaricaRiassunto(ARiassunto);
                                                AnItem.AddChild(SchedaFatturaPassiva.GetDescrizione(ARiassunto),SchedaFatturaPassiva)
                                              }
                                              else
                                              {
                                                var SchedaMovimento = new TSchedaMovimento(SystemInformation.AdvQuery);
                                                SchedaMovimento.CaricaRiassunto(ARiassunto);
                                                AnItem.AddChild(SchedaMovimento.GetDescrizione(ARiassunto),SchedaMovimento)
                                              }
                                                
                                             });           
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista movimenti');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },this.ChiaveFornitore == undefined ?'RiassuntoMovimentiPerMese' : 'RiassuntoMovimentiPerMesePerFornitore')
   }
}

export class TSchedaAnnoMovimentiFornitoriVuota extends TSchedaGenerica
{
   constructor (AdvQuery,Anno,ChiaveFornitore)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveFornitore  = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaAnnoMovimentiFornitoriVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     var Self = this
     let Parametri = { AnnoMovimento : this.Chiave, ChiaveFornitore : this.ChiaveFornitore}
     SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ListaMesi");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaMovimento)
                                             {
                                                let MesiInIta = TZDateFunct.GetMesiInItaliano()
                                                ASchedaMovimento.MESE = MesiInIta[ASchedaMovimento.MESE - 1]
                                                var SchedaMesiMovimentiVuota = new TSchedaMesiMovimentiFornitoriVuota(Self.AdvQuery,Self.Chiave,ASchedaMovimento.MESE,Self.ChiaveFornitore);
                                                var NodeIniziale = AnItem.AddChild(SchedaMesiMovimentiVuota.GetDescrizione(),SchedaMesiMovimentiVuota)
                                                NodeIniziale.HasChildren = true;
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista anno dei preventivi');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       }, 'ListaMesiMovimentiXFornitore')
   }
}

export class TSchedaMesiMovimentiFornitoriVuota extends TSchedaGenerica
{
  constructor (AdvQuery,Anno,Mese,ChiaveFornitore)
  {
    super(AdvQuery);
    this.Chiave           = Mese;
    this.Anno             = Anno;
    this.ChiaveFornitore  = ChiaveFornitore;
  }

  GetClassName()
  {
    return 'TSchedaMesiMovimentiFornitoriVuota';
  }

  GetImageIndex()
  {
    return 'Calendario.png'
  }

  GetDescrizione()
  {
  return this.Chiave
  }

  BeforeExpand(AnItem,OnSuccess)
  {
    let MesiInIta = TZDateFunct.GetMesiInItaliano()
      for( let i = 0; i <= MesiInIta.length; i++)
        if(this.Chiave == MesiInIta[i])
          this.Chiave = i + 1

    let Parametri = { AnnoMovimento : this.Anno, MeseMovimento : this.Chiave, ChiaveFornitore: this.ChiaveFornitore}
    SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                      function(Results)
                                      {
                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoMovimenti");

                                        if(ArrayInfo != undefined )
                                        {  
                                          ArrayInfo.forEach(function(ARiassunto)
                                          {
                                            var SchedaMovimento = new TSchedaMovimento(SystemInformation.AdvQuery);
                                            SchedaMovimento.CaricaRiassunto(ARiassunto);
                                            AnItem.AddChild(SchedaMovimento.GetDescrizione(ARiassunto),SchedaMovimento)
                                          });
                                          OnSuccess();
                                        }
                                        else console.error('Impossibile ottenere la lista movimenti');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'RiassuntoMovimentiPerMesePerFornitore')
  }
}

export class TSchedaAnnoMovimentiClientiVuota extends TSchedaGenerica
{
   constructor (AdvQuery,Anno,ChiaveCliente)
   {
      super(AdvQuery);
      this.Chiave = Anno;
      this.ChiaveCliente  = ChiaveCliente;
   }

   GetClassName()
   {
     return 'TSchedaAnnoMovimentiClientiVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     var Self = this
     let Parametri = { AnnoMovimento : this.Chiave, ChiaveCliente : this.ChiaveCliente}
     SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ListaMesi");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(ASchedaMovimento)
                                             {
                                                let MesiInIta = TZDateFunct.GetMesiInItaliano()
                                                ASchedaMovimento.MESE = MesiInIta[ASchedaMovimento.MESE - 1]
                                                var SchedaMesiMovimentiVuota = new TSchedaMesiMovimentiClientiVuota(Self.AdvQuery,Self.Chiave,ASchedaMovimento.MESE,Self.ChiaveCliente);
                                                var NodeIniziale = AnItem.AddChild(SchedaMesiMovimentiVuota.GetDescrizione(),SchedaMesiMovimentiVuota)
                                                NodeIniziale.HasChildren = true;
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista anno dei preventivi');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       }, 'ListaMesiMovimentiXCliente')
   }
}

export class TSchedaMesiMovimentiClientiVuota extends TSchedaGenerica
{
  constructor (AdvQuery,Anno,Mese,ChiaveCliente)
  {
    super(AdvQuery);
    this.Chiave           = Mese;
    this.Anno             = Anno;
    this.ChiaveCliente  = ChiaveCliente;
  }

  GetClassName()
  {
    return 'TSchedaMesiMovimentiClientiVuota';
  }

  GetImageIndex()
  {
    return 'Calendario.png'
  }

  GetDescrizione()
  {
  return this.Chiave
  }

  BeforeExpand(AnItem,OnSuccess)
  {
    let MesiInIta = TZDateFunct.GetMesiInItaliano()
      for( let i = 0; i <= MesiInIta.length; i++)
        if(this.Chiave == MesiInIta[i])
          this.Chiave = i + 1

    let Parametri = { AnnoMovimento : this.Anno, MeseMovimento : this.Chiave, ChiaveCliente: this.ChiaveCliente}
    SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                      function(Results)
                                      {
                                        let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"RiassuntoMovimenti");

                                        if(ArrayInfo != undefined )
                                        {  
                                          ArrayInfo.forEach(function(ARiassunto)
                                          {
                                            var SchedaMovimento = new TSchedaMovimento(SystemInformation.AdvQuery);
                                            SchedaMovimento.CaricaRiassunto(ARiassunto);
                                            AnItem.AddChild(SchedaMovimento.GetDescrizione(ARiassunto),SchedaMovimento)
                                          });
                                          OnSuccess();
                                        }
                                        else console.error('Impossibile ottenere la lista movimenti');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'RiassuntoMovimentiPerMesePerCliente')
  }
}

export class TSchedaIndiceFatturePassive extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore)
   {
      super(AdvQuery);
      this.ChiaveFornitore = ChiaveFornitore;
      this.Chiave          = ID_NODO_FATTURE_PASSIVE
   }

   GetImageIndex()
   {
     return 'FatturaPassiva.png'
   }

   GetDescrizione()
   {
     return 'Fatture fornitori'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveFornitore : this.ChiaveFornitore}
    SystemInformation.AdvQuery.GetSQL('FatturePassive',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoFatturePassive");
                                          if(ArrayInfo != undefined)
                                          { 
                                            var SchedaInsoluti = new TSchedaFattureInsolutePregresseFornitori(SystemInformation.AdvQuery, Self.ChiaveFornitore);
                                            AnItem.AddChild(SchedaInsoluti.GetDescrizione(), SchedaInsoluti) 
                                            
                                            ArrayInfo.forEach(function(AnnoFatturaPassiva)
                                            {
                                                var SchedaAnnoFatturaPassivaVuota = new TSchedaAnnoFatturaPassivaVuota(Self.AdvQuery,Self.ChiaveFornitore,AnnoFatturaPassiva.ANNO_FATTURA_PASSIVA);
                                                var NodeAnno = AnItem.AddChild(SchedaAnnoFatturaPassivaVuota.GetDescrizione(),SchedaAnnoFatturaPassivaVuota)
                                                NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle fatture passive');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnniFatturePassiveXFornitore')
  }
}

export class TSchedaIndiceNoteDiCreditoPassive extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore)
   {
      super(AdvQuery);
      this.ChiaveFornitore = ChiaveFornitore;
      this.Chiave          = ID_NODO_NOTE_DI_CREDITO_PASSIVE
   }

   GetImageIndex()
   {
     return 'NotaDiCreditoPassiva.png'
   }

   GetDescrizione()
   {
     return 'Note di credito fornitori'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveFornitore : this.ChiaveFornitore}
    SystemInformation.AdvQuery.GetSQL('FatturePassive',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoNoteDiCredito");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(AnnoNotaDiCredito)
                                            {
                                                var SchedaAnnoNotaDiCreditoVuota = new TSchedaAnnoNotaDiCreditoPassivaVuota(Self.AdvQuery,Self.ChiaveFornitore,AnnoNotaDiCredito.ANNO_FATTURA_PASSIVA);
                                                var NodeAnno = AnItem.AddChild(SchedaAnnoNotaDiCreditoVuota.GetDescrizione(),SchedaAnnoNotaDiCreditoVuota)
                                                NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista delle fatture passive');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnniNoteDiCredito')
  }
}

export class TSchedaIndiceAutoFatture extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore)
   {
      super(AdvQuery);
      this.ChiaveFornitore = ChiaveFornitore;
      this.Chiave          = ID_NODO_AUTOFATTURE
   }

   GetImageIndex()
   {
     return 'AutoFattura.png'
   }

   GetDescrizione()
   {
     return 'AutoFatture'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveFornitore : this.ChiaveFornitore}
    SystemInformation.AdvQuery.GetSQL('Autofatture',Parametri,
                                      function(Results)
                                      {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"LsAnnoAutoFatture");
                                          if(ArrayInfo != undefined)
                                          {  
                                            ArrayInfo.forEach(function(AnnoAutoFattura)
                                            {
                                                var SchedaAnnoAutoFatturaVuota = new TSchedaAnnoAutoFatturaVuota(Self.AdvQuery,Self.ChiaveFornitore,AnnoAutoFattura.ANNO_AUTOFATTURA);
                                                var NodeAnno = AnItem.AddChild(SchedaAnnoAutoFatturaVuota.GetDescrizione(),SchedaAnnoAutoFatturaVuota)
                                                NodeAnno.HasChildren = true;
                                            });
                                            OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista degli anni delle autofatture');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'LsAnnoAutoFatture')
  }
}

export class TSchedaAnnoAutoFatturaVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore,Anno)
   {
      super(AdvQuery);
      this.Chiave          = Anno;
      this.ChiaveFornitore = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaAnnoAutoFatturaVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveFornitore : this.ChiaveFornitore, AnnoAutoFattura : this.Chiave, RiassuntoPerAnno : true  }
      SystemInformation.AdvQuery.ExecuteExternalScript('GetRiassuntoAutofattura', 
                                                        Parametri,
                                                        function(Result)
                                                        {  
                                                          if(Result                               != undefined &&
                                                             Result.RiassuntoAutofatture          != undefined   )
                                                          {
                                                            Result.RiassuntoAutofatture.forEach(function(AAutoFattura)
                                                            {
                                                                var SchedaAutoFattura = new TSchedaAutoFattura(SystemInformation.AdvQuery);
                                                                SchedaAutoFattura.CaricaRiassunto(AAutoFattura);
                                                                AnItem.AddChild(SchedaAutoFattura.GetDescrizione(AAutoFattura),SchedaAutoFattura)
                                                            });
                                                            OnSuccess();
                                                          }
                                                          else console.error('Impossibile ottenere il Riassunto delle autofatture');
                                                        },
                                                        function(HTTPError,SubHTTPError,Response)
                                                        {
                                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        })
   }
}

export class TSchedaAnnoFatturaPassivaVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore,Anno)
   {
      super(AdvQuery);
      this.Chiave          = Anno;
      this.ChiaveFornitore = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaAnnoFatturaVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveFornitore : this.ChiaveFornitore, AnnoFatturaPassiva : this.Chiave }
     SystemInformation.AdvQuery.GetSQL('FatturePassive',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Riassunto");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(AFatturaPassiva)
                                             {
                                                var SchedaFatturaPassiva = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                                SchedaFatturaPassiva.CaricaRiassunto(AFatturaPassiva);
                                                AnItem.AddChild(SchedaFatturaPassiva.GetDescrizione(AFatturaPassiva),SchedaFatturaPassiva)
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista delle fatture passive');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },'RiassuntoFatturePassivePerAnno')
   }
}

export class TSchedaAnnoNotaDiCreditoPassivaVuota extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore,Anno)
   {
      super(AdvQuery);
      this.Chiave          = Anno;
      this.ChiaveFornitore = ChiaveFornitore;
   }

   GetClassName()
   {
     return 'TSchedaAnnoFatturaVuota';
   }

   GetImageIndex()
   {
     return 'Calendario.png'
   }

   GetDescrizione()
   {
    return this.Chiave
   }

   BeforeExpand(AnItem,OnSuccess)
   {
     let Parametri = { ChiaveFornitore : this.ChiaveFornitore, AnnoFatturaPassiva : this.Chiave }
     SystemInformation.AdvQuery.GetSQL('FatturePassive',Parametri,
                                       function(Results)
                                       {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Riassunto");
                                           if(ArrayInfo != undefined)
                                           {  
                                             ArrayInfo.forEach(function(AFatturaPassiva)
                                             {
                                                var SchedaFatturaPassiva = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                                SchedaFatturaPassiva.CaricaRiassunto(AFatturaPassiva);
                                                AnItem.AddChild(SchedaFatturaPassiva.GetDescrizione(AFatturaPassiva),SchedaFatturaPassiva)
                                             });
                                             OnSuccess();
                                           }
                                           else console.error('Impossibile ottenere la lista delle fatture passive');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },'RiassuntoNoteDiCreditoPerAnno')
   }
}

export class TSchedaIndiceDocumenti extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveCliente)
   {
      super(AdvQuery);
      this.ChiaveCliente   = ChiaveCliente;
   }

   GetImageIndex()
   {
     return 'Documenti.png'
   }

   GetDescrizione()
   {
     return 'Documenti'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var SchedaIndiceFatture = new TSchedaIndiceFatture('',this.ChiaveCliente);
    var NodeFattura = AnItem.AddChild(SchedaIndiceFatture.GetDescrizione(),SchedaIndiceFatture)
    NodeFattura.HasChildren = true;
    OnSuccess()
  }
}

export class TSchedaIndiceMovFornitore extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveFornitore)
   {
      super(AdvQuery);
      this.ChiaveFornitore   = ChiaveFornitore;
      this.Chiave            = ID_NODO_MOV_FORNITORE
   }

   GetImageIndex()
   {
     return 'Movimenti.png'
   }

   GetDescrizione()
   {
     return 'Movimenti'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    let Parametri = { ChiaveFornitore : this.ChiaveFornitore}
    SystemInformation.AdvQuery.GetSQL('MovimentiConti',Parametri,
                                      function(Results)
                                      {   
                                          
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ListaAnni");
                                          if(ArrayInfo != undefined)
                                          { 
                                             ArrayInfo.forEach(function(AnnoMovimento)
                                             {
                                                var SchedaAnnoMovimentiVuota = new TSchedaAnnoMovimentiFornitoriVuota(Self.AdvQuery,AnnoMovimento.ANNO,Self.ChiaveFornitore);
                                                var NodeAnno                 = AnItem.AddChild(SchedaAnnoMovimentiVuota.GetDescrizione(),SchedaAnnoMovimentiVuota)
                                                NodeAnno.HasChildren = true;

                                             });
                                             OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista dei movimenti');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'ListaAnniMovimentiXFornitore')
  }
}

export class TSchedaIndiceMovCliente extends TSchedaGenerica
{
   constructor (AdvQuery,ChiaveCliente)
   {
      super(AdvQuery);
      this.ChiaveCliente     = ChiaveCliente;
      this.Chiave            = ID_NODO_MOV_CLIENTE
   }

   GetImageIndex()
   {
     return 'Movimenti.png'
   }

   GetDescrizione()
   {
     return 'Movimenti'
   }

   BeforeExpand(AnItem,OnSuccess)
   {
    var Self = this
    SystemInformation.AdvQuery.GetSQL('MovimentiConti',{ChiaveCliente : this.ChiaveCliente},
                                      function(Results)
                                      {   
                                          
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ListaAnni");
                                          if(ArrayInfo != undefined)
                                          { 
                                             ArrayInfo.forEach(function(AnnoMovimento)
                                             {
                                                var SchedaAnnoMovimentiVuota = new TSchedaAnnoMovimentiClientiVuota(Self.AdvQuery,AnnoMovimento.ANNO,Self.ChiaveCliente);
                                                var NodeAnno                 = AnItem.AddChild(SchedaAnnoMovimentiVuota.GetDescrizione(),SchedaAnnoMovimentiVuota)
                                                NodeAnno.HasChildren = true;

                                             });
                                             OnSuccess();
                                          }
                                          else console.error('Impossibile ottenere la lista dei movimenti');
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      },'ListaAnniMovimentiXCliente')
  }
}

