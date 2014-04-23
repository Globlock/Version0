using System;
using System.Runtime.InteropServices;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;

namespace Globlock_Client {

    class INIAccess {

        public string path;
        private string[] sections = {"Project", "Database", "Server", "Port", "sample4"};
        private string defaultKey = "default";

        [DllImport("kernel32")]
        private static extern long WritePrivateProfileString(string section, string key,string val,string filePath);
        [DllImport("kernel32")]
        private static extern int GetPrivateProfileString(string section, string key,string def, StringBuilder retVal, int size,string filePath);

        // Constructor
        public INIAccess(string path) {
            this.path = path;
        }

        // Write to the INIfile
        public void IniWriteValue(string Section, string Key, string Value) {
            WritePrivateProfileString(Section, Key, Value, this.path);
        }

        // Read from the INI file
        public string IniReadValue(string Section, string Key) {
            StringBuilder temp = new StringBuilder(255);
            int i = GetPrivateProfileString(Section, Key, "", temp, 255, this.path);
            return temp.ToString();
        }

        public bool captured() {
            //string curFile = @"c:\Globlock\settings.ini";
            Console.WriteLine(File.Exists(path) ? "File exists." : "File does not exist.");
            if (File.Exists(@path)) return testReadable(); 
            return true;
        }

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
