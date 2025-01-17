﻿using System;
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
using System.IO;

namespace Globlock_Client {

    public class BrokerManager {
        
        #region Data Members
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
        public string tagID;
        public string localFile;
        public bool fileUploaded;

        private string saltValue;

        // Web Interaction variables
        private NameValueCollection dataPOST;
        public string API_FILENAME { get; set; }
        public string API_ADDRESS { get; set; }
        public System.Uri HTTP_ADDR { get; set; }
        private byte[] serverResponse;
        public string decodedString;
        private string messageHolder;
        public bool errorState;
        
        // Request Types
        public const string REQUEST_TYPE_HAND = "Handshake Request";                         // 00 "HANDSHAKE"
        public const string REQUEST_TYPE_SESH = "Session Token Request";                     // 01 "SESSION"
        public const string REQUEST_TYPE_VALD = "Validate a Globe Object";                   // 02 "VALIDATE"
        public const string REQUEST_TYPE_ABRT = "Abort a Session Token";                     // 03 "ABORT"
        public const string REQUEST_TYPE_SETT = "Assign a Project to a Globe";               // 04 "SET"
        public const string REQUEST_TYPE_FORC = "Assign (Override) a Project to a Globe";    // 05 "FORCE"
        public const string REQUEST_TYPE_DROP = "Drop a Globe association";                  // 06 "DROP"
        public const string REQUEST_TYPE_PULL = "Pull down Globe Files from Server";         // 07 "PUSH"
        public const string REQUEST_TYPE_PUSH = "Push files to the Server";                  // 08 "PULL"
                
        public const string HTTP_POST = "POST";
        public const string REQUEST_ERROR_400 = "SERVER ERROR 400";
        #endregion

        #region Constructor
        // Constructor
        public BrokerManager() {
            testMode = false;
            irrecoverableError = false;                    
            prepareINIFile();
            prepareDatabase();
            prepareWebClient();
            prepareRequest();
            //prepareDevice(); handled in Login
        }
        #endregion

        #region Broker Prep
        private void prepareINIFile(){
            iniAccess = new Obj_SettingsAccess();
            iniAccess.inspectFile();
            drivePaths = new Obj_FilePaths(iniAccess, testMode);
            //TEST
            //MessageBox.Show("INI & Drive Paths Created - Working Directory: " + drivePaths.dPath_Working_Directory);
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
                MessageBox.Show(String.Format("An irrecoverable error has occured! [{0}]",messageHolder));
                Environment.Exit(0);
            }
            brokerDatabase.databaseTransaction(messageHolder);
        }
        #endregion
                
        #region Requests
        /// <summary>
        /// REQUEST REPONSE
        /// Accepts string parameters (optional) and envokes the appropriate 
        /// methods (i.e. setups up each POST request) depending on the type selected.
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
                    setupVALIDATE(args);
                    serverRequest("VALIDATE");
                    break;
                case REQUEST_TYPE_ABRT:
                case "03":
                    setupABORT(args);
                    serverRequest("ABORT");
                    break;
                case REQUEST_TYPE_SETT:
                case "04":
                    setupSET(args);
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
                    setupPULL(args);
                    serverRequest("PULL");
                    break;
                case REQUEST_TYPE_PUSH:
                case "08":
                    serverRequest("PUSH");
                    break;
            }
            if (int.Parse(brokerRequest.error.code) > 0) {
                errorState = true;
            } else {

            } 

        }

        // Get the current session token
        public string getSessionToken() {
            return brokerRequest.session.token;
        }

        /** SERVER REQUEST 
         * Attempts to POST to the server, and deserialises the returned JSON message 
         * to a an instance of a BrokerRequest object
         */
        private void serverRequest(string type) {
            this.writetoDB(String.Format("Attempting '{0}' Server Interaction on {1}", type, HTTP_ADDR));
            try {
                serverResponse = webClient.UploadValues(HTTP_ADDR, HTTP_POST, dataPOST);
                if (serverResponse.Equals(null)) throw new Exception("Server Unavailable");
                decodedString = System.Text.Encoding.Default.GetString(serverResponse);
                // DeserializeObject
                brokerRequest = JsonConvert.DeserializeObject<BrokerRequest>(decodedString);
            } catch (Exception e) {
                this.writetoDB(String.Format("'{0}' failed on {1} with Exception {2}", type, HTTP_ADDR, e.ToString()));
                Debug.WriteLine("Exception occured {0}", e);
                brokerRequest = new BrokerRequest();
                brokerRequest.updateError("9999", e.Source + ": " + e.Message);
                irrecoverableError = true;
            }
        }

        /** HANDLE SESSION RESPONSE
         * Alert user of failed session request attempts
         */
        private void handleSessionResponse() {
            if (!(int.Parse(brokerRequest.error.code) == 0)) {
                switch (int.Parse(brokerRequest.error.code)) {
                    case 401:
                        MessageBox.Show("Invalid Username or Password! Server will not allow access!");
                        irrecoverableError = true;
                        break;
                    case 500:
                        MessageBox.Show("Internal Server Error! Server will not allow access!");
                        irrecoverableError = true;
                        break;
                    case 404:
                        MessageBox.Show("Server Location not Found or File not Found!");
                        irrecoverableError = true;
                        break;
                    default:
                        MessageBox.Show("Server Error: " + brokerRequest.error.code);
                        irrecoverableError = true;
                        break;
                }
            }
            if (irrecoverableError) Environment.Exit(0);
                
            currentUser.setSuper();
        }
        #endregion

        #region User Definition
        /** Assign User as Current */
        public void assignUser(Obj_User user) {
            this.currentUser = user;
        }

        /** Retrieve Current User */
        public Obj_User retrieveUser() {
            return this.currentUser;
        }

        // Dispose of the current user
        public void dispose(bool rememberMe) {
            if (!rememberMe) brokerDatabase.markNonCurrent();
            else markUserCurrent();
        }

        /** Mark User Current */
        public void markUserCurrent() {
            brokerDatabase.markNonCurrent();
            string[] tempDetails = currentUser.getServerFormat();
            if (!brokerDatabase.userExists(tempDetails[0])) brokerDatabase.insertUser(tempDetails[0], tempDetails[1]);
            brokerDatabase.markCurrent(tempDetails[0]);
        }

        // Make the user curernt
        public bool userIsCurrent() {
            DataTable users = brokerDatabase.getCurrentUser();
            if (users.Columns.Count == 1) {
                DataRow dr = users.Rows[0];
                string username = dr["username"].ToString();
                string password = dr["password"].ToString();
                bool super = dr["super"].ToString().Equals("1");
                currentUser = new Obj_User(username, password, super);
                return true;
            }
            return false;
        }
        #endregion

        #region Database Transactions
        // Write to the database using database broker
        public void writetoDB(string message) {
            brokerDatabase.databaseTransaction(message);
        }
        // List all users in local DB
        public string[] listUsers() {
            return brokerDatabase.listAllUsers();
        }
        #endregion

        #region POST Setups
        /** HANDSHAKE */
        private void setupHAND() {
            dataPOST.Add("request_header","HANDSHAKE");
            dataPOST.Add("request_body", "aq7548aq");
        }

        /** SESSION */
        private void setupSession(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "SESSION");
            dataPOST.Add("user_name", args[0]);
            dataPOST.Add("user_pass", args[1]);
            //MessageBox.Show(String.Format("Header: {0}\nUsername: {1}\nPass: {2}", dataPOST["request_header"], dataPOST["user_name"], dataPOST["user_pass"]));//TEST
        }

        /** VALIDATE */
        private void setupVALIDATE(string[] args){
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "VALIDATE");
            dataPOST.Add("session_token",args[0]);
            dataPOST.Add("globe_id", args[1]);
        }

        /** ABORT */
        private void setupABORT(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "ABORT");
            dataPOST.Add("session_token", args[0]);
        }

        /** SET */
        private void setupSET(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header","SET");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_project", args[1]);
            dataPOST.Add("globe_id", args[2]);
        }

        /** FORCE */
        private void setupFORCE(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header","FORCE");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_id",args[1]);
            dataPOST.Add("globe_project",args[2]);
        }

        /** DROP */
        private void setupDROP(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "DROP");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_id", args[1]);
            dataPOST.Add("globe_project",  args[2]);
        }

        /** PUSH */
        private void setupPUSH(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header",  "PUSH");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_id", args[1]);
        }

        /** PULL */
        private void setupPULL(string[] args) {
            dataPOST = new NameValueCollection();
            dataPOST.Add("request_header", "PULL");
            dataPOST.Add("session_token", args[0]);
            dataPOST.Add("globe_id", args[1]);
        }
        #endregion

        #region Port/Device Methods
        // Get the Port
        public string getPort() {
            return brokerDevice.arduinoPort;
        }

        // Test the Reader Device
        public bool testDevice() {
            brokerDevice.connectToDevice();
            if (brokerDevice.arduinoPort.Equals("Error")) return false;
            return true;
        }
        #endregion

        #region File Upload & Download
        // Download the files listed in broker
        internal bool downloadFile() {
            try {
                int count = 0;
                //Local File & Paths
                string localPath = System.IO.Path.Combine(this.drivePaths.dPath_Working_Directory, this.brokerRequest.globe.id); //Working Dir combined with globe Object ID
                if (Directory.Exists(localPath)) Directory.Delete(localPath, true);
                if (!Directory.Exists(localPath)) Directory.CreateDirectory(localPath);
                foreach (string file in brokerRequest.listitem) {
                    if (file.Length > 4) {
                        localFile = System.IO.Path.Combine(localPath, file);
                        if (File.Exists(localFile)) File.Delete(localFile);
                        //Remote File and Paths
                        string downloadfile = this.brokerRequest.list.root + "/" + this.brokerRequest.listitem[count];
                        count++;
                        Uri uri1 = new Uri(downloadfile);
                        //MessageBox.Show(downloadfile);
                        using (WebClient downloadClient = new WebClient()) {
                            downloadClient.DownloadFile(uri1, @localFile);
                        }
                        if (File.Exists(localFile)) System.Diagnostics.Process.Start(@localFile);
                    }
                }
                return true;
            } catch (Exception e) {
                //MessageBox.Show(e.ToString());
                return false;
            }
        }
        // Upload files for current Globe Project
        internal bool uploadFile() {
            try {
                // Setup Variables
                HttpWebResponse response = null;
                HttpData httpForm;
                string localPath, yourUrl;
                string[] dirs;
                int filecount;
                fileUploaded = false;
                //Local File & Paths
                localPath = System.IO.Path.Combine(this.drivePaths.dPath_Working_Directory, this.brokerRequest.globe.id); //Working Dir combined with globe Object ID
                // Ensure the localPath exists before attempting to read its contents
                if (Directory.Exists(localPath)) {
                    dirs = Directory.GetFiles(localPath);
                    //MessageBox.Show(this.API_ADDRESS);
                    yourUrl = "http://localhost/API/GLOBLOCK.php";
                    httpForm = new HttpData(yourUrl);
                    filecount = 0;
                    foreach (string file in dirs) {
                        filecount++;
                        //file = @"C:\Globlock\test1.txt";
                        httpForm.AttachFile("file" + filecount, @file);
                    }
                    httpForm.SetValue("request_header", "PUSH");
                    httpForm.SetValue("session_token", this.brokerRequest.session.token);
                    httpForm.SetValue("globe_id", this.brokerRequest.globe.id);
                    response = httpForm.Submit();
                    fileUploaded = (filecount>0);
                }
                try {
                    Directory.Delete(localPath, true);
                } catch(Exception e) { 
                }
                return true;
            } catch (Exception e) {
                MessageBox.Show(e.ToString());
                return false;
            }
        
        }

        #endregion
    
    }

}
