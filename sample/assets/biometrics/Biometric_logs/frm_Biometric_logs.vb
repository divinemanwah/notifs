
Imports System.Net
Imports System.Threading
Imports System.Web
Imports System.Security.Cryptography
Imports System.Text
Imports System.IO


Public Class frm_Biometric_logs

    'Create Standalone SDK class dynamicly.
    Public zk_fun As New zkemkeeper.CZKEM

    Private bIsConnected = False 'the boolean value identifies whether the device is connected
    Private iMachineNumber As Integer 'the serial number of the device.After connecting the device ,this value will be changed.

    Public iGLCount As Integer 'count number of log
    Public lvItem As New ListViewItem("Items", 0) 'listview alias

    Public server_ip As String = ""
    Public biometric_ip As String = ""
    Public idwErrorCode As Integer
    
    Private Sub frm_Biometric_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        lvLogs.FullRowSelect = True
        lvLogs.AllowColumnReorder = True

		lvLogs.Items.Clear()
    End Sub


    Private Sub btn_connect_Click(sender As Object, e As EventArgs) Handles btn_connect.Click

        If txt_bio_ip.Text.Trim() = "" Or txt_port.Text.Trim() = "" Then
            MsgBox("IP and Port cannot be null", MsgBoxStyle.Exclamation, "Error")
            Return
        End If

        Cursor = Cursors.WaitCursor
        
        
        'Disconnecting to Device
        If btn_connect.Text = "Disconnect" Then
        	zk_fun.Disconnect()

            RemoveHandler zk_fun.OnVerify, AddressOf RT_fun_OnVerify
            RemoveHandler zk_fun.OnAttTransactionEx, AddressOf RT_fun_OnAttTransactionEx
			
            bIsConnected = False
            btn_connect.Text = "Connect"
            lbl_cur_state.Text = "Current State: Disconnected"
            
            txt_server_ip.ReadOnly = False
            txt_bio_ip.ReadOnly = False
            txt_port.ReadOnly = False
            
            server_ip = ""
            biometric_ip = ""

            Cursor = Cursors.Default
            Return
        End If
		
		
		'Connecting to Device
        bIsConnected = zk_fun.Connect_Net(txt_bio_ip.Text.Trim(), Convert.ToInt32(txt_port.Text.Trim()))
		
        If bIsConnected = True Then
        	btn_connect.Refresh()
            
            iMachineNumber = 1 'In fact,when you are using the tcp/ip communication,this parameter will be ignored,that is any integer will all right.Here we use 1.
			
            server_ip = txt_server_ip.Text
            biometric_ip = txt_bio_ip.Text
			
            If zk_fun.RegEvent(iMachineNumber, 65535) = True Then 'Here you can register the realtime events that you want to be triggered(the parameters 65535 means registering all)
                AddHandler zk_fun.OnVerify, AddressOf RT_fun_OnVerify
                AddHandler zk_fun.OnAttTransactionEx, AddressOf RT_fun_OnAttTransactionEx
            End If
            
            btn_connect.Text = "Disconnect"
            lbl_cur_state.Text = "Current State: Connected"
            
            txt_server_ip.ReadOnly = True
            txt_bio_ip.ReadOnly = True
            txt_port.ReadOnly = True
            
            zk_fun.EnableDevice(iMachineNumber, True) 'enable the device
        Else
            zk_fun.GetLastError(idwErrorCode)
            MsgBox("Unable to connect the device,ErrorCode=" & idwErrorCode, MsgBoxStyle.Exclamation, "Error")
        End If

        Cursor = Cursors.Default
    End Sub

	
    'After you have placed your finger on the sensor(or swipe your card to the device),this event will be triggered.
    'If you passes the verification,the returned value userid will be the user enrollnumber,or else the value will be -1;
    Private Sub RT_fun_OnVerify(ByVal iUserID As Integer)

        If iUserID = -1 Then
            Dim iYear As String = DateTime.Now.ToString("yyyy")
            Dim iMonth As String = DateTime.Now.ToString("MM")
            Dim iDay As String = DateTime.Now.ToString("dd")
            Dim iHour As String = DateTime.Now.ToString("HH")
            Dim iMinute As String = DateTime.Now.ToString("mm")
            Dim iSecond As String = DateTime.Now.ToString("ss")
            Dim sDate As String = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss")

            Call lv_new_log(iUserID, "Error", "Error", sDate, "Error")

            Call Save_Log(iUserID, iUserID, sDate, iYear, iMonth, iDay, iHour, iMinute, iSecond)
        End If
    End Sub


    'If your fingerprint(or your card) passes the verification,this event will be triggered
    Private Sub RT_fun_OnAttTransactionEx(ByVal sEnrollNumber As String, ByVal iIsInValid As Integer, ByVal iAttState As Integer, ByVal iVerifyMethod As Integer, _
                      ByVal iYear As Integer, ByVal iMonth As Integer, ByVal iDay As Integer, ByVal iHour As Integer, ByVal iMinute As Integer, ByVal iSecond As Integer, ByVal iWorkCode As Integer)

        Dim sAttState As String = Att_State(iAttState)

        Dim sDate As String = Number_Format(iYear) & "-" + Number_Format(iMonth) & "-" & Number_Format(iDay) & " " & Number_Format(iHour) & ":" & Number_Format(iMinute) & ":" & Number_Format(iSecond)

        Call lv_new_log(sEnrollNumber, iVerifyMethod, sAttState, sDate, iWorkCode)

        Call Save_Log(sEnrollNumber, iAttState, sDate, iYear, iMonth, iDay, iHour, iMinute, iSecond)
    End Sub


    ' Save log
    Public Sub Save_Log(sEnrollNumber, iAttState, sDate, iYear, iMonth, iDay, iHour, iMinute, iSecond)

        Using md5Hash As MD5 = MD5.Create()

            Dim compileLog As String = sEnrollNumber & iAttState & sDate

            ' Encrypt date with enroll number and in/out mode
            Dim sHash As String = GetMd5Hash(md5Hash, compileLog)

            ' Create a request using a URL that can receive a post. 
            Dim request As WebRequest = WebRequest.Create("http://" & server_ip & "/save_biometric_logs.php")

            ' Set the Method property of the request to POST.
            request.Method = "POST"

            ' Create POST data
            Dim postData As String = "serverIP=" & HttpUtility.UrlEncode(server_ip) &
                                     "&sEnrollNumber=" & HttpUtility.UrlEncode(sEnrollNumber) &
                                     "&iAttState=" & HttpUtility.UrlEncode(iAttState) &
                                     "&sDate=" & HttpUtility.UrlEncode(sDate) &
                                     "&sHash=" & HttpUtility.UrlEncode(sHash) &
                                     "&iYear=" & HttpUtility.UrlEncode(iYear) &
                                     "&iMonth=" & HttpUtility.UrlEncode(iMonth) &
                                     "&iDay=" & HttpUtility.UrlEncode(iDay) &
                                     "&iHour=" & HttpUtility.UrlEncode(iHour) &
                                     "&iMinute=" & HttpUtility.UrlEncode(iMinute) &
                                     "&iSecond=" & HttpUtility.UrlEncode(iSecond)

            ' Convert the doc string into a byte array
            Dim bytes As Byte() = Encoding.UTF8.GetBytes(postData)

            ' Set the ContentType property of the WebRequest.
            request.ContentType = "application/x-www-form-urlencoded"

            ' Assign the content length
            request.ContentLength = bytes.Length

            ' Get the request stream.
            Dim dataStream As Stream = request.GetRequestStream()

            ' Write the data to the request stream.
            dataStream.Write(bytes, 0, bytes.Length)

            ' Close the Stream object.
            dataStream.Close()

            ' Get the response.
            Dim response As WebResponse = request.GetResponse()

            ' Display the status.
            ' Console.WriteLine(CType(response, HttpWebResponse).StatusDescription)

            ' Get the stream containing content returned by the server.
            dataStream = response.GetResponseStream()

            ' Open the stream using a StreamReader for easy access.
            Dim reader As New StreamReader(dataStream)

            ' Read the content.
            Dim responseFromServer As String = reader.ReadToEnd()

            ' Display the content.
            ' Console.WriteLine(responseFromServer)

            ' Clean up the streams.
            reader.Close()
            dataStream.Close()
            response.Close()

        End Using
    End Sub
    
    
    'Create a Md5Hash
    Public Function GetMd5Hash(ByVal md5Hash As MD5, ByVal input As String) As String
        ' Convert the input string to a byte array and compute the hash. 
        Dim data As Byte() = md5Hash.ComputeHash(Encoding.UTF8.GetBytes(input))

        ' Create a new Stringbuilder to collect the bytes 
        ' and create a string. 
        Dim sBuilder As New StringBuilder()

        ' Loop through each byte of the hashed data  
        ' and format each one as a hexadecimal string. 
        Dim i As Integer
        For i = 0 To data.Length - 1
            sBuilder.Append(data(i).ToString("x2"))
        Next i

        ' Return the hexadecimal string. 
        Return sBuilder.ToString()

    End Function 'GetMd5Hash
    
    
		
	
#Region "Save Logs By Batch"
    Private Sub btn_save_logs_Click(sender As Object, e As EventArgs)

        If bIsConnected = False Then
            MsgBox("Please connect the device first", MsgBoxStyle.Exclamation, "Error")
            Return
        End If
        
        Dim rec As String = ""
        Dim sEnrollNumber As String = ""
        Dim iVerifyMethod As Integer
        Dim iAttState As Integer
        Dim iYear As Integer
        Dim iMonth As Integer
        Dim iDay As Integer
        Dim iHour As Integer
        Dim iMinute As Integer
        Dim iSecond As Integer
        Dim iWorkcode As Integer
		
        Dim idwErrorCode As Integer
        Dim recCnt As Integer
        Dim iValue = 0
        
        Cursor = Cursors.WaitCursor
        lvLogs.Items.Clear()
        iGLCount = 0
        
        zk_fun.EnableDevice(iMachineNumber, False) 'disable the device

        If zk_fun.ReadGeneralLogData(iMachineNumber) Then 'read all the attendance records to the memory
        	
        	If zk_fun.GetDeviceStatus(iMachineNumber, 6, iValue) = True Then 'Here we use the function "GetDeviceStatus" to get the record's count.The parameter "Status" is 6.
	            recCnt = iValue
	        End If
	        
	        Dim arr_rec(recCnt - 1) As String
	        
	        Dim i = 0
	        
	        MsgBox(recCnt)
	        
	        If recCnt > 0 Then
            'get records from the memory
	            While zk_fun.SSR_GetGeneralLogData(iMachineNumber, sEnrollNumber, iVerifyMethod, iAttState, iYear, iMonth, iDay, iHour, iMinute, iSecond, iWorkcode)
					
	                arr_rec(i) = "recs[]=" & HttpUtility.UrlEncode(sEnrollNumber & "|" & iAttState & "|" & iYear & "|" & iMonth & "|" & iDay & "|" & iHour & "|" & iMinute & "|" & iSecond)
	                
	                i = i + 1
	            End While
	            
	            Dim recs As String = Strings.Join(arr_rec, "&")
	            Call Save_Log_By_Batch(recs)
	        End If

        Else
            Cursor = Cursors.Default
            zk_fun.GetLastError(idwErrorCode)
            If idwErrorCode <> 0 Then
                MsgBox("Reading data from terminal failed,ErrorCode: " & idwErrorCode, MsgBoxStyle.Exclamation, "Error")
            Else
                MsgBox("No data from terminal returns!", MsgBoxStyle.Exclamation, "Error")
            End If
        End If

        zk_fun.EnableDevice(iMachineNumber, True) 'enable the device

        Cursor = Cursors.Default
    End Sub
    
    
	Public Sub Save_Log_By_Batch(rec)
		
        ' Create a request using a URL that can receive a post. 
        Dim request As WebRequest = WebRequest.Create("http://" & server_ip & "/biometrics_record.php")
		
        ' Set the Method property of the request to POST.
        request.Method = "POST"
		
        ' Create POST data
        Dim postData As String = rec
        
        ' Convert the doc string into a byte array
        Dim bytes As Byte() = Encoding.UTF8.GetBytes(postData)

        ' Set the ContentType property of the WebRequest.
        request.ContentType = "application/x-www-form-urlencoded"

        ' Assign the content length
        request.ContentLength = bytes.Length

        ' Get the request stream.
        Dim dataStream As Stream = request.GetRequestStream()

        ' Write the data to the request stream.
        dataStream.Write(bytes, 0, bytes.Length)

        ' Close the Stream object.
        dataStream.Close()
		
        ' Get the response.
        Dim response As WebResponse = request.GetResponse()

        ' Display the status.
        ' Console.WriteLine(CType(response, HttpWebResponse).StatusDescription)

        ' Get the stream containing content returned by the server.
        dataStream = response.GetResponseStream()

        ' Open the stream using a StreamReader for easy access.
        Dim reader As New StreamReader(dataStream)

        ' Read the content.
        Dim responseFromServer As String = reader.ReadToEnd()

        ' Display the content.
        ' Console.WriteLine(responseFromServer)

        ' Clean up the streams.
        reader.Close()
        dataStream.Close()
        response.Close()
	End Sub
#End Region	
	
	
#Region "Back-up Biometrics Logs"
	 Sub Btn_back_up_logs_Click(sender As Object, e As EventArgs)
    	If bIsConnected = False Then
            MsgBox("Please connect the device first", MsgBoxStyle.Exclamation, "Error")
            Return
        End If
        
        Dim rec As String = ""
        Dim sEnrollNumber As String = ""
        Dim iVerifyMethod As Integer
        Dim iAttState As Integer
        Dim iYear As Integer
        Dim iMonth As Integer
        Dim iDay As Integer
        Dim iHour As Integer
        Dim iMinute As Integer
        Dim iSecond As Integer
        Dim iWorkcode As Integer
		
        Dim idwErrorCode As Integer
        Dim recCnt As Integer
        Dim iValue = 0
        
        Cursor = Cursors.WaitCursor
        lvLogs.Items.Clear()
        iGLCount = 0
        
        zk_fun.EnableDevice(iMachineNumber, False) 'disable the device

        If zk_fun.ReadGeneralLogData(iMachineNumber) Then 'read all the attendance records to the memory
        	
        	If zk_fun.GetDeviceStatus(iMachineNumber, 6, iValue) = True Then 'Here we use the function "GetDeviceStatus" to get the record's count.The parameter "Status" is 6.
	            recCnt = iValue
	        End If
	        
	        Dim arr_rec(recCnt - 1) As String
	        
	        Dim i = 0
	        
	        MsgBox(recCnt)
	        
	        If recCnt > 0 Then
            'get records from the memory
	            While zk_fun.SSR_GetGeneralLogData(iMachineNumber, sEnrollNumber, iVerifyMethod, iAttState, iYear, iMonth, iDay, iHour, iMinute, iSecond, iWorkcode)
					
	                arr_rec(i) = "recs[]=" & HttpUtility.UrlEncode(sEnrollNumber & "|" & iAttState & "|" & iYear & "|" & iMonth & "|" & iDay & "|" & iHour & "|" & iMinute & "|" & iSecond)
	                
	                i = i + 1
	            End While
	            
	            Dim recs As String = Strings.Join(arr_rec, "&")
	            Call biometrics_log_backup(recs)
	        End If

        Else
            Cursor = Cursors.Default
            zk_fun.GetLastError(idwErrorCode)
            If idwErrorCode <> 0 Then
                MsgBox("Reading data from terminal failed,ErrorCode: " & idwErrorCode, MsgBoxStyle.Exclamation, "Error")
            Else
                MsgBox("No data from terminal returns!", MsgBoxStyle.Exclamation, "Error")
            End If
        End If

        zk_fun.EnableDevice(iMachineNumber, True) 'enable the device

        Cursor = Cursors.Default
    End Sub
    
    
	Public Sub biometrics_log_backup(rec)
		
        ' Create a request using a URL that can receive a post. 
        Dim request As WebRequest = WebRequest.Create("http://" & server_ip & "/biometrics_log_backup.php")
		
        ' Set the Method property of the request to POST.
        request.Method = "POST"
		
        ' Create POST data
        Dim postData As String = rec
        
        ' Convert the doc string into a byte array
        Dim bytes As Byte() = Encoding.UTF8.GetBytes(postData)

        ' Set the ContentType property of the WebRequest.
        request.ContentType = "application/x-www-form-urlencoded"

        ' Assign the content length
        request.ContentLength = bytes.Length

        ' Get the request stream.
        Dim dataStream As Stream = request.GetRequestStream()

        ' Write the data to the request stream.
        dataStream.Write(bytes, 0, bytes.Length)

        ' Close the Stream object.
        dataStream.Close()
		
        ' Get the response.
        Dim response As WebResponse = request.GetResponse()

        ' Display the status.
        ' Console.WriteLine(CType(response, HttpWebResponse).StatusDescription)

        ' Get the stream containing content returned by the server.
        dataStream = response.GetResponseStream()

        ' Open the stream using a StreamReader for easy access.
        Dim reader As New StreamReader(dataStream)

        ' Read the content.
        Dim responseFromServer As String = reader.ReadToEnd()

        ' Display the content.
        ' Console.WriteLine(responseFromServer)

        ' Clean up the streams.
        reader.Close()
        dataStream.Close()
        response.Close()
	End Sub
	
#End Region
	
	
#Region "Clear Logs"
	Sub Btn_clear_logs_Click(sender As Object, e As EventArgs)
    	If bIsConnected = False Then
            MsgBox("Please connect the device first", MsgBoxStyle.Exclamation, "Error")
            Return
    	End If
    	
        Dim idwErrorCode As Integer

        lvLogs.Items.Clear()
        zk_fun.EnableDevice(iMachineNumber, False) 'disable the device
        
        If zk_fun.ClearGLog(iMachineNumber) = True Then
            zk_fun.RefreshData(iMachineNumber) 'the data in the device should be refreshed
            MsgBox("All att Logs have been cleared from device!", MsgBoxStyle.Information, "Success")
        Else
            zk_fun.GetLastError(idwErrorCode)
            MsgBox("Operation failed,ErrorCode=" & idwErrorCode, MsgBoxStyle.Exclamation, "Error")
        End If

        zk_fun.EnableDevice(iMachineNumber, True) 'enable the device
    End Sub
    
#End Region
	
	
	
    'In/Out Mode value
    Public Function Att_State(iAttState)
        Select Case iAttState
            Case 0 : Return "Check-In"
            Case 1 : Return "Check-Out"
            Case 2 : Return "Break-Out"
            Case 3 : Return "Break-In"
            Case 4 : Return "OT-In"
            Case 5 : Return "OT-out"
            Case Else : Return "Error"
        End Select
    End Function


    'Number formating (For String Used)
    Public Function Number_Format(num)
        Dim sNum

        Select Case num
            Case 0 : sNum = "00"
            Case 1 : sNum = "01"
            Case 2 : sNum = "02"
            Case 3 : sNum = "03"
            Case 4 : sNum = "04"
            Case 5 : sNum = "05"
            Case 6 : sNum = "06"
            Case 7 : sNum = "07"
            Case 8 : sNum = "08"
            Case 9 : sNum = "09"
            Case Else : sNum = num.ToString()
        End Select

        Return sNum
    End Function


    'Add new log in listview
    Public Sub lv_new_log(sEnrollNumber, iVerifyMethod, sAttState, sDate, iWorkCode)
        iGLCount += 1

        lvItem = lvLogs.Items.Add(iGLCount)
        lvItem.SubItems.Add(sEnrollNumber)
        lvItem.SubItems.Add(iVerifyMethod)
        lvItem.SubItems.Add(sAttState)
        lvItem.SubItems.Add(sDate)
        lvItem.SubItems.Add(iWorkCode)
    End Sub

    
    
    
    
   
    
    
End Class
