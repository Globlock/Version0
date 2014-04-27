using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO.Ports;
using System.Windows.Forms;
using System.Management;

namespace Globlock_Client {
    class BrokerDevice {

        public BrokerDevice(){
            
        }

        public static string[] getPorts() {
            string[] portList = {"undefined"};
            try {
                portList = SerialPort.GetPortNames();
                portList = portList.Distinct().ToArray();
                // Usage in form
                //foreach (string s in portList) {listBox1.Items.Add(s);}
            } catch (Exception e) {
                MessageBox.Show("An error occurred while retrieving port information: " + e.Message);
                Application.Exit();
            } 
            return portList;
        }
    }
}
