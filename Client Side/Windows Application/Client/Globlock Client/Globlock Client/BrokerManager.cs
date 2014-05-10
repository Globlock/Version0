using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Collections.Specialized;
using System.Diagnostics;
using System.Windows;
using System.Windows.Forms;
using System.Net;
using Newtonsoft.Json;
using System.Data;

namespace Globlock_Client {
    public class BrokerManager {
        // Test
        private bool testMode;
        private bool irrecoverableError;
        
        // Broker and Web Objects
        public BrokerRequest brokerRequest { get; set; }
        private BrokerDatabase brokerDatabase { get; set; }
        private BrokerDevice brokerDevice { get; set; }
        private WebClient webClient;
        private Obj_SettingsAccess iniAccess;
        private Obj_FilePaths drivePaths;
        private Obj_User currentUser;

        private string saltValue;

        // Web Interaction variables
        private NameValueCollection dataPOST;
        public string API_FILENAME { get; set; }
        public string API_ADDRESS { get; set; }
        public System.Uri HTTP_ADDR { get; set; }
        private byte[] serverResponse;
        private string decodedString;
        private string messageHolder;
        public bool errorState;
        
        /// <summary>
        /// Request Types
        public const string REQUEST_TYPE_HAND = "Handshake Request";                         // 00 "HANDSHAKE"
        public const string REQUEST_TYPE_SESH = "Session Token Request";                     // 01 "SESSION"
        public const string REQUEST_TYPE_VALD = "Validate a Globe Object";                   // 02 "VALIDATE"
        public const string REQUEST_TYPE_ABRT = "Abort a Session Token";                     // 03 "ABORT"
        public const string REQUEST_TYPE_SETT = "Assign a Project to a Globe";               // 04 "SET"
        public const string REQUEST_TYPE_FORC = "Assign (Override) a Project to a Globe";    // 05 "FORCE"
        public const string REQUEST_TYPE_DROP = "Drop a Globe association";                  // 06 "DROP"
        public const string REQUEST_TYPE_PULL = "Pull down Globe Files from Server";         // 07 "PUSH"
        public const string REQUEST_TYPE_PUSH = "Push files to the Server";                  // 08 "PULL"
        /// </summary>
        
        public const string HTTP_POST = "POST";
        public const string REQUEST_ERROR_400 = "SERVER ERROR 400";
        
        // Constructor
        public BrokerManager() {
            testMode = true;
            irrecoverableError = false;                    
            prepareINIFile();
            prepareDatabase();
            prepareWebClient();
            prepareRequest();
        }

        public bool userIsCurrent() {
            DataTable users = brokerDatabase.getCurrentUser();
            if (users.Columns.Count == 1){
                DataRow dr = users.Rows[0];
                string username = dr["username"].ToString();
                string password = dr["password"].ToString();
                bool super = dr["super"].ToString().Equals("1");
                currentUser = new Obj_User(username, password, super);
                return true;
            }
            return false;
        }

        public string[] listUsers() {
            return brokerDatabase.listAllUsers();
        }

        #region Broker Prep
        private void prepareINIFile(){
            iniAccess = new Obj_SettingsAccess();
            iniAccess.inspectFile();
            drivePaths = new Obj_FilePaths(iniAccess, testMode);
            //TEST
            MessageBox.Show("INI & Drive Paths Created - Working Directory: " + drivePaths.dPath_Working_Directory);
        }

        private void prepareDatabase() {
            brokerDatabase = new BrokerDatabase(drivePaths.dPath_Database_FullPath, drivePaths.dPath_Database_Filename);
            brokerDatabase.databaseTransaction("Application Initialized!");
        }

        private void prepareWebClient() {
            decodedString = "Undefined";
            serverResponse = null;
            dataPOST = new NameValueCollection();
            webClient = new WebClient();
            HTTP_ADDR = drivePaths.server_API_URI;
            brokerDatabase.databaseTransaction("Web Client Created");
        }

        private void prepareRequest() {
            brokerRequest = new BrokerRequest();
            brokerDatabase.databaseTransaction("Request Broker Created");
        }

        private void prepareDevice() {
            brokerDevice = new BrokerDevice();
            brokerDevice.connectToDevice();
            messageHolder = String.Format("Device found on '{0}' !", brokerDevice.arduinoPort);
            if (!brokerDevice.validDeviceFound) {
                messageHolder = "NO GLOBLOCK DEVICE FOUND, CANNOT CONTINUE!";
                irrecoverableError = true;
            }
            brokerDatabase.databaseTransaction(messageHolder);
        }
        #endregion

        public string getSessionToken() {
            return brokerRequest.session.token;
        }

        #region Requests
        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        public void requestResponse(string type, string[] args = null){ 
            switch(type){
                case REQUEST_TYPE_HAND:
                case "00":
                    setupHAND();
                    serverRequest("HANDSHAKE");
                    break;
                case REQUEST_TYPE_SESH:
                case "01":
                    setupSession(args);
                    serverRequest("SESSION");
                    handleSessionResponse();
                    break;
                case REQUEST_TYPE_VALD:
                case "02":
                    serverRequest("VALIDATE");
                    break;
                case REQUEST_TYPE_ABRT:
                case "03":
                    setupABORT(args);
                    serverRequest("ABORT");
                    break;
                case REQUEST_TYPE_SETT:
                case "04":
                    serverRequest("SET");
                    break;
                case REQUEST_TYPE_FORC:
                case "05":
                    serverRequest("FORCE");
                    break;
                case REQUEST_TYPE_DROP:
                case "06":
                    serverRequest("DROP");
                    break;
                case REQUEST_TYPE_PULL:
                case "07":
                    serverRequest("PUSH");
                    break;
                case REQUEST_TYPE_PUSH:
                case "08":
                    serverRequest("PULL");
                    break;
            }
            if (int.Parse(brokerRequest.error.code) > 0) {
                errorState = true;
            } else {

            } 

        }

        private void serverRequest(string type) {
            Debug.WriteLine("Attempting '{0}' Server Interaction on {1}", type, HTTP_ADDR);
            try {
                serverResponse = webClient.UploadValues(HTTP_ADDR, HTTP_POST, dataPOST);
                if (serverResponse.Equals(null)) throw new Exception("Server Unavailable");
                decodedString = System.Text.Encoding.Default.GetString(serverResponse);
                brokerRequest = JsonConvert.DeserializeObject<BrokerRequest>(decodedString);
                MessageBox.Show(decodedString);//TEST
            } catch (Exception e) {
                Debug.WriteLine("Exception occured {0}", e);
                brokerRequest = new BrokerRequest();
                brokerRequest.updateError("9999", e.Source + ": " + e.Message);
                irrecoverableError = true;
            } finally {
                Debug.WriteLine("Request broker Error [{0}]:[{1}]", brokerRequest.error.code, brokerRequest.error.message);
            }

        }
        
        #endregion

        private void handleSessionResponse() {
            if (!(int.Parse(brokerRequest.error.code) == 0)) {
                switch (int.Parse(brokerRequest.error.code)) {
                    case 401:
                        MessageBox.Show("Invalid Username or Password! Server will not allow access!");
                        irrecoverableError = true;
                        break;
                    // TO DO - Add error codes
                }
            }
            if (irrecoverableError) {
                MessageBox.Show("An irrecoverable error has occured!");
                Application.Exit();
            } else {
                if (getSessionToken()[0] == '1') {
                    currentUser.setSuper();
                    MessageBox.Show("Access granted at Super User Level!");
                } else {
                    MessageBox.Show("Access granted!");
                }
            }
            
        }

        public void assignUser(Obj_User user) {
            this.currentUser = user;
        }



        private void setupHAND() {
            dataPOST.Add("request_header","HANDSHAKE");
            dataPOST.Add("request_body", "aq7548aq");
        }

        private void setupSession(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "SESSION");
            dataPOST.Add("user_name", args[0]);
            dataPOST.Add("user_pass", args[1]);
            MessageBox.Show(String.Format("Header: {0}\nUsername: {1}\nPass: {2}", dataPOST["request_header"], dataPOST["user_name"], dataPOST["user_pass"]));//TEST
        }
        
        private void setupVALIDATE(string[] args){
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "VALIDATE");
            dataPOST.Add("session_token",args[0]);
            dataPOST.Add("globe_id", args[1]);
        }

        private void setupABORT(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "ABORT");
            dataPOST.Add("session_token", args[0]);
        }

        private void setupSET(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header","SET");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_project", args[1]);
        }

        private void setupFORCE(string[] args) {
            dataPOST.Add("request_header","FORCE");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_id",args[1]);
            dataPOST.Add("globe_project",args[2]);
        }

        private void setupDROP(string[] args) {
            dataPOST.Add("request_header", "DROP");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_id", args[1]);
            dataPOST.Add("globe_project",  args[2]);
        }
        
        private void setupPUSH(string[] args) {
            // TO DO
            //dataPOST.Add(["request_header"] = "PUSH";
            //dataPOST.Add(["session_token"] = args[0];
            //dataPOST.Add(["globe_id"] = args[1];
            //dataPOST.Add(["globe_project"] = args[2];
        
        }

        private void setupPULL(string[] args) {
            // TO DO
            //dataPOST.Add(["request_header"] = "PUSH";
            //dataPOST.Add(["session_token"] = args[0];
            //dataPOST.Add(["globe_id"] = args[1];
            //dataPOST.Add(["globe_project"] = args[2];
        }
        
        private void validateHANDSHAKE() { 
            
        }

        public void dispose(bool rememberMe) {
            if (!rememberMe) brokerDatabase.markNonCurrent();
            else markUserCurrent();
        }

        public void markUserCurrent() {
            brokerDatabase.markNonCurrent();
            string[] tempDetails = currentUser.getServerFormat();
            if (!brokerDatabase.userExists(tempDetails[0])) brokerDatabase.insertUser(tempDetails[0], tempDetails[1]);
            brokerDatabase.markCurrent(tempDetails[0]);
        }

    }
}
