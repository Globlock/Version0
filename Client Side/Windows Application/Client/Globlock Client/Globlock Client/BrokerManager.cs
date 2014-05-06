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
    class BrokerManager {
        // Test
        private bool testMode;
        private bool irrecoverableError;
        
        // Broker and Web Objects
        public BrokerRequest brokerRequest { get; set; }
        public BrokerDatabase brokerDatabase { get; set; }
        public BrokerDevice brokerDevice { get; set; }
        private WebClient webClient;
        private INIAccess iniAccess;
        private PathObject drivePaths;
        private UserObject currentUser;

        private string saltValue;

        // Web Interaction variables
        private NameValueCollection dataPOST;
        public string API_FILENAME { get; set; }
        public string API_ADDRESS { get; set; }
        public System.Uri HTTP_ADDR { get; set; }
        private byte[] serverResponse;
        private string decodedString;
        private string messageHolder;
        
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
            validateUser();
        }

        public bool validateUser() {
            DataTable users = brokerDatabase.getCurrentUser();
            if (users.Columns.Count > 0){
                DataRow dr = users.Rows[0];
                string username = dr["username"].ToString();
                string password = dr["password"].ToString();
                bool super = dr["password"].ToString().Equals("1");
                currentUser = new UserObject(username, password, super);
                MessageBox.Show("Current User: " + username);
                return true;
            } 
            return false;

        }

        private void prepareINIFile(){
            iniAccess = new INIAccess();
            iniAccess.inspectFile();
            drivePaths = new PathObject(iniAccess, testMode);
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



        public string getSessionToken() {
            return brokerRequest.session.token;
        }
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
                    break;
                case REQUEST_TYPE_VALD:
                case "02":
                    serverRequest("VALIDATE");
                    break;
                case REQUEST_TYPE_ABRT:
                case "03":
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
            if (int.Parse(brokerRequest.error.code) > 0) new Toast("Server Error occured :" + brokerRequest.error.code + " - " + brokerRequest.error.message).Show();
            else { 
                
            } 

        }

        private void serverRequest(string type) {
            Debug.WriteLine("Attempting '{0}' Server Interaction on {1}", type, HTTP_ADDR);
            try {
                serverResponse = webClient.UploadValues(HTTP_ADDR, HTTP_POST, dataPOST);
                decodedString = System.Text.Encoding.Default.GetString(serverResponse);
                brokerRequest = JsonConvert.DeserializeObject<BrokerRequest>(decodedString);
            }catch(Exception e){
                Debug.WriteLine("Exception occured {0}", e);
                brokerRequest = new BrokerRequest();
                brokerRequest.updateError("9999", e.Source + ": " + e.Message);
                irrecoverableError = true;
            }finally{
                Debug.WriteLine("Request broker Error [{0}]:[{1}]", brokerRequest.error.code, brokerRequest.error.message);
            }

        }

        private void setupHAND() {
            dataPOST.Add("request_header","HANDSHAKE");
            dataPOST.Add("request_body", "Sample Message for Encryption");
        }

        private void setupSession(string[] args) {
            dataPOST.Add("request_header", "SESSION");
            dataPOST.Add("user_name", args[0]);
            dataPOST.Add("user_pass", args[1]);
        }
        
        private void setupVALIDATE(string[] args){
            dataPOST.Add("request_header", "VALIDATE");
            dataPOST.Add("session_token",args[0]);
            dataPOST.Add("globe_id", args[1]);
        }

        private void setupABORT(string[] args) {
            dataPOST.Add("request_header", "ABORT");
            dataPOST.Add("session_token", args[0]);
        }

        private void setupSET(string[] args) {
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

        public class UserObject {
            private string username;
            private string password;
            private string encryptedPassword;
            private bool superUser;
            private StringBuilder returnValue;
            private bool super;

            public UserObject(string username, string password) {
                this.username = username;
                this.password = password;
                this.super = false;
            }

            public UserObject(string username, string password, bool super) {
                this.username = username;
                this.password = password;
                this.super = super;
            }

            public string encryptPassword() {
                return SHA1HashStringForUTF8String(password);
            }

            private static string SHA1HashStringForUTF8String(string s) {
                byte[] bytes = Encoding.UTF8.GetBytes(s);
                var sha1 = System.Security.Cryptography.SHA1.Create();
                byte[] hashBytes = sha1.ComputeHash(bytes);
                return HexStringFromBytes(hashBytes);
            }
 
            private static string HexStringFromBytes(byte[] bytes) {
                var sb = new StringBuilder();
                foreach (byte b in bytes) {
                    var hex = b.ToString("x2");
                    sb.Append(hex);
                }
                return sb.ToString();
            }

        }

        public class PathObject {
            private bool testMode;
            public string dPath_Working_Directory { get; set; }
            public string dPath_Database_Location { get; set; }
            public string dPath_Database_Filename { get; set; }
            public string dPath_Database_FullPath { get; set; }
            public string dPath_Database_Absolute { get; set; }
            public string server_API_Address { get; set; }
            public string server_API_Filename { get; set; }
            public System.Uri server_API_URI { get; set; }

            public PathObject() { 
            }

            public PathObject(INIAccess iniAccess, bool testMode) {
                this.testMode = testMode;
                this.buildFromINI(iniAccess);
            }

            internal void buildFromINI(INIAccess iniAccess) {
                dPath_Working_Directory = iniAccess.IniReadValue("WORKINGDIRECTORY", "directory");
                dPath_Database_Location = iniAccess.IniReadValue("DATABASE", "location");
                dPath_Database_Filename = iniAccess.IniReadValue("DATABASE", "filename");
                dPath_Database_FullPath = System.IO.Path.Combine(dPath_Working_Directory, dPath_Database_Location);
                dPath_Database_Absolute = System.IO.Path.Combine(dPath_Working_Directory, dPath_Database_Location, dPath_Database_Filename);
                
                if (testMode) {                                                                             //Testing only
                    server_API_URI = new System.Uri("http://localhost/26042014/requestBroker_test.php");    //Testing only
                    server_API_Filename = "requestBroker_test.php";                                         //Testing only
                    server_API_Address = "http://localhost/26042014/";                                      //Testing only
                    return;
                }
                server_API_Address = iniAccess.IniReadValue("SERVER", "location");
                server_API_Filename = iniAccess.IniReadValue("SERVER", "filename");
                server_API_URI = new System.Uri(System.IO.Path.Combine(server_API_Address, server_API_Filename));    //Testing only
            }
        }
    }
}
