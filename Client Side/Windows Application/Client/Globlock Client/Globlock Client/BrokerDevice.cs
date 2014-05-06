using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO.Ports;
using System.Windows.Forms;
using System.Management;
using System.Configuration;
using System.Threading;
using System.ComponentModel;

namespace Globlock_Client {
    class BrokerDevice {

        public bool validDeviceFound;
        public string arduinoPort;

        private string[] portList;
        private SerialPortObject arduinoDevice;
        
        public BrokerDevice(){
            validDeviceFound = false;
            arduinoPort = "";
            portList = getPorts();
        }

        public void connectToDevice() {
            try {
                arduinoPort = getArduinoPort();
            } catch (Exception e) {
                System.Diagnostics.Debug.WriteLine("Error occured! " + e);
            }
        }

        private string getArduinoPort() {
            try {
                foreach (string port in portList) {
                    arduinoPort = port;
                    if (!validDeviceFound)
                        if (attemptHandshake() == 1) 
                            System.Diagnostics.Debug.WriteLine("Successfully connected to '{0}'. Valid Device Found!", port);
                }
            } catch (Exception e) {
                System.Diagnostics.Debug.WriteLine("Error occured! " + e);
            }
            return arduinoPort;
        }

        private int attemptHandshake() {
            arduinoDevice = new SerialPortObject(arduinoPort);
            int result = arduinoDevice.initialize();
            if (result == 1) validDeviceFound = true;
            return result;
        }

        public static string[] getPorts() {
            return SerialPortObject.getPorts();
        }


        public class SerialPortObject {

            #region Declarations
            public const int SERIAL_HANDSHAKE = 0;
            public const int SERIAL_REPORT = 1;
            public const int SERIAL_DATA = 9;
            public const int SERIAL_TIMEOUT = 1000;
            private ThreadStart comms;
            private Thread threadComms;

            private Dictionary<String, String> dataPacket;
            private string receivedMessage, packetHeader, packetbody;
            private bool receiveddata;
            private bool validReponse;
            private int baudrate { get; set; }
            private int databits { get; set; }
            private StopBits stopbits { get; set; }
            private Parity parity { get; set; }
            private string portname { get; set; }
            private string datalog { get; set; }
            private string[] dataList { get; set; }
            private SerialPort sPort { get; set; }
            private DateTime dateTime;
            
            
            private string timeNow, logentry, entrydata;
            #endregion

            #region Connect
            public int initialize() {
                connectPort();
                createThread(new ThreadStart(initializeHandshake));
                if (sPort.IsOpen && connectionSuccess()) {
                    closePort();
                    return validResponse();
                } else return -1;
            }

            private void connectPort() {
                //Not needed: sPort = new SerialPort(portname, baudrate, parity, databits, stopbits);
                sPort = new SerialPort(portname); // Default port settings
                sPort.DataReceived += new SerialDataReceivedEventHandler(dataReceived);
                sPort.Open();
            }

            private void closePort() {
                sPort.Close();
                sPort.Dispose();
            }

            private int validResponse() {
                if (validReponse) return 1;
                return -1;
            }

            public int interact(string type, string message) {
                connectPort();
                createThread(new ThreadStart(displayOnDevice));
                if (sPort.IsOpen && connectionSuccess()) {
                    closePort();
                    return validResponse();
                } else return -1;
            }

            private void displayOnDevice() {
                this.sendData("#H#");
            }

            private void createThread(ThreadStart ts) {
                receiveddata = validReponse = false;
                threadComms = new Thread(ts);
            }

            private bool connectionSuccess() {
                threadComms.Start();
                Thread.Sleep(SERIAL_TIMEOUT);
                if (receiveddata == false) return false;
                else return true;
            }

            private void initializeHandshake() {
                this.sendData("#H#");
            }
            #endregion 

            #region Input
            private void dataReceived(object sender, SerialDataReceivedEventArgs e) {
                receivedMessage = sPort.ReadTo("\x03");//Read until the ETX code
                dataList = receivedMessage.Split(new string[] { "\x02", "|" }, StringSplitOptions.RemoveEmptyEntries);
                dataPacket = new Dictionary<string, string>();
                foreach (string s in dataList.ToList()) {
                    packetHeader = s.Substring(0, 3);
                    packetbody = s.Substring(3, s.Length-3);
                    switch (packetHeader) {
                        case "#H#":
                            dataPacket.Add("Header", packetbody);
                            validReponse = true;
                            break;
                        case "#B#":
                            dataPacket.Add("Body", packetbody);
                            break;
                        case "#F#":
                            dataPacket.Add("Footer", packetbody);
                            break;
                    }
                }
                receiveddata = true;
                logentry = String.Join("[{0}] Received : #({1})#{2}", timeStamp(), sender.ToString(), receivedMessage);
                this.appendToLog(logentry);
                System.Diagnostics.Debug.WriteLine(logentry);
            }

            private void dataReceivedTest() {
                string testInput = sPort.ReadExisting();
            }
            #endregion

            #region Logging
            public void appendToLog(string data) { datalog += "\n\r" + data; }
            #endregion

            #region Output
            public void sendData(string data){
                try {
                    sPort.Write(data);
                    logentry = String.Join("[{0}] Sent : {1}", timeStamp(), data);
                    this.appendToLog(logentry);
                } catch (Exception e) {
                    MessageBox.Show("An error occurred while retrieving port information: " + e.Message);
                }
            }
            #endregion

            #region Constructors
            public SerialPortObject(string port, string parity, string baudrate, string databits, string stopbits){
                this.portname = port;
                this.baudrate = Convert.ToInt32(baudrate);
                this.databits = Convert.ToInt32(databits);
                this.stopbits = (StopBits)Enum.Parse(typeof(StopBits), stopbits);
                this.parity = (Parity)Enum.Parse(typeof(Parity), parity);
                receiveddata = false;
            }

            public SerialPortObject(string port) {
                this.portname = port;
                this.baudrate = 9600;
                this.databits = 8;  // "4","5","6","7","8"
                this.stopbits = (StopBits)Enum.Parse(typeof(StopBits), "One"); // "None", "One", "OnePointFive", "Two"
                this.parity = (Parity)Enum.Parse(typeof(Parity), "Even"); // "Even", "Odd", "None", "Mark", "Space"
            }
            #endregion

            #region Static Support
            public static string[] getPorts() {
                string[] portList = { "undefined" };
                try {
                    portList = SerialPort.GetPortNames();
                    portList = portList.Distinct().ToArray();
                    // Usage in form foreach (String s in System.IO.Ports.SerialPort.GetPortNames()) txtPort.Items.Add(s); // foreach (string s in portList) {listBox1.Items.Add(s);}
                } catch (Exception e) {
                    MessageBox.Show("An error occurred while retrieving port information: " + e.Message);
                    Application.Exit();
                }
                return portList;
            }

            public static string timeStamp(){
                DateTime dateTime = DateTime.Now;
                string timeStamp = dateTime.ToShortDateString() + dateTime.ToShortTimeString();
                return timeStamp;
            }
            #endregion

            #region Background
            private void backgroundThread(int type, string message = null) {
                BackgroundWorker bw = new BackgroundWorker();
                bw.WorkerReportsProgress = true;

                switch (type) {
                    case SERIAL_HANDSHAKE:// Wait for result, send single handshake
                        break;
                    case SERIAL_REPORT: // Oneway
                        break;
                    case SERIAL_DATA: // Wait for result, send data
                        break;
                }

                bw.RunWorkerCompleted += new RunWorkerCompletedEventHandler(
                    delegate(object o, RunWorkerCompletedEventArgs args) {
                        receiveddata = true;
                    });

                bw.DoWork += new DoWorkEventHandler(
                    delegate(object o, DoWorkEventArgs args) {
                        BackgroundWorker b = o as BackgroundWorker;
                        b.ReportProgress(50);
                        Thread.Sleep(1000);
                        b.ReportProgress(100);
                    });

                bw.ProgressChanged += new ProgressChangedEventHandler(
                    delegate(object o, ProgressChangedEventArgs args) {

                    });

                bw.RunWorkerAsync();

            }
            #endregion
        
        }


    }
}
