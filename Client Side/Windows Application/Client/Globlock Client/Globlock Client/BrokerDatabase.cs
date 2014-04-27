using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SQLite;

namespace Globlock_Client {
    
    class BrokerDatabase {
        // Database Declarations
        SQLiteConnection sqlite_conn;
        SQLiteCommand sqlite_cmd;
        SQLiteDataReader sqlite_datareader;

        private string dbPath, connectionData, dbFilename;
        private int sqlLiteVersion = 3;
        private string[] schema = { "CREATE TABLE test (id integer primary key, text varchar(100));",
                                    "CREATE TABLE IF NOT EXISTS Customer (ID INTEGER NOT NULL primary key, Name varchar(50), Designation varchar(50), Age INTEGER, DateModified datetime, DateCreated datetime);",
                                    "CREATE TABLE IF NOT EXISTS Transactions( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,[desc] VARCHAR(250),[datemodified] datetime);", 
                                    "CREATE TABLE IF NOT EXISTS Users ( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, [datecreated] datetime, [name] VARCHAR(250),[pass] VARCHAR(250));"};
        private string[] inserts = {"INSERT INTO test (id, text) VALUES (1, 'Test Text 1');",
                                    "INSERT INTO Transactions (id, desc) VALUES (1, 'Test Text 1');"};
        SQLiteConnection connection;
        SQLiteCommand cmd;
        
        public BrokerDatabase(string dbPath, string dbFilename) {
            testFilePaths(dbPath, dbFilename);
            //transact("Created DatabaseBroker Object");

        }

        private void testFilePaths(string path, string filename) {
            dbPath = path;
            dbFilename = System.IO.Path.Combine(dbPath, filename);
            connectionData = "Data Source=" + dbFilename + ";version=" + sqlLiteVersion + ";New=True;Compress=True;";
            if (!File.Exists(this.dbFilename)) {
                System.IO.Directory.CreateDirectory(dbPath);
                sqlite_conn = new SQLiteConnection(connectionData);
                createSchema();
            } else {
                connectionData = "Data Source=" + dbFilename + ";version=" + sqlLiteVersion + ";Compress=True;";
                sqlite_conn = new SQLiteConnection(connectionData);
            }
        }

        private void createSchema() {
            try {
                sqlite_conn.Open();
                sqlite_cmd = sqlite_conn.CreateCommand();

                foreach (String createTable in schema) {
                    sqlite_cmd.CommandText = createTable;
                    sqlite_cmd.ExecuteNonQuery();
                }
                foreach (String insert in inserts) {
                    sqlite_cmd.CommandText = insert;
                    sqlite_cmd.ExecuteNonQuery();
                }
                sqlite_cmd.CommandText = "SELECT * FROM test";
                // Now the SQLiteCommand object can give us a DataReader-Object:
                sqlite_datareader = sqlite_cmd.ExecuteReader();

                // The SQLiteDataReader allows us to run through the result lines:
                while (sqlite_datareader.Read()) {// Read() returns true if there is still a result line to read
                    // Print out the content of the text field:
                    System.Console.WriteLine(sqlite_datareader["text"]);
                }

            } catch (Exception e) {
                Console.WriteLine("Error occured! " + e);
            } finally {
                sqlite_conn.Close();
            }
        }
        /*
        public void transact(string transMsg) {
            insertCustomer = "INSERT INTO Customer VALUES (5, 'Allen', 'Manager', 35, " + DateTimeSQLite(DateTime.Now) + ", " + DateTimeSQLite(DateTime.Now) + ")";
            //string insertTrans = "INSERT INTO Transactions(desc, datemodified) Values (" + transMsg + ", " + DateTimeSQLite(DateTime.Now) + " )"; 
            cmd = new SQLiteCommand(insertCustomer); 
            cmd.Connection = connection; 
            connection.Open(); 
            try { 
                cmd.ExecuteNonQuery(); 
            } catch (Exception e){
                Console.WriteLine("Error occured! " + e);
            } finally { 
                connection.Close(); 
            } 
        }
        */
        private string DateTimeSQLite(DateTime datetime) {
            // http://techreadme.blogspot.ie/2012/11/sqlite-read-write-datetime-values-using.html
            string dateTimeFormat = "{0}-{1}-{2} {3}:{4}:{5}.{6}";
            return string.Format(dateTimeFormat, datetime.Year, datetime.Month, datetime.Day, datetime.Hour, datetime.Minute, datetime.Second,datetime.Millisecond);
        }

    }

}
