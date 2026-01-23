unit QrStampaPreventivo;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery;

type
  TQR_STAMPA_PREVENTIVO = class(TQuickRep)
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
    QryPreventivi: TpFIBDataSet;
    QryVociPreventivi: TpFIBDataSet;
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
    QRLabel5: TQRLabel;
    LB_SCONTO: TQRLabel;
    FOOTER_PRODOTTI: TQRBand;
    QRShape2: TQRShape;
    DESCRIZIONE: TQRMemo;
    LB_TITOLO_DOCUMENTO: TQRLabel;
    BAND_NOTE: TQRChildBand;
    NOTE: TQRMemo;
    QRLabel3: TQRLabel;
    QRShape3: TQRShape;
    QRLabel6: TQRLabel;
    QR_CODE: TQRImage;
    QRLabel24: TQRLabel;
    LB_NS_IBAN: TQRLabel;
    QRShape4: TQRShape;
    QRLabel7: TQRLabel;
    QRShape5: TQRShape;
    QRLabel8: TQRLabel;
    QRLabel11: TQRLabel;
    QRLabel12: TQRLabel;
    BAND_TOTALE: TQRBand;
    QRShape14: TQRShape;
    QRShape11: TQRShape;
    QRShape13: TQRShape;
    QRLabel42: TQRLabel;
    LB_TOT_IMPONIBILE: TQRLabel;
    QRLabel44: TQRLabel;
    LB_TOT_IMPOSTA: TQRLabel;
    QRLabel48: TQRLabel;
    LB_TOTALE: TQRLabel;
    QRShape9: TQRShape;
    QRShape10: TQRShape;
    QRLabel1: TQRLabel;
    LB_IMPONIBILE_RIT_ACCONTO: TQRLabel;
    QRLabel13: TQRLabel;
    LB_PERC_RIT_ACCONTO: TQRLabel;
    QRShape15: TQRShape;
    QRLabel18: TQRLabel;
    LB_RIT_ACCONTO: TQRLabel;
    QRShape16: TQRShape;
    QRLabel20: TQRLabel;
    LB_NETTO_A_PAGARE: TQRLabel;
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
    procedure LB_TITOLO_DOCUMENTOPrint(sender: TObject; var Value: string);
    procedure BAND_NOTEBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
  private
   FHandleImage           : THandleImage;
   FSummaryAlreadyPrinted : Boolean;
   FChiavePreventivo      : Integer;
   FORM_PREVIEW           : TFORM_PREVIEW;
   FListaVoci             : TObjectList;
   FNotePrinted           : Boolean;
  public
   Procedure Init(Connection : TpFIBQuery; ChiavePreventivo : Integer);
  end;

implementation

Type TSingolaVoce = class(TObject)
                      IVA        : Integer;
                      Ivato      : Double;
                      Imponibile : Double;
                    end;

{$R *.DFM}
procedure TQR_STAMPA_PREVENTIVO.BAND_NOTEBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
begin
  PrintBand := IsValidName(QryPreventivi.FieldByName('NOTE').AsString) and (not FNotePrinted);
  if PrintBand then
     begin
      NOTE.Lines.Text := QryPreventivi.FieldByName('NOTE').AsString;
      FNotePrinted := True;
     end;
end;

procedure TQR_STAMPA_PREVENTIVO.BAND_SUMMARYBeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
var i              : Integer;
    Totale         : Double;
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
  PrintBand := QryVociPreventivi.Eof and (not FSummaryAlreadyPrinted);
  if not PrintBand then Exit;
  FSummaryAlreadyPrinted := True;

  // CALCOLO DELLE IVE E DEL TOTALE
  Totale := 0; TotaleImponib := 0;
  FillChar(IVE,SizeOf(IVE),0);
  for i := 0 to FListaVoci.Count - 1 do
      begin
       AVoce := TSingolaVoce(FListaVoci[i]);
       Totale := Totale + AVoce.Ivato;
       TotaleImponib := TotaleImponib + AVoce.Imponibile;
       NdxIva := FindIVA(AVoce.IVA);
       if NdxIVA = -1 then
          raise Exception.Create('Non è possibile visualizzare più di tre aliquote IVA')
       else
         begin
           IVE[NdxIva].Imponibile := IVE[NdxIva].Imponibile + AVoce.Imponibile;
           IVE[NdxIva].Imposta    := IVE[NdxIva].Imposta + (AVoce.Ivato - AVoce.Imponibile);
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
  LB_TOT_IMPONIBILE.Caption              := FormatFloat('0.00',TotaleImponib);
  LB_TOT_IMPOSTA.Caption                 := FormatFloat('0.00',Totale - TotaleImponib);
  LB_TOTALE.Caption                      := FormatFloat('0.00',Totale);
  Ritenuta                               := (QryPreventivi.FieldByName('RITENUTA').AsInteger / 100) * TotaleImponib;

  if Ritenuta = 0 then
     begin
      LB_IMPONIBILE_RIT_ACCONTO.Caption      := '0.00';
      LB_PERC_RIT_ACCONTO.Caption            := '0';
      LB_RIT_ACCONTO.Caption                 := '0.00';
     end
  else
     begin
      LB_IMPONIBILE_RIT_ACCONTO.Caption      := LB_TOT_IMPONIBILE.Caption;
      LB_PERC_RIT_ACCONTO.Caption            := QryPreventivi.FieldByName('RITENUTA').AsString;
      LB_RIT_ACCONTO.Caption                 := FormatFloat('0.00',Ritenuta);
     end;
  LB_NETTO_A_PAGARE.Caption              := FormatFloat('0.00',Totale - Ritenuta);


end;

Constructor TQR_STAMPA_PREVENTIVO.Create(AOwner : TComponent);
begin
 Inherited Create(AOwner);
 FHandleImage := THandleImage.Create;
 FListaVoci   := TObjectList.Create;
end;

Destructor TQR_STAMPA_PREVENTIVO.Destroy;
begin
 FHandleImage.Free;
 FListaVoci.Clear;
 FListaVoci.Free;
 Inherited Destroy;
end;

Procedure TQR_STAMPA_PREVENTIVO.Init(Connection : TpFIBQuery; ChiavePreventivo : Integer);
begin
  FChiavePreventivo         := ChiavePreventivo;

  QryPreventivi.Database    := Connection.Database;
  QryPreventivi.Transaction := Connection.Transaction;
  CopyArrayInTStringList(QryPreventivi.SelectSQL,['SELECT * FROM PREVENTIVI',
                                                  ' WHERE PREVENTIVI.CHIAVE = ' + IntToStr(ChiavePreventivo)]);
  QryVociPreventivi.Database    := Connection.Database;
  QryVociPreventivi.Transaction := Connection.Transaction;
  QryVociPreventivi.SQLs.SelectSQL.Clear;
  CopyArrayInTStringList(QryVociPreventivi.SelectSQL,['SELECT * FROM VOCI_PREVENTIVI',
                                                   'WHERE PREVENTIVO = ' + IntToStr(ChiavePreventivo),
                                                   'ORDER BY ORDINAMENTO']);
  QryGeneric.Database    := Connection.Database;
  QryGeneric.Transaction := Connection.Transaction;


  if FileExists(ExtractFilePath(Application.ExeName) + '\LogoFatture.jpg') then
      FHandleImage.LoadFileInTPicture(QR_LOGO.Picture,ExtractFilePath(Application.ExeName) + '\LogoFatture.jpg');
  if FileExists(ExtractFilePath(Application.ExeName) + '\QRCode.jpg') then
      FHandleImage.LoadFileInTPicture(QR_CODE.Picture,ExtractFilePath(Application.ExeName) + '\QRCode.jpg');
end;

procedure TQR_STAMPA_PREVENTIVO.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

procedure TQR_STAMPA_PREVENTIVO.QuickRepBeforePrint(Sender: TCustomQuickRep;
  var PrintReport: Boolean);
begin
 FListaVoci.Clear;
 FSummaryAlreadyPrinted := False;
 FNotePrinted           := False;
 QryPreventivi.Open;
 QryVociPreventivi.Open;
end;

procedure TQR_STAMPA_PREVENTIVO.QuickRepAfterPrint(Sender: TObject);
begin
 QryPreventivi.Close;
 QryVociPreventivi.Close;
end;

procedure TQR_STAMPA_PREVENTIVO.DENOMINAZIONE_SOCIETAPrint(sender: TObject;
  var Value: String);
begin
 Value := SystemInformation.DenominazioneSocieta;
end;

procedure TQR_STAMPA_PREVENTIVO.BAND_INTESTAZIONEBeforePrint(Sender: TQRCustomBand;
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
 CopyArrayInTStringList(INTESTATARIO.Lines,[QryPreventivi.FieldByName('TITOLO_CLIENTE').AsString + ' ' +
                                              QryPreventivi.FieldByName('GENERALITA_CLIENTE').AsString,
                                            QryPreventivi.FieldByName('INDIRIZZO_CLIENTE').AsString,
                                            Trim(QryPreventivi.FieldByName('CAP_CLIENTE').AsString) + ' ' +
                                                 QryPreventivi.FieldByName('COMUNE_CLIENTE').AsString + ' (' +
                                                 QryPreventivi.FieldByName('PROVINCIA_CLIENTE').AsString + ')',
                                            'P.IVA: ' + QryPreventivi.FieldByName('PARTITA_IVA').AsString + ' ' +
                                            'C.F. ' + QryPreventivi.FieldByName('COD_FISCALE').AsString]);
 if IsValidName(QryPreventivi.FieldByName('DESTINAZIONE').AsString) then
    CopyArrayInTStringList(DESTINATARIO.Lines,[Sinistra(QryPreventivi.FieldByName('DESTINAZIONE').AsString,45),
                                               '',
                                               QryPreventivi.FieldByName('INDIRIZZO_DESTINAZIONE').AsString,
                                               Trim(QryPreventivi.FieldByName('CAP_DESTINAZIONE').AsString) + ' ' +
                                                    QryPreventivi.FieldByName('COMUNE_DESTINAZIONE').AsString + ' (' +
                                                    QryPreventivi.FieldByName('PROVINCIA_DESTINAZIONE').AsString + ')'])
 else
    begin
     QrLabel22.Caption := ' ';
     DESTINATARIO.Lines.Clear;
     QRShape42.Width := 0;
    end;
end;

procedure TQR_STAMPA_PREVENTIVO.LB_TITOLO_DOCUMENTOPrint(sender: TObject;
  var Value: string);
var Nome : String;
begin
 Nome := 'PREVENTIVO ';
 if QryPreventivi.FieldByName('STATO').AsString = ID_PREVENTIVO_CONFERMATO then
    Nome := 'CONFERMA D''ORDINE ';
 Value := Nome + QryPreventivi.FieldByName('ID_PREVENTIVO').AsString + '/' +
          QryPreventivi.FieldByName('ANNO_PREVENTIVO').AsString;
end;

procedure TQR_STAMPA_PREVENTIVO.QRSubDetail1BeforePrint(Sender: TQRCustomBand;
  var PrintBand: Boolean);
var Quantita             : Double;
    Importo,ImportoIvato : Double;
    ImportoSingolo       : Double;
    Sconto,IVA           : Integer;
    AVoce                : TSingolaVoce;
    ImportoScontato      : Double;
begin
 if not QryVociPreventivi.FieldByName('VOCE_VUOTA').IsNull  then
    begin
      DESCRIZIONE.Font.Style    := DESCRIZIONE.Font.Style + [fsBold];
      DESCRIZIONE.Lines.Text    := QryVociPreventivi.FieldByName('VOCE_VUOTA').AsString;
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
      DESCRIZIONE.Lines.Text    := QryVociPreventivi.FieldByName('DESCRIZIONE').AsString;
      LB_UNITA.Caption          := QryVociPreventivi.FieldByName('UNITA_DI_MISURA').AsString;
      Quantita                  := CentsToReal(QryVociPreventivi.FieldByName('QUANTITA').AsInteger);
      ImportoSingolo            := CentsToReal(QryVociPreventivi.FieldByName('IMPORTO').AsInteger);
      IVA                       := QryVociPreventivi.FieldByName('IVA').AsInteger;
      Sconto                    := QryVociPreventivi.FieldByName('SCONTO').AsInteger;
      if QryVociPreventivi.FieldByName('IMPORTO_IVATO').AsString <> TRUE_VALUE then
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
      ImportoIvato     := RealToCents(ImportoIvato) / 100;
      AVoce            := TSingolaVoce.Create;
      AVoce.IVA        := IVA;
      AVoce.Imponibile := ImportoScontato;
      AVoce.Ivato      := ImportoIvato;
      FListaVoci.Add(AVoce);
      LB_QUANTITA.Caption := FormatFloat('0.00',Quantita);
      LB_IVA.Caption      := IntToStr(IVA) + '%';
      LB_PREZZO.Caption   := FormatFloat('0.00',ImportoSingolo);
      LB_SCONTO.Caption   := IntToStr(Sconto) + '%';
      LB_IMPORTO.Caption  := FormatFloat('0.00',ImportoScontato);
     end;
 end;

Procedure TQR_STAMPA_PREVENTIVO.LB_NS_IBANPrint(sender: TObject;
  var Value: string);
begin
  Value := SystemInformation.IBAN;
end;

procedure TQR_STAMPA_PREVENTIVO.LB_BANCAPrint(sender: TObject; var Value: string);
begin
  Value := QryPreventivi.FieldByName('BANCA_APPOGGIO').AsString;
  if IsValidName(QryPreventivi.FieldByName('ABI').AsString) and
     IsValidName(QryPreventivi.FieldByName('CAB').AsString) then
     Value := Value + ' - ABI: ' + QryPreventivi.FieldByName('ABI').AsString +
              ' - CAB: ' +  QryPreventivi.FieldByName('CAB').AsString;
end;

procedure TQR_STAMPA_PREVENTIVO.LB_CONDIZIONI_PAGAMENTOPrint(sender: TObject;
  var Value: string);
begin
  if QryPreventivi.FieldByName('COND_PAGAMENTO').IsNull then
     Value := ' - '
  else Value := GetDescrizioneRecord(QryGeneric,TABLECondPagamenti,'DESCRIZIONE','CHIAVE',
                                     QryPreventivi.FieldByName('COND_PAGAMENTO').AsString,'<????>');
end;

procedure TQR_STAMPA_PREVENTIVO.LB_DATA_DOCUMENTOPrint(sender: TObject;
  var Value: string);
begin
 Value := FormatDateTime('dd/mm/yyyy',QryPreventivi.FieldByName('DATA_PREVENTIVO').AsDateTime);
end;

end.
