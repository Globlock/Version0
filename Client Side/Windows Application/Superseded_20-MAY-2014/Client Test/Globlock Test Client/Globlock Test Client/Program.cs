using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Linq;
using System.Net;
using System.Text;
using System.Threading.Tasks;
using System.Windows;

namespace Globlock_Test_Client
{
    class Program {
        static void Main(string[] args) {

            // Constant Values
            const string HTTP_POST = "POST";
            const string HTTP_ADDR = "http://192.168.1.32/Globlock/Version0/Server%20Side/Test/testJSON.php";    //TO DO - Read from inifile

            // Byte Array Response from Server
            byte[] serverResponse;
            string decodedString;
            // Webclient to handle requests
            WebClient webClient = new WebClient();

            // Name Value collection to hold POST values
            var dataPOST = new NameValueCollection();
            dataPOST["name"] = "ALEXQUIG";              //TO DO - Encapsulate in method and pass data as params
            dataPOST["pass"] = "P4SSWORD";

            Console.WriteLine("Starting Client to Server Test");
            Console.WriteLine("------------------------------");
            Console.WriteLine();

            // Assign response
            serverResponse = webClient.UploadValues(HTTP_ADDR, HTTP_POST, dataPOST);
            
            // Output response to the console (byte by byte)
            for (int i = 0; i < serverResponse.Length; i++) {
                Console.Write(serverResponse.GetValue(i));
            }
            decodedString = System.Text.Encoding.Default.GetString(serverResponse);
            Console.Write(decodedString);

            brokerObject tmp = JsonConvert.DeserializeObject<brokerObject>(decodedString);
            Console.WriteLine("Name :" + tmp.Name);
            Console.WriteLine("Pass :" + tmp.Pass);
            Console.WriteLine("List :" + tmp.list[3]);
        }

        public class brokerObject {
            public string Name { get; set; }
            public string Pass { get; set; }
            public int a { get; set; }
            public int b { get; set; }
            public int c { get; set; }
            public string[] list { get; set;}

        }


    }
}
