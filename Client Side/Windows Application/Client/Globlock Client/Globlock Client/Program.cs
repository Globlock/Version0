using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Forms;
// using Newtonsoft.Json; // ADD Reference
using System.Net;
using System.Text;
using System.IO;

namespace Globlock_Client {
    static class Program {
        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main() {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            // Create INI Access and broker Objects
            Application.Run(new Main());
            setupApplicationSettings();
        }

        static void setupApplicationSettings() { 
            // Load Initialisation File
            INIAccess iniAccess = new INIAccess();
            iniAccess.inspectFile();

            // Load Database File
            string dbfile = iniAccess.IniReadValue("DATABASE", "location");
            //DatabaseBroker dbBroker = new DatabaseBroker(iniAccess.IniReadValue("DATABASE", "location"), iniAccess.IniReadValue("DATABASE", "filename"));
            Debug.WriteLine("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
            Debug.WriteLine("File: "+ dbfile);
            Debug.WriteLine("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");

            BrokerManager brokerM = new BrokerManager("API path", "API filename", Directory.GetCurrentDirectory(), "database.db"); //TESTING ONLY
            brokerM.requestResponse(BrokerManager.REQUEST_TYPE_HAND);

            string[] s = BrokerDevice.getPorts();
        }
        
    }
}
