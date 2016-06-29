<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class frm_Biometric_logs
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
    	Me.lvLogsch6 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch4 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch3 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch2 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogsch1 = New System.Windows.Forms.ColumnHeader()
    	Me.lvLogs = New System.Windows.Forms.ListView()
    	Me.lvLogsch5 = New System.Windows.Forms.ColumnHeader()
    	Me.GroupBox2 = New System.Windows.Forms.GroupBox()
    	Me.lbl_cur_state = New System.Windows.Forms.Label()
    	Me.Label3 = New System.Windows.Forms.Label()
    	Me.txt_server_ip = New System.Windows.Forms.TextBox()
    	Me.btn_connect = New System.Windows.Forms.Button()
    	Me.Label2 = New System.Windows.Forms.Label()
    	Me.Label1 = New System.Windows.Forms.Label()
    	Me.txt_port = New System.Windows.Forms.TextBox()
    	Me.txt_bio_ip = New System.Windows.Forms.TextBox()
    	Me.GroupBox1 = New System.Windows.Forms.GroupBox()
    	Me.groupBox3 = New System.Windows.Forms.GroupBox()
    	Me.btn_clear_logs = New System.Windows.Forms.Button()
    	Me.btn_back_up_logs = New System.Windows.Forms.Button()
    	Me.btn_save_logs = New System.Windows.Forms.Button()
    	Me.GroupBox2.SuspendLayout
    	Me.GroupBox1.SuspendLayout
    	Me.groupBox3.SuspendLayout
    	Me.SuspendLayout
    	'
    	'lvLogsch6
    	'
    	Me.lvLogsch6.Text = "WorkCode"
    	Me.lvLogsch6.Width = 85
    	'
    	'lvLogsch4
    	'
    	Me.lvLogsch4.Text = "In/Out Mode"
    	Me.lvLogsch4.Width = 100
    	'
    	'lvLogsch3
    	'
    	Me.lvLogsch3.Text = "Verify Mode"
    	Me.lvLogsch3.Width = 90
    	'
    	'lvLogsch2
    	'
    	Me.lvLogsch2.Text = "User No."
    	Me.lvLogsch2.Width = 80
    	'
    	'lvLogsch1
    	'
    	Me.lvLogsch1.Text = "Count"
    	Me.lvLogsch1.Width = 75
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
    	Me.lvLogs.Size = New System.Drawing.Size(619, 440)
    	Me.lvLogs.TabIndex = 8
    	Me.lvLogs.UseCompatibleStateImageBehavior = false
    	Me.lvLogs.View = System.Windows.Forms.View.Details
    	'
    	'lvLogsch5
    	'
    	Me.lvLogsch5.Text = "Date"
    	Me.lvLogsch5.Width = 160
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
    	Me.GroupBox2.Size = New System.Drawing.Size(631, 467)
    	Me.GroupBox2.TabIndex = 9
    	Me.GroupBox2.TabStop = false
    	Me.GroupBox2.Text = "  Transaction Records  "
    	'
    	'lbl_cur_state
    	'
    	Me.lbl_cur_state.AutoSize = true
    	Me.lbl_cur_state.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.lbl_cur_state.ForeColor = System.Drawing.Color.MediumBlue
    	Me.lbl_cur_state.Location = New System.Drawing.Point(12, 210)
    	Me.lbl_cur_state.Name = "lbl_cur_state"
    	Me.lbl_cur_state.Size = New System.Drawing.Size(194, 18)
    	Me.lbl_cur_state.TabIndex = 8
    	Me.lbl_cur_state.Text = "Current State: Disconnected"
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
    	Me.txt_server_ip.TabIndex = 8
    	Me.txt_server_ip.Text = "10.120.10.139"
    	Me.txt_server_ip.TextAlign = System.Windows.Forms.HorizontalAlignment.Center
    	'
    	'btn_connect
    	'
    	Me.btn_connect.Font = New System.Drawing.Font("Microsoft Sans Serif", 12!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.btn_connect.Location = New System.Drawing.Point(76, 140)
    	Me.btn_connect.Name = "btn_connect"
    	Me.btn_connect.Size = New System.Drawing.Size(208, 33)
    	Me.btn_connect.TabIndex = 0
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
    	Me.Label1.Size = New System.Drawing.Size(96, 18)
    	Me.Label1.TabIndex = 6
    	Me.Label1.Text = "Biometric IP :"
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
    	Me.GroupBox1.TabIndex = 7
    	Me.GroupBox1.TabStop = false
    	Me.GroupBox1.Text = "  Biometric Connection Details  "
    	'
    	'groupBox3
    	'
    	Me.groupBox3.Anchor = CType((System.Windows.Forms.AnchorStyles.Bottom Or System.Windows.Forms.AnchorStyles.Left),System.Windows.Forms.AnchorStyles)
    	Me.groupBox3.Controls.Add(Me.btn_clear_logs)
    	Me.groupBox3.Controls.Add(Me.btn_back_up_logs)
    	Me.groupBox3.Controls.Add(Me.btn_save_logs)
    	Me.groupBox3.Font = New System.Drawing.Font("Microsoft Sans Serif", 9.75!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.groupBox3.Location = New System.Drawing.Point(12, 278)
    	Me.groupBox3.Name = "groupBox3"
    	Me.groupBox3.Size = New System.Drawing.Size(302, 182)
    	Me.groupBox3.TabIndex = 10
    	Me.groupBox3.TabStop = false
    	Me.groupBox3.Text = "Batch and Back-Up "
    	'
    	'btn_clear_logs
    	'
    	Me.btn_clear_logs.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.btn_clear_logs.Location = New System.Drawing.Point(38, 132)
    	Me.btn_clear_logs.Name = "btn_clear_logs"
    	Me.btn_clear_logs.Size = New System.Drawing.Size(229, 33)
    	Me.btn_clear_logs.TabIndex = 14
    	Me.btn_clear_logs.Text = "Clear Logs"
    	Me.btn_clear_logs.UseVisualStyleBackColor = true
    	AddHandler Me.btn_clear_logs.Click, AddressOf Me.Btn_clear_logs_Click
    	'
    	'btn_back_up_logs
    	'
    	Me.btn_back_up_logs.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.btn_back_up_logs.Location = New System.Drawing.Point(38, 83)
    	Me.btn_back_up_logs.Name = "btn_back_up_logs"
    	Me.btn_back_up_logs.Size = New System.Drawing.Size(229, 33)
    	Me.btn_back_up_logs.TabIndex = 13
    	Me.btn_back_up_logs.Text = "Back-Up Biometrics Log"
    	Me.btn_back_up_logs.UseVisualStyleBackColor = true
    	AddHandler Me.btn_back_up_logs.Click, AddressOf Me.Btn_back_up_logs_Click
    	'
    	'btn_save_logs
    	'
    	Me.btn_save_logs.Font = New System.Drawing.Font("Microsoft Sans Serif", 11.25!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0,Byte))
    	Me.btn_save_logs.Location = New System.Drawing.Point(38, 34)
    	Me.btn_save_logs.Name = "btn_save_logs"
    	Me.btn_save_logs.Size = New System.Drawing.Size(229, 33)
    	Me.btn_save_logs.TabIndex = 12
    	Me.btn_save_logs.Text = "Save Logs By Batch"
    	Me.btn_save_logs.UseVisualStyleBackColor = true
    	AddHandler Me.btn_save_logs.Click, AddressOf Me.Btn_save_logs_Click
    	'
    	'frm_Biometric_logs
    	'
    	Me.AutoScaleDimensions = New System.Drawing.SizeF(6!, 13!)
    	Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
    	Me.BackColor = System.Drawing.Color.Gainsboro
    	Me.ClientSize = New System.Drawing.Size(963, 491)
    	Me.Controls.Add(Me.groupBox3)
    	Me.Controls.Add(Me.GroupBox2)
    	Me.Controls.Add(Me.lbl_cur_state)
    	Me.Controls.Add(Me.GroupBox1)
    	Me.Name = "frm_Biometric_logs"
    	Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
    	Me.Text = "Biometric Connection and Transaction"
    	Me.GroupBox2.ResumeLayout(false)
    	Me.GroupBox1.ResumeLayout(false)
    	Me.GroupBox1.PerformLayout
    	Me.groupBox3.ResumeLayout(false)
    	Me.ResumeLayout(false)
    	Me.PerformLayout
    End Sub
    Friend btn_clear_logs As System.Windows.Forms.Button
    Friend btn_back_up_logs As System.Windows.Forms.Button
    Private groupBox3 As System.Windows.Forms.GroupBox
    Friend btn_save_logs As System.Windows.Forms.Button
    Private WithEvents lvLogsch6 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch4 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch3 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch2 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogsch1 As System.Windows.Forms.ColumnHeader
    Private WithEvents lvLogs As System.Windows.Forms.ListView
    Private WithEvents lvLogsch5 As System.Windows.Forms.ColumnHeader
    Friend WithEvents GroupBox2 As System.Windows.Forms.GroupBox
    Friend WithEvents lbl_cur_state As System.Windows.Forms.Label
    Friend WithEvents Label3 As System.Windows.Forms.Label
    Friend WithEvents txt_server_ip As System.Windows.Forms.TextBox
    Friend WithEvents btn_connect As System.Windows.Forms.Button
    Friend WithEvents Label2 As System.Windows.Forms.Label
    Friend WithEvents Label1 As System.Windows.Forms.Label
    Friend WithEvents txt_port As System.Windows.Forms.TextBox
    Friend WithEvents txt_bio_ip As System.Windows.Forms.TextBox
    Friend WithEvents GroupBox1 As System.Windows.Forms.GroupBox

End Class
