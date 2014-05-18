using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Globlock_Client {

    public class BrokerRequest {

        public Header header { get; set; }
        public Error error { get; set; }
        public User user { get; set; }
        public Session session { get; set; }
        public Globe globe { get; set; }
        public Status status { get; set; }
        public Action action { get; set; }
        public List list { get; set; }
        public List<string> listitem { get; set; }

        public void updateError(string code, string message) {
            this.error = new Error();
            this.error.code = code;
            this.error.message = message;
        }
    }

    public class Header {
        public string type { get; set; }
        public string message { get; set; }
    }

    public class Error {
        public string code { get; set; }
        public string message { get; set; }
    }

    public class User {
        public object name { get; set; }
        public string pass { get; set; }
    }

    public class Session {
        public string token { get; set; }
    }

    public class Globe {
        public string id { get; set; }
        public string project { get; set; }
    }

    public class Status {
        public string assigned { get; set; }
    }

    public class Action {
        public string test { get; set; }
        public string set { get; set; }
        public string abort { get; set; }
        public string redo { get; set; }
        public string drop { get; set; }
        public string pull { get; set; }
        public string push { get; set; }
    }

    public class List {
        public string count { get; set; }
        public string size { get; set; }
        public string root { get; set; }
    }

}
