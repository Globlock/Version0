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
using System.Threading;

namespace Globlock_Client {
    static class Program {

        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main() {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            initializeApplication();
        }

        static void initializeApplication() {
            BrokerManager brokerM = new BrokerManager();
            if (!brokerM.validateUser()) {
                Application.Run(new GUI_Login(brokerM));
            } else {
                Application.Run(new GUI_Main(brokerM));
            }
        }
        
    }
}
