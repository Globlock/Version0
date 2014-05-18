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
    public partial class GUI_SetProject : Form {
        private BrokerManager brokerM;
        public bool keepAlive = true;
        public GUI_SetProject(BrokerManager brokerManager) {
            this.brokerM = brokerManager;
            InitializeComponent();
            setupCombo();
        }

        private void setupCombo() {
            var dataSource = new List<Obj_Project>();
            foreach (string s in brokerM.brokerRequest.listitem) {
                dataSource.Add(new Obj_Project() { Name = s, Value = s });    
            }
            this.cmboProjects.DataSource = dataSource;
            this.cmboProjects.DisplayMember = "Name";
            this.cmboProjects.ValueMember = "Value";
            this.cmboProjects.DropDownStyle = ComboBoxStyle.DropDownList;
        }

        private void GUI_SetProject_Load(object sender, EventArgs e) {
            this.Location = new Point((Screen.PrimaryScreen.WorkingArea.Width - this.Width) / 2, (Screen.PrimaryScreen.WorkingArea.Height - this.Height));
        }

        private void btnGo_Click(object sender, EventArgs e) {
            string[] args = { brokerM.getSessionToken(), cmboProjects.SelectedValue.ToString(), brokerM.tagID };
            MessageBox.Show(cmboProjects.SelectedValue.ToString());
            brokerM.requestResponse(BrokerManager.REQUEST_TYPE_SETT, args);
            if (brokerM.errorState) outputError(brokerM.brokerRequest.error.message);
            //MessageBox.Show(brokerM.decodedString);
            this.Dispose();
        }

        private void outputError(string error = "") {
            MessageBox.Show("An irrecoverable error has occured! " + error);
            Environment.Exit(0);
        }

        private void GUI_SetProject_FormClosing(object sender, FormClosingEventArgs e) {
            keepAlive = false;
        }
    }
}
