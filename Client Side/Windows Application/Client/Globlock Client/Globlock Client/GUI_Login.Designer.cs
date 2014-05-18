namespace Globlock_Client {
    partial class GUI_Login {
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(GUI_Login));
            this.chkRemember = new System.Windows.Forms.CheckBox();
            this.lblUser = new System.Windows.Forms.Label();
            this.lblPass = new System.Windows.Forms.Label();
            this.txtBoxUser = new System.Windows.Forms.TextBox();
            this.txtBoxPass = new System.Windows.Forms.TextBox();
            this.btnGo = new System.Windows.Forms.Button();
            this.pnlDetails = new System.Windows.Forms.Panel();
            this.pnlDetails.SuspendLayout();
            this.SuspendLayout();
            // 
            // chkRemember
            // 
            this.chkRemember.AutoSize = true;
            this.chkRemember.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.chkRemember.Location = new System.Drawing.Point(362, 87);
            this.chkRemember.Name = "chkRemember";
            this.chkRemember.RightToLeft = System.Windows.Forms.RightToLeft.Yes;
            this.chkRemember.Size = new System.Drawing.Size(86, 17);
            this.chkRemember.TabIndex = 2;
            this.chkRemember.Text = "remember me";
            this.chkRemember.UseVisualStyleBackColor = true;
            // 
            // lblUser
            // 
            this.lblUser.AutoSize = true;
            this.lblUser.Image = ((System.Drawing.Image)(resources.GetObject("lblUser.Image")));
            this.lblUser.Location = new System.Drawing.Point(10, 12);
            this.lblUser.MinimumSize = new System.Drawing.Size(32, 32);
            this.lblUser.Name = "lblUser";
            this.lblUser.Size = new System.Drawing.Size(32, 32);
            this.lblUser.TabIndex = 1;
            // 
            // lblPass
            // 
            this.lblPass.AutoSize = true;
            this.lblPass.Image = ((System.Drawing.Image)(resources.GetObject("lblPass.Image")));
            this.lblPass.Location = new System.Drawing.Point(10, 48);
            this.lblPass.MinimumSize = new System.Drawing.Size(32, 32);
            this.lblPass.Name = "lblPass";
            this.lblPass.Size = new System.Drawing.Size(32, 32);
            this.lblPass.TabIndex = 2;
            // 
            // txtBoxUser
            // 
            this.txtBoxUser.Font = new System.Drawing.Font("Monospac821 BT", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtBoxUser.Location = new System.Drawing.Point(48, 15);
            this.txtBoxUser.Name = "txtBoxUser";
            this.txtBoxUser.Size = new System.Drawing.Size(400, 30);
            this.txtBoxUser.TabIndex = 0;
            // 
            // txtBoxPass
            // 
            this.txtBoxPass.Font = new System.Drawing.Font("Monospac821 BT", 14.25F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.txtBoxPass.Location = new System.Drawing.Point(48, 51);
            this.txtBoxPass.Name = "txtBoxPass";
            this.txtBoxPass.PasswordChar = '*';
            this.txtBoxPass.Size = new System.Drawing.Size(400, 30);
            this.txtBoxPass.TabIndex = 1;
            this.txtBoxPass.KeyDown += new System.Windows.Forms.KeyEventHandler(this.txtBoxPass_KeyDown);
            // 
            // btnGo
            // 
            this.btnGo.FlatStyle = System.Windows.Forms.FlatStyle.Flat;
            this.btnGo.Image = ((System.Drawing.Image)(resources.GetObject("btnGo.Image")));
            this.btnGo.Location = new System.Drawing.Point(454, 15);
            this.btnGo.MinimumSize = new System.Drawing.Size(64, 64);
            this.btnGo.Name = "btnGo";
            this.btnGo.Size = new System.Drawing.Size(64, 65);
            this.btnGo.TabIndex = 3;
            this.btnGo.UseVisualStyleBackColor = true;
            this.btnGo.Click += new System.EventHandler(this.btnGo_Click);
            // 
            // pnlDetails
            // 
            this.pnlDetails.Controls.Add(this.btnGo);
            this.pnlDetails.Controls.Add(this.txtBoxUser);
            this.pnlDetails.Controls.Add(this.lblUser);
            this.pnlDetails.Controls.Add(this.chkRemember);
            this.pnlDetails.Controls.Add(this.txtBoxPass);
            this.pnlDetails.Controls.Add(this.lblPass);
            this.pnlDetails.Location = new System.Drawing.Point(0, 0);
            this.pnlDetails.Name = "pnlDetails";
            this.pnlDetails.Size = new System.Drawing.Size(529, 113);
            this.pnlDetails.TabIndex = 6;
            // 
            // GUI_Login
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(529, 114);
            this.Controls.Add(this.pnlDetails);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.Name = "GUI_Login";
            this.Text = "Login";
            this.Load += new System.EventHandler(this.Login_Load);
            this.pnlDetails.ResumeLayout(false);
            this.pnlDetails.PerformLayout();
            this.ResumeLayout(false);

        }

        #endregion

        private System.Windows.Forms.CheckBox chkRemember;
        private System.Windows.Forms.Label lblUser;
        private System.Windows.Forms.Label lblPass;
        private System.Windows.Forms.TextBox txtBoxUser;
        private System.Windows.Forms.TextBox txtBoxPass;
        private System.Windows.Forms.Button btnGo;
        private System.Windows.Forms.Panel pnlDetails;
    }
}

