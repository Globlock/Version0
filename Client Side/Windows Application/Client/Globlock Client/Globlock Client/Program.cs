using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Globlock_Client {
    static class Program {
        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main() {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            testINI();
            //setupApplicationSettings();
            //Application.Run(new Main());
        }

        static void setupApplicationSettings() { 
            // Load Initialisation File
            INIAccess iniAccess = new INIAccess();
            iniAccess.inspectFile();
            // Load Database File
            string dbfile = iniAccess.IniReadValue("DATABASE", "location");
            //Debug.WriteLine(iniAccess.IniReadValue("DATABASE", "location"));
            //Debug.WriteLine(iniAccess.IniReadValue("DATABASE", "filename"));
            //DatabaseBroker dbBroker = new DatabaseBroker(iniAccess.IniReadValue("DATABASE", "location"), iniAccess.IniReadValue("DATABASE", "filename"));
            Debug.WriteLine("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
            Debug.WriteLine("File: "+ dbfile);
            Debug.WriteLine("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
        }

        static void testINI() {
            INIAccess iniAccess = new INIAccess();
            iniAccess.inspectFile();
        }
        
    }
}
