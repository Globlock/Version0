using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Globlock_Client {
    public class Obj_User {
            private string username;
            private string password;
            private string encryptedPassword;
           // private bool superUser;
            private string salt = "";
            //private StringBuilder returnValue;
            private bool super;

            public Obj_User(string username, string password) {
                this.username = username;
                this.password = password;
                this.encryptedPassword = encryptPassword();
                this.super = false;
            }

            public Obj_User(string username, string password, bool super) {
                this.username = username;
                this.password = password;
                this.super = super;
            }

            public string getName() {
                return this.username;
            }
            
            public string encryptPassword() {
                return SHA1HashStringForUTF8String(password);
            }

            private static string SHA1HashStringForUTF8String(string s) {
                byte[] bytes = Encoding.UTF8.GetBytes(s);
                var sha1 = System.Security.Cryptography.SHA1.Create();
                byte[] hashBytes = sha1.ComputeHash(bytes);
                return HexStringFromBytes(hashBytes);
            }

            private static string HexStringFromBytes(byte[] bytes) {
                var sb = new StringBuilder();
                foreach (byte b in bytes) {
                    var hex = b.ToString("x2");
                    sb.Append(hex);
                }
                return sb.ToString();
            }

            public void setSuper() {
                this.super = true;
            }

            public string[] getServerFormat() {
                string[] serverFormat = new String[] { this.username, this.encryptedPassword };
                return serverFormat;
            }
        }
}
