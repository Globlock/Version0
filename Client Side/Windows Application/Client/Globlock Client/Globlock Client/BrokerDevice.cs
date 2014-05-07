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
        private Obj_SerialPort arduinoDevice;
        
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

        public static string[] getPorts() {
            return Obj_SerialPort.getPorts();
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
            arduinoDevice = new Obj_SerialPort(arduinoPort);
            int result = arduinoDevice.initialize();
            if (result == 1) validDeviceFound = true;
            return result;
        }



    }
}
