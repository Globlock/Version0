using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Globlock_Client {
    public partial class Main : Form {
        private string dbLocation, dbFilename, serverAddress;
        public Main() {
            InitializeComponent();
            initializeSettings();

        }

        private void Main_Load(object sender, EventArgs e) {

        }

        private void initializeSettings() {
            //INIAccess local = new INIAccess();
            //local
            //   dbLocation = local.IniReadValue("Database","location");
            //    dbFilename = local.IniReadValue("Database", "filename");
            //}
            //DatabaseBroker dbBroker = new DatabaseBroker(dbLocation, dbFilename);
        }
    }
}
