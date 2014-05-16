using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DeviceTest {
    public class DeviceMessage {
        public string Header { get; set; }
        public string Body { get; set; }
        public string Footer { get; set; }
    }

    public class DeviceObject {
        public DeviceMessage DeviceMessage { get; set; }
    }
}
