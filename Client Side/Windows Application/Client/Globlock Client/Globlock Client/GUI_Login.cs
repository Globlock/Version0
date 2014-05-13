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
    public partial class GUI_Login : Form {
        private Icon ico;
        private AutoCompleteStringCollection userSource;
        private Obj_User user;
        public BrokerManager brokerManager {get; set;}

        #region Constructors
        public GUI_Login() {
            InitializeComponent();
        }
        
        public GUI_Login(BrokerManager brokerManager) {
            InitializeComponent();
            this.brokerManager = brokerManager;
            setupAutoComplete();
        }
        #endregion

        #region Form Visuals
        private void setupAutoComplete() { 
            // List
            userSource = new AutoCompleteStringCollection();
            userSource.AddRange(brokerManager.listUsers());
            // AutoComplete properties
            txtBoxUser.AutoCompleteCustomSource = userSource;
            txtBoxUser.AutoCompleteMode = AutoCompleteMode.SuggestAppend;
            txtBoxUser.AutoCompleteSource = AutoCompleteSource.CustomSource;
        }

        private void Login_Load(object sender, EventArgs e) {
            this.Location = new Point((Screen.PrimaryScreen.WorkingArea.Width - this.Width) / 2,
                          (Screen.PrimaryScreen.WorkingArea.Height - this.Height));
            ico = notifyIcon1.Icon;
        }
        #endregion

        private void notifyIcon1_MouseDoubleClick(object sender, MouseEventArgs e) {
            this.Show();
        }

        private void logOffToolStripMenuItem_Click(object sender, EventArgs e) {
            //brokerManager.
        }

        private void validateUserServerside(){
            // ** Possibly move this to brokerManager for invokation, so it can be accessed from Main
            // See if the User already exists in the DB
            // If not, create a new entry
            // call getSession token to test user details
        }
        private void validateUserinDB() {
            
        }
        private void addUserToDB() {

        }

        private void btnGo_Click(object sender, EventArgs e) {
            createUserObject();
            this.Hide();
            brokerManager.requestResponse(BrokerManager.REQUEST_TYPE_HAND);
            if (brokerManager.errorState) {
                outputError();
            } else { 
                attemptSessionRetrieval();
                abortSession();
                if (chkRemember.Checked) brokerManager.markUserCurrent();
            }
        }

        private void outputError() {
            MessageBox.Show("An irrecoverable error has occured!");
            Application.Exit();
        }
        private void attemptSessionRetrieval() {
            brokerManager.requestResponse(BrokerManager.REQUEST_TYPE_SESH, user.getServerFormat());
            if (brokerManager.errorState) outputError();
        }
        private void abortSession() {
            string[] token = {brokerManager.getSessionToken().Substring(1)};
            brokerManager.requestResponse(BrokerManager.REQUEST_TYPE_ABRT, token);
            if (brokerManager.errorState) outputError();
        }

        private void createUserObject() {
            user = new Obj_User(txtBoxUser.Text, txtBoxPass.Text);
            brokerManager.assignUser(user);
        }

        private void contextMenuStrip1_Opening(object sender, CancelEventArgs e) {

        }
        // If user is in DB, check details against DB
        // If user is not in DB, add to DB
        // Then test against Server


    }
}
