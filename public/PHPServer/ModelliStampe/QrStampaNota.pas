unit QrStampaNota;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,ZStringConvFunct,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery;

type
  TQR_STAMPA_NOTA_DI_CREDITO = class(TQuickRep)
    BAND_INTESTAZIONE: TQRBand;
    DENOMINAZIONE_SOCIETA: TQRLabel;
    INTESTAZIONE_SOCIETA: TQRMemo;
    LB_UNITA: TQRLabel;
    LB_QUANTITA: TQRLabel;
    BAND_SUMMARY: TQRBand;
    LB_IVA: TQRLabel;
    LB_PREZZO: TQRLabel;
    LB_IMPORTO: TQRLabel;
    HEADER_BAND_PRODOTTI: TQRBand;
    QRLabel9: TQRLabel;
    QRLabel10: TQRLabel;
    QRLabel14: TQRLabel;
    QRLabel15: TQRLabel;
    QRLabel16: TQRLabel;
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
    QryNote: TpFIBDataSet;
    QryVociNote: TpFIBDataSet;
    QR_LOGO: TQRImage;
    QRShape1: TQRShape;
    QRLabel2: TQRLabel;
    INTESTATARIO: TQRMemo;
    QRShape42: TQRShape;
    QRLabel22: TQRLabel;
    DESTINATARIO: TQRMemo;
    QRShape49: TQRShape;
    QRLabel33: TQRLabel;
    QRLabel34: TQRLabel;
    LB_CONDIZIONI_PAGAMENTO: TQRLabel;
    LB_DATA_DOCUMENTO: TQRLabel;
    QRLabel37: TQRLabel;
    QRLabel38: TQRLabel;
    LB_BANCA: TQRLabel;
    QRSysData1: TQRSysData;
    QryGeneric: TpFIBQuery;
    QRLabel4: TQRLabel;
    QRLabel42: TQRLabel;
    LB_TOT_IMPONIBILE: TQRLabel;
    QRShape11: TQRShape;
    QRLabel44: TQRLabel;
    LB_TOT_IMPOSTA: TQRLabel;
    QRShape13: TQRShape;
    QRShape14: TQRShape;
    QRLabel48: TQRLabel;
    LB_TOTALE: TQRLabel;
    QRShape9: TQRShape;
    QRShape10: TQRShape;
    QRLabel8: TQRLabel;
    LB_IMPONIBILE_RIT_ACCONTO: TQRLabel;
    QRLabel12: TQRLabel;
    LB_PERC_RIT_ACCONTO: TQRLabel;
    QRShape15: TQRShape;
    QRLabel18: TQRLabel;
    LB_RIT_ACCONTO: TQRLabel;
    QRShape16: TQRShape;
    QRLabel20: TQRLabel;
    LB_NETTO_A_PAGARE: TQRLabel;
    FOOTER_PRODOTTI: TQRBand;
    QRShape2: TQRShape;
    DESCRIZIONE: TQRMemo;
    BAND_NOTE: TQRChildBand;
    QRLabel3: TQRLabel;
    QRLabel1: TQRLabel;
    QRLabel30: TQRLabel;
    LB_TIPO_DOCUMENTO: TQRLabel;
    LB_NUM_DOCUMENTO: TQRLabel;
    LB_SCONTO: TQRLabel;
    NOTE: TQRLabel;
    QRLabel7: TQRLabel;
    QRLabel5: TQRLabel;
    QRLabel6: TQRLabel;
    QRLabel11: TQRLabel;
    LB_CAUSALE: TQRLabel;
    qrlabel: TQRLabel;
    QRLabel24: TQRLabel;
    LB_NS_IBAN: TQRLabel;
    procedure QuickRepPreview(Sender: TObject);
    procedure QuickRepBeforePrint(Sender: TCustomQuickRep;
      var PrintReport: Boolean);
    procedure QuickRepAfterPrint(Sender: TObject);
    procedure DENOMINAZIONE_SOCIETAPrint(sender: TObject;
      var Value: String);
    procedure BAND_INTESTAZIONEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure QRSubDetail1BeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    Constructor Create(AOwner : TComponent); OverRide;
    Destructor Destroy; OverRide;
    procedure LB_DATA_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure LB_CONDIZIONI_PAGAMENTOPrint(sender: TObject; var Value: string);
    procedure LB_BANCAPrint(sender: TObject; var Value: string);
    procedure LB_NS_IBANPrint(sender: TObject; var Value: string);
    procedure BAND_SUMMARYBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_NOTEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure LB_NUM_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure LB_TIPO_DOCUMENTOPrint(sender: TObject; var Value: string);
  private
   FHandleImage           : THandleImage;
   FSummaryAlreadyPrinted : Boolean;
   FChiaveNota            : Integer;
   FORM_PREVIEW           : TFORM_PREVIEW;
   FListaVoci             : TObjectList;
  public
   Procedure Init(Connection : TpFIBQuery; ChiaveNota : Integer);
  end;

implementation

Type TSingolaVoce = class(TObject)
                      IVA        : Integer;
                      Ivato      : Double;
                      Imponibile : Double;
                    end;

{$R *.DFM}
procedure TQR_STAMPA_NOTA_DI_CREDITO.BAND_NOTEBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
begin
  PrintBand := IsValidName(QryNote.FieldByName('NOTE').AsString);
  if PrintBand then
     NOTE.Lines.Text := QryNote.FieldByName('NOTE').AsString;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.BAND_SUMMARYBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
var i              : Integer;
    TotaleNota     : Double;
    TotaleImposte  : Double;
    TotaleImponib  : Double;
    Ritenuta       : Double;
    IVE            : Array[1..3] of record
                                      IVA          : Integer;
                                      Imponibile   : Double;
                                      Imposta      : Double;
                                    end;
    AVoce          : TSingolaVoce;
    NdxIva         : Integer;
    LabelIVA       : Array[1..3] of TQrLabel;
    LabelImpIVA    : Array[1..3] of TQrLabel;
    LabelImpostaIVA: Array[1..3] of TQrLabel;

Function FindIVA (IVA : Integer) : Integer;
var i : Integer;
begin
  Result := -1;
  for i := Low(IVE) to High(IVE) do
      begin
         if IVE[i].IVA = IVA then
            begin Result := i; Break; end;
         if IVE[i].IVA = 0 then
            begin IVE[i].IVA := IVA; Result := i; Break; end;
      end;
end;

begin
  PrintBand := QryVociNote.Eof and (not FSummaryAlreadyPrinted);
  if not PrintBand then Exit;
  FSummaryAlreadyPrinted := True;

  // CALCOLO DELLE IVE E DEL TOTALE
  TotaleImposte := 0; TotaleImponib := 0;
  FillChar(IVE,SizeOf(IVE),0);
  for i := 0 to FListaVoci.Count - 1 do
      begin
       AVoce := TSingolaVoce(FListaVoci[i]);
       TotaleImponib := TotaleImponib + AVoce.Imponibile;
       TotaleImposte := TotaleImposte + (AVoce.Imponibile * AVoce.IVA / 100);
       NdxIva := FindIVA(AVoce.IVA);
       if NdxIVA = -1 then
          raise Exception.Create('Non è possibile visualizzare più di tre aliquote IVA')
       else
         begin
           IVE[NdxIva].Imponibile := IVE[NdxIva].Imponibile + AVoce.Imponibile;
           IVE[NdxIva].Imposta    := IVE[NdxIva].Imposta + (AVoce.Imponibile * AVoce.IVA / 100);
         end;
      end;

  // VISUALIZZAZIONE DELL'IVA
  LabelIVA[1]       := LB_IVA1;
  LabelImpIVA[1]    := LB_IMPONIBILE1;
  LabelImpostaIVA[1]:= LB_IMPOSTA1;
  LabelIVA[2]       := LB_IVA2;
  LabelImpIVA[2]    := LB_IMPONIBILE2;
  LabelImpostaIVA[2]:= LB_IMPOSTA2;
  LabelIVA[3]       := LB_IVA3;
  LabelImpIVA[3]    := LB_IMPONIBILE3;
  LabelImpostaIVA[3]:= LB_IMPOSTA3;

  for i := Low(IVE) to High(IVE) do
      begin
        if IVE[i].IVA = 0 then
           begin
             LabelIVA[i].Caption         := ' ';
             LabelImpIVA[i].Caption      := ' ';
             LabelImpostaIVA[i].Caption  := ' ';
           end
        else
           begin
             LabelIVA[i].Caption         := IntToStr(IVE[i].IVA);
             LabelImpIVA[i].Caption      := FormatFloat('0.00',IVE[i].Imponibile);
             LabelImpostaIVA[i].Caption  := FormatFloat('0.00',IVE[i].Imposta);
           end;
      end;

  // VISUALIZZAZIONE TOTALE
  LB_TOT_IMPONIBILE.Caption              := FormatFloat('0.00',RealToCents(TotaleImponib) / 100);
  LB_TOT_IMPOSTA.Caption                 := FormatFloat('0.00',RealToCents(TotaleImposte) / 100);
  TotaleNota                             := (RealToCents(TotaleImponib) + RealToCents(TotaleImposte)) / 100;
  LB_TOTALE.Caption                      := FormatFloat('0.00',RealToCents(TotaleNota) / 100);
  Ritenuta                               := (QryNote.FieldByName('RITENUTA').AsInteger / 100) * TotaleImponib;

  if Ritenuta = 0 then
     begin
      LB_IMPONIBILE_RIT_ACCONTO.Caption      := '0.00';
      LB_PERC_RIT_ACCONTO.Caption            := '0';
      LB_RIT_ACCONTO.Caption                 := '0.00';
     end
  else
     begin
      TotaleNota                          := TotaleNota - Ritenuta;
      LB_IMPONIBILE_RIT_ACCONTO.Caption      := LB_TOT_IMPONIBILE.Caption;
      LB_PERC_RIT_ACCONTO.Caption            := QryNote.FieldByName('RITENUTA').AsString;
      LB_RIT_ACCONTO.Caption                 := FormatFloat('0.00',RealToCents(Ritenuta) / 100);
     end;
  LB_NETTO_A_PAGARE.Caption              := FormatFloat('0.00',RealToCents(TotaleNota) / 100);
end;

Constructor TQR_STAMPA_NOTA_DI_CREDITO.Create(AOwner : TComponent);
begin
 Inherited Create(AOwner);
 FHandleImage := THandleImage.Create;
 FListaVoci   := TObjectList.Create;
end;

Destructor TQR_STAMPA_NOTA_DI_CREDITO.Destroy;
begin
 FHandleImage.Free;
 FListaVoci.Clear;
 FListaVoci.Free;
 Inherited Destroy;
end;

Procedure TQR_STAMPA_NOTA_DI_CREDITO.Init(Connection : TpFIBQuery; ChiaveNota : Integer);
begin
  FChiaveNota         := ChiaveNota;

  QryNote.Database    := Connection.Database;
  QryNote.Transaction := Connection.Transaction;
  CopyArrayInTStringList(QryNote.SelectSQL,['SELECT * FROM NOTE_DI_CREDITO',
                                                  ' WHERE CHIAVE = ' + IntToStr(ChiaveNota)]);
  QryVociNote.Database    := Connection.Database;
  QryVociNote.Transaction := Connection.Transaction;
  QryVociNote.SQLs.SelectSQL.Clear;
  CopyArrayInTStringList(QryVociNote.SelectSQL,['SELECT * FROM VOCI_NOTE_CREDITO',
                                                   'WHERE NOTA = ' + IntToStr(ChiaveNota),
                                                   'ORDER BY ORDINAMENTO']);
  QryGeneric.Database    := Connection.Database;
  QryGeneric.Transaction := Connection.Transaction;


  if FileExists(ExtractFilePath(Application.ExeName) + '\LogoFatture.jpg') then
      FHandleImage.LoadFileInTPicture(QR_LOGO.Picture,ExtractFilePath(Application.ExeName) + '\LogoFatture.jpg');
  if FileExists(ExtractFilePath(Application.ExeName) + '\QRCode.jpg') then
      FHandleImage.LoadFileInTPicture(QR_CODE.Picture,ExtractFilePath(Application.ExeName) + '\QRCode.jpg');
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.QuickRepBeforePrint(Sender: TCustomQuickRep;
  var PrintReport: Boolean);
begin
 FListaVoci.Clear;
 FSummaryAlreadyPrinted := False;
 QryNote.Open;
 QryVociNote.Open;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.QuickRepAfterPrint(Sender: TObject);
begin
 QryNote.Close;
 QryVociNote.Close;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.DENOMINAZIONE_SOCIETAPrint(sender: TObject;
  var Value: String);
begin
 Value := SystemInformation.DenominazioneSocieta;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.BAND_INTESTAZIONEBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
begin
 if FSummaryAlreadyPrinted then
    begin
     PrintBand := False;
     Exit;
    end;
 INTESTAZIONE_SOCIETA.Lines.Assign(SystemInformation.IntestazioneSocieta);
 while INTESTAZIONE_SOCIETA.Lines.Count < 6 do
       INTESTAZIONE_SOCIETA.Lines.Insert(0,' ');
 CopyArrayInTStringList(INTESTATARIO.Lines,[QryNote.FieldByName('TITOLO_CLIENTE').AsString + ' ' +
                                              Sinistra(QryNote.FieldByName('GENERALITA_CLIENTE').AsString,45),
                                            '',
                                            QryNote.FieldByName('INDIRIZZO_CLIENTE').AsString,
                                            Trim(QryNote.FieldByName('CAP_CLIENTE').AsString) + ' ' +
                                                 QryNote.FieldByName('COMUNE_CLIENTE').AsString + ' (' +
                                                 QryNote.FieldByName('PROVINCIA_CLIENTE').AsString + ')',
                                            'P.IVA: ' + QryNote.FieldByName('PARTITA_IVA').AsString + ' ' +
                                            'C.F. ' + QryNote.FieldByName('COD_FISCALE').AsString]);
 if IsValidName(QryNote.FieldByName('DESTINAZIONE').AsString) then
    CopyArrayInTStringList(DESTINATARIO.Lines,[Sinistra(QryNote.FieldByName('DESTINAZIONE').AsString,45),
                                               '',
                                               QryNote.FieldByName('INDIRIZZO_DESTINAZIONE').AsString,
                                               Trim(QryNote.FieldByName('CAP_DESTINAZIONE').AsString) + ' ' +
                                                    QryNote.FieldByName('COMUNE_DESTINAZIONE').AsString + ' (' +
                                                    QryNote.FieldByName('PROVINCIA_DESTINAZIONE').AsString + ')'])
 else
    begin
     QrLabel22.Caption := ' ';
     DESTINATARIO.Lines.Clear;
     QRShape42.Width := 0;
    end;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.QRSubDetail1BeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
var Quantita             : Double;
    Importo,ImportoIvato : Double;
    ImportoSingolo       : Double;
    ImportoScontato      : Double;
    IVA,Sconto           : Integer;
    AVoce                : TSingolaVoce;
begin
 if not QryVociNote.FieldByName('VOCE_VUOTA').IsNull  then
    begin
      DESCRIZIONE.Font.Style    := DESCRIZIONE.Font.Style + [fsBold];
      DESCRIZIONE.Lines.Text    := QryVociNote.FieldByName('VOCE_VUOTA').AsString;
      LB_UNITA.Caption          := ' ';
      LB_QUANTITA.Caption       := ' ';
      LB_PREZZO.Caption         := ' ';
      LB_IMPORTO.Caption        := ' ';
      LB_IVA.Caption            := ' ';
      LB_SCONTO.Caption         := ' ';
    end
 else
    begin
      DESCRIZIONE.Font.Style    := DESCRIZIONE.Font.Style - [fsBold];
      DESCRIZIONE.Lines.Text    := QryVociNote.FieldByName('DESCRIZIONE').AsString;
      LB_UNITA.Caption          := QryVociNote.FieldByName('UNITA_DI_MISURA').AsString;
      Quantita                  := CentsToReal(QryVociNote.FieldByName('QUANTITA').AsInteger);
      ImportoSingolo            := CentsToReal(QryVociNote.FieldByName('IMPORTO').AsInteger);
      IVA                       := QryVociNote.FieldByName('IVA').AsInteger;
      Sconto                    := QryVociNote.FieldByName('SCONTO').AsInteger;
      if QryVociNote.FieldByName('IMPORTO_IVATO').AsString <> TRUE_VALUE then
          ImportoIvato    := GetIvatoFromImporto(ImportoSingolo,IVA)
      else
         begin
           ImportoIvato   := ImportoSingolo;
           ImportoSingolo := GetImportoFromIvato(ImportoIvato,IVA);
         end;
      Importo          := ImportoSingolo * Quantita;
      ImportoScontato  := Importo * ((100 - Sconto) / 100);
      ImportoScontato  := RealToCents(ImportoScontato) / 100;
      ImportoIvato     := ImportoIvato * Quantita * ((100 - Sconto) / 100);
      if TappullaIVAFattureXGorla(QryNote.FieldByName('DATA_NOTA').AsDateTime,
                                  QryNote.FieldByName('ANNO_NOTA').AsInteger,
                                  QryNote.FieldByName('ID_NOTA').AsInteger) then
         ImportoIvato     := RealToCents(ImportoIvato) / 100;
      AVoce            := TSingolaVoce.Create;
      AVoce.IVA        := IVA;
      AVoce.Imponibile := ImportoScontato;
      FListaVoci.Add(AVoce);
      LB_QUANTITA.Caption := FormatFloat('0.00',Quantita);
      LB_IVA.Caption      := IntToStr(IVA) + '%';
      LB_PREZZO.Caption   := FormatFloat('0.00',ImportoSingolo);
      LB_SCONTO.Caption   := IntToStr(Sconto) + '%';
      LB_IMPORTO.Caption  := FormatFloat('0.00',ImportoScontato);
     end;
 end;

Procedure TQR_STAMPA_NOTA_DI_CREDITO.LB_NS_IBANPrint(sender: TObject;
  var Value: string);
begin
  Value := SystemInformation.IBAN;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.LB_NUM_DOCUMENTOPrint(sender: TObject;
  var Value: string);
begin
 if QryNote.FieldByName('DATA_NOTA').AsDateTime < EncodeDate(2019,01,01) then
    Value := QryNote.FieldByName('ID_NOTA').AsString +
             SostBoolean(QryNote.FieldByName('FATTURA_ELETTRONICA').AsString = TRUE_VALUE,'E/','/') +
             QryNote.FieldByName('ANNO_NOTA').AsString
 else Value := GetProgressivoInvio(False,
                                   QryNote.FieldByName('ID_NOTA').AsInteger,
                                   QryNote.FieldByName('ANNO_NOTA').AsInteger);
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.LB_TIPO_DOCUMENTOPrint(sender: TObject; var Value: string);
begin
 if QryNote.FieldByName('FATTURA_ELETTRONICA').AsString = TRUE_VALUE then
    Value := 'Nota di credito elettronica'
 else Value := 'Nota di credito';
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.LB_BANCAPrint(sender: TObject; var Value: string);
begin
  Value := QryNote.FieldByName('BANCA_APPOGGIO').AsString;
  if IsValidName(QryNote.FieldByName('ABI').AsString) and
     IsValidName(QryNote.FieldByName('CAB').AsString) then
     Value := Value + ' - ABI: ' + QryNote.FieldByName('ABI').AsString +
              ' - CAB: ' +  QryNote.FieldByName('CAB').AsString;
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.LB_CONDIZIONI_PAGAMENTOPrint(sender: TObject;
  var Value: string);
begin
  if QryNote.FieldByName('COND_PAGAMENTO').IsNull then
     Value := ' - '
  else Value := GetDescrizioneRecord(QryGeneric,TABLECondPagamenti,'DESCRIZIONE','CHIAVE',
                                     QryNote.FieldByName('COND_PAGAMENTO').AsString,'<????>');
end;

procedure TQR_STAMPA_NOTA_DI_CREDITO.LB_DATA_DOCUMENTOPrint(sender: TObject;
  var Value: string);
begin
 Value := FormatDateTime('dd/mm/yyyy',QryNote.FieldByName('DATA_NOTA').AsDateTime);
end;

end.
