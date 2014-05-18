using System;
using System.Collections.Generic;
using System.IO.Ports;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Globlock_Client {

    class BrokerReader {

        /** Private Members */
        private SerialPort arduino;
        private List<byte> bBuffer;
        private string lastSerialResponse;
        private string port;

        /** Constants */
        public const int SERIAL_TIMEOUT = 2000;
        public const int DEVICE_STATE_INITIALIZING      = 01;
        public const int DEVICE_STATE_INITIALIZED       = 02;
        public const int DEVICE_STATE_READING           = 10;
        public const int DEVICE_STATE_READ_COMPLETE     = 11;
        public const int DEVICE_STATE_HANDSHAKE_SUCCESS = 22;
        public const int DEVICE_STATE_WRITING           = 30;
        public const int DEVICE_STATE_WAITING           = 40;
        public const int DEVICE_STATE_LISTENING         = 77;
        public const int DEVICE_STATE_ERROR             = 99;
        
        /** Public Members */
        public int STATUS;
        public string STATUSMESSAGE;
        public string validPort;
        public bool RUNNING;

        /** Constructor */
        public BrokerReader(string port) {
            this.port = port;
            if (initialPortConfiguration()) {
                RUNNING = true;
                start();
            }
        }

        private void start() {
            //MessageBox.Show("Start!");
            clearcontainers();
            arduino = new SerialPort(port, 9600);
            arduino.DataReceived += new SerialDataReceivedEventHandler(receivedTag);
            arduino.Open();
            STATUS = DEVICE_STATE_LISTENING;
        }

        public void restart() {
            if (arduino.IsOpen) {
                try { 
                    arduino.Close();
                } catch(Exception e){
                    System.Diagnostics.Debug.WriteLine("Unable to close during resatart... ");
                }
            }
            start();
        }

        private void receivedTag(object sender, SerialDataReceivedEventArgs e) {
            STATUS = DEVICE_STATE_READING;
            bBuffer = new List<byte>();
            while (arduino.BytesToRead > 0) bBuffer.Add((byte)arduino.ReadByte());
            ProcessBuffer(bBuffer);
        }

        private void ProcessBuffer(List<byte> bBuffer) {
            lastSerialResponse += System.Text.Encoding.ASCII.GetString(bBuffer.ToArray());
            if (lastSerialResponse.Length > 10) {
                if (lastSerialResponse.Contains("Complete") || lastSerialResponse.Contains("COMPLETE")) {
                    Console.Beep();
                    System.Diagnostics.Debug.WriteLine(String.Format("Received Complete: {0}", lastSerialResponse));
                    STATUS = DEVICE_STATE_READ_COMPLETE;
                    STATUSMESSAGE = lastSerialResponse;
                    Console.Beep();
                } else {
                    //Not yet complete, allow to buffer
                    Console.Beep();
                    System.Diagnostics.Debug.WriteLine(String.Format("Received Not Complete: {0}", lastSerialResponse));
                }
            } else {
                //Not yet complete, allow to buffer
                Console.Beep();
                System.Diagnostics.Debug.WriteLine(String.Format("Received Short: {0}", lastSerialResponse));
            }
        }

        private bool initialPortConfiguration() {
            clearcontainers();
            STATUS = DEVICE_STATE_INITIALIZING;
            arduino = new SerialPort(port, 9600);
            arduino.DataReceived += new SerialDataReceivedEventHandler(receivedDeviceHandshake);
            arduino.Open();
            if (arduino.IsOpen) {
                STATUS = DEVICE_STATE_WRITING;
                arduino.WriteLine("#H#");
                STATUS = DEVICE_STATE_WAITING;
                Thread.Sleep(SERIAL_TIMEOUT);
                if (STATUS == DEVICE_STATE_READ_COMPLETE) {
                    if (testResponse() == DEVICE_STATE_HANDSHAKE_SUCCESS) {
                        return true;
                    }
                }
            }
            return false;
        }

        private int testResponse() {
            if (lastSerialResponse.Contains("RESPONSE")) return DEVICE_STATE_HANDSHAKE_SUCCESS;
            return 99;
        }
        
        private void receivedDeviceHandshake(object sender, SerialDataReceivedEventArgs e) {
            STATUS = DEVICE_STATE_READING;
            bBuffer = new List<byte>();
            while (arduino.BytesToRead > 0) bBuffer.Add((byte)arduino.ReadByte());
            ProcessHandshakeBuffer(bBuffer);
        }

        private void ProcessHandshakeBuffer(List<byte> bBuffer) {
            lastSerialResponse += System.Text.Encoding.ASCII.GetString(bBuffer.ToArray());
            if (lastSerialResponse.Length > 10) {
                if (lastSerialResponse.Contains("Complete") || lastSerialResponse.Contains("COMPLETE")) {
                    Console.Beep();
                    System.Diagnostics.Debug.WriteLine(String.Format("Received Complete: {0}", lastSerialResponse));
                    Console.Beep();
                    STATUS = DEVICE_STATE_READ_COMPLETE;
                    Console.Beep();
                    arduino.Close();
                    STATUSMESSAGE = lastSerialResponse;
                    Console.Beep();
                } else {
                    //Not yet complete, allow to continue
                    Console.Beep();
                    System.Diagnostics.Debug.WriteLine(String.Format("Received Not Complete: {0}", lastSerialResponse));
                }
            } else {
                Console.Beep();
                System.Diagnostics.Debug.WriteLine(String.Format("Received Short: {0}", lastSerialResponse));
            }
        }
       
        private void clearcontainers() {
            lastSerialResponse = "";
            STATUS = 0;
            STATUSMESSAGE = "";
            bBuffer = new List<byte>();
        }

    }
}
