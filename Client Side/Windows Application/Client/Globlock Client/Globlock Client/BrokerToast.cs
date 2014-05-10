using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Globlock_Client {
    class BrokerToast {

        private string messageHolder;

        public BrokerToast(string message) {
            this.messageHolder = message;
            generateMessage();
        }

        public static void send(string message) {
            BrokerToast temp = new BrokerToast(message);
        }

        private void generateMessage() {
            System.Threading.Thread t = new System.Threading.Thread(new System.Threading.ThreadStart(toastThread));
            t.Start();
        }

        private void toastThread() {
            GUI_Toast gt = new GUI_Toast(messageHolder);
            gt.Show();
        }
    }

    
}
