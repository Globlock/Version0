namespace Globlock_Client {
    partial class GUI_SetProject {
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(GUI_SetProject));
            this.lblHelp = new System.Windows.Forms.Label();
            this.btnGo = new System.Windows.Forms.Button();
            this.cmboProjects = new System.Windows.Forms.ComboBox();
            this.SuspendLayout();
            // 
            // lblHelp
            // 
            this.lblHelp.AutoSize = true;
            this.lblHelp.Location = new System.Drawing.Point(13, 49);
            this.lblHelp.Name = "lblHelp";
            this.lblHelp.Size = new System.Drawing.Size(258, 13);
            this.lblHelp.TabIndex = 8;
            this.lblHelp.Text = "Select a Globe Projoect above and click go to assign";
            // 
            // btnGo
            // 
            this.btnGo.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnGo.Image = ((System.Drawing.Image)(resources.GetObject("btnGo.Image")));
            this.btnGo.Location = new System.Drawing.Point(434, 12);
            this.btnGo.MinimumSize = new System.Drawing.Size(64, 64);
            this.btnGo.Name = "btnGo";
            this.btnGo.Size = new System.Drawing.Size(64, 65);
            this.btnGo.TabIndex = 7;
            this.btnGo.UseVisualStyleBackColor = true;
            this.btnGo.Click += new System.EventHandler(this.btnGo_Click);
            // 
            // cmboProjects
            // 
            this.cmboProjects.Font = new System.Drawing.Font("Monospac821 BT", 14.25F);
            this.cmboProjects.FormattingEnabled = true;
            this.cmboProjects.Location = new System.Drawing.Point(12, 12);
            this.cmboProjects.Name = "cmboProjects";
            this.cmboProjects.Size = new System.Drawing.Size(400, 30);
            this.cmboProjects.TabIndex = 6;
            // 
            // GUI_SetProject
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(515, 91);
            this.Controls.Add(this.lblHelp);
            this.Controls.Add(this.btnGo);
            this.Controls.Add(this.cmboProjects);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "GUI_SetProject";
            this.Text = "Globlock Client - Running...";
            this.Load += new System.EventHandler(this.GUI_SetProject_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label lblHelp;
        private System.Windows.Forms.Button btnGo;
        private System.Windows.Forms.ComboBox cmboProjects;
    }
}