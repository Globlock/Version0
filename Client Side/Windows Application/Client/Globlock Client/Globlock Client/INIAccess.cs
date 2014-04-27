using System;
using System.Runtime.InteropServices;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;

namespace Globlock_Client {

    class INIAccess {

        // Local File
        private string path, filename, absolute;
        private const string DEFAULT_INI = "settings.ini";
        private string[] sections = { "PROJECT", "WORKING DIRECTORY", "DATABASE", "SERVER", "PORT", "SALT" };
        private string defaultKey = "default";

        [DllImport("kernel32")]
        private static extern long WritePrivateProfileString(string section, string key,string val,string filePath);
        [DllImport("kernel32")]
        private static extern int GetPrivateProfileString(string section, string key,string def, StringBuilder retVal, int size,string filePath);

        // Constructor
        public INIAccess(string path, string filename) {
            this.path = path;
            this.filename = filename;
            this.absolute = System.IO.Path.Combine(path, filename);
        }

        public INIAccess() {
            this.filename = DEFAULT_INI;
            this.path = System.IO.Directory.GetCurrentDirectory();
            this.absolute = System.IO.Path.Combine(path, filename);
        }

        public void inspectFile() {
            // Output Values to Debug
            System.Diagnostics.Debug.WriteLine("Path: " + path);
            System.Diagnostics.Debug.WriteLine("File: " + filename);
            System.Diagnostics.Debug.WriteLine("Absolute: " + absolute);
            // Create Directory if it doesn't exist
            if (!Directory.Exists(path)) System.IO.Directory.CreateDirectory(path);
            // Create Default Settings File if it doesn't exist
            if (!File.Exists(absolute)) {
                createDefaultSettingsFile();
            } else {
                path = IniReadValue("WORKINGDIRECTORY", "directory");
                filename = IniReadValue("WORKINGDIRECTORY", "settings");
                absolute = System.IO.Path.Combine(path, filename);
            }
        }

        private void createDefaultSettingsFile() {
            // Skeleton Settings structure 
            string[] defaultSettings = {    "[PROJECT]", "title=Globlock","version=1.0","default=empty",
                                            "[WORKING DIRECTORY]","directory="+ path,"settings="+ filename,"source=", "default=empty",
                                            "[DATABASE]","location=Database","filename=GloblockLocal.db","default=empty",
                                            "[SERVER]","location=http://localhost/Globlock", "API=FileAccessAPI", "default=empty",
                                            "[PORT]","port_num=","default=empty",
                                            "[SALT]","handshake=","default=empty" };
            // For each String in Array, write to file (Add extra line space for sections)
            using (System.IO.StreamWriter file = new System.IO.StreamWriter(@absolute)) {
                foreach (string entry in defaultSettings) {
                    if (entry.Contains("[")) file.WriteLine(); 
                    file.WriteLine(entry);
                }
            }
            System.Diagnostics.Debug.WriteLine("Created New File: " + absolute);
        }

        // Write to the INIfile
        public void IniWriteValue(string Section, string Key, string Value) {
            WritePrivateProfileString(Section, Key, Value, this.path);
        }

        // Read from the INI file
        public string IniReadValue(string Section, string Key) {
            StringBuilder temp = new StringBuilder(255);
            int i = GetPrivateProfileString(Section, Key, "", temp, 255, this.absolute);
            return temp.ToString();
        }

        // Test all the sections exist
        private bool testReadable(){
            string value = "";
            try{
                foreach (String section in sections){
                    value = IniReadValue(section, defaultKey);
                    if (value.Length ==0) throw new System.ArgumentNullException("Key Value Cannot Be 'NULL'", section+":"+defaultKey);
                }
                return true;
            }catch(Exception e){
                Console.WriteLine("Error occured! " + e);
            } 
            return false;
        }

    }
}
