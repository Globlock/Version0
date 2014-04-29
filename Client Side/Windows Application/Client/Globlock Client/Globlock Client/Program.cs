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
            //Application.Run(new Main());
            setupApplicationSettings();
        }

        static void setupApplicationSettings() {
            BrokerManager brokerM = new BrokerManager(); //TESTING ONLY\
            //brokerM.
            //brokerM.requestResponse(BrokerManager.REQUEST_TYPE_HAND);
            //string[] s = BrokerDevice.getPorts();
        }

        //private static BrokerDatabase setupDatabaseBroker(INIAccess iniAccess) {
        //    string workingDir = iniAccess.IniReadValue("WORKINGDIRECTORY", "directory");
        //    string dbLocation = iniAccess.IniReadValue("DATABASE", "location");
        //    string dbFilename = iniAccess.IniReadValue("DATABASE", "filename");
        //    string dbFullPath = System.IO.Path.Combine(workingDir, dbLocation);
        //    string dbAbsolute = System.IO.Path.Combine(workingDir, dbLocation, dbFilename);

        //    BrokerDatabase dbBroker = new BrokerDatabase(dbFullPath, dbFilename);
        //    return dbBroker;
        //}


        //private static INIAccess setupINI() {
        //    // Load Initialisation File
        //    INIAccess iniAccess = new INIAccess();
        //    iniAccess.inspectFile();
        //    Debug.WriteLine("Working Directory: " + iniAccess.IniReadValue("WORKINGDIRECTORY", "directory"));
        //    return iniAccess;
        //}

        
        
    }
}
