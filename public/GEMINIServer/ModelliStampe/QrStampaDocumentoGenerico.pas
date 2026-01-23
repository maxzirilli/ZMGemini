unit QrStampaDDTUscente;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls,ZDateFunct,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery,
     ZStringConvFunct,ZFatturaElettronica;

type
  TQR_STAMPA_DDT_USCENTE = class(TQuickRep)
    BAND_INTESTAZIONE: TQRBand;
    DENOMINAZIONE_SOCIETA: TQRLabel;
    INTESTAZIONE_SOCIETA: TQRMemo;
    QryFatture: TpFIBDataSet;
    QryVociFattura: TpFIBDataSet;
    QR_LOGO: TQRImage;
    QryGeneric: TpFIBQuery;
    BAND_OGGETTO: TQRBand;
    DESCRIZIONE: TQRMemo;
    LB_TITOLO: TQRLabel;
    procedure QuickRepPreview(Sender: TObject);
    procedure QuickRepBeforePrint(Sender: TCustomQuickRep;
      var PrintReport: Boolean);
    procedure QuickRepAfterPrint(Sender: TObject);
    procedure DENOMINAZIONE_SOCIETAPrint(sender: TObject;
      var Value: String);
    procedure BAND_INTESTAZIONEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_FIRMABeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    Constructor Create(AOwner : TComponent); OverRide;
    Destructor Destroy; OverRide;
    procedure LB_DATA_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure LB_NUM_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure LB_CAUSALEPrint(sender: TObject; var Value: string);
    procedure LB_TRASPORTO_A_MEZZOPrint(sender: TObject; var Value: string);
    procedure LB_NS_IBANPrint(sender: TObject; var Value: string);
    procedure LB_RIF_ORDINEPrint(sender: TObject; var Value: string);
    procedure LB_DATA_ORDINEPrint(sender: TObject; var Value: string);
    procedure LB_TRASPORTOPrint(sender: TObject; var Value: string);
    procedure BAND_SUMMARYBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure LB_NOTA_FATTURAPrint(sender: TObject; var Value: string);
    procedure LB_TIPO_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure BAND_CAUSALEBeforePrint(Sender: TQRCustomBand; var PrintBand: Boolean);
    procedure QRLabel30Print(sender: TObject; var Value: string);
    procedure QRLabel34Print(sender: TObject; var Value: string);
    procedure QRLabel7Print(sender: TObject; var Value: string);
    procedure RATADATAPrint(sender: TObject; var Value: string);
    procedure RATA_IMPONIBILE1Print(sender: TObject; var Value: string);
    procedure LB_DATA_RATA_GENERICAPrint(sender: TObject; var Value: string);
    procedure LB_IMPONIBILE_RATA_GENERICAPrint(sender: TObject;
      var Value: string);
    procedure QRSubDetail2BeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_HEADER_RATEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
  private
   FHandleImage           : THandleImage;
   FSummaryAlreadyPrinted : Boolean;
   FChiaveFattura         : Integer;
   FORM_PREVIEW           : TFORM_PREVIEW;
   FListaVoci             : TObjectList;
   FRateImponibili        : TObjectList;
  public
   Procedure Init(Connection : TpFIBQuery; ChiaveFattura : Integer);
  end;

implementation

Type TSingolaRata = class(TObject)
                      Data       : TDateTime;
                      Imponibile : Double;
                    end;

Type TSingolaVoce = class(TObject)
                      IVA        : Integer;
                      Imponibile : Double;
                    end;

{$R *.DFM}
procedure TQR_STAMPA_DDT_USCENTE.BAND_SUMMARYBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
var i                 : Integer;
    TotaleImposte     : Double;
    TotaleImponib     : Double;
    TotaleFattura     : Double;
    Ritenuta          : Double;
    IVE               : Array[1..3] of record
                                         IVA          : Integer;
                                         Imponibile   : Double;
                                         Imposta      : Double;
                                       end;
    AVoce             : TSingolaVoce;
    NdxIva            : Integer;
    LastRata          : Integer;
    LabelIVA          : Array[1..3] of TQrLabel;
    LabelImpIVA       : Array[1..3] of TQrLabel;
    LabelImpostaIVA   : Array[1..3] of TQrLabel;
    TotaleDaIncassare : Double;
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
  PrintBand := QryVociFattura.Eof and (not FSummaryAlreadyPrinted);
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
  TotaleFattura                          := (RealToCents(TotaleImponib) + RealToCents(TotaleImposte)) / 100;
  LB_TOTALE.Caption                      := FormatFloat('0.00',RealToCents(TotaleFattura) / 100);
  Ritenuta                               := (QryFatture.FieldByName('RITENUTA').AsInteger / 100) * TotaleImponib;

  if Ritenuta = 0 then
     begin
      LB_IMPONIBILE_RIT_ACCONTO.Caption      := '0.00';
      LB_PERC_RIT_ACCONTO.Caption            := '0';
      LB_RIT_ACCONTO.Caption                 := '0.00';
     end
  else
     begin
      TotaleFattura                          := TotaleFattura - Ritenuta;
      LB_IMPONIBILE_RIT_ACCONTO.Caption      := LB_TOT_IMPONIBILE.Caption;
      LB_PERC_RIT_ACCONTO.Caption            := QryFatture.FieldByName('RITENUTA').AsString;
      LB_RIT_ACCONTO.Caption                 := FormatFloat('0.00',RealToCents(Ritenuta) / 100);
     end;
  LB_NETTO_A_PAGARE.Caption              := FormatFloat('0.00',RealToCents(TotaleFattura) / 100);

  // CALCOLO DELLE RATE
  TotaleDaIncassare := TotaleFattura;
  if QryFatture.FieldByName('FATTURA_ELETTRONICA').AsString = TRUE_VALUE then
     if QryFatture.FieldByName('ESIGIBILITA_IVA').AsString = FATT_ESIG_IVA_CODICI[ivaScissione] then
        TotaleDaIncassare := TotaleImponib;

  // ARROTONDA L'ULTIMA RATA
  TotaleImponib := 0.0;
  for i := 0 to FRateImponibili.Count - 1 do
      TotaleImponib := TotaleImponib + TSingolaRata(FRateImponibili[i]).Imponibile;

  // RATE NASCOSTE SE AVVISO DI FATTURA
  if QryFatture.FieldByName('ID_FATTURA').AsInteger = 0 then
     begin
       QRLabel6.Height          := 0;
       QRLabel28.Height         := 0;
       QRShape5.Visible         := False;
       QrShape4.Visible         := False;
       QrShape3.Visible         := False;
       RATA_IMPONIBILE1.Height  := 0;
       RATA_IMPONIBILE2.Height  := 0;
       RATA_IMPONIBILE3.Height  := 0;
       RATA_DATA1.Height        := 0;
       RATA_DATA2.Height        := 0;
       RATA_DATA3.Height        := 0;
     end;
end;

Constructor TQR_STAMPA_DDT_USCENTE.Create(AOwner : TComponent);
begin
 Inherited Create(AOwner);
 FHandleImage := THandleImage.Create;
 FListaVoci   := TObjectList.Create;
 FRateImponibili := TObjectList.Create;
end;

Destructor TQR_STAMPA_DDT_USCENTE.Destroy;
begin
 FHandleImage.Free;
 FListaVoci.Clear;
 FListaVoci.Free;
 FRateImponibili.Clear;
 FRateImponibili.Free;
 Inherited Destroy;
end;

Procedure TQR_STAMPA_DDT_USCENTE.Init(Connection : TpFIBQuery; ChiaveFattura : Integer);
begin
  FChiaveFattura         := ChiaveFattura;

  QryFatture.Database    := Connection.Database;
  QryFatture.Transaction := Connection.Transaction;
  CopyArrayInTStringList(QryFatture.SelectSQL,['SELECT * FROM FATTURE',
                                               ' WHERE FATTURE.CHIAVE = ' + IntToStr(ChiaveFattura)]);


  QryRateFattura.Database := Connection.Database;
  QryRateFattura.Transaction := Connection.Transaction;
  CopyArrayInTStringList(QryRateFattura.SelectSQL,['SELECT * FROM RATE_FATTURA',
                                                   ' WHERE FATTURA = ' + IntToStr(FChiaveFattura),
                                                   'ORDER BY DATA']);

  QryVociFattura.Database    := Connection.Database;
  QryVociFattura.Transaction := Connection.Transaction;
  QryVociFattura.SQLs.SelectSQL.Clear;
  CopyArrayInTStringList(QryVociFattura.SelectSQL,['SELECT * FROM VOCI_FATTURE',
                                                   'WHERE FATTURA = ' + IntToStr(ChiaveFattura),
                                                   'ORDER BY ORDINAMENTO']);
  QryGeneric.Database    := Connection.Database;
  QryGeneric.Transaction := Connection.Transaction;


  if FileExists(ExtractFilePath(Application.ExeName) + '\LogoFatture.jpg') then
      FHandleImage.LoadFileInTPicture(QR_LOGO.Picture,ExtractFilePath(Application.ExeName) + '\LogoFatture.jpg');
  if FileExists(ExtractFilePath(Application.ExeName) + '\QRCode.jpg') then
      FHandleImage.LoadFileInTPicture(QR_CODE.Picture,ExtractFilePath(Application.ExeName) + '\QRCode.jpg');
end;

procedure TQR_STAMPA_DDT_USCENTE.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

procedure TQR_STAMPA_DDT_USCENTE.RATADATAPrint(sender: TObject;var Value: string);
begin
 Value := ' ';
 if FRateImponibili.Count >= TComponent(Sender).Tag then
    begin
      if (TComponent(Sender).Tag = 3) and (FRateImponibili.Count > 3) then
         Value := '*vedi prospetto'
      else Value := FormatDateTime('dd/mm/yyyy',TSingolaRata(fRateImponibili[TComponent(Sender).Tag - 1]).Data)
    end;
end;

procedure TQR_STAMPA_DDT_USCENTE.RATA_IMPONIBILE1Print(sender: TObject;var Value: string);
begin
 Value := ' ';
 if FRateImponibili.Count >= TComponent(Sender).Tag then
    begin
      if (TComponent(Sender).Tag = 3) and (FRateImponibili.Count > 3) then
         Value := ''
      else Value := FormatFloat('0.00',TSingolaRata(fRateImponibili[TComponent(Sender).Tag - 1]).Imponibile);
    end;

end;

procedure TQR_STAMPA_DDT_USCENTE.QuickRepBeforePrint(Sender: TCustomQuickRep;
  var PrintReport: Boolean);
var SingolaRata       : TSingolaRata;
begin
 FListaVoci.Clear;
 FSummaryAlreadyPrinted := False;
 FRateImponibili.Clear;
 With QryGeneric do
      begin
        CopyArrayInTStringList(SQL,['SELECT * FROM RATE_FATTURA',
                                    ' WHERE FATTURA = ' + IntToStr(FChiaveFattura),
                                    'ORDER BY DATA']);
        try
         ExecQuery;
         while not Eof do
               begin
                SingolaRata            := TSingolaRata.Create;
                SingolaRata.Data       := FieldByName('DATA').AsDate;
                SingolaRata.Imponibile := CentsToReal(FieldByName('IMPORTO').AsInteger);
                FRateImponibili.Add(SingolaRata);
                Next;
               end;
        finally
         Close;
        end;
      end;
 QryFatture.Open;
 QryVociFattura.Open;
 QryRateFattura.Open;
end;

procedure TQR_STAMPA_DDT_USCENTE.QuickRepAfterPrint(Sender: TObject);
begin
 QryRateFattura.Close;
 QryFatture.Close;
 QryVociFattura.Close;
end;

procedure TQR_STAMPA_DDT_USCENTE.DENOMINAZIONE_SOCIETAPrint(sender: TObject;
  var Value: String);
begin
 Value := SystemInformation.DenominazioneSocieta;
end;

procedure TQR_STAMPA_DDT_USCENTE.BAND_CAUSALEBeforePrint(Sender: TQRCustomBand; var PrintBand: Boolean);
begin
 PrintBand := IsValidName(QryFatture.FieldByName('CAUSALE').AsString);
 if PrintBand then
    CAUSALE_FATTURA.Lines.Text := QryFatture.FieldByName('CAUSALE').AsString;
end;

procedure TQR_STAMPA_DDT_USCENTE.BAND_HEADER_RATEBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
begin
  PrintBand := FRateImponibili.Count > 3
end;

procedure TQR_STAMPA_DDT_USCENTE.BAND_INTESTAZIONEBeforePrint(Sender: TQRCustomBand;
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
 CopyArrayInTStringList(INTESTATARIO.Lines,[QryFatture.FieldByName('TITOLO_CLIENTE').AsString + ' ' +
                                              QryFatture.FieldByName('GENERALITA_CLIENTE').AsString,
                                            QryFatture.FieldByName('INDIRIZZO_CLIENTE').AsString,
                                            Trim(QryFatture.FieldByName('CAP_CLIENTE').AsString) + ' ' +
                                                 QryFatture.FieldByName('COMUNE_CLIENTE').AsString + ' (' +
                                                 QryFatture.FieldByName('PROVINCIA_CLIENTE').AsString + ')',
                                            'P.IVA: ' + QryFatture.FieldByName('PARTITA_IVA').AsString + ' ' +
                                            'C.F.: ' + QryFatture.FieldByName('COD_FISCALE').AsString]);
 if QryFatture.FieldByName('COD_ENTE_FATTURA_ELETTRONICA').AsString <> '' then
    INTESTATARIO.Lines.Add('COD.SDI: ' + QryFatture.FieldByName('COD_ENTE_FATTURA_ELETTRONICA').AsString);
 if IsValidName(QryFatture.FieldByName('DESTINAZIONE').AsString) then
    CopyArrayInTStringList(DESTINATARIO.Lines,[Sinistra(QryFatture.FieldByName('DESTINAZIONE').AsString,45),
                                               '',
                                               QryFatture.FieldByName('INDIRIZZO_DESTINAZIONE').AsString,
                                               Trim(QryFatture.FieldByName('CAP_DESTINAZIONE').AsString) + ' ' +
                                                    QryFatture.FieldByName('COMUNE_DESTINAZIONE').AsString + ' (' +
                                                    QryFatture.FieldByName('PROVINCIA_DESTINAZIONE').AsString + ')'])
 else
    begin
     QrLabel22.Caption := ' ';
     DESTINATARIO.Lines.Clear;
     QRShape42.Width := 0;
    end;
end;

procedure TQR_STAMPA_DDT_USCENTE.QRLabel30Print(sender: TObject;var Value: string);
begin
 Value := SostBoolean(QryFatture.FieldByName('ANNO_FATTURA').IsNull,' ','Nr. documento');
end;

procedure TQR_STAMPA_DDT_USCENTE.QRLabel34Print(sender: TObject;
  var Value: string);
begin
 Value := SostBoolean(QryFatture.FieldByName('ANNO_FATTURA').IsNull,
                     SostBoolean(QryFatture.FieldByName('DATA_FATTURA').AsDateTime >= EncodeDate(2019,02,01),'Mese emissione','Generazione'),
                                 'Data documento');
end;

procedure TQR_STAMPA_DDT_USCENTE.QRLabel7Print(sender: TObject;
  var Value: string);
begin
  if QryFatture.FieldByName('DATA_FATTURA').AsDateTime >= EncodeDate(2019,01,01) then
     Value := 'Documento non valido ai fini fiscali'
  else Value := ' ';
end;

procedure TQR_STAMPA_DDT_USCENTE.BAND_FIRMABeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
var Quantita             : Double;
    Importo,ImportoIvato : Double;
    ImportoSingolo       : Double;
    Sconto,IVA           : Integer;
    AVoce                : TSingolaVoce;
    ImportoScontato      : Double;
begin
 if not QryVociFattura.FieldByName('VOCE_VUOTA').IsNull  then
    begin
      DESCRIZIONE.Font.Style    := DESCRIZIONE.Font.Style + [fsBold];
      DESCRIZIONE.Lines.Text    := QryVociFattura.FieldByName('VOCE_VUOTA').AsString;
      LB_UNITA.Caption          := ' ';
      LB_QUANTITA.Caption       := ' ';
      LB_PREZZO.Caption         := ' ';
      LB_IMPORTO.Caption        := ' ';
      LB_SCONTO.Caption         := ' ';
      LB_IVA.Caption            := ' ';
    end
 else
    begin
      DESCRIZIONE.Font.Style    := DESCRIZIONE.Font.Style - [fsBold];
      DESCRIZIONE.Lines.Text    := QryVociFattura.FieldByName('DESCRIZIONE').AsString;
      LB_UNITA.Caption          := QryVociFattura.FieldByName('UNITA_DI_MISURA').AsString;
      Quantita                  := CentsToReal(QryVociFattura.FieldByName('QUANTITA').AsInteger);
      ImportoSingolo            := CentsToReal(QryVociFattura.FieldByName('IMPORTO').AsInteger);
      IVA                       := QryVociFattura.FieldByName('IVA').AsInteger;
      Sconto                    := QryVociFattura.FieldByName('SCONTO').AsInteger;
      if QryVociFattura.FieldByName('IMPORTO_IVATO').AsString <> TRUE_VALUE then
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
      if TappullaIVAFattureXGorla(QryFatture.FieldByName('DATA_FATTURA').AsDateTime,
                                  QryFatture.FieldByName('ANNO_FATTURA').AsInteger,
                                  QryFatture.FieldByName('ID_FATTURA').AsInteger) then
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

procedure TQR_STAMPA_DDT_USCENTE.QRSubDetail2BeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
begin
 PrintBand := FRateImponibili.Count > 3
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_NOTA_FATTURAPrint(sender: TObject;
  var Value: string);
begin
  Value := QryFatture.FieldByName('NOTE_IN_FATTURA').AsString;
  if Value = '' then Value := ' ';
end;

Procedure TQR_STAMPA_DDT_USCENTE.LB_NS_IBANPrint(sender: TObject;
  var Value: string);
begin
  Value := SystemInformation.IBAN;
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_NUM_DOCUMENTOPrint(sender: TObject;
  var Value: string);
begin
 if QryFatture.FieldByName('ANNO_FATTURA').IsNull then
    Value := ' '
 else
   begin
    if QryFatture.FieldByName('DATA_FATTURA').AsDateTime < EncodeDate(2019,01,01) then
       Value := QryFatture.FieldByName('ID_FATTURA').AsString +
                  SostBoolean(QryFatture.FieldByName('FATTURA_ELETTRONICA').AsString = TRUE_VALUE,'E/','/') +
                  QryFatture.FieldByName('ANNO_FATTURA').AsString
    else Value := GetProgressivoInvio(True,
                                      QryFatture.FieldByName('ID_FATTURA').AsInteger,
                                      QryFatture.FieldByName('ANNO_FATTURA').AsInteger);
   end
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_RIF_ORDINEPrint(sender: TObject;
  var Value: string);
begin
  Value := QryFatture.FieldByName('RIF_VOSTRO_ORDINE').AsString;
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_TIPO_DOCUMENTOPrint(sender: TObject; var Value: string);
begin
 if QryFatture.FieldByName('ANNO_FATTURA').IsNull then
    Value := 'AVVISO DI FATTURA ELETTRONICA - COPIA DI CORTESIA'
 else
   begin
     if QryFatture.FieldByName('FATTURA_ELETTRONICA').AsString = TRUE_VALUE then
        Value := 'FATTURA ELETTRONICA - COPIA DI CORTESIA'
     else Value := 'FATTURA';
   end;
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_TRASPORTOPrint(sender: TObject;
  var Value: string);
begin
  Value := QryFatture.FieldByName('TRASPORTO').AsString;
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_TRASPORTO_A_MEZZOPrint(sender: TObject; var Value: string);
begin
  Value := QryFatture.FieldByName('BANCA_APPOGGIO').AsString;
  if IsValidName(QryFatture.FieldByName('ABI').AsString) and
     IsValidName(QryFatture.FieldByName('CAB').AsString) then
     Value := Value + ' - ABI: ' + QryFatture.FieldByName('ABI').AsString +
              ' - CAB: ' +  QryFatture.FieldByName('CAB').AsString;
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_CAUSALEPrint(sender: TObject;
  var Value: string);
begin
  if QryFatture.FieldByName('COND_PAGAMENTO').IsNull then
     Value := ' - '
  else Value := GetDescrizioneRecord(QryGeneric,TABLECondPagamenti,'DESCRIZIONE','CHIAVE',
                                     QryFatture.FieldByName('COND_PAGAMENTO').AsString,'<????>');
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_DATA_DOCUMENTOPrint(sender: TObject; var Value: string);
var Generazione : Integer;
begin
 if QryFatture.FieldByName('ANNO_FATTURA').IsNull then
    begin
     Generazione := QryFatture.FieldByName('GENERAZIONE_AUTOMATICA').AsInteger;
     if Generazione = 0 then
        Value := 'Manuale'
     else Value := MesiInItaliano[Generazione mod 100] + '/' + IntToStr(Generazione div 100);
    end
 else Value := FormatDateTime('dd/mm/yyyy',QryFatture.FieldByName('DATA_FATTURA').AsDateTime);
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_DATA_ORDINEPrint(sender: TObject;
  var Value: string);
begin
  if QryFatture.FieldByName('DATA_ORDINE').AsDateTime = 0.0 then
     Value := ' - '
  else Value := FormatDateTime('dd/mm/yyyy',QryFatture.FieldByName('DATA_ORDINE').AsDateTime);
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_DATA_RATA_GENERICAPrint(sender: TObject;
  var Value: string);
begin
  Value := FormatDateTime('dd/mm/yyyy',QryRateFattura.FieldByName('DATA').AsDateTime);
end;

procedure TQR_STAMPA_DDT_USCENTE.LB_IMPONIBILE_RATA_GENERICAPrint(sender: TObject;
  var Value: string);
begin
 Value := FormatFloat('0.00',CentsToReal(QryRateFattura.FieldByName('IMPORTO').AsInteger)) + '€';
end;

end.
