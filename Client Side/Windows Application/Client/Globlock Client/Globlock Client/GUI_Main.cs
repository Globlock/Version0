using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Globlock_Client {

    public partial class GUI_Main : Form {

        private BrokerManager brokerM;
        private Obj_User currentUser;
        private string tagID;
        public bool showMe;
        private bool receiveddata;
        private string lastGlobeObject;
        //private string historicalTag;
        BrokerReader arduino;

        public GUI_Main() {
            InitializeComponent();
            initializeSettings();
        }
        public GUI_Main(BrokerManager brokerManager) {
            lastGlobeObject = "";
            this.brokerM = brokerManager;
            currentUser = brokerM.retrieveUser();
            InitializeComponent();
            initializeSettings();
        }

        private void Main_Load(object sender, EventArgs e) {
            this.Location = new Point((Screen.PrimaryScreen.WorkingArea.Width - this.Width) / 2, (Screen.PrimaryScreen.WorkingArea.Height - this.Height));
        }

        private void initializeSettings() {
            arduino = new BrokerReader("COM6");
            new GUI_Toast("Handshaking Complete, you may now listen on device...").Show();
            //envokeThread();
        }

        private void waitForComms(bool restart = true) {
            try {
                if (restart) arduino.restart();
                while (arduino.STATUS == BrokerReader.DEVICE_STATE_LISTENING) {
                    Thread.Sleep(500);
                }
                Thread.Sleep(1000); //Allow to buffer
                if (arduino.STATUS == BrokerReader.DEVICE_STATE_READ_COMPLETE) {
                    handleTagRead(arduino.STATUSMESSAGE);
                    handleResponse();
                } else if (arduino.STATUS == BrokerReader.DEVICE_STATE_READING) {
                    waitForComms(false);
                } else {
                    outputError("An error has occured [Status]" + arduino.STATUS);
                }
            } catch (Exception e) {
                outputError(e.ToString());
            }
        }

        private void handleTagRead(string deviceMessage) {
            //MessageBox.Show(arduino.STATUSMESSAGE);
            tagID = getTagFromMessage(deviceMessage);
            brokerM.tagID = tagID;
            brokerM.writetoDB(String.Format("User {0} received TAG ID: {1} on {2}", currentUser.getName(), tagID, arduino.validPort));
            validateTag();
            //handle Response
            string possibleActions = "";
            possibleActions += "Pull: " + brokerM.brokerRequest.action.pull + "\n";
            possibleActions += "Push: " + brokerM.brokerRequest.action.push + "\n";
            possibleActions += "Set: " + brokerM.brokerRequest.action.set + "\n";
            possibleActions += "Assigned: " + brokerM.brokerRequest.status.assigned;
            //MessageBox.Show(possibleActions);
            handleResponse();
        }

        private void handleResponse() {
            // In the event the globe object read at the device is new
            if (brokerM.brokerRequest.status.assigned.Equals("false") && brokerM.brokerRequest.action.set.Equals("true")) {
                DialogResult result = MessageBox.Show("This globe has not been assigned to a globe project,\nwould you like to assign it?", "New Globe Object", MessageBoxButtons.YesNoCancel, MessageBoxIcon.Warning);
                if (result == DialogResult.Yes) {
                    envokeSetProject();
                }
            }
            // In the event the globe object read at the device is pullable
            if (brokerM.brokerRequest.action.pull.Equals("true")) {
                //Test to see if its the same globe as the last one read
                string thisGlobe = brokerM.brokerRequest.globe.id;
                if (lastGlobeObject.Equals(thisGlobe)){
                    new Thread(() => new GUI_Toast("Attempting to PUSH!").ShowDialog()).Start();
                    grabAndPush(thisGlobe);
                } else {
                    downloadAndOpen();
                }
            }
            waitForComms();
        }



        private void downloadAndOpen() {
            // Attempt Pull Request on Server
            attemptPull();
            string size, count, proj, message;
            size = brokerM.brokerRequest.list.size;
            count = brokerM.brokerRequest.list.count;
            proj = brokerM.brokerRequest.globe.project;
            message = String.Format("Found {0} files on the server for project '{1}'. Total file size {2}", count, proj, size);
            new Thread(() => new GUI_Toast(message).ShowDialog()).Start();
            if (!brokerM.downloadFile()) {
                new Thread(() => new GUI_Toast("Unable to download Globe, please try again later!").ShowDialog()).Start();
            } else {
                // If successfully pulled, update the lastGlobeObject (so PUSH can be attempted later)
                lastGlobeObject = brokerM.brokerRequest.globe.id;
            }
                
        }

        private void grabAndPush(string thisGlobe) {
            if (!brokerM.uploadFile()) {
                new Thread(() => new GUI_Toast("Unable to upload Globe, please try again later!").ShowDialog()).Start();
            } else {
                // If successfully pulled, update the lastGlobeObject (so PUSH can be attempted later)
                if (brokerM.fileUploaded) {
                    new Thread(() => new GUI_Toast("Upload Successful!").ShowDialog()).Start();
                } else {
                    new Thread(() => new GUI_Toast("No changes or files for Upload...").ShowDialog()).Start();
                }
                Thread.Sleep(3000);
                lastGlobeObject = "";
            }
        }

        private void setupGUIForSet() {
            var dataSource = new List<Obj_Project>();
            foreach(string s in brokerM.brokerRequest.listitem){
                dataSource.Add(new Obj_Project() {Name = s, Value = s});
            }
            
            this.BeginInvoke(new MethodInvoker(delegate {
                this.comboBox1.DataSource = dataSource;
                this.comboBox1.DisplayMember = "Name";
                this.comboBox1.ValueMember = "Value";
                this.comboBox1.DropDownStyle = ComboBoxStyle.DropDownList;
                this.Visible = true;
            }));
            MessageBox.Show("Generating Combo");
        }

        #region SERVER CALLS
        private string getSession() {
            brokerM.requestResponse(BrokerManager.REQUEST_TYPE_SESH, currentUser.getServerFormat());
            if (brokerM.errorState) outputError();
            return brokerM.getSessionToken();
        }

        private void validateTag() {
            string[] args = { getSession(), tagID };
            brokerM.requestResponse(BrokerManager.REQUEST_TYPE_VALD, args);
            if (brokerM.errorState) outputError();
        }

        private void attemptPull() {
            string[] args = { brokerM.getSessionToken(), tagID };
            brokerM.requestResponse(BrokerManager.REQUEST_TYPE_PULL, args);
            if (brokerM.errorState) outputError();
        }
        #endregion

        private string getTagFromMessage(string deviceMessage) {
            string identifier = "TAGID:";
            int startIndex = deviceMessage.IndexOf(identifier) + identifier.Length;
            tagID = deviceMessage.Substring(startIndex, 10); //Get 10 digit alphanumeric tag ID
            return tagID;
        }

        private void outputError(string error = "") {
            MessageBox.Show("An irrecoverable error has occured! " + error);
            Environment.Exit(0);
        }

        internal void updateBroker(BrokerManager brokerM) {
            this.brokerM = brokerM;
        }


        #region THREADING
        public void envokeThread() {
            if (arduino.RUNNING) {
                new Thread(() => new GUI_Toast("Listening on Device...").ShowDialog()).Start();
                System.Threading.Thread t = new System.Threading.Thread(new System.Threading.ThreadStart(waitThread));
                t.Start();
            }
        }
        private void waitThread() {
            Thread.Sleep(500); //Allow form to load
            waitForComms();
        }
        
        
        private void envokeSetProject() {
            new Thread(() => new GUI_SetProject(brokerM).ShowDialog()).Start();
        }
        #endregion

        private void btnGo_Click(object sender, EventArgs e) {

        }

        private void logOffToolStripMenuItem_Click(object sender, EventArgs e) {
            Environment.Exit(0);
        }        
        
        private void exitToolStripMenuItem_Click_2(object sender, EventArgs e) {
            // Remove user from current
            Environment.Exit(0);
        }

        private void listenToolStripMenuItem_Click(object sender, EventArgs e) {
            envokeThread();
        }

    }
}
