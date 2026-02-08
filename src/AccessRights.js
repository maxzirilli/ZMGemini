export const CLIENTI = {
                          ZMSoftware          : 'ZMSOFTWARE',
                          Generico            : 'GENERICO',
                       }  

export class TAccessRights
{
  constructor(NoteInstallazioni)
  {
    this.NoteInstallazioni = NoteInstallazioni
  }

  ExcelRiassuntoClientiVisibile()
  {
      return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }


  VisibilitaAbilitazionePatentini()
  {
    return (this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware)
  }

  VisibilitaSituazioneContabileCliente()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaListinoPrezziCliente()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaColonnaSaldiPrimaNota()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaTecnicoSorveglianza()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaTecnicoZoneAssegnate()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaLogModifiche()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaDettaglioRitenute()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaLogVariazioni()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaAllegatiCliente()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaGestoreMailSolleciti()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaNumeroAnticipo()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaCampanellaLog()
  {
    return (this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware)
  }

  VisibilitaCategorieClienti()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaLogAzioniUtenti()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }


  VisualizzaEsitoSorveglianza()
  {
    return (this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware)
  }

  VisibilitaNotaDiDebito()
  {
    return this.NoteInstallazioni.Cliente == CLIENTI.ZMSoftware
  }

  VisibilitaOpzioniPortoDDT()
  {
    return (this.NoteInstallazioni.Cliente == CLIENTI.GeneralSecurityFire)
  }

}