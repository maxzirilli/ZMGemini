import { TAdvQuery,TAdvQueryErrors } from '../../../../../../Librerie/VUE/ZAdvQuery.js';
import { TZEconomicFunct } from '../../../../../../Librerie/VUE/ZEconomicFunct.js'
import axios from 'axios';
import { SchedaGenericaConfigurazione, TSchedaGenericaCharCase } from '../../../../../../Librerie/VUE/ZSchedaGenerica.js';
import { TAccessRights } from './AccessRights.js';

                      
export const LOCALSTORAGE = { 
                              TokenRememberMe : "XFG2lo",
                              TokenPartitaIVA : "GeminiPartitaIVA"
                            }  

export const NUMERO_VERSIONE_GEMINI = '0.000.000'
export const NOME_PROGRAMMA = 'Gemini'

export const TIPO_COMUNICAZIONI = { // uguale a php
                                    AvvisoGenerico                  : 'A',
                                    FornitoriSenzaCodice            : 'F',
                                    NoteGestite                     : 'N',
                                    AllarmeProdotto                 : 'D',
                                    QuantitaFornitureNonControllate : 'Q',
                                  }

export const TIPOLOGIA_MODIFICA_ANAGRAFICA = {
                                                Cliente : 'C',
                                                Filiale : 'F',
                                             }

export const NUMERO_LETTERE_PER_RICERCA = 2

export const LUGLIO_2024                = 202407

export const ANNI_SCADENZA_ESTINTORI = 18

export const OPERAZIONE_BLOB = {
                                  Inserimento : 'I',
                                  Update      : 'U',
                                  Delete      : 'D',
                                  Select      : 'S'
                               }

export const MODALITA_INCASSO = {
                                  Assegni      : 'A',
                                  Contante     : 'C',
                                  Pos          : 'P',
                                  NonIncassato : 'N'
                                }

export const RIFERIMENTO_CAUSALI = {
                                      Cliente   : 'C',
                                      Fornitore : 'F',
                                   }  

export const STATO_PREVENTIVO = {
                                  Confermato  : 'C',
                                  Sospeso     : 'S',
                                  Rifiutato   : 'R'
                                }

export const STATO_DDT = {
                                  Concluso    : 'C',
                                  InCorso     : 'I',
                                  Annullato   : 'A'
                                }      

export const DOCUMENTO_CORRELATO = {
                                      NessunDocumento  : 'N',
                                      Ordine           : 'O',
                                   }

export const PAGAMENTO_BOLLO = {
                                  NessunBollo      : 'N',
                                  PagatoDaNoi      : 'P',
                                  PagatoDalCliente : 'C'
                                }                                


export const TIPO_AUTOFATTURA = {
                                  ExtraComunitarie : 'E',
                                  IntraComunitarie : 'I',
                                  SanMarino        : 'S',
                                  ExArticolo17     : 'A',
                                  IntegrFatture    : 'F',
                                  Splafonamento    : 'P',
                                }                                
                                 
export const TIPO_FILE = {
                            FileImmagine   : 'I',
                            FilePdf        : 'P',
                            FileAltro      : 'A'
                         }

export const TIPO_SCONTO = {
                              Listino   : 'L',
                              Prezzo    : 'P',
                              Sconto    : 'S'
                          }

export const RUOLI = {
                        Amministratore      : 'A',
                        SuperUtente         : 'S',
                        Tecnico             : 'T',
                        Dipendente          : 'D',
                        Clienti             : 'C',      
                     }

export const TIPI_FATTURAZIONE        = {
                                          Annuale           : 'A',
                                          Semestrale        : 'S'
                                        }
export const PROVA_DINAMICA = {
                                PrimaVisita   : 'P',
                                SecondaVisita : 'S'
                              }

export const FATT_ELE_ESIGIBILITA_IVA = {
                                             Differita : 'D',
                                             Immediata : 'I',
                                             Scissione : 'S'
                                         }

export const TRASPORTO_A_MEZZO        = {
                                             Mittente     : 'M',
                                             Destinatario : 'D',
                                             Vettore      : 'V'
                                         }

export const DASHBOARD_FILTER_TYPES = 
{
    Clienti                    : 'Clienti',
    Fatture                    : 'Fatture',
    Autofatture                : 'Autofatture',
    Fornitore                  : 'Fornitori',
    Filiali                    : 'Filiali',
    Magazzino                  : 'Magazzino',
    Tecnici                    : 'Tecnici',
    MovimentiConti             : 'Movimenti/Conti',
    NoteDiCredito              : 'Note di credito',
    FatturePassive             : 'Fatture passive',
    NoteDiCreditoPassive       : 'Note di credito passive',
    Prodotti                   : 'Prodotti',
    VociPreventiviPredefinite  : 'VociPreventiviPredefinite',
    PrimaNota                  : 'Prima nota',
    Movimenti                  : 'Movimenti',
    DDT                        : 'Documenti di trasporto',
    DDTEntranti                : 'Entranti',
    Conferme                   : 'Conferme',
    Preventivi                 : 'Preventivi',
    MovimentiMagazzini         : 'Movimenti Magazzini',
    DocScaricoProdottiComposti : 'Doc scarico prod composti',
    Magazzini                  : 'Magazzini'
}
export const PRIORITA_URGENZA    = {
                                      MoltoBassa  : 'M',
                                      Bassa       : 'B',
                                      Normale     : 'N',
                                      Alta        : 'A',
                                      Urgente     : 'U'
                                    }

export const TIPOLOGIA_DOCUMENTI = {
                                      Fattura            : 'F',
                                      PreventivoConferma : 'P',
                                      NotaDiCredito      : 'N',
                                      PreventivoMulti    : 'M',
                                      Interno            : 'I', //Email di benvenuto
                                   } 

export const TIPOLOGIA_RIGHE_CONTO_CLIENTE = {
                                                      Movimenti          : 'M',
                                                      Fatture            : 'F',
                                                      NotaDiCredito      : 'N',
                                                      RataFattura        : 'R',
                                                      FattPregresse      : 'P',
                                                      AperturaAnno       : 'A',
                                                  } 

export const TIPOLOGIA_ATTIVAZIONE = {
                                       Bombola : 'B',
                                       Ampolla : 'A'
                                     }                                                                                                           
                
export const LIMITA_RIGHE_CARICATE = 100;

export const CAPTION_FILTERS = 'FiltersCaption';
                                     
class TSystemInformation
{
  MenuTrigger = {
                  TriggerPopupScaricoFileZip: 0
                }
  InserimentoClienteGuidatoTrigger =  {
                                        TriggerInserimentoGuidato: false
                                      }
  UltimaDataDalContoClienteInserita = ''
  UltimaDataAlContoClienteInserita  = ''

  UltimaDataDalContoFornitoreInserita =''
  UltimaDataAlContoFornitoreInserita  =''

  GetLsRiferimentoCausali()
  {
     return [
               {
                 CHIAVE       : RIFERIMENTO_CAUSALI.Cliente,
                 DESCRIZIONE  : 'Cliente'
               },
               {
                 CHIAVE       : RIFERIMENTO_CAUSALI.Fornitore,
                 DESCRIZIONE  : 'Fornitore'
               }
            ]
  }

  GetLsTrasportoAMezzo()
  {
     return [
               {
                 CHIAVE       : TRASPORTO_A_MEZZO.Mittente,
                 DESCRIZIONE  : 'Mittente'
               },
               {
                 CHIAVE       : TRASPORTO_A_MEZZO.Destinatario,
                 DESCRIZIONE  : 'Destinatario'
               },
               {
                 CHIAVE       : TRASPORTO_A_MEZZO.Vettore,
                 DESCRIZIONE  : 'Vettore'
               },
            ]
  }


  GetLsTipiDocumentoCorrelato()
  {
     return [
               {
                 Id           : DOCUMENTO_CORRELATO.NessunDocumento,
                 Descrizione  : 'Nessun documento'
               },
               {
                 Id           : DOCUMENTO_CORRELATO.Ordine,
                 Descrizione  : 'Ordine'
               },
            ]
  }

  GetLsPrioritaUrgenze()
  {
     return [
               {
                 Id           : PRIORITA_URGENZA.MoltoBassa,
                 Descrizione  : 'Molto bassa'
               },
               {
                 Id           : PRIORITA_URGENZA.Bassa,
                 Descrizione  : 'Bassa'
               },
               {
                 Id           : PRIORITA_URGENZA.Normale,
                 Descrizione  : 'Normale'
               },
               {
                 Id           : PRIORITA_URGENZA.Alta,
                 Descrizione  : 'Alta'
               },
               {
                 Id           : PRIORITA_URGENZA.Urgente,
                 Descrizione  : 'Urgente'
               }
            ]
  }

  GetLsTipiFatturazione()
  {
     return [
               {
                 Id           : TIPI_FATTURAZIONE.Annuale,
                 Descrizione  : 'Annuale'
               },
               {
                 Id           : TIPI_FATTURAZIONE.Semestrale,
                 Descrizione  : 'Semestrale'
               }
            ]
  }

  CaricaUltimoCodiceFornitore(OnSuccess)
  {
     SystemInformation.AdvQuery.GetSQL("Fornitori",{},
                                       function(Results)
                                       {
                                         let ArrayInfo  = SystemInformation.AdvQuery.FindResults(Results,"UltimoCodiceFornitore");
                                         let CodiceUltimoFornitore = ''
                                         if(ArrayInfo != undefined)
                                           CodiceUltimoFornitore = ArrayInfo[0].ULTIMO_CODICE_FORNITORE
                                         OnSuccess(CodiceUltimoFornitore)
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                           SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },
                                       'UltimoCodiceFornitore');
  }   

  CaricaUltimoCodiceCliente(OnSuccess,MassimoCodice = false)
  {
     SystemInformation.AdvQuery.GetSQL("Clienti",{},
                                       function(Results)
                                       {
                                         let ArrayInfo  = SystemInformation.AdvQuery.FindResults(Results,"UltimoCodiceCliente");
                                         let CodiceUltimoCliente = '';
                                         if(ArrayInfo != undefined)
                                           CodiceUltimoCliente = ArrayInfo[0].ULTIMO_CODICE_CLIENTE
                                         OnSuccess(CodiceUltimoCliente)
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                           SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       },
                                       MassimoCodice ? 'CodiceClienteMassimo' : 'UltimoCodiceCliente');
  }

  GetLsPagamentoBollo()
  {
     return [
               {
                 Id           : PAGAMENTO_BOLLO.NessunBollo,
                 Descrizione  : 'Nessun bollo'
               },
               {
                 Id           : PAGAMENTO_BOLLO.PagatoDalCliente,
                 Descrizione  : 'Bollo pagato dal cliente'
               },
               {
                 Id           : PAGAMENTO_BOLLO.PagatoDaNoi,
                 Descrizione  : 'Bollo pagato da noi'
               }
            ]
  }

  GetLsStatoPreventivi()
  {
     return [
               {
                 Id           : STATO_PREVENTIVO.Confermato,
                 Descrizione  : 'Confermato'
               },
               {
                 Id           : STATO_PREVENTIVO.Rifiutato,
                 Descrizione  : 'Rifiutato'
               },
               {
                 Id           : STATO_PREVENTIVO.Sospeso,
                 Descrizione  : 'Sospeso'
               }
            ]
  }

  GetLsStatoDDT()
  {
     return [
               {
                 Id           : STATO_DDT.Concluso,
                 Descrizione  : 'Concluso'
               },
               {
                 Id           : STATO_DDT.Annullato,
                 Descrizione  : 'Annullato'
               },
               {
                 Id           : STATO_DDT.InCorso,
                 Descrizione  : 'In corso'
               }
            ]
  }


  GetLsProvaDinamica()
  {
    //Scheda Riepilogo attrezzature ne è scollegato
    //da collegare il giorno che verrano fatte altre modifiche
    return[
            {
              Id          : PROVA_DINAMICA.PrimaVisita,
              Descrizione : 'Prima visita'
            },
            {
              Id          : PROVA_DINAMICA.SecondaVisita,
              Descrizione : 'Seconda visita'
            },
          ]
  }

  GetDescrizioneProvaDinamica(Parametro)
  {
    let Risultato = this.GetLsProvaDinamica().find(Result => Result.Id == Parametro)
    return Risultato.Descrizione
  }

  GetLsEsigibilitaIVA()
  {
     return [
               {
                 Id          : FATT_ELE_ESIGIBILITA_IVA.Immediata,
                 Descrizione : 'Immediata'
               },
               {
                 Id          : FATT_ELE_ESIGIBILITA_IVA.Differita,
                 Descrizione : 'Differita'
               },
               {
                 Id          : FATT_ELE_ESIGIBILITA_IVA.Scissione,
                 Descrizione : 'Scissione dei pagamenti'
               }
            ]
  }

  GetLsContiCasse(AddDefault = true)
  { 
    var Result          = []
    if(AddDefault)
    {
      let OggettoDefault  = {}
      OggettoDefault.DESCRIZIONE = '-'
      OggettoDefault.CHIAVE      = -1
      Result.push(OggettoDefault)
    }
    
    for(let i = 0; i < this.Configurazioni.ContiCasse.length; i++)
    {
      let Oggetto = {}
      Oggetto.DESCRIZIONE = this.Configurazioni.ContiCasse[i].BANCA == ''? 
                            'Cassa - ' + this.Configurazioni.ContiCasse[i].DESCRIZIONE : 
                            'Conto corrente - ' + this.Configurazioni.ContiCasse[i].BANCA + ' - ' + this.Configurazioni.ContiCasse[i].NR_CONTO
      Oggetto.CHIAVE      = this.Configurazioni.ContiCasse[i].CHIAVE
      Result.push(Oggetto)
    }
    return Result
    
  }

  Configurazione = {}

  GetInitConfig(OnEndFunction)
  {
    var Self = this
    axios.get('GEMINIConfiguration.json').then(function(Answer)
    {
      Self.DeveloperMode  = Answer.data.DEVELOPER_MODE
      Self.AttesaPopupCaricamentoAlbero  = Answer.data.MILLISECONDI_PER_POPUP_CARICAMENTO_ALBERO
      Self.Configurazione = Answer.data
      Self.NoteInstallazioni = { Cliente : "UNKNOWN"}
      OnEndFunction(Answer.data.URL_SERVER);
    })
    .catch(function(Error)
    {
      console.log(Error)
      OnEndFunction("");
    })
  }

  GetUserInformation(OnSuccess,OnError)
  {
     var Self = this;
     this.AdvQuery.ExecuteExternalScript('GetPersonalizzazioni', {},function(Result)
     {
        Self.NoteInstallazioni = JSON.parse(Result.NoteInstallazione);
        Self.AccessRights      = new TAccessRights(Self.NoteInstallazioni);
        Self.AdvQuery.GetSQL("TutteLeConfigurazioni",{},
                             function(Results)
                             {
                               let ArrayInfo = Self.AdvQuery.FindResults(Results,"UserInformation");
                               if(ArrayInfo != undefined && ArrayInfo.length > 0)
                               {
                                  Self.UserInformation = {                                    
                                                            UserName              : ArrayInfo[0].USERNAME,
                                                            RagioneSociale        : ArrayInfo[0].RAGIONE_SOCIALE,
                                                            Ruolo                 : ArrayInfo[0].ROLE,
                                                            EMail                 : ArrayInfo[0].EMAIL,
                                                            Immagine              : ArrayInfo[0].IMMAGINE,
                                                            PrimoAccesso          : ArrayInfo[0].PRIMO_ACCESSO
                                                         };
                                  Self.GetConfigurazioni(OnSuccess)
                               }
                               else OnError("Impossibile ottenere le informazione dell'utente");
                             },
                             function(HTTPError,SubHTTPError)
                             {
                               OnError(HTTPError,SubHTTPError);
                             });
     },function(HTTPError,SubHTTPError,Response)
     {
       SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
     });
  }

  ChangeStileCaratteriInput()
  {
     switch(SchedaGenericaConfigurazione.CharCase)
     {
        case TSchedaGenericaCharCase.NonModificare  : document.documentElement.style.setProperty('--StileCaratteri', 'none');
                                                      break; 
        case TSchedaGenericaCharCase.TuttoMaiuscolo : document.documentElement.style.setProperty('--StileCaratteri', 'uppercase');
                                                      break; 
        case TSchedaGenericaCharCase.TuttoMinuscolo : document.documentElement.style.setProperty('--StileCaratteri', 'lowercase');
                                                      break; 

     }
  }

  GetConfigurazioni(OnSuccess)
  {
    var self = this;
    this.AdvQuery.GetSQL("TutteLeConfigurazioni",{},
                         function(Results)
                         {
                           let ArrayInfo = self.AdvQuery.FindResults(Results,"Impostazioni");
                           if(ArrayInfo != undefined && ArrayInfo.length > 0)
                           {
                              self.Configurazioni = {
                                                        Impostazioni                            : self.AdvQuery.FindResults(Results,"Impostazioni")[0],
                                                        Province                                : self.AdvQuery.FindResults(Results,"Province"),
                                                        Regioni                                 : self.AdvQuery.FindResults(Results,"Regioni"),
                                                        Nazioni                                 : self.AdvQuery.FindResults(Results,"Nazioni"),
                                                        ModalitaPagamenti                       : self.AdvQuery.FindResults(Results,"ModalitaPagamenti"),  
                                                        CondPagamenti                           : self.AdvQuery.FindResults(Results,"CondPagamenti"),
                                                        Causali                                 : self.AdvQuery.FindResults(Results,"Causali"),
                                                        ScissionePagamenti                      : self.AdvQuery.FindResults(Results,"ScissionePagamenti"),
                                                        CatMovimenti                            : self.AdvQuery.FindResults(Results,"CategoriaMovimenti"),
                                                        CatClienti                              : self.AdvQuery.FindResults(Results,"CategorieClienti"),
                                                        Fornitori                               : self.AdvQuery.FindResults(Results,'Fornitori'),
                                                        Clienti                                 : self.AdvQuery.FindResults(Results,'Clienti'),
                                                        ClientiNonAttivi                        : self.AdvQuery.FindResults(Results,'ClientiNonAttivi'),
                                                        UnitaDiMisura                           : self.AdvQuery.FindResults(Results,'UnitaDiMisura'),
                                                        EmailSegnalazioni                       : self.AdvQuery.FindResults(Results,'EmailSegnalazioni'),
                                                        Titoli                                  : self.AdvQuery.FindResults(Results,'Titoli'),
                                                        Testi                                   : self.AdvQuery.FindResults(Results,"Testi")[0],
                                                        Zone                                    : self.AdvQuery.FindResults(Results,'Zone'),
                                                        Settori                                 : self.AdvQuery.FindResults(Results,'Settori'),
                                                        Magazzini                               : self.AdvQuery.FindResults(Results,'Magazzini'),
                                                        Prodotti                                : self.AdvQuery.FindResults(Results,'Prodotti'),
                                                        VociPreventiviPredefinite               : self.AdvQuery.FindResults(Results, 'VociPreventiviPredefinite'),
                                                        ContiCasse                              : self.AdvQuery.FindResults(Results,'ContiCasse'),
                                                    }

                              self.Configurazioni.StatoItaliano = self.Configurazioni.Nazioni.find(function(Nazione)
                              {
                                if(Nazione.SIGLA == 'IT')
                                  return Nazione.CHIAVE
                              });
                              SchedaGenericaConfigurazione.CharCase = self.Configurazioni.Impostazioni.STILE_STRINGHE
                              self.ChangeStileCaratteriInput();

                              if(self.Configurazioni.StatoItaliano != undefined)
                                 self.Configurazioni.StatoItaliano = self.Configurazioni.StatoItaliano.CHIAVE;
                              else self.Configurazioni.StatoItaliano = -1;
                              OnSuccess()
                           }
                           else  SystemInformation.HandleError("Impossibile ottenere le Configurazioni");
                         },
                         function(HTTPError,SubHTTPError)
                         {
                          SystemInformation.HandleError(HTTPError,SubHTTPError);
                         },
                         'SelectConfigurazioni');
  }

  Init(OnEndFunction)
  {
    var self = this;
    var OnNoLogin   = function()
    {
       var ElementiUrl = window.location.href.split('#')
       if(ElementiUrl.length > 1)
       {
         ElementiUrl = ElementiUrl[1].split('/')
         if(ElementiUrl[1].toLowerCase() == 'appmainwindow')
            window.location.assign(self.Configurazione.URL_WEBAPP)
       }   
        OnEndFunction()
    }

    this.GetInitConfig(function(UrlServer)
    {
      self.AdvQuery.UrlServer = UrlServer;
      let Token       = localStorage.getItem(LOCALSTORAGE.TokenRememberMe);
      let PartitaIVA  = localStorage.getItem(LOCALSTORAGE.TokenPartitaIVA);

      if(Token != null && PartitaIVA != null)
      {
         self.AdvQuery.LoginWithToken(Token,
                                      function()
                                      {
                                         SystemInformation.GetUserInformation(function()
                                         {
                                           OnEndFunction();
                                         },
                                         function(HTTPError,SubHTTPError)
                                         {
                                           SystemInformation.HandleError(HTTPError,SubHTTPError);
                                           OnEndFunction();
                                         });
                                         self.AdvQuery.LastToken = Token
                                         self.AdvQuery.LastPIVA  = PartitaIVA
                                      },
                                      function()
                                      {
                                         OnNoLogin()
                                      },
                                      PartitaIVA);
      }
      else OnNoLogin();

    });
  }

  constructor()
  {
    this.AdvQuery = new TAdvQuery('');
    this.UserInformation = {};
    this.Configurazioni = {};
  }

  HandleError(Riga1,Riga2,Response)
  {
    if(Response == TAdvQueryErrors.HTTP_ERROR_NOT_LOGGED)
        this.Router.push("/login");
     else alert(Riga1 + "\n" + Riga2);
  }

  GetTotaliNotaDiCreditoFromServer(ChiaviNota, OnSuccess)
  {
    let Parametri = {}
    Parametri.ChiaviNota = ChiaviNota
    let Self = this
    this.AdvQuery.ExecuteExternalScript('GetTotaliNota', 
                                        Parametri, 
                                        function(Result)
                                        {  
                                          if(Result != undefined)
                                          {
                                            if(OnSuccess != undefined)
                                              OnSuccess(Result.TotaliNota);
                                          }
                                          else Self.HandleError('Errore nel calcolo dei totali','','');
                                        },
                                        this.HandleError) 
  }

  FormattaImporto(Importo)
  {
    return TZEconomicFunct.EuropeanCurrencyFormat(Importo);
  }

  ResettaPasswordUtente(Chiave, OnSuccess)
  {
    var ObjQuery = { Operazioni : [] };
    
    ObjQuery.Operazioni.push({
                              Query     : "ResettaPassword",
                              Parametri : { CHIAVE : Chiave }
                            })

    SystemInformation.AdvQuery.PostSQL('Admin_Super_GestioneUtenti', 
                                       ObjQuery, 
                                       function()
                                       {
                                          if(OnSuccess != undefined)
                                            OnSuccess()
                                       }, 
                                       SystemInformation.HandleError)
  }
  
  GoToFirstPage() 
  {
    this.Router.push('/appMainWindow/Dashboard');
  }

  GetProdottiSemplici()
  {
    let Result = []
    let LSTuttiProdotti = []
    LSTuttiProdotti = this.Configurazioni.Prodotti

    for(let i = 0; i < LSTuttiProdotti.length; i++)
    {
      if(LSTuttiProdotti[i].PRODOTTO_COMPOSTO == 'F')
        Result.push(LSTuttiProdotti[i])
    }
    return Result
  }

  GetProdottiCompostiXMagazzino(IdMagazzino)
  {
    var ListaProdottiComposti = []
    var TuttiProdotti = this.Configurazioni.Prodotti

    for(var i = 0; i < TuttiProdotti.length; i++)
    {
        var ProdottoCorrente = TuttiProdotti[i]

        if(ProdottoCorrente.PRODOTTO_COMPOSTO != 'F')
        {
            if(ProdottoCorrente.ID_MAGAZZINO != undefined && ProdottoCorrente.ID_MAGAZZINO == IdMagazzino)
                ListaProdottiComposti.push(ProdottoCorrente)
        }
    }

    return ListaProdottiComposti
  }

  // GetRagioneSocialeCliente(IdCliente)
  // {
  //   for(let i = 0; i < this.Configurazioni.Clienti.length; i++)
  //     if(this.Configurazioni.Clienti[i].CHIAVE == IdCliente)
  //       return this.Configurazioni.Clienti[i].RAGIONE_SOCIALE
  //   return 'cliente non trovato'
  // }

  // GetRagioneSocialeFornitore(IdFornitore)
  // {
  //   for(let i = 0; i < this.Configurazioni.Fornitori.length; i++)
  //     if(this.Configurazioni.Fornitori[i].CHIAVE == IdFornitore)
  //       return this.Configurazioni.Fornitori[i].RAGIONE_SOCIALE
  //   return 'fornitore non trovato'
  // }

  GetRagioneSociale(IdAnagrafica)
  {
    for(let i = 0; i < this.Configurazioni.Clienti.length; i++)
      if(this.Configurazioni.Clienti[i].CHIAVE == IdAnagrafica)
        return this.Configurazioni.Clienti[i].RAGIONE_SOCIALE

    for(let i = 0; i < this.Configurazioni.Fornitori.length; i++)
      if(this.Configurazioni.Fornitori[i].CHIAVE == IdAnagrafica)
        return this.Configurazioni.Fornitori[i].RAGIONE_SOCIALE
    return 'Anagrafica non trovata'
  }

  GetTargaProvincia(ChiaveProvincia)
  {
    let Result = ''
    for(let i = 0; i < this.Configurazioni.Province.length; i++)
      if(this.Configurazioni.Province[i].CHIAVE == ChiaveProvincia)
        Result = this.Configurazioni.Province[i].TARGA
    return Result
  }

  GetChiaveProvincia(Targa)
  {
    let Result = ''
    for(let i = 0; i < this.Configurazioni.Province.length; i++)
      if(this.Configurazioni.Province[i].TARGA == Targa)
        Result = this.Configurazioni.Province[i].CHIAVE
    return Result
  }

  GetDescrizioneProvincia(ChiaveProvincia)
  {
    let Result = 'Non presente'
    for(let i = 0; i < this.Configurazioni.Province.length; i++)
      if(this.Configurazioni.Province[i].CHIAVE == ChiaveProvincia)
        Result = this.Configurazioni.Province[i].NOME
    return Result
  }

  GetDescrizioneZona(ChiaveZona)
  {
    let Result = 'Non presente'
    for(let i = 0; i < this.Configurazioni.Zone.length; i++)
      if(this.Configurazioni.Zone[i].CHIAVE == ChiaveZona)
        Result = this.Configurazioni.Zone[i].DESCRIZIONE
    return Result
  }

  GetIfZonaIsZTL(ChiaveZona)
  {
    let Result = false
    for(let i = 0; i < this.Configurazioni.Zone.length; i++)
      if(this.Configurazioni.Zone[i].CHIAVE == ChiaveZona)
        Result = this.Configurazioni.Zone[i].IS_ZTL == 'T'
    return Result
  }

  GetDescrizioneTipiEstintori(Chiave)
  {
    for(let i = 0; i < this.Configurazioni.TipiEstintori.length; i++)
      if(this.Configurazioni.TipiEstintori[i].CHIAVE == Chiave)
        return this.Configurazioni.TipiEstintori[i].DESCRIZIONE
    return '' 
  }


  GetDescrizioneMagazzino(Chiave)
  {
    let Result = ''

    const MagazzinoTrovato = this.Configurazioni.Magazzini.find( Magazzino => Magazzino.CHIAVE == Chiave );

    if (MagazzinoTrovato) 
      Result = MagazzinoTrovato.DESCRIZIONE;

    return Result 
  }
}

export var SystemInformation = new TSystemInformation();