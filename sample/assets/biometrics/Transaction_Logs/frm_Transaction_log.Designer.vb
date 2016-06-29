<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class frm_Transaction_log
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()> _
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    'Required by the Windows Form Designer
    Private components As System.ComponentModel.IContainer

    'NOTE: The following procedure is required by the Windows Form Designer
    'It can be modified using the Windows Form Designer.  
    'Do not modify it using the code editor.
    <System.Diagnostics.DebuggerStepThrough()> _
    Private Sub InitializeComponent()
    	Me.GroupBox2 = New System.Windows.Forms.GroupBox()
    	Me.lvLogs = New System.Windows.Forms.ListView()
    	Me.lvLogsch1 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch2 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch3 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch4 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch5 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch6 = New System.Windows.Forms.ColumnHeader()
    	Me.lbl_cur_state = New System.Windows.Forms.Label()
    	Me.GroupBox1 = New System.Windows.Forms.GroupBox()
    	Me.Label3 = New System.Windows.Forms.Label()
    	Me.txt_server_ip = New System.Windows.Forms.TextBox()
    	Me.btn_connect = New System.Windows.Forms.Button()
    	Me.Label2 = New System.Windows.Forms.Label()
    	Me.Label1 = New System.Windows.Forms.Label()
    	Me.txt_port = New System.Windows.Forms.TextBox()
    	Me.txt_bio_ip = New System.Windows.Forms.TextBox()
    	Me.GroupBox3 = New System.Windows.Forms.GroupBox()
    	Me.btn_save_logs = New System.Windows.Forms.Button()
    	Me.Label8 = New System.Windows.Forms.Label()
    	Me.txt_table_name = New System.Windows.Forms.TextBox()
    	Me.Label7 = New System.Windows.Forms.Label()
    	Me.txt_db_name = New System.Windows.Forms.TextBox()
    	Me.Label4 = New System.Windows.Forms.Label()
    	Me.txt_host_name = New System.Windows.Forms.TextBox()
    	Me.Label5 = New System.Windows.Forms.Label()
    	Me.Label6 = New System.Windows.Forms.Label()
    	Me.txt_password = New System.Windows.Forms.TextBox()
    	Me.txt_user_name = New System.Windows.Forms.TextBox()
    	Me.GroupBox2.SuspendLayout
    	Me.GroupBox1.SuspendLayout
    	Me.GroupBox3.SuspendLayout
    	Me.SuspendLayout
    	'
    	'GroupBox2
    	'
    	Me.GroupBox2.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom)  _
    	    	    	Or System.Windows.Forms.AnchorStyles.Left)  _
    	    	    	Or System.Windows.Forms.AnchorStyles.Right),System.Windows.Forms.AnchorStyles)
    	Me.GroupBox2.Controls.Add(Me.lvLogs)
    	Me.GroupBox2.Font = New System.Drawing.Font("Microsoft Sans Serif", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.GroupBox2.Location = New System.Drawing.Point(320, 12)
    	Me.GroupBox2.Name = "GroupBox2"
    	Me.GroupBox2.Size = New System.Drawing.Size(628, 497)
    	Me.GroupBox2.TabIndex = 9
    	Me.GroupBox2.TabStop = false
    	Me.GroupBox2.Text = "  Transaction Records  "
    	'
    	'lvLogs
    	'
    	Me.lvLogs.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom)  _
    	    	    	Or System.Windows.Forms.AnchorStyles.Left)  _
    	    	    	Or System.Windows.Forms.AnchorStyles.Right),System.Windows.Forms.AnchorStyles)
    	Me.lvLogs.Columns.AddRange(New System.Windows.Forms.ColumnHeader() {Me.lvLogsch1, Me.lvLogsch2, Me.lvLogsch3, Me.lvLogsch4, Me.lvLogsch5, Me.lvLogsch6})
    	Me.lvLogs.Font = New System.Drawing.Font("Microsoft Sans Serif", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.lvLogs.GridLines = true
    	Me.lvLogs.Location = New System.Drawing.Point(6, 21)
    	Me.lvLogs.Name = "lvLogs"
    	Me.lvLogs.Size = New System.Drawing.Size(616, 470)
    	Me.lvLogs.TabIndex = 10
    	Me.lvLogs.UseCompatibleStateImageBehavior = false
    	Me.lvLogs.View = System.Windows.Forms.View.Details
    	'
    	'lvLogsch1
    	'
    	Me.lvLogsch1.Text = "Count"
    	Me.lvLogsch1.Width = 75
    	'
    	'lvLogsch2
    	'
    	Me.lvLogsch2.Text = "User No."
    	Me.lvLogsch2.Width = 80
    	'
    	'lvLogsch3
    	'
    	Me.lvLogsch3.Text = "Verify Mode"
    	Me.lvLogsch3.Width = 90
    	'
    	'lvLogsch4
    	'
    	Me.lvLogsch4.Text = "In/Out Mode"
    	Me.lvLogsch4.Width = 100
    	'
    	'lvLogsch5
    	'
    	Me.lvLogsch5.Text = "Date"
    	Me.lvLogsch5.Width = 160
    	'
    	'lvLogsch6
    	'
    	Me.lvLogsch6.Text = "WorkCode"
    	Me.lvLogsch6.Width = 85
    	'
    	'lbl_cur_state
    	'
    	Me.lbl_cur_state.AutoSize = true
    	Me.lbl_cur_state.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.lbl_cur_state.ForeColor = System.Drawing.Color.MediumBlue
    	Me.lbl_cur_state.Location = New System.Drawing.Point(12, 200)
    	Me.lbl_cur_state.Name = "lbl_cur_state"
    	Me.lbl_cur_state.Size = New System.Drawing.Size(194, 18)
    	Me.lbl_cur_state.TabIndex = 14
    	Me.lbl_cur_state.Text = "Current State: Disconnected"
    	'
    	'GroupBox1
    	'
    	Me.GroupBox1.Controls.Add(Me.Label3)
    	Me.GroupBox1.Controls.Add(Me.txt_server_ip)
    	Me.GroupBox1.Controls.Add(Me.btn_connect)
    	Me.GroupBox1.Controls.Add(Me.Label2)
    	Me.GroupBox1.Controls.Add(Me.Label1)
    	Me.GroupBox1.Controls.Add(Me.txt_port)
    	Me.GroupBox1.Controls.Add(Me.txt_bio_ip)
    	Me.GroupBox1.Font = New System.Drawing.Font("Microsoft Sans Serif", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.GroupBox1.Location = New System.Drawing.Point(12, 12)
    	Me.GroupBox1.Name = "GroupBox1"
    	Me.GroupBox1.Size = New System.Drawing.Size(302, 185)
    	Me.GroupBox1.TabIndex = 13
    	Me.GroupBox1.TabStop = false
    	Me.GroupBox1.Text = "  Biometrics Connection Details  "
    	'
    	'Label3
    	'
    	Me.Label3.AutoSize = true
    	Me.Label3.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label3.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label3.Location = New System.Drawing.Point(26, 35)
    	Me.Label3.Name = "Label3"
    	Me.Label3.Size = New System.Drawing.Size(76, 18)
    	Me.Label3.TabIndex = 9
    	Me.Label3.Text = "Server IP :"
    	'
    	'txt_server_ip
    	'
    	Me.txt_server_ip.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_server_ip.Location = New System.Drawing.Point(118, 32)
    	Me.txt_server_ip.Name = "txt_server_ip"
    	Me.txt_server_ip.Size = New System.Drawing.Size(166, 24)
    	Me.txt_server_ip.TabIndex = 0
    	Me.txt_server_ip.Text = "10.120.10.139"
    	Me.txt_server_ip.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'btn_connect
    	'
    	Me.btn_connect.Font = New System.Drawing.Font("Microsoft Sans Serif", 12!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.btn_connect.Location = New System.Drawing.Point(118, 140)
    	Me.btn_connect.Name = "btn_connect"
    	Me.btn_connect.Size = New System.Drawing.Size(166, 33)
    	Me.btn_connect.TabIndex = 3
    	Me.btn_connect.Text = "Connect"
    	Me.btn_connect.UseVisualStyleBackColor = true
    	'
    	'Label2
    	'
    	Me.Label2.AutoSize = true
    	Me.Label2.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label2.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label2.Location = New System.Drawing.Point(58, 102)
    	Me.Label2.Name = "Label2"
    	Me.Label2.Size = New System.Drawing.Size(44, 18)
    	Me.Label2.TabIndex = 7
    	Me.Label2.Text = "Port :"
    	'
    	'Label1
    	'
    	Me.Label1.AutoSize = true
    	Me.Label1.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label1.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label1.Location = New System.Drawing.Point(6, 68)
    	Me.Label1.Name = "Label1"
    	Me.Label1.Size = New System.Drawing.Size(104, 18)
    	Me.Label1.TabIndex = 6
    	Me.Label1.Text = "Biometrics IP :"
    	'
    	'txt_port
    	'
    	Me.txt_port.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_port.Location = New System.Drawing.Point(118, 99)
    	Me.txt_port.Name = "txt_port"
    	Me.txt_port.Size = New System.Drawing.Size(166, 24)
    	Me.txt_port.TabIndex = 2
    	Me.txt_port.Text = "4370"
    	Me.txt_port.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'txt_bio_ip
    	'
    	Me.txt_bio_ip.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_bio_ip.Location = New System.Drawing.Point(118, 65)
    	Me.txt_bio_ip.Name = "txt_bio_ip"
    	Me.txt_bio_ip.Size = New System.Drawing.Size(166, 24)
    	Me.txt_bio_ip.TabIndex = 1
    	Me.txt_bio_ip.Text = "10.120.10.140"
    	Me.txt_bio_ip.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'GroupBox3
    	'
    	Me.GroupBox3.Controls.Add(Me.btn_save_logs)
    	Me.GroupBox3.Controls.Add(Me.Label8)
    	Me.GroupBox3.Controls.Add(Me.txt_table_name)
    	Me.GroupBox3.Controls.Add(Me.Label7)
    	Me.GroupBox3.Controls.Add(Me.txt_db_name)
    	Me.GroupBox3.Controls.Add(Me.Label4)
    	Me.GroupBox3.Controls.Add(Me.txt_host_name)
    	Me.GroupBox3.Controls.Add(Me.Label5)
    	Me.GroupBox3.Controls.Add(Me.Label6)
    	Me.GroupBox3.Controls.Add(Me.txt_password)
    	Me.GroupBox3.Controls.Add(Me.txt_user_name)
    	Me.GroupBox3.Font = New System.Drawing.Font("Microsoft Sans Serif", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.GroupBox3.Location = New System.Drawing.Point(12, 247)
    	Me.GroupBox3.Name = "GroupBox3"
    	Me.GroupBox3.Size = New System.Drawing.Size(302, 262)
    	Me.GroupBox3.TabIndex = 15
    	Me.GroupBox3.TabStop = false
    	Me.GroupBox3.Text = "  Database Connection Details  "
    	'
    	'btn_save_logs
    	'
    	Me.btn_save_logs.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.btn_save_logs.Location = New System.Drawing.Point(16, 214)
    	Me.btn_save_logs.Name = "btn_save_logs"
    	Me.btn_save_logs.Size = New System.Drawing.Size(268, 35)
    	Me.btn_save_logs.TabIndex = 9
    	Me.btn_save_logs.Text = "Save Logs Not Save to DB"
    	Me.btn_save_logs.UseVisualStyleBackColor = true
    	'
    	'Label8
    	'
    	Me.Label8.AutoSize = true
    	Me.Label8.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label8.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label8.Location = New System.Drawing.Point(13, 172)
    	Me.Label8.Name = "Label8"
    	Me.Label8.Size = New System.Drawing.Size(96, 18)
    	Me.Label8.TabIndex = 13
    	Me.Label8.Text = "Table Name :"
    	'
    	'txt_table_name
    	'
    	Me.txt_table_name.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_table_name.Location = New System.Drawing.Point(118, 169)
    	Me.txt_table_name.Name = "txt_table_name"
    	Me.txt_table_name.Size = New System.Drawing.Size(166, 24)
    	Me.txt_table_name.TabIndex = 8
    	Me.txt_table_name.Text = "tk_biometric_log"
    	Me.txt_table_name.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'Label7
    	'
    	Me.Label7.AutoSize = true
    	Me.Label7.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label7.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label7.Location = New System.Drawing.Point(30, 138)
    	Me.Label7.Name = "Label7"
    	Me.Label7.Size = New System.Drawing.Size(81, 18)
    	Me.Label7.TabIndex = 11
    	Me.Label7.Text = "DB Name :"
    	'
    	'txt_db_name
    	'
    	Me.txt_db_name.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_db_name.Location = New System.Drawing.Point(118, 135)
    	Me.txt_db_name.Name = "txt_db_name"
    	Me.txt_db_name.Size = New System.Drawing.Size(166, 24)
    	Me.txt_db_name.TabIndex = 7
    	Me.txt_db_name.Text = "intra"
    	Me.txt_db_name.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'Label4
    	'
    	Me.Label4.AutoSize = true
    	Me.Label4.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label4.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label4.Location = New System.Drawing.Point(19, 35)
    	Me.Label4.Name = "Label4"
    	Me.Label4.Size = New System.Drawing.Size(92, 18)
    	Me.Label4.TabIndex = 9
    	Me.Label4.Text = "Host Name :"
    	'
    	'txt_host_name
    	'
    	Me.txt_host_name.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_host_name.Location = New System.Drawing.Point(118, 32)
    	Me.txt_host_name.Name = "txt_host_name"
    	Me.txt_host_name.ReadOnly = true
    	Me.txt_host_name.Size = New System.Drawing.Size(166, 24)
    	Me.txt_host_name.TabIndex = 4
    	Me.txt_host_name.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'Label5
    	'
    	Me.Label5.AutoSize = true
    	Me.Label5.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label5.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label5.Location = New System.Drawing.Point(26, 105)
    	Me.Label5.Name = "Label5"
    	Me.Label5.Size = New System.Drawing.Size(83, 18)
    	Me.Label5.TabIndex = 7
    	Me.Label5.Text = "Password :"
    	'
    	'Label6
    	'
    	Me.Label6.AutoSize = true
    	Me.Label6.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.Label6.ForeColor = System.Drawing.SystemColors.ControlText
    	Me.Label6.Location = New System.Drawing.Point(19, 71)
    	Me.Label6.Name = "Label6"
    	Me.Label6.Size = New System.Drawing.Size(92, 18)
    	Me.Label6.TabIndex = 6
    	Me.Label6.Text = "User Name :"
    	'
    	'txt_password
    	'
    	Me.txt_password.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_password.Location = New System.Drawing.Point(118, 102)
    	Me.txt_password.Name = "txt_password"
    	Me.txt_password.PasswordChar = Global.Microsoft.VisualBasic.ChrW(42)
    	Me.txt_password.Size = New System.Drawing.Size(166, 24)
    	Me.txt_password.TabIndex = 6
    	Me.txt_password.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'txt_user_name
    	'
    	Me.txt_user_name.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.txt_user_name.Location = New System.Drawing.Point(118, 68)
    	Me.txt_user_name.Name = "txt_user_name"
    	Me.txt_user_name.Size = New System.Drawing.Size(166, 24)
    	Me.txt_user_name.TabIndex = 5
    	Me.txt_user_name.Text = "root"
    	Me.txt_user_name.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'frm_Transaction_log
    	'
    	Me.AutoScaleDimensions = New System.Drawing.SizeF(6!, 13!)
    	Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
    	Me.BackColor = System.Drawing.Color.Gainsboro
    	Me.ClientSize = New System.Drawing.Size(960, 519)
    	Me.Controls.Add(Me.GroupBox3)
    	Me.Controls.Add(Me.lbl_cur_state)
    	Me.Controls.Add(Me.GroupBox1)
    	Me.Controls.Add(Me.GroupBox2)
    	Me.Name = "frm_Transaction_log"
    	Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
    	Me.Text = "Transaction Logs"
    	Me.GroupBox2.ResumeLayout(false)
    	Me.GroupBox1.ResumeLayout(false)
    	Me.GroupBox1.PerformLayout
    	Me.GroupBox3.ResumeLayout(false)
    	Me.GroupBox3.PerformLayout
    	Me.ResumeLayout(false)
    	Me.PerformLayout
    End Sub
    Friend WithEvents GroupBox2 As System.Windows.Forms.GroupBox
    Private WithEvents lvLogs As System.Windows.Forms.ListView
    Private WithEvents lvLogsch1 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch2 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch3 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch4 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch5 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch6 As System.Windows.Forms.ColumnHeader
    Friend WithEvents lbl_cur_state As System.Windows.Forms.Label
    Friend WithEvents GroupBox1 As System.Windows.Forms.GroupBox
    Friend WithEvents Label3 As System.Windows.Forms.Label
    Friend WithEvents txt_server_ip As System.Windows.Forms.TextBox
    Friend WithEvents btn_connect As System.Windows.Forms.Button
    Friend WithEvents Label2 As System.Windows.Forms.Label
    Friend WithEvents Label1 As System.Windows.Forms.Label
    Friend WithEvents txt_port As System.Windows.Forms.TextBox
    Friend WithEvents txt_bio_ip As System.Windows.Forms.TextBox
    Friend WithEvents GroupBox3 As System.Windows.Forms.GroupBox
    Friend WithEvents Label4 As System.Windows.Forms.Label
    Friend WithEvents txt_host_name As System.Windows.Forms.TextBox
    Friend WithEvents Label5 As System.Windows.Forms.Label
    Friend WithEvents Label6 As System.Windows.Forms.Label
    Friend WithEvents txt_password As System.Windows.Forms.TextBox
    Friend WithEvents txt_user_name As System.Windows.Forms.TextBox
    Friend WithEvents Label7 As System.Windows.Forms.Label
    Friend WithEvents txt_db_name As System.Windows.Forms.TextBox
    Friend WithEvents btn_save_logs As System.Windows.Forms.Button
    Friend WithEvents Label8 As System.Windows.Forms.Label
    Friend WithEvents txt_table_name As System.Windows.Forms.TextBox

End Class
