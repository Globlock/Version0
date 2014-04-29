using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SQLite;

namespace Globlock_Client {
    
    class BrokerDatabase {

        
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
            this.dbPath = dbPath; this.dbFilename = dbFilename;
            testFilePaths(dbPath, dbFilename);
            //transact("Created DatabaseBroker Object");

        }

        private void testFilePaths(string path, string filename) {
            dbPath = path;
            dbFilename = System.IO.Path.Combine(dbPath, filename);
            connectionData = "Data Source=" + dbFilename + ";version=" + sqlLiteVersion + ";New=True;Compress=True;";
            if (!File.Exists(this.dbFilename)) {
                System.Diagnostics.Debug.WriteLine("Database Not Found - Attempting to Create...");
                System.IO.Directory.CreateDirectory(dbPath);
                sqlite_conn = new SQLiteConnection(connectionData);
                createSchema();
                System.Diagnostics.Debug.WriteLine("Database Created!");
            } else {
                System.Diagnostics.Debug.WriteLine("Database Found - Attempting to Connect...");
                connectionData = "Data Source=" + dbFilename + ";version=" + sqlLiteVersion + ";Compress=True;";
                sqlite_conn = new SQLiteConnection(connectionData);
                System.Diagnostics.Debug.WriteLine("Connection Created!");
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
        
        public void transact(string transMsg) {
            sqlite_conn = new SQLiteConnection(connectionData);
            sqlite_conn.Open();
            sqlite_cmd = sqlite_conn.CreateCommand();
            //insertCustomer = "INSERT INTO Customer VALUES (5, 'Allen', 'Manager', 35, " + DateTimeSQLite(DateTime.Now) + ", " + DateTimeSQLite(DateTime.Now) + ")";
            //cmd = new SQLiteCommand(insertTrans);
            string insertTrans = "INSERT INTO Transactions(desc, datemodified) Values (" + transMsg + ", " + DateTimeSQLite(DateTime.Now) + " )";
            sqlite_cmd.CommandText = insertTrans;
            sqlite_cmd.ExecuteNonQuery();
            //1cmd.Connection = connection; 
//            connection.Open(); 
            try { 
                cmd.ExecuteNonQuery(); 
            } catch (Exception e){
                Console.WriteLine("Error occured! " + e);
            } finally { 
                sqlite_conn.Close(); 
            } 
        }
        
        private string DateTimeSQLite(DateTime datetime) {
            // http://techreadme.blogspot.ie/2012/11/sqlite-read-write-datetime-values-using.html
            string dateTimeFormat = "{0}-{1}-{2} {3}:{4}:{5}.{6}";
            return string.Format(dateTimeFormat, datetime.Year, datetime.Month, datetime.Day, datetime.Hour, datetime.Minute, datetime.Second,datetime.Millisecond);
        }


        public void testInsert(string table) {
            Dictionary<String, String> data = new Dictionary<String, String>();

            data.Add("desc", "Test Insert");
            data.Add("datemodified", DateTimeSQLite(DateTime.Now));
            try {
                this.insert(table, data);
            }catch(Exception e){
                System.Diagnostics.Debug.WriteLine("Error occured!" + e);
            }
        
        }

        public bool insert(String tableName, Dictionary<String, String> data) {
            String columns = "";
            String values = "";
            Boolean returnCode = true;

            foreach (KeyValuePair<String, String> val in data) {
                columns += String.Format(" {0},", val.Key.ToString());
                values += String.Format(" '{0}',", val.Value);
            }
            columns = columns.Substring(0, columns.Length - 1); //remove final comma
            values = values.Substring(0, values.Length - 1);
            try {
                this.executeNonQuery(String.Format("insert into {0}({1}) values({2});", tableName, columns, values));
            } catch(Exception e) {
                System.Diagnostics.Debug.WriteLine("Error occured!" + e);
                returnCode = false;
            }
            return returnCode;
        }
        /// <summary>
        /// Executes a non query, and returns the number of rows affected by the transaction
        /// </summary>
        /// <param name="sql">SQL Command to execute on the SQLite DB</param>
        /// <returns></returns>
        public int executeNonQuery(string sql) {
            sqlite_conn = new SQLiteConnection(connectionData);
            sqlite_conn.Open();
            sqlite_cmd = new SQLiteCommand(sqlite_conn);
            sqlite_cmd.CommandText = sql;
            int rowsUpdated = sqlite_cmd.ExecuteNonQuery();
            sqlite_conn.Close();
            return rowsUpdated;
        }

    }

}
