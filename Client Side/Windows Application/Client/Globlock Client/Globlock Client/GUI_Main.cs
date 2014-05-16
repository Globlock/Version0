using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Globlock_Client {
    public partial class GUI_Main : Form {
        private BrokerManager brokerM;

        public GUI_Main() {
            InitializeComponent();
            initializeSettings();
        }
        public GUI_Main(BrokerManager brokerManager) {
            this.brokerM = brokerManager;
            InitializeComponent();
            initializeSettings();
        }

        private void Main_Load(object sender, EventArgs e) {
            this.Location = new Point((Screen.PrimaryScreen.WorkingArea.Width - this.Width) / 2,
                          (Screen.PrimaryScreen.WorkingArea.Height - this.Height));
        }

        private void initializeSettings() {
            
        }

        private void button1_Click(object sender, EventArgs e) {
            //OpenFileDialog openFileDialog1 = new OpenFileDialog();
            //openFileDialog1.InitialDirectory = "c:\\";
            //openFileDialog1.Filter = "txt files (*.txt)|*.txt|All files (*.*)|*.*";
            //openFileDialog1.FilterIndex = 2;
            //openFileDialog1.RestoreDirectory = true;
            //if (openFileDialog1.ShowDialog() == DialogResult.OK) {
            //        MessageBox.Show("Folder Selected: "+ openFileDialog1.FileName);
            //}
            //FolderBrowserDialog fbd = new FolderBrowserDialog();
            //if (fbd.ShowDialog() == DialogResult.OK) {
            //       MessageBox.Show("Folder Selected: "+ fbd.SelectedPath);   
            //}
        }

        internal void updateBroker(BrokerManager brokerM) {
            this.brokerM = brokerM;
        }

        private void exitToolStripMenuItem_Click_2(object sender, EventArgs e) {
            Environment.Exit(0);
        }

        private void testDeviceToolStripMenuItem_Click(object sender, EventArgs e) {
            if (brokerM.testDevice()) MessageBox.Show(String.Format("Device Available on {0}",brokerM.getPort()));

        }

        
    }
}
