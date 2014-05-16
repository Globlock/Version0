using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO.Ports;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Globlock_Client {
    public partial class GUI_Login : Form {
        private Icon ico;
        private AutoCompleteStringCollection userSource;
        private Obj_User user;
        private SerialPort arduino;
        private Boolean receivedResponse;
        private string validPort = "";
        private string[] portList;
        private int SERIAL_TIMEOUT = 2000;

        public BrokerManager brokerManager {get; set;}

        #region Constructors
        public GUI_Login() {
            InitializeComponent();
        }
        
        public GUI_Login(BrokerManager brokerManager) {
            InitializeComponent();
            this.brokerManager = brokerManager;
            setupAutoComplete();
            attemptDeviceComms();
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
        }
        #endregion

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
            GUI_Main mainGui = new GUI_Main(brokerManager);
        }

        private void outputError(string error="") {
            MessageBox.Show("An irrecoverable error has occured! "+error);
            Environment.Exit(0);
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


        private void attemptDeviceComms() {
            MessageBox.Show("Serial Test Attempt");
            portList = SerialPort.GetPortNames();

            foreach (string port in portList) {
                arduino = new SerialPort(port); // Default port settings
                arduino.DataReceived += new SerialDataReceivedEventHandler(dataReceived);
                arduino.Open();
                arduino.Write("#H#");
                Thread.Sleep(SERIAL_TIMEOUT); //2000
                if (receivedResponse == true) {
                    validPort = arduino.PortName;
                    MessageBox.Show(validPort);
                }
            }
        }

        private void dataReceived(object sender, SerialDataReceivedEventArgs e) {
            
            string rm = "";
            int headerstart, bodystart, footerstart;
            try {
                rm = arduino.ReadTo("\x03");//Read until the ETX code
                receivedResponse = true;
                MessageBox.Show(rm);
                //headerstart = rm.IndexOf("#H:");
                //bodystart = rm.IndexOf("#B:");
                //footerstart = rm.IndexOf("#F:");
                //if ()
                validPort = arduino.PortName;
            }catch(Exception exception){
                MessageBox.Show(exception.ToString());
            }
        }


     
    }
}
