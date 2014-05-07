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
        private string dbLocation, dbFilename, serverAddress;
        private BrokerManager brokerM;
        public GUI_Main() {
            InitializeComponent();
            initializeSettings();
        }
        public GUI_Main(BrokerManager brokerManager) {
            InitializeComponent();
            initializeSettings();
        }

        private void Main_Load(object sender, EventArgs e) {

        }

        private void initializeSettings() {
            //INIAccess local = new INIAccess();
            //local
            //   dbLocation = local.IniReadValue("Database","location");
            //    dbFilename = local.IniReadValue("Database", "filename");
            //}
            //DatabaseBroker dbBroker = new DatabaseBroker(dbLocation, dbFilename);
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
            FolderBrowserDialog fbd = new FolderBrowserDialog();
            if (fbd.ShowDialog() == DialogResult.OK) {
                    MessageBox.Show("Folder Selected: "+ fbd.SelectedPath);   
            }
        }

        internal void updateBroker(BrokerManager brokerM) {
            this.brokerM = brokerM;
        }
    }
}
