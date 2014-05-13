using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.IO.Ports;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace WindowsFormsApplication1 {
    public partial class Form1 : Form {
        public SerialPort sPort;
        public Form1() {
            InitializeComponent();
        }

        private void button1_Click(object sender, EventArgs e) {
         
            //Not needed: sPort = new SerialPort(portname, baudrate, parity, databits, stopbits);
            string[] portnames = SerialPort.GetPortNames();
            sPort = new SerialPort("COM6"); // Default port settings
            sPort.DataReceived += new SerialDataReceivedEventHandler(dataReceived);
            sPort.Open();
        }

        private void dataReceived(object sender, SerialDataReceivedEventArgs e) {
            if (sPort.IsOpen){
                string s = sPort.ReadExisting();
                if (s.Length > 8){
                    MessageBox.Show(s);
                }
            }
        }

        
    }
}
