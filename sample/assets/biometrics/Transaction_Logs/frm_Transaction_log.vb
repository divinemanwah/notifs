
Imports MySql.Data.MySqlClient
Imports System.Net
Imports System.Threading
Imports System.Web
Imports System.Security.Cryptography
Imports System.Text
Imports System.IO


Public Class frm_Transaction_log

    'create MySQL connection
    Private MySqlCon As New MySqlConnection

    'Create Standalone SDK class dynamicly.
    Public zk_fun As New zkemkeeper.CZKEM

    Private bIsConnected = False 'the boolean value identifies whether the device is connected
    Private iMachineNumber As Integer 'the serial number of the device.After connecting the device ,this value will be changed.

    Public iGLCount As Integer 'count number of log
    Public lvItem As New ListViewItem("Items", 0) 'listview alias

    Public server_ip As String = ""
    Public biometric_ip As String = ""

    Private Sub frm_Transaction_log_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        lvLogs.FullRowSelect = True
        lvLogs.AllowColumnReorder = True

        lvLogs.Items.Clear()

        txt_host_name.Text = txt_server_ip.Text
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
        Dim idwErrorCode As Integer
        bIsConnected = zk_fun.Connect_Net(txt_bio_ip.Text.Trim(), Convert.ToInt32(txt_port.Text.Trim()))

        If bIsConnected = True Then
            btn_connect.Refresh()
            
            iMachineNumber = 1 'In fact,when you are using the tcp/ip communication,this parameter will be ignored,that is any integer will all right.Here we use 1.

            server_ip = txt_server_ip.Text
            biometric_ip = txt_bio_ip.Text

            zk_fun.RegEvent(iMachineNumber, 65535) 'Here you can register the realtime events that you want to be triggered(the parameters 65535 means registering all)
            
            btn_connect.Text = "Disconnect"
            lbl_cur_state.Text = "Current State: Connected"
            
            txt_server_ip.ReadOnly = True
            txt_bio_ip.ReadOnly = True
            txt_port.ReadOnly = True
            
        Else
            zk_fun.GetLastError(idwErrorCode)
            MsgBox("Unable to connect the device,ErrorCode=" & idwErrorCode, MsgBoxStyle.Exclamation, "Error")
        End If

        Cursor = Cursors.Default

    End Sub


    'Save Logs for those Logs not save to Database
    Private Sub btn_save_logs_Click(sender As Object, e As EventArgs) Handles btn_save_logs.Click

        If bIsConnected = False Then
            MsgBox("Please connect the device first", MsgBoxStyle.Exclamation, "Error")
            Return
        End If

'        'Check Connection for Database
'        If (DB_Connection() = False) Then
'            Return
'        End If
        
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
        
        'zk_fun.EnableDevice(iMachineNumber, True) 'disable the device

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
	                
	                
'	                Dim sDate As String = Number_Format(iYear) & "-" + Number_Format(iMonth) & "-" & Number_Format(iDay) & " " & Number_Format(iHour) & ":" & Number_Format(iMinute) & ":" & Number_Format(iSecond)
'	                
'	                Dim sAttState As String = Att_State(iAttState)
'					
'	                If (validate_logs_to_DB(sEnrollNumber, sDate) = False) Then
'	                    Call lv_new_log(sEnrollNumber, iVerifyMethod, sAttState, sDate, iWorkcode)
'	
'	                    Call Save_Log(sEnrollNumber, iAttState, sDate, iYear, iMonth, iDay, iHour, iMinute, iSecond)
'	                End If

	            End While
	            
	            Dim recs As String = Strings.Join(arr_rec, "&")
	            Call Save_Log(recs)
	        End If
	        
'            MySqlCon.Close()

        Else
            Cursor = Cursors.Default
            zk_fun.GetLastError(idwErrorCode)
            If idwErrorCode <> 0 Then
                MsgBox("Reading data from terminal failed,ErrorCode: " & idwErrorCode, MsgBoxStyle.Exclamation, "Error")
            Else
                MsgBox("No data from terminal returns!", MsgBoxStyle.Exclamation, "Error")
            End If
        End If

        'zk_fun.EnableDevice(iMachineNumber, True) 'enable the device

        Cursor = Cursors.Default
    End Sub



	'used in record array type
	Public Sub Save_Log(rec)
		
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




    'Check Connection for Database
    Public Function DB_Connection()
        Dim sUid As String = txt_user_name.Text
        Dim sPwd As String = txt_password.Text
        Dim sDB As String = txt_db_name.Text

        Try
            MySqlCon.ConnectionString = "Server=" & server_ip & "; Uid=" & sUid & "; Pwd=" & sPwd & "; Database=" & sDB

            MySqlCon.Open()
            MySqlCon.Close()

            MsgBox("Connection to database is okay.", MsgBoxStyle.Information, "Connected")
            Return True

        Catch ex As Exception
            MsgBox(ex.Message)
            Return False
        End Try

    End Function


    'Validate transation log if records already save to DB
    Public Function validate_logs_to_DB(sEnrollNumber, sDate)
        Dim cnt As Integer = 0
		Dim tbl As String = txt_table_name.Text
        
        Try
            MySqlCon.Open()
			
            Dim sqlCmd As New MySqlCommand
			
            Dim sqlText As String = "Select Count(*) 'cnt' From " & tbl & " Where enroll_number = " & sEnrollNumber & " and dt_log = '" & sDate & "' "
			
            With sqlCmd
                .Connection = MySqlCon
                .CommandText = sqlText
            End With
			
            Dim sqlReader As MySqlDataReader = sqlCmd.ExecuteReader()
			
            If sqlReader.Read = True Then
                cnt = sqlReader("cnt")
            End If
			
            MySqlCon.Close()
			
            If cnt > 0 Then
                Return True
            Else
                Return False
            End If
			
        Catch ex As Exception
            Return False
        End Try

    End Function
	

	' Save log per record
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

        lvItem = lvLogs.Items.Add(iGLCount.ToString())
        lvItem.SubItems.Add(sEnrollNumber)
        lvItem.SubItems.Add(iVerifyMethod.ToString())
        lvItem.SubItems.Add(sAttState)
        lvItem.SubItems.Add(sDate)
        lvItem.SubItems.Add(iWorkCode.ToString())
    End Sub


    Private Sub txt_server_ip_TextChanged(sender As Object, e As EventArgs) Handles txt_server_ip.TextChanged
        txt_host_name.Text = txt_server_ip.Text
    End Sub






    ''Save Log to database
    'Public Sub Save_Logs(sEnrollNumber, iAttState, sDate, iYear, iMonth, iDay, iHour, iMinute, iSecond)
        'Try
            'Dim sqlCmd As New MySqlCommand
            'Dim sqlText
'
            'MySqlCon.Open()
'
            'sqlText = "     INSERT INTO biometric_log   " & _
                        '"   (                           " & _
                        '"       enroll_number,          " & _
                        '"       in_out_mode,            " & _
                        '"       year_log,               " & _
                        '"       month_log,              " & _
                        '"       day_log,                " & _
                        '"       hour_log,               " & _
                        '"       min_log,                " & _
                        '"       sec_log,                " & _
                        '"       dt_log,                 " & _
                        '"       client_ip,              " & _
                        '"       is_valid,               " & _
                        '"       is_late_save            " & _
                        '"   )                           " & _
                        '"   VALUES                      " & _
                        '"   (                           " & _
                        '" " & sEnrollNumber & ",        " & _
                        '" " & iAttState & ",            " & _
                        '" " & iYear & ",                " & _
                        '" " & iMonth & ",               " & _
                        '" " & iDay & ",                 " & _
                        '" " & iHour & ",                " & _
                        '" " & iMinute & ",              " & _
                        '" " & iSecond & ",              " & _
                        '"'" & sDate & "',               " & _
                        '"'" & server_ip & "',           " & _
                        '"   1,                          " & _
                        '"   1                           " & _
                        '"   )                           "
'
            'With sqlCmd
                '.Connection = MySqlCon
                '.CommandText = sqlText
                '.ExecuteNonQuery()
            'End With
'
            ''Return True
'
            'MySqlCon.Close()
'
        'Catch ex As Exception
            ''Return False
'
        'End Try
    'End Sub

End Class
