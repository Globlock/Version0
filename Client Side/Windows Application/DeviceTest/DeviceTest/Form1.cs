using Newtonsoft.Json;
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

namespace DeviceTest {
    public partial class Form1 : Form {
        string[] portList;
        private SerialPort arduino;
        private int SERIAL_TIMEOUT = 2000;
        private bool validResponse;
        private string validPort;
        private string lastReponse;
        List<byte> bBuffer;
        string sBuffer;
        public Form1() {
            InitializeComponent();
        }

        private void button1_Click(object sender, EventArgs e) {
            Thread thread = new Thread(new ThreadStart(attemptDeviceComms));
            thread.Start();
            Thread.Sleep(SERIAL_TIMEOUT);
            if (validResponse) {
                MessageBox.Show("Globlock Reader found on " + validPort);
                MessageBox.Show("Response " + lastReponse);
                try {
                    DeviceObject dm = JsonConvert.DeserializeObject<DeviceObject>(lastReponse);
                    MessageBox.Show(dm.DeviceMessage.Header);
                }catch(Exception exc){
                    MessageBox.Show("Exception: Data Corrupt!");
                }
            }


        }
        private void attemptDeviceComms() {
            bBuffer = new List<byte>();
            sBuffer = String.Empty;
            //MessageBox.Show("Serial Test Attempt");
            portList = SerialPort.GetPortNames();
            // Loop through Each port.
            foreach (string port in portList) {
                arduino = new SerialPort(port, 9600); // Default port settings
                arduino.DataReceived += new SerialDataReceivedEventHandler(dataReceived);
                try {
                    arduino.Open();
                    if (arduino.IsOpen) {
                        arduino.Write("#H#");
                    }
                } catch (Exception e) {
                    //MessageBox.Show(e.ToString());
                }
            }
        }

        private void dataReceived(object sender, SerialDataReceivedEventArgs e) {
            string rm = "";
            int headerstart, bodystart, footerstart;
            try {
                //Buffer Binary Data
                while (arduino.BytesToRead > 0) bBuffer.Add((byte)arduino.ReadByte());
                ProcessBuffer(bBuffer);
                
                // Buffer string data
                //sBuffer += arduino.ReadExisting();
                //ProcessBuffer(sBuffer);
                //rm = arduino.ReadLine(); //("\x03");//Read until the ETX code
                validResponse = true;
                lastReponse = rm.Trim();
                validPort = arduino.PortName;
                arduino.Close();
            } catch (Exception exception) {
            }
        }

        private void ProcessBuffer(List<byte> bBuffer) {
            string test = System.Text.Encoding.ASCII.GetString(bBuffer.ToArray());
            MessageBox.Show(test);
        }
        private void ProcessBuffer(string sBuffer) {
            // Look in the string for useful information
            // then remove the useful data from the buffer
            MessageBox.Show(sBuffer);
        }


    }
}
