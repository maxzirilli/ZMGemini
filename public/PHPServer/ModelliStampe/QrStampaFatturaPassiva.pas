unit QrStampaFatturaPassiva;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls,ZDateFunct,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery,
     ZStringConvFunct,ZFatturaElettronica,ZFatturaElettronicaImportaDaFTP;

type
  TQR_STAMPA_FATTURA_PASSIVA = class(TQuickRep)
    LB_QUANTITA: TQRLabel;
    LB_IVA: TQRLabel;
    LB_PREZZO: TQRLabel;
    LB_TOTALE_RIGA: TQRLabel;
    HEADER_BAND_VOCI: TQRBand;
    QRLabel10: TQRLabel;
    QRLabel14: TQRLabel;
    QRLabel15: TQRLabel;
    QryFatture: TpFIBDataSet;
    QryVociFattura: TpFIBDataSet;
    QryGeneric: TpFIBQuery;
    DESCRIZIONE: TQRLabel;
    QRLabel5: TQRLabel;
    LB_SCONTO3: TQRLabel;
    FOOTER_BAND_VOCI: TQRBand;
    QRShape2: TQRShape;
    LB_SCONTO1: TQRLabel;
    LB_SCONTO2: TQRLabel;
    QRLabel21: TQRLabel;
    QRLabel24: TQRLabel;
    QRLabel25: TQRLabel;
    BENE_SERVIZIO: TQRMemo;
    QryCassePrevidenziali: TpFIBDataSet;
    QryRitenute: TpFIBDataSet;
    BAND_INTESTAZIONE: TQRBand;
    INTESTAZIONE_SOCIETA: TQRMemo;
    DENOMINAZIONE_SOCIETA: TQRLabel;
    QRShape1: TQRShape;
    QRLabel2: TQRLabel;
    QRShape42: TQRShape;
    QRShape49: TQRShape;
    QRLabel30: TQRLabel;
    LB_NUM_DOCUMENTO: TQRLabel;
    QRLabel34: TQRLabel;
    LB_DATA_DOCUMENTO: TQRLabel;
    QRLabel38: TQRLabel;
    QRSysData1: TQRSysData;
    LB_IBAN: TQRLabel;
    LB_OGGETTO: TQRLabel;
    LB_TIPO_DOCUMENTO: TQRLabel;
    QRLabel8: TQRLabel;
    QRLabel11: TQRLabel;
    QRLabel12: TQRLabel;
    INTESTATARIO: TQRMemo;
    QRLabel17: TQRLabel;
    BAND_SUMMARY: TQRBand;
    QRShape10: TQRShape;
    QRShape14: TQRShape;
    QRShape3: TQRShape;
    QRShape4: TQRShape;
    QRShape44: TQRShape;
    LB_IMPONIBILE1: TQRLabel;
    LB_IMPOSTA1: TQRLabel;
    LB_IVA1: TQRLabel;
    LB_IMPONIBILE2: TQRLabel;
    LB_IVA2: TQRLabel;
    LB_IMPOSTA2: TQRLabel;
    LB_IMPONIBILE3: TQRLabel;
    LB_IVA3: TQRLabel;
    LB_IMPOSTA3: TQRLabel;
    QRShape45: TQRShape;
    QRLabel23: TQRLabel;
    QRLabel26: TQRLabel;
    QRLabel27: TQRLabel;
    QRShape47: TQRShape;
    QRShape48: TQRShape;
    QRLabel6: TQRLabel;
    RATA_IMPONIBILE1: TQRLabel;
    RATA_DATA1: TQRLabel;
    RATA_IMPONIBILE2: TQRLabel;
    RATA_DATA2: TQRLabel;
    RATA_IMPONIBILE3: TQRLabel;
    RATA_DATA3: TQRLabel;
    QRLabel28: TQRLabel;
    QRShape5: TQRShape;
    QRLabel48: TQRLabel;
    LB_TOTALE: TQRLabel;
    QRLabel35: TQRLabel;
    LB_RIT_ACCONTO: TQRLabel;
    QRLabel39: TQRLabel;
    DESTINATARIO: TQRMemo;
    HEADER_CASSE_PREVIDENZIALI: TQRBand;
    QRShape8: TQRShape;
    QRLabel37: TQRLabel;
    QRLabel41: TQRLabel;
    QRLabel46: TQRLabel;
    QRLabel13: TQRLabel;
    QRBand1: TQRBand;
    HEADER_BAND_RITENUTE: TQRBand;
    QRShape9: TQRShape;
    QRLabel19: TQRLabel;
    QRLabel31: TQRLabel;
    QRLabel32: TQRLabel;
    BAND_CASSE_PREVIDENZIALI: TQRSubDetail;
    CASSA_IVA: TQRLabel;
    CASSA_ALIQUOTA: TQRLabel;
    CASSA_DESCRIZIONE: TQRMemo;
    CASSA_IMPORTO: TQRLabel;
    BAND_RITENUTE: TQRSubDetail;
    RITENUTA_ALIQUOTA: TQRLabel;
    RITENUTA_IMPORTO: TQRLabel;
    RITENUTA_TIPO: TQRLabel;
    Codice: TQRLabel;
    LB_CODICE: TQRMemo;
    QRLabel1: TQRLabel;
    procedure QuickRepPreview(Sender: TObject);
    procedure QuickRepBeforePrint(Sender: TCustomQuickRep;
      var PrintReport: Boolean);
    procedure QuickRepAfterPrint(Sender: TObject);
    procedure BAND_INTESTAZIONEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure LB_NUM_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure LB_DATA_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure BAND_CAUSALEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure FOOTER_BAND_VOCIAfterPrint(Sender: TQRCustomBand;
      BandPrinted: Boolean);
    procedure BAND_SUMMARYBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_VOCIBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_CASSE_PREVIDENZIALIBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_RITENUTEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure LB_RIT_ACCONTOPrint(sender: TObject; var Value: string);
  private
   FChiaveFattura         : Integer;
   FTotaleRitenute        : Integer;
   FORM_PREVIEW           : TFORM_PREVIEW;
   FSummaryAlreadyPrinted : Boolean;
  public
   Procedure Init(Connection : TpFIBQuery; ChiaveFattura : Integer);
  end;

implementation

Type TSingolaVoce = class(TObject)
                      IVA        : Integer;
                      Imponibile : Double;
                    end;

{$R *.DFM}
Procedure TQR_STAMPA_FATTURA_PASSIVA.Init(Connection : TpFIBQuery; ChiaveFattura : Integer);
begin
  FChiaveFattura         := ChiaveFattura;

  QryFatture.Database    := Connection.Database;
  QryFatture.Transaction := Connection.Transaction;
  CopyArrayInTStringList(QryFatture.SelectSQL,['SELECT * FROM FATTURE_PASSIVE',
                                               ' WHERE CHIAVE = ' + IntToStr(ChiaveFattura)]);
  QryVociFattura.Database    := Connection.Database;
  QryVociFattura.Transaction := Connection.Transaction;
  QryVociFattura.SQLs.SelectSQL.Clear;
  CopyArrayInTStringList(QryVociFattura.SelectSQL,['SELECT * FROM VOCI_FATTURE_PASSIVE',
                                                   'WHERE FATTURA = ' + IntToStr(ChiaveFattura),
                                                   'ORDER BY NUMERO_LINEA']);

  QryCassePrevidenziali.Database    := Connection.Database;
  QryCassePrevidenziali.Transaction := Connection.Transaction;
  CopyArrayInTStringList(QryCassePrevidenziali.SelectSQL,['SELECT * FROM CASSE_PREVIDENZIALI_FATT_PASS WHERE FATTURA = ' + IntToStr(ChiaveFattura)]);

  QryRitenute.Database    := Connection.Database;
  QryRitenute.Transaction := Connection.Transaction;
  CopyArrayInTStringList(QryRitenute.SelectSQL,['SELECT * FROM RITENUTE_FATTURE_PASSIVE WHERE FATTURA = ' + IntToStr(ChiaveFattura)]);

  QryGeneric.Database    := Connection.Database;
  QryGeneric.Transaction := Connection.Transaction;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.LB_DATA_DOCUMENTOPrint(
  sender: TObject; var Value: string);
begin
  Value := FormatDateTime('dd/mm/yyyy',QryFatture.FieldByName('DATA').AsDateTime);
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.LB_NUM_DOCUMENTOPrint(sender: TObject;
  var Value: string);
begin
 Value := QryFatture.FieldByName('NUMERO').AsString;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.LB_RIT_ACCONTOPrint(sender: TObject;
  var Value: string);
begin
  Value := FormatFloat('0.00',CentsToReal(FTotaleRitenute));
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.QuickRepBeforePrint(Sender: TCustomQuickRep;
  var PrintReport: Boolean);
begin
 FSummaryAlreadyPrinted := False;
 FTotaleRitenute := 0;
 QryFatture.Open;
 QryVociFattura.Open;
 QryCassePrevidenziali.Open;
 QryRitenute.Open;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.BAND_VOCIBeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
begin
 DESCRIZIONE.Lines.Text := QryVociFattura.FieldByName('MEMO_DESCRIZIONE').AsString;
 if (QryVociFattura.FieldByName('QUANTITA').AsInteger = 0) and
    (QryVociFattura.FieldByName('PREZZO_UNITARIO').AsInteger = 0) then
    begin
      LB_DDT.Caption            := ' ';
      LB_IVA.Caption            := ' ';
      LB_PREZZO.Caption         := ' ';
      LB_QUANTITA.Caption       := ' ';
      LB_SCONTO1.Caption        := ' ';
      LB_SCONTO2.Caption        := ' ';
      LB_SCONTO3.Caption        := ' ';
      LB_TOTALE_RIGA.Caption    := ' ';
      LB_DATA_DDT.Caption       := ' ';
      DESCRIZIONE.Font.Style    := DESCRIZIONE.Font.Style + [fsBold];
    end
 else
    begin
      DESCRIZIONE.Font.Style := DESCRIZIONE.Font.Style - [fsBold];
      if IsValidName(QryVociFattura.FieldByName('DDT').AsString) then
         LB_DDT.Caption := QryVociFattura.FieldByName('DDT').AsString
      else LB_DDT.Caption := '-';
      LB_IVA.Caption := IntToStr(QryVociFattura.FieldByName('IVA').AsInteger);
      LB_PREZZO.Caption := FormatFloat('0.000',MillesimiToReal(QryVociFattura.FieldByName('PREZZO_UNITARIO').AsInteger));
      if QryVociFattura.FieldByName('QUANTITA').AsInteger <> 0 then
         LB_QUANTITA.Caption := FormatFloat('0.00',CentsToReal(QryVociFattura.FieldByName('QUANTITA').AsInteger))
      else LB_QUANTITA.Caption := '1.00';
      if QryVociFattura.FieldByName('SCONTO1').AsInteger = 0 then LB_SCONTO1.Caption := '-'
      else LB_SCONTO1.Caption := FormatFloat('0.##',CentsToReal(QryVociFattura.FieldByName('SCONTO1').AsInteger)) + '%';
      if QryVociFattura.FieldByName('SCONTO2').AsInteger = 0 then LB_SCONTO2.Caption := '-'
      else LB_SCONTO2.Caption := FormatFloat('0.##',CentsToReal(QryVociFattura.FieldByName('SCONTO2').AsInteger)) + '%';
      if QryVociFattura.FieldByName('SCONTO3').AsInteger = 0 then LB_SCONTO3.Caption := '-'
      else LB_SCONTO3.Caption := FormatFloat('0.##',CentsToReal(QryVociFattura.FieldByName('SCONTO3').AsInteger)) + '%';
      LB_TOTALE_RIGA.Caption := FormatFloat('0.000',MillesimiToReal(QryVociFattura.FieldByName('PREZZO_TOTALE').AsInteger));
      if QryVociFattura.FieldByName('DATA_DDT').AsDateTime = 0.0 then
         LB_DATA_DDT.Caption := '-'
      else LB_DATA_DDT.Caption := FormatDateTime('dd/mm/yyyy',QryVociFattura.FieldByName('DATA_DDT').AsDateTime);
    end;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.QuickRepAfterPrint(Sender: TObject);
begin
 QryFatture.Close;
 QryVociFattura.Close;
 QryCassePrevidenziali.Close;
 QryRitenute.Close;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.BAND_CASSE_PREVIDENZIALIBeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
begin
 CASSA_DESCRIZIONE.Lines.Text := DESCR_CASSE_PREVIDENZIALI[TFatturaTipoCassaPrevidenziale(QryCassePrevidenziali.FieldByName('TIPO').AsInteger)];
 CASSA_ALIQUOTA.Caption       := QryCassePrevidenziali.FieldByName('ALIQUOTA').AsString + '%';
 CASSA_IMPORTO.Caption        := FormatFloat('0.00',CentsToReal(QryCassePrevidenziali.FieldByName('IMPORTO').AsInteger));
 CASSA_IVA.Caption            := QryCassePrevidenziali.FieldByName('IVA').AsString + '%';
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.BAND_CAUSALEBeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
begin
 PrintBand := IsValidName(QryFatture.FieldByName('OGGETTO').AsString);
 if PrintBand then
    CAUSALE_FATTURA.Lines.Text := QryFatture.FieldByName('OGGETTO').AsString;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.BAND_INTESTAZIONEBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
begin
 CopyArrayInTStringList(INTESTATARIO.Lines,[QryFatture.FieldByName('TITOLO').AsString + ' ' +
                                              QryFatture.FieldByName('RAGIONE_SOCIALE').AsString,
                                            QryFatture.FieldByName('INDIRIZZO_FATTURAZIONE').AsString,
                                            Trim(QryFatture.FieldByName('CAP_FATTURAZIONE').AsString) + ' ' +
                                                 QryFatture.FieldByName('COMUNE_FATTURAZIONE').AsString + ' (' +
                                                 QryFatture.FieldByName('PROV_FATTURAZIONE').AsString + ')',
                                            'P.IVA: ' + QryFatture.FieldByName('PARTITA_IVA').AsString + ' ' +
                                            'C.F.: ' + QryFatture.FieldByName('CODICE_FISCALE').AsString]);
 DESTINATARIO.Lines.Assign(SystemInformation.IntestazioneSocieta);
 DESTINATARIO.Lines.Insert(0,SystemInformation.DenominazioneSocieta);
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.BAND_RITENUTEBeforePrint(Sender: TQRCustomBand; var PrintBand: Boolean);
begin
  RITENUTA_TIPO.Caption      := FATT_DESCRIZIONI_TIPO_RITENUTA[TFatturaTipoRitenuta(QryRitenute.FieldByName('TIPO').AsInteger)];
  RITENUTA_ALIQUOTA.Caption  := FormatFloat('0.00',CentsToReal(QryRitenute.FieldByName('ALIQUOTA').AsInteger)) + '%';
  RITENUTA_IMPORTO.Caption   := FormatFloat('0.00',CentsToReal(QryRitenute.FieldByName('IMPORTO').AsInteger));
  FTotaleRitenute            := FTotaleRitenute + QryRitenute.FieldByName('IMPORTO').AsInteger;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.BAND_SUMMARYBeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
var i          : Integer;
    TotImposta : Double;
    TotImpon   : Double;
    TotArrot   : Double;
begin
  PrintBand := FSummaryAlreadyPrinted;
  if PrintBand then
     With QryGeneric do
          begin
            CopyArrayInTStringList(SQL,['SELECT * FROM RATE_FATTURE_PASSIVE WHERE FATTURA = ' + IntToStr(FChiaveFattura)]);
            try
              ExecQuery;
              for i := 1 to 3 do
                  begin
                   if Eof then
                      begin
                       TQRLabel(Self.FindComponent('RATA_IMPONIBILE' + IntToStr(i))).Caption := ' ';
                       TQRLabel(Self.FindComponent('RATA_DATA' + IntToStr(i))).Caption       := ' ';
                      end
                   else
                      begin
                       TQRLabel(Self.FindComponent('RATA_IMPONIBILE' + IntToStr(i))).Caption := FormatFloat('0.00',CentsToReal(FieldByName('IMPORTO').AsInteger));
                       TQRLabel(Self.FindComponent('RATA_DATA' + IntToStr(i))).Caption       := FormatDateTime('dd/mm/yyyy',FieldByName('DATA_SCADENZA').AsDate);
                       Next;
                      end
                  end;
            finally
              Close;
            end;
            CopyArrayInTStringList(SQL,['SELECT * FROM RIEPILOGHI_FATTURE WHERE FATTURA = ' + IntToStr(FChiaveFattura)]);
            TotImposta := 0; TotImpon := 0; TotArrot := 0;
            try
              ExecQuery;
              for i := 1 to 3 do
                  begin
                   if Eof then
                      begin
                       TQRLabel(Self.FindComponent('LB_IMPONIBILE' + IntToStr(i))).Caption     := ' ';
                       TQRLabel(Self.FindComponent('LB_IVA' + IntToStr(i))).Caption            := ' ';
                       TQRLabel(Self.FindComponent('LB_ARROTONDAMENTO' + IntToStr(i))).Caption := ' ';
                       TQRLabel(Self.FindComponent('LB_IMPOSTA' + IntToStr(i))).Caption        := ' ';
                      end
                   else
                      begin
                       TQRLabel(Self.FindComponent('LB_IMPONIBILE' + IntToStr(i))).Caption     := FormatFloat('0.00',CentsToReal(FieldByName('IMPONIBILE').AsInteger));
                       TQRLabel(Self.FindComponent('LB_IVA' + IntToStr(i))).Caption            := FieldByName('IVA').AsString;
                       TQRLabel(Self.FindComponent('LB_ARROTONDAMENTO' + IntToStr(i))).Caption := FormatFloat('0.00',CentsToReal(FieldByName('ARROTONDAMENTO').AsInteger));
                       TQRLabel(Self.FindComponent('LB_IMPOSTA' + IntToStr(i))).Caption        := FormatFloat('0.00',CentsToReal(FieldByName('IMPOSTA').AsInteger));
                       TotImposta := TotImposta + CentsToReal(FieldByName('IMPOSTA').AsInteger);
                       TotImpon := TotImpon + CentsToReal(FieldByName('IMPONIBILE').AsInteger);
                       TotArrot := TotArrot + CentsToReal(FieldByName('ARROTONDAMENTO').AsInteger);
                       Next;
                      end
                  end;
            finally
              Close;
            end;
            LB_TOT_IMPONIBILE.Caption     := FormatFloat('0.00',TotImpon - TotArrot);
            LB_TOT_IMPOSTA.Caption        := FormatFloat('0.00',TotImposta);
            LB_TOTALE.Caption             := FormatFloat('0.00',CentsToReal(QryFatture.FieldByName('TOTALE_FATTURA').AsInteger));
            LB_NETTO_A_PAGARE.Caption     := FormatFloat('0.00',CentsToReal(QryFatture.FieldByName('TOTALE_FATTURA').AsInteger -
                                                                            FTotaleRitenute));
          end;
end;

procedure TQR_STAMPA_FATTURA_PASSIVA.FOOTER_BAND_VOCIAfterPrint(Sender: TQRCustomBand; BandPrinted: Boolean);
begin
 FSummaryAlreadyPrinted := True;
end;

end.
