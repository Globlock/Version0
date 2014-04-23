using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Globlock_Client {
    public partial class Login : Form {
        public Login() {
            InitializeComponent();
        }

        private void label1_Click(object sender, EventArgs e) {

        }

        private Icon ico;
        private void Login_Load(object sender, EventArgs e) {
            // center of center
            //CenterToScreen();
            // center of bottom of screen
            this.Location = new Point((Screen.PrimaryScreen.WorkingArea.Width - this.Width) / 2,
                          (Screen.PrimaryScreen.WorkingArea.Height - this.Height));
            ico = notifyIcon1.Icon;

        }

        private void button1_Click(object sender, EventArgs e) {
            this.Hide();
            Toast t = new Toast("Hello world, this is a toast");
            t.Show();
        }

        private void notifyIcon1_MouseDoubleClick(object sender, MouseEventArgs e) {
            this.Show();
        }

        private void logOffToolStripMenuItem_Click(object sender, EventArgs e) {

        }
    }
}
