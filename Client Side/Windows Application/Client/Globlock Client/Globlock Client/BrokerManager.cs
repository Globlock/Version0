using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Diagnostics;
using System.Windows;
using System.Windows.Forms;
using System.Net;
using System.Text;
using Newtonsoft.Json;

namespace Globlock_Client {
    class BrokerManager {
        // Test
        private bool testMode;

        // Broker and Web Objects
        public BrokerRequest req_broker { get; set; }
        public BrokerDatabase db_broker { get; set; }
        private WebClient webClient;
        
        // Web Interaction variables
        private NameValueCollection dataPOST;
        public string API_FILENAME { get; set; }
        public string API_ADDRESS { get; set; }
        public System.Uri HTTP_ADDR { get; set; }
        private byte[] serverResponse;
        private string decodedString;

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
        
        // Constructor
        public BrokerManager(string path, string filename) {
            testMode = true;                                                                    //Testing only  
            setupAddress(path, filename);
            prepareWebClient();
        }
        
        public string getSessionToken() {
            return req_broker.session.token;
        }

        private void prepareWebClient() {
            decodedString = "Undefined";
            serverResponse = null;
            dataPOST = new NameValueCollection();
            webClient = new WebClient();  
        }

        private void setupAddress(string path, string filename) {
          
            if (testMode) {                                                                     //Testing only
                HTTP_ADDR = new System.Uri("http://localhost/26042014/requestBroker_test.php"); //Testing only
                API_FILENAME = "requestBroker_test.php";                                        //Testing only
                API_ADDRESS = "http://localhost/26042014/";                                     //Testing only
            } else {
                API_FILENAME = filename; 
                API_ADDRESS = path;
                HTTP_ADDR = new System.Uri(System.IO.Path.Combine(path, filename)); 
            }
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
            if (int.Parse(req_broker.error.code) > 0) new Toast("Server Error occured :" + req_broker.error.code + " - " + req_broker.error.message).Show();
            else { 
                
            } 

        }

        private void serverRequest(string type) {
            Debug.WriteLine("Attempting '{0}' Server Interaction on {1}", type, HTTP_ADDR);
            try {
                serverResponse = webClient.UploadValues(HTTP_ADDR, HTTP_POST, dataPOST);
                decodedString = System.Text.Encoding.Default.GetString(serverResponse);
                req_broker = JsonConvert.DeserializeObject<BrokerRequest>(decodedString);
            }catch(Exception e){
                Debug.WriteLine("Exception occured {0}", e);
            }finally{
                Debug.WriteLine("Request broker Error [{0}]:[{1}]", req_broker.error.code, req_broker.error.message);
                req_broker.error.code = "0001";
                req_broker.error.message = "Test Message";
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
    }
}
