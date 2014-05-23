using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading;
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

        /** Populate the Combo Box for user globe project selection */
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

        /** Position screen at bottom in the centre */
        private void GUI_SetProject_Load(object sender, EventArgs e) {
            this.Location = new Point((Screen.PrimaryScreen.WorkingArea.Width - this.Width) / 2, (Screen.PrimaryScreen.WorkingArea.Height - this.Height));
        }

        /** User has selected a project */
        private void btnGo_Click(object sender, EventArgs e) {
            string[] args = { brokerM.getSessionToken(), cmboProjects.SelectedValue.ToString(), brokerM.tagID };
            brokerM.requestResponse(BrokerManager.REQUEST_TYPE_SETT, args);
            if (brokerM.errorState) outputError(brokerM.brokerRequest.error.message);
            new Thread(() => new GUI_Toast(String.Format("Successfully Assigned Globe Object '{0}' to Project '{1}'", brokerM.brokerRequest.globe.id, brokerM.brokerRequest.globe.project)).ShowDialog()).Start();
            
            this.Dispose();
        }

        /** An error has occured */
        private void outputError(string error = "") {
            MessageBox.Show("An irrecoverable error has occured! " + error);
            Environment.Exit(0);
        }
    }
}
