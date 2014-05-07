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
    public partial class GUI_Toast : Form {

        private Timer timerMove;
        private int startPosX, startPosY;
        private string msg;
        private bool complete = false;

        public GUI_Toast() {
            InitializeComponent();
            setupMovement();
        }

        public GUI_Toast(string msg) {
            InitializeComponent();
            setupForm(msg);
        }

        private void setupForm(string msg){
            passMessage(msg);
            setupWidth();
            setupPosition();
            setupMovement();
            setupFadeIn();
            startUnload();
        }

        private void setupWidth() {
            this.Size = new Size(lblMessage.Width + 40, this.Size.Height);
        }

        private void setupPosition() {
            TopMost = true;
            ShowInTaskbar = false;

        }

        private void setupMovement() { 
            timerMove = new Timer();
            timerMove.Interval = 25;
            timerMove.Tick += timer_Tick;
        }

        private void setupFadeIn() {
            int duration = 1000;//in milliseconds
            int steps = 100;
            Timer timer = new Timer();
            timer.Interval = duration / steps;

            int currentStep = 0;
            timer.Tick += (arg1, arg2) => {
                Opacity = ((double)currentStep) / steps;
                currentStep++;
                if (currentStep >= steps) {
                    timer.Stop();
                    timer.Dispose();
                    complete = true;
                }
            };

            timer.Start();
        }

        private void startUnload() {
            int duration = 1000;//in milliseconds
            int steps = 100;
            int pause = 200;
            Timer fadeOut = new Timer();
            fadeOut.Interval = duration / steps;
            int currentStep = 1;
            fadeOut.Tick += (arg1, arg2) => {
                if (complete && (pause <= 0)) {
                    Opacity = (((double)steps - (double)currentStep) / 100);
                    currentStep++;
                    if (currentStep >= steps) {
                        fadeOut.Stop();
                        fadeOut.Dispose();
                        this.Close();
                    }
                } else {
                    pause--;
                }
            };
            fadeOut.Start();
        }

        public void passMessage(string msg) {
            this.msg = msg;
            this.lblMessage.Text = msg;
        }
        protected override void OnLoad(EventArgs e) {
            // Move window out of screen
            startPosX = Screen.PrimaryScreen.WorkingArea.Width - Width;
            startPosY = Screen.PrimaryScreen.WorkingArea.Height;
            SetDesktopLocation(startPosX, startPosY);
            base.OnLoad(e);
            // Begin animation
            timerMove.Start();
        }

        void timer_Tick(object sender, EventArgs e) {
            //Lift window by 5 pixels
            startPosY -= 5;
            //If window is fully visible stop the timer
            if (startPosY < Screen.PrimaryScreen.WorkingArea.Height - Height) {
                timerMove.Stop();
                timerMove.Dispose();
            } else {
                SetDesktopLocation(startPosX, startPosY);
            }
        }

        private void Toast_Load(object sender, EventArgs e) {

        }

        private void unloadToast() {

        }

    }
}
