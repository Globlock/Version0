namespace Globlock_Client {
    partial class GUI_Main {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing) {
            if (disposing && (components != null)) {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent() {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(GUI_Main));
            this.contextMenuStrip1 = new System.Windows.Forms.ContextMenuStrip(this.components);
            this.listenToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.showToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.logOffToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.exitToolStripMenuItem = new System.Windows.Forms.ToolStripMenuItem();
            this.notifyIcon1 = new System.Windows.Forms.NotifyIcon(this.components);
            this.comboBox1 = new System.Windows.Forms.ComboBox();
            this.btnGo = new System.Windows.Forms.Button();
            this.lblHelp = new System.Windows.Forms.Label();
            this.contextMenuStrip1.SuspendLayout();
            this.SuspendLayout();
            // 
            // contextMenuStrip1
            // 
            this.contextMenuStrip1.Items.AddRange(new System.Windows.Forms.ToolStripItem[] {
            this.listenToolStripMenuItem,
            this.showToolStripMenuItem,
            this.logOffToolStripMenuItem,
            this.exitToolStripMenuItem});
            this.contextMenuStrip1.Name = "contextMenuStrip1";
            this.contextMenuStrip1.Size = new System.Drawing.Size(115, 92);
            // 
            // listenToolStripMenuItem
            // 
            this.listenToolStripMenuItem.Name = "listenToolStripMenuItem";
            this.listenToolStripMenuItem.Size = new System.Drawing.Size(114, 22);
            this.listenToolStripMenuItem.Text = "Listen";
            this.listenToolStripMenuItem.Click += new System.EventHandler(this.listenToolStripMenuItem_Click);
            // 
            // showToolStripMenuItem
            // 
            this.showToolStripMenuItem.Name = "showToolStripMenuItem";
            this.showToolStripMenuItem.Size = new System.Drawing.Size(114, 22);
            this.showToolStripMenuItem.Text = "Show";
            this.showToolStripMenuItem.Click += new System.EventHandler(this.showToolStripMenuItem_Click);
            // 
            // logOffToolStripMenuItem
            // 
            this.logOffToolStripMenuItem.Name = "logOffToolStripMenuItem";
            this.logOffToolStripMenuItem.Size = new System.Drawing.Size(114, 22);
            this.logOffToolStripMenuItem.Text = "Log Off";
            // 
            // exitToolStripMenuItem
            // 
            this.exitToolStripMenuItem.Name = "exitToolStripMenuItem";
            this.exitToolStripMenuItem.Size = new System.Drawing.Size(114, 22);
            this.exitToolStripMenuItem.Text = "Exit";
            this.exitToolStripMenuItem.Click += new System.EventHandler(this.exitToolStripMenuItem_Click_2);
            // 
            // notifyIcon1
            // 
            this.notifyIcon1.ContextMenuStrip = this.contextMenuStrip1;
            this.notifyIcon1.Icon = ((System.Drawing.Icon)(resources.GetObject("notifyIcon1.Icon")));
            this.notifyIcon1.Text = "Globlock Client";
            this.notifyIcon1.Visible = true;
            // 
            // comboBox1
            // 
            this.comboBox1.Font = new System.Drawing.Font("Monospac821 BT", 14.25F);
            this.comboBox1.FormattingEnabled = true;
            this.comboBox1.Location = new System.Drawing.Point(12, 14);
            this.comboBox1.Name = "comboBox1";
            this.comboBox1.Size = new System.Drawing.Size(400, 30);
            this.comboBox1.TabIndex = 1;
            // 
            // btnGo
            // 
            this.btnGo.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnGo.Image = ((System.Drawing.Image)(resources.GetObject("btnGo.Image")));
            this.btnGo.Location = new System.Drawing.Point(434, 14);
            this.btnGo.MinimumSize = new System.Drawing.Size(64, 64);
            this.btnGo.Name = "btnGo";
            this.btnGo.Size = new System.Drawing.Size(64, 65);
            this.btnGo.TabIndex = 4;
            this.btnGo.UseVisualStyleBackColor = true;
            // 
            // lblHelp
            // 
            this.lblHelp.AutoSize = true;
            this.lblHelp.Location = new System.Drawing.Point(13, 51);
            this.lblHelp.Name = "lblHelp";
            this.lblHelp.Size = new System.Drawing.Size(258, 13);
            this.lblHelp.TabIndex = 5;
            this.lblHelp.Text = "Select a Globe Projoect above and click go to assign";
            // 
            // GUI_Main
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(510, 92);
            this.Controls.Add(this.lblHelp);
            this.Controls.Add(this.btnGo);
            this.Controls.Add(this.comboBox1);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "GUI_Main";
            this.Text = "Globlock Client - Running...";
            this.Load += new System.EventHandler(this.Main_Load);
            this.contextMenuStrip1.ResumeLayout(false);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.ContextMenuStrip contextMenuStrip1;
        private System.Windows.Forms.ToolStripMenuItem logOffToolStripMenuItem;
        private System.Windows.Forms.NotifyIcon notifyIcon1;
        private System.Windows.Forms.ToolStripMenuItem exitToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem showToolStripMenuItem;
        private System.Windows.Forms.ToolStripMenuItem listenToolStripMenuItem;
        private System.Windows.Forms.ComboBox comboBox1;
        private System.Windows.Forms.Button btnGo;
        private System.Windows.Forms.Label lblHelp;
    }
}