object QR_STAMPA_FATTURA: TQR_STAMPA_FATTURA
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
    Height = 484
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
      914.702380952381100000
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
    object QRShape42: TQRShape
      Left = 11
      Top = 208
      Width = 982
      Height = 205
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        387.425595238095200000
        20.788690476190480000
        393.095238095238100000
        1855.863095238095000000)
      XLColumn = 0
      Shape = qrsRectangle
      VertAdjust = 0
    end
    object DESTINATARIO: TQRMemo
      Left = 21
      Top = 212
      Width = 415
      Height = 78
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        147.410714285714300000
        39.687500000000000000
        400.654761904762000000
        784.300595238095400000)
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
    object LB_NOME_CAUSALE: TQRLabel
      Left = 11
      Top = 457
      Width = 990
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        20.788690476190480000
        863.675595238095400000
        1870.982142857143000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Data fattura'
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
    object QRLabel2: TQRLabel
      Left = 11
      Top = 419
      Width = 990
      Height = 31
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        58.586309523809540000
        20.788690476190480000
        791.860119047619200000
        1870.982142857143000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'CONTO CLIENTE'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -19
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 14
    end
    object MM_CONTATTI_DESTINATARIO: TQRMemo
      Left = 21
      Top = 304
      Width = 415
      Height = 97
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        183.318452380952400000
        39.687500000000000000
        574.523809523809500000
        784.300595238095400000)
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
    object MM_AMMINISTRATORE: TQRMemo
      Left = 568
      Top = 212
      Width = 415
      Height = 78
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        147.410714285714300000
        1073.452380952381000000
        400.654761904762000000
        784.300595238095400000)
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
    object MM_CONTATTI_AMMINISTRATORE: TQRMemo
      Left = 568
      Top = 304
      Width = 415
      Height = 97
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        183.318452380952400000
        1073.452380952381000000
        574.523809523809500000
        784.300595238095400000)
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
    object LB_AMM: TQRLabel
      Left = 510
      Top = 212
      Width = 45
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        963.839285714285800000
        400.654761904762000000
        85.044642857142860000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Amm:'
      Color = clWhite
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
  end
  object BAND_SUMMARY: TQRSubDetail
    Left = 53
    Top = 537
    Width = 1005
    Height = 24
    Frame.Color = clBlack
    Frame.DrawTop = True
    Frame.DrawBottom = True
    Frame.DrawLeft = True
    Frame.DrawRight = True
    AlignToBottom = False
    BeforePrint = BAND_SUMMARYBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      45.357142857142850000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    Master = Owner
    DataSet = QryVociFattura
    FooterBand = BAND_FOOTER
    PrintBefore = False
    PrintIfEmpty = False
    object QRShape4: TQRShape
      Left = 881
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1664.985119047619000000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape6: TQRShape
      Left = 766
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1447.648809523810000000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape3: TQRShape
      Left = 650
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1228.422619047619000000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape2: TQRShape
      Left = 166
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        313.720238095238100000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape1: TQRShape
      Left = 95
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        179.538690476190500000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRLabel1: TQRLabel
      Left = 2
      Top = 1
      Width = 98
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        3.779761904761905000
        1.889880952380953000
        185.208333333333300000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'DATA'
      Color = 16771536
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
    object QRLabel5: TQRLabel
      Left = 104
      Top = 1
      Width = 67
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        196.547619047619100000
        1.889880952380953000
        126.622023809523800000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'N.'
      Color = 16771536
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
    object QRLabel6: TQRLabel
      Left = 175
      Top = 1
      Width = 479
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        330.729166666666700000
        1.889880952380953000
        905.252976190476300000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'DESCRIZIONE'
      Color = 16771536
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
      Left = 890
      Top = 1
      Width = 111
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1681.994047619048000000
        1.889880952380953000
        209.776785714285700000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'SALDO'
      Color = 16771536
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
    object QRLabel7: TQRLabel
      Left = 776
      Top = 1
      Width = 111
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1466.547619047619000000
        1.889880952380953000
        209.776785714285700000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'AVERE'
      Color = 16771536
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
    object QRLabel9: TQRLabel
      Left = 660
      Top = 1
      Width = 111
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1247.321428571429000000
        1.889880952380953000
        209.776785714285700000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'DARE'
      Color = 16771536
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
  object BAND_ELENCO_FATTURE: TQRChildBand
    Left = 53
    Top = 561
    Width = 1005
    Height = 24
    Frame.Color = clBlack
    Frame.DrawTop = True
    Frame.DrawBottom = True
    Frame.DrawLeft = True
    Frame.DrawRight = True
    AlignToBottom = False
    BeforePrint = BAND_ELENCO_FATTUREBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      45.357142857142850000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    ParentBand = BAND_SUMMARY
    PrintOrder = cboAfterParent
    object LB_DATA_DOCUMENTO: TQRLabel
      Left = -1
      Top = 2
      Width = 98
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        -1.889880952380953000
        3.779761904761905000
        185.208333333333300000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Data fattura'
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
    object LB_NUMERO_DOCUMENTO: TQRLabel
      Left = 104
      Top = 2
      Width = 67
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        196.547619047619100000
        3.779761904761905000
        126.622023809523800000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'N123'
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
    object LB_SALDO: TQRLabel
      Left = 891
      Top = 2
      Width = 110
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1683.883928571429000000
        3.779761904761905000
        207.886904761904800000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Totale da pagare'
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
    object QRShape7: TQRShape
      Left = 95
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        179.538690476190500000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape8: TQRShape
      Left = 166
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        313.720238095238100000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape9: TQRShape
      Left = 650
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1228.422619047619000000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape10: TQRShape
      Left = 766
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1447.648809523810000000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape11: TQRShape
      Left = 881
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1664.985119047619000000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object LB_AVERE: TQRLabel
      Left = 776
      Top = 2
      Width = 109
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1466.547619047619000000
        3.779761904761905000
        205.997023809523800000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Totale da pagare'
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
    object LB_DARE: TQRLabel
      Left = 660
      Top = 2
      Width = 111
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1247.321428571429000000
        3.779761904761905000
        209.776785714285700000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Totale da pagare'
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
    object MM_DESCRIZIONE_DOCUMENTO: TQRMemo
      Left = 175
      Top = 2
      Width = 479
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        330.729166666666700000
        3.779761904761905000
        905.252976190476300000)
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
  end
  object QRLabel4: TQRLabel
    Left = 19
    Top = 6
    Width = 63
    Height = 22
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    Size.Values = (
      41.577380952380950000
      35.907738095238090000
      11.339285714285710000
      119.062500000000000000)
    XLColumn = 0
    Alignment = taLeftJustify
    AlignToBand = False
    AutoSize = True
    AutoStretch = False
    Caption = 'Causale'
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
  object BAND_FOOTER: TQRBand
    Left = 53
    Top = 585
    Width = 1005
    Height = 44
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
      83.154761904761910000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbGroupFooter
    object QRLabel10: TQRLabel
      Left = 552
      Top = 13
      Width = 62
      Height = 27
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        51.026785714285720000
        1043.214285714286000000
        24.568452380952380000
        117.172619047619100000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Totali:'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -15
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 11
    end
    object LB_TOTALE_DARE: TQRLabel
      Left = 660
      Top = 13
      Width = 110
      Height = 27
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        51.026785714285720000
        1247.321428571429000000
        24.568452380952380000
        207.886904761904800000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '99999.99'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -15
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 11
    end
    object LB_TOTALE_AVERE: TQRLabel
      Left = 776
      Top = 13
      Width = 109
      Height = 27
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        51.026785714285720000
        1466.547619047619000000
        24.568452380952380000
        205.997023809523800000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '99999.99'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -15
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 11
    end
    object LB_TOTALE_SALDO: TQRLabel
      Left = 891
      Top = 13
      Width = 110
      Height = 27
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        51.026785714285720000
        1683.883928571429000000
        24.568452380952380000
        207.886904761904800000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '99999.99'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -15
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 11
    end
  end
  object QryFatture: TpFIBDataSet
    Left = 48
  end
  object QryVociFattura: TpFIBDataSet
    Left = 128
  end
  object QryGeneric: TpFIBQuery
    Left = 192
  end
  object QryRateFattura: TpFIBDataSet
    Left = 269
  end
end
