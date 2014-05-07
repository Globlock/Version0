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

        public BrokerManager brokerManager {get; set;}

        public GUI_Login() {
            InitializeComponent();
        }
        
        public GUI_Login(BrokerManager brokerManager) {
            InitializeComponent();
            this.brokerManager = brokerManager;
            setupAutoComplete();
        }
 
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

        private void notifyIcon1_MouseDoubleClick(object sender, MouseEventArgs e) {
            this.Show();
        }

        private void logOffToolStripMenuItem_Click(object sender, EventArgs e) {

        }

        private void validateUserServerside(){
            // ** Possibly move this to brokerManager for invokation, so it can be accessed from Main
            // See if the User already exists in the DB
            // If not, create a new entry
            // call getSession token to test user details
        }
        private void getSessionToken() { 
            // Using the request Broker, attempt to communicate with the server and retrieve a session token
        }

        private void addUserToDB() {

        }

        private void btnGo_Click(object sender, EventArgs e) {
            this.Hide();
            GUI_Toast t = new GUI_Toast("Connecting to the server...");
            t.Show();
            //Handshake
            brokerManager.requestResponse(BrokerManager.REQUEST_TYPE_HAND);
            if (brokerManager.errorState) {
                //Application.Exit();
            } else { 
                //attempt Session Token Retrieval, with username and password
            }
        }


  
    }
}
