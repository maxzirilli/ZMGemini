object QR_STAMPA_DDT_USCENTE: TQR_STAMPA_DDT_USCENTE
  Left = 0
  Top = 0
  Width = 1111
  Height = 1572
  Frame.Color = clBlack
  Frame.DrawTop = False
  Frame.DrawBottom = False
  Frame.DrawLeft = False
  Frame.DrawRight = False
  AfterPrint = QuickRepAfterPrint
  AfterPreview = QuickRepAfterPrint
  BeforePrint = QuickRepBeforePrint
  Font.Charset = ANSI_CHARSET
  Font.Color = clWindowText
  Font.Height = -13
  Font.Name = 'Times New Roman'
  Font.Style = []
  Functions.Strings = (
    'PAGENUMBER'
    'COLUMNNUMBER'
    'REPORTTITLE'
    'QRSTRINGSBAND1')
  Functions.DATA = (
    '0'
    '0'
    #39#39
    #39#39)
  OnPreview = QuickRepPreview
  Options = [FirstPageHeader, LastPageFooter]
  Page.Columns = 1
  Page.Orientation = poPortrait
  Page.PaperSize = A4
  Page.Continuous = False
  Page.Values = (
    100.000000000000000000
    2970.000000000000000000
    100.000000000000000000
    2100.000000000000000000
    100.000000000000000000
    100.000000000000000000
    0.000000000000000000)
  PrinterSettings.Copies = 1
  PrinterSettings.OutputBin = Auto
  PrinterSettings.Duplex = False
  PrinterSettings.FirstPage = 0
  PrinterSettings.LastPage = 0
  PrinterSettings.UseStandardprinter = False
  PrinterSettings.UseCustomBinCode = False
  PrinterSettings.CustomBinCode = 0
  PrinterSettings.ExtendedDuplex = 0
  PrinterSettings.UseCustomPaperCode = False
  PrinterSettings.CustomPaperCode = 0
  PrinterSettings.PrintMetaFile = False
  PrinterSettings.PrintQuality = 0
  PrinterSettings.Collate = 0
  PrinterSettings.ColorOption = 0
  PrintIfEmpty = True
  ShowProgress = False
  SnapToGrid = True
  Units = MM
  Zoom = 140
  PrevFormStyle = fsNormal
  PreviewInitialState = wsNormal
  PrevInitialZoom = qrZoomToFit
  PreviewDefaultSaveType = stQRP
  PreviewLeft = 0
  PreviewTop = 0
  object BAND_INTESTAZIONE: TQRBand
    Left = 53
    Top = 53
    Width = 1005
    Height = 633
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    BeforePrint = BAND_INTESTAZIONEBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      1196.294642857143000000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbPageHeader
    object DENOMINAZIONE_SOCIETA: TQRLabel
      Left = 443
      Top = 20
      Width = 550
      Height = 32
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        60.476190476190480000
        837.217261904762000000
        37.797619047619050000
        1039.434523809524000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'DENOMINAZIONE_SOCIETA'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -19
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      OnPrint = DENOMINAZIONE_SOCIETAPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 14
    end
    object INTESTAZIONE_SOCIETA: TQRMemo
      Left = 443
      Top = 60
      Width = 550
      Height = 136
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        257.023809523809500000
        837.217261904762000000
        113.392857142857100000
        1039.434523809524000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = True
      Color = clWhite
      Lines.Strings = (
        '1'
        '2'
        '3'
        '4'
        '5'
        '6')
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 10
    end
    object QR_LOGO: TQRImage
      Left = 11
      Top = 17
      Width = 425
      Height = 185
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        349.627976190476200000
        20.788690476190480000
        32.127976190476190000
        803.199404761904800000)
      XLColumn = 0
      Stretch = True
    end
    object QRShape1: TQRShape
      Left = 11
      Top = 216
      Width = 472
      Height = 158
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        298.979166666666700000
        21.166666666666670000
        407.458333333333300000
        891.645833333333400000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRLabel2: TQRLabel
      Left = 19
      Top = 219
      Width = 175
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        35.907738095238090000
        413.883928571428600000
        330.729166666666700000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Intestatario documento'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object INTESTATARIO: TQRMemo
      Left = 17
      Top = 251
      Width = 458
      Height = 115
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        216.958333333333300000
        31.750000000000000000
        473.604166666666700000
        865.187500000000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -11
      Font.Name = 'Times New Roman'
      Font.Style = []
      Lines.Strings = (
        '0'
        '2'
        '3'
        '4'
        '5'
        '6')
      ParentFont = False
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 8
    end
    object QRShape42: TQRShape
      Left = 521
      Top = 216
      Width = 472
      Height = 158
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        298.979166666666700000
        984.250000000000000000
        407.458333333333300000
        891.645833333333400000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRLabel22: TQRLabel
      Left = 527
      Top = 219
      Width = 97
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        995.967261904762000000
        413.883928571428600000
        183.318452380952400000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Destinatario'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object DESTINATARIO: TQRMemo
      Left = 526
      Top = 251
      Width = 458
      Height = 112
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        211.666666666666700000
        994.833333333333400000
        473.604166666666700000
        865.187500000000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Color = clWhite
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 10
    end
    object QRShape49: TQRShape
      Left = 11
      Top = 382
      Width = 981
      Height = 245
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        463.020833333333400000
        20.788690476190480000
        721.934523809523800000
        1853.973214285715000000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRLabel1: TQRLabel
      Left = 16
      Top = 386
      Width = 120
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        30.238095238095240000
        729.494047619047700000
        226.785714285714300000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Tipo documento'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel30: TQRLabel
      Left = 825
      Top = 386
      Width = 112
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1559.151785714286000000
        729.494047619047700000
        211.666666666666700000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Nr. documento'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      OnPrint = QRLabel30Print
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object LB_TIPO_DOCUMENTO: TQRLabel
      Left = 17
      Top = 413
      Width = 760
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.333333333333330000
        31.750000000000000000
        780.520833333333400000
        1436.687500000000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Documento di trasporto'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      OnPrint = LB_TIPO_DOCUMENTOPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object LB_NUM_DOCUMENTO: TQRLabel
      Left = 825
      Top = 414
      Width = 158
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1559.151785714286000000
        782.410714285714200000
        298.601190476190500000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      OnPrint = LB_NUM_DOCUMENTOPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel33: TQRLabel
      Left = 16
      Top = 453
      Width = 169
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        30.238095238095240000
        856.116071428571400000
        319.389880952381000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Causale del Trasporto'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel34: TQRLabel
      Left = 825
      Top = 453
      Width = 122
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1559.151785714286000000
        856.116071428571400000
        230.565476190476200000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Data documento'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      OnPrint = QRLabel34Print
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object LB_CAUSALE: TQRLabel
      Left = 17
      Top = 479
      Width = 760
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.333333333333330000
        31.750000000000000000
        904.875000000000000000
        1436.687500000000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      OnPrint = LB_CAUSALEPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object LB_DATA_DOCUMENTO: TQRLabel
      Left = 825
      Top = 479
      Width = 158
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.333333333333330000
        1558.395833333333000000
        904.875000000000000000
        298.979166666666700000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      OnPrint = LB_DATA_DOCUMENTOPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel37: TQRLabel
      Left = 15
      Top = 520
      Width = 141
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        28.348214285714280000
        982.738095238095400000
        266.473214285714300000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Trasporto a mezzo'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel38: TQRLabel
      Left = 824
      Top = 520
      Width = 55
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1557.261904761905000000
        982.738095238095400000
        103.943452380952400000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Pagina'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object LB_TRASPORTO_A_MEZZO: TQRLabel
      Left = 15
      Top = 547
      Width = 760
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.333333333333330000
        29.104166666666670000
        1034.520833333333000000
        1436.687500000000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      OnPrint = LB_TRASPORTO_A_MEZZOPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRSysData1: TQRSysData
      Left = 824
      Top = 547
      Width = 59
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1557.261904761905000000
        1033.764880952381000000
        111.502976190476200000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      Color = clWhite
      Data = qrsPageNumber
      Transparent = False
      ExportAs = exptText
      FontSize = 10
    end
  end
  object HEADER_BAND_PRODOTTI: TQRBand
    Left = 53
    Top = 729
    Width = 1005
    Height = 30
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      56.696428571428570000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbGroupHeader
    object QRShape2: TQRShape
      Left = 11
      Top = 0
      Width = 981
      Height = 30
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Frame.Width = 0
      Size.Values = (
        55.856481481481480000
        20.578703703703700000
        0.000000000000000000
        1853.553240740741000000)
      XLColumn = 0
      Brush.Color = 16771536
      Pen.Style = psClear
      Pen.Width = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRLabel9: TQRLabel
      Left = 807
      Top = 4
      Width = 28
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1525.133928571429000000
        7.559523809523811000
        52.916666666666680000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'UM'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = True
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel10: TQRLabel
      Left = 906
      Top = 2
      Width = 69
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1712.232142857143000000
        3.779761904761905000
        130.401785714285700000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Quantit'#224
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = True
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel4: TQRLabel
      Left = 24
      Top = 5
      Width = 94
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        45.357142857142850000
        9.449404761904763000
        177.648809523809500000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Descrizione'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = True
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
  end
  object FOOTER_PRODOTTI: TQRBand
    Left = 53
    Top = 801
    Width = 1005
    Height = 6
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      11.339285714285710000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbGroupFooter
  end
  object QRSubDetail1: TQRSubDetail
    Left = 53
    Top = 759
    Width = 1005
    Height = 42
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    BeforePrint = QRSubDetail1BeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      79.375000000000000000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    Master = Owner
    DataSet = QryVociFattura
    FooterBand = FOOTER_PRODOTTI
    HeaderBand = HEADER_BAND_PRODOTTI
    PrintBefore = False
    PrintIfEmpty = False
    object LB_UNITA: TQRLabel
      Left = 807
      Top = 6
      Width = 54
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1525.133928571429000000
        11.339285714285710000
        102.053571428571400000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Unit'#224
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object LB_QUANTITA: TQRLabel
      Left = 906
      Top = 0
      Width = 63
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1712.232142857143000000
        0.000000000000000000
        119.062500000000000000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '999.99'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object DESCRIZIONE: TQRMemo
      Left = 24
      Top = 6
      Width = 777
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        45.357142857142850000
        11.339285714285710000
        1468.437500000000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -11
      Font.Name = 'Times New Roman'
      Font.Style = []
      Lines.Strings = (
        '0'
        '2'
        '3'
        '4'
        '5'
        '6')
      ParentFont = False
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 8
    end
  end
  object BAND_CAUSALE: TQRChildBand
    Left = 53
    Top = 686
    Width = 1005
    Height = 43
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    BeforePrint = BAND_CAUSALEBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      81.264880952380970000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    ParentBand = BAND_INTESTAZIONE
    PrintOrder = cboAfterParent
    object QRLabel3: TQRLabel
      Left = 15
      Top = 1
      Width = 36
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        28.348214285714280000
        1.889880952380953000
        68.035714285714290000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Note'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object NOTE_DDT: TQRMemo
      Left = 15
      Top = 25
      Width = 970
      Height = 16
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        30.238095238095240000
        28.348214285714280000
        47.247023809523810000
        1833.184523809524000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = True
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -11
      Font.Name = 'Times New Roman'
      Font.Style = []
      Lines.Strings = (
        'ABC')
      ParentFont = False
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 8
    end
  end
  object QRSubDetail2: TQRSubDetail
    Left = 53
    Top = 856
    Width = 1005
    Height = 29
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    BeforePrint = QRSubDetail2BeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      54.806547619047620000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    Master = Owner
    HeaderBand = BAND_HEADER_RATE
    PrintBefore = False
    PrintIfEmpty = True
  end
  object BAND_HEADER_RATE: TQRBand
    Left = 53
    Top = 807
    Width = 1005
    Height = 49
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    BeforePrint = BAND_HEADER_RATEBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = True
    Size.Values = (
      92.604166666666680000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbGroupHeader
  end
  object BAND_SUMMARY: TQRBand
    Left = 53
    Top = 885
    Width = 1005
    Height = 265
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    BeforePrint = BAND_SUMMARYBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      500.818452380952300000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbPageFooter
    object QRShape5: TQRShape
      Left = 234
      Top = 60
      Width = 151
      Height = 53
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        100.163690476190500000
        442.232142857142800000
        113.392857142857100000
        285.372023809523800000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRShape13: TQRShape
      Left = 536
      Top = 112
      Width = 457
      Height = 81
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        153.080357142857200000
        1012.976190476190000000
        211.666666666666700000
        863.675595238095400000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRLabel20: TQRLabel
      Left = 24
      Top = 70
      Width = 130
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        45.357142857142850000
        132.291666666666700000
        245.684523809523800000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Netto a pagare'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRShape3: TQRShape
      Left = 11
      Top = 12
      Width = 223
      Height = 52
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        98.273809523809540000
        20.788690476190480000
        22.678571428571430000
        421.443452380952400000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRShape6: TQRShape
      Left = 11
      Top = 60
      Width = 223
      Height = 53
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        100.163690476190500000
        20.788690476190480000
        113.392857142857100000
        421.443452380952400000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRShape9: TQRShape
      Left = 536
      Top = 12
      Width = 457
      Height = 53
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        100.163690476190500000
        1012.976190476190000000
        22.678571428571430000
        863.675595238095400000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRShape10: TQRShape
      Left = 536
      Top = 60
      Width = 457
      Height = 53
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        100.163690476190500000
        1012.976190476190000000
        113.392857142857100000
        863.675595238095400000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRLabel6: TQRLabel
      Left = 240
      Top = 65
      Width = 84
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        453.571428571428500000
        122.842261904761900000
        158.750000000000000000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Data inizio'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel8: TQRLabel
      Left = 19
      Top = 15
      Width = 196
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        35.907738095238090000
        28.348214285714280000
        370.416666666666700000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Aspetto esteriore dei beni'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRShape4: TQRShape
      Left = 234
      Top = 12
      Width = 151
      Height = 49
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        92.604166666666680000
        442.232142857142800000
        22.678571428571430000
        285.372023809523800000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRShape7: TQRShape
      Left = 385
      Top = 12
      Width = 151
      Height = 49
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        92.604166666666680000
        727.604166666666800000
        22.678571428571430000
        285.372023809523800000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRShape8: TQRShape
      Left = 385
      Top = 60
      Width = 151
      Height = 53
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        100.163690476190500000
        727.604166666666800000
        113.392857142857100000
        285.372023809523800000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object QRLabel11: TQRLabel
      Left = 15
      Top = 37
      Width = 179
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        28.348214285714280000
        69.925595238095240000
        338.288690476190500000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel7: TQRLabel
      Left = 240
      Top = 16
      Width = 64
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        453.571428571428500000
        30.238095238095240000
        120.952380952381000000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Nr. colli'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      OnPrint = QRLabel7Print
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel5: TQRLabel
      Left = 391
      Top = 15
      Width = 67
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        738.943452380952600000
        28.348214285714280000
        126.622023809523800000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Peso kg.'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel12: TQRLabel
      Left = 391
      Top = 65
      Width = 80
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        738.943452380952600000
        122.842261904761900000
        151.190476190476200000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Ora inizio'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel13: TQRLabel
      Left = 240
      Top = 90
      Width = 145
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        453.571428571428500000
        170.089285714285700000
        274.032738095238100000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel14: TQRLabel
      Left = 240
      Top = 37
      Width = 139
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        453.571428571428500000
        69.925595238095240000
        262.693452380952400000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel15: TQRLabel
      Left = 391
      Top = 37
      Width = 139
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        738.943452380952600000
        69.925595238095240000
        262.693452380952400000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel16: TQRLabel
      Left = 391
      Top = 90
      Width = 139
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        738.943452380952600000
        170.089285714285700000
        262.693452380952400000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel17: TQRLabel
      Left = 542
      Top = 15
      Width = 43
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1024.315476190476000000
        28.348214285714280000
        81.264880952380970000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Porto'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel18: TQRLabel
      Left = 542
      Top = 37
      Width = 309
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1024.315476190476000000
        69.925595238095240000
        583.973214285714200000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '************'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel19: TQRLabel
      Left = 547
      Top = 65
      Width = 137
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1033.764880952381000000
        122.842261904761900000
        258.913690476190500000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Firma conducente'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel21: TQRLabel
      Left = 547
      Top = 119
      Width = 143
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1033.764880952381000000
        224.895833333333300000
        270.252976190476200000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Firma destinatario'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
  end
  object QryVociFattura: TpFIBDataSet
    Left = 128
  end
  object QryGeneric: TpFIBQuery
    Left = 192
  end
  object QryFatture: TpFIBDataSet
    Left = 48
  end
end
