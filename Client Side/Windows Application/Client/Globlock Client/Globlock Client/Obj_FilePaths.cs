using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Globlock_Client {
    class Obj_FilePaths {
        private bool testMode;
        public string dPath_Working_Directory { get; set; }
        public string dPath_Database_Location { get; set; }
        public string dPath_Database_Filename { get; set; }
        public string dPath_Database_FullPath { get; set; }
        public string dPath_Database_Absolute { get; set; }
        public string server_API_Address { get; set; }
        public string server_API_Filename { get; set; }
        public System.Uri server_API_URI { get; set; }

        public Obj_FilePaths() {
        }

        public Obj_FilePaths(Obj_SettingsAccess iniAccess, bool testMode) {
            this.testMode = testMode;
            this.buildFromINI(iniAccess);
        }

        internal void buildFromINI(Obj_SettingsAccess iniAccess) {
            dPath_Working_Directory = iniAccess.IniReadValue("WORKINGDIRECTORY", "directory");
            dPath_Database_Location = iniAccess.IniReadValue("DATABASE", "location");
            dPath_Database_Filename = iniAccess.IniReadValue("DATABASE", "filename");
            dPath_Database_FullPath = System.IO.Path.Combine(dPath_Working_Directory, dPath_Database_Location);
            dPath_Database_Absolute = System.IO.Path.Combine(dPath_Working_Directory, dPath_Database_Location, dPath_Database_Filename);

            if (testMode) {                                                                             //Testing only
                server_API_URI = new System.Uri("http://localhost/09052014/FileAccessAPI.php");    //Testing only
                server_API_Filename = "FileAccessAPI.php";                                         //Testing only
                server_API_Address = "http://localhost/09052014/";                                      //Testing only
                return;
            }
            server_API_Address = iniAccess.IniReadValue("SERVER", "location");
            server_API_Filename = iniAccess.IniReadValue("SERVER", "filename");
            server_API_URI = new System.Uri(System.IO.Path.Combine(server_API_Address, server_API_Filename));    //Testing only
        }
    }
}
