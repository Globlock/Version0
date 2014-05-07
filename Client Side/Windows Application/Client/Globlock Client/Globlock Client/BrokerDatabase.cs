using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.SQLite;
using System.Data;

namespace Globlock_Client {
    
    public class BrokerDatabase {

        /// <summary>
        /// Path & Version declarations
        /// </summary>
        private string databasePath, absolutePath, connectionData, databaseFilename;
        private int sqlLiteVersion = 3;

        /// <summary>
        /// SQLite Connection Objects
        /// </summary>
        private SQLiteConnection sqlite_conn;
        private SQLiteCommand sqlite_cmd;
        private SQLiteDataReader sqlite_datareader;

        /// <summary>
        /// Data & Result Objects
        /// </summary>
        private Dictionary<String, String> data;
        private DataTable resultData;
        private int rowsUpdated;

        /// <summary>
        /// Default Schema SQL statments
        /// </summary>
        private string[] schema = { "CREATE TABLE IF NOT EXISTS Sessions( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,[sessiontoken] VARCHAR(250),[datemodified] datetime);", 
                                    "CREATE TABLE IF NOT EXISTS Transactions( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,[desc] VARCHAR(250),[datemodified] datetime);",
                                    "CREATE TABLE IF NOT EXISTS UserTable( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,[username] VARCHAR(250),[password] VARCHAR(250),[super] VARCHAR(2),[current] VARCHAR(2));"};
        
        /// <summary>
        /// Constructor for BrokerDatabase
        /// </summary>
        /// <param name="databasePath">Folder Path (string) to folder containing the database file</param>
        /// <param name="databaseFilename">Filename (string) of the DB file to be used</param>
        public BrokerDatabase(string databasePath, string databaseFilename) {
            this.databasePath = databasePath;
            this.databaseFilename = databaseFilename;
            this.absolutePath = System.IO.Path.Combine(databasePath, databaseFilename);
            this.connectionData = "Data Source=" + absolutePath + ";version=" + sqlLiteVersion + ";New=True;Compress=True;";
            this.createConnection();
        }

        /// <summary>
        /// Creates a connection to the database and passes boolean for schema to be created
        /// </summary>
        private void createConnection() {
            createConnection(File.Exists(this.absolutePath));
        }

        /// <summary>
        /// Creates a connection to the database
        /// </summary>
        /// <param name="schema">Boolean value that determines if database schema exists (Folder structure and database created if not)</param>
        private void createConnection(bool schema) {
            sqlite_conn = new SQLiteConnection(connectionData);
            if (!schema) {
                System.Diagnostics.Debug.WriteLine("Database Not Found - Attempting to Create...");
                System.IO.Directory.CreateDirectory(databasePath);
                createSchema();
                databaseTransaction("New Database Created!");
            } else {
                System.Diagnostics.Debug.WriteLine("Database Found - Attempting to Connect...");
                databaseTransaction("Connection Attempt");
            }
        }

        /// <summary>
        /// Creates the database Schema by running each statement from the 'schema' array of string values
        /// </summary>
        private void createSchema() {
            try {
                foreach (String createTable in schema) {
                    this.openConnection(createTable);
                        int rowsUpdated = sqlite_cmd.ExecuteNonQuery();
                    this.closeConnection();
                }
                System.Diagnostics.Debug.WriteLine("Database Schema Created!");
            } catch (Exception e) {
                Console.WriteLine("Error occured! " + e);
            }
        }

        /// <summary>
        /// Method that returns correctly qualified/formatted string for datetime entry to SQLite database
        /// </summary>
        /// <param name="datetime">DateTime value to be converted to SQLite format</param>
        /// <returns>SQLite Formatted datatime value</returns>
        private string DateTimeSQLite(DateTime datetime) {
            // Modified from http://techreadme.blogspot.ie/2012/11/sqlite-read-write-datetime-values-using.html
            string dateTimeFormat = "{0}-{1}-{2} {3}:{4}:{5}.{6}";
            return string.Format(dateTimeFormat, datetime.Year, datetime.Month, datetime.Day, datetime.Hour, datetime.Minute, datetime.Second,datetime.Millisecond);
        }

        /// <summary>
        /// Method that Writes a value to the Transactions table of the SQLite database, by calling databaseTransaction, for each value in the array parameter
        /// </summary>
        /// <param name="descriptions">Array of values to be written to the database</param>
        internal void databaseTransaction(string[] descriptions) {
            foreach (string desc in descriptions) {
                databaseTransaction(desc);
            }
        }

        /// <summary>
        /// Method that Writes a string value to the description column and current datetime in the Transactions table of the SQLite database
        /// </summary>
        /// <param name="description">Value to be written to the description field of the table</param>
        internal void databaseTransaction(string description) {
            data = new Dictionary<String, String>();
            data.Add("desc", description);
            data.Add("datemodified", DateTimeSQLite(DateTime.Now));
            try {
                this.insertData("Transactions", data);
            } catch (Exception e) {
                System.Diagnostics.Debug.WriteLine("Error occured! " + e);
            }
        }

        /// <summary>
        /// Inserts the data (passed to the method in the format of a Data Dictionary of key value pairs) to a specified table in the SQLite database
        /// </summary>
        /// <param name="tableName">Table in the SQLite database to be written to</param>
        /// <param name="data">Key Value pairs to be inserted to the table in the SQLite database</param>
        /// <returns>Boolean value representing the success of the insert operation</returns>
        public bool insertData(String tableName, Dictionary<String, String> data) {
            String cols = "", values = "";
            bool insertSuccess = true;
            foreach (KeyValuePair<String, String> val in data) {
                cols += String.Format(" {0},", val.Key.ToString());
                values += String.Format(" '{0}',", val.Value);
            }
            cols = cols.Substring(0, cols.Length - 1); //remove final comma
            values = values.Substring(0, values.Length - 1);
            try {
                this.executeNonQuery(String.Format("insert into {0}({1}) values({2});", tableName, cols, values));
            } catch(Exception e) {
                System.Diagnostics.Debug.WriteLine("Error occured! " + e);
                insertSuccess = false;
            }
            return insertSuccess;
        }

        public bool updateData(String tableName, Dictionary<String, String> data, String where)    {
            String vals = "";
            Boolean returnCode = true;
            if (data.Count >= 1) {
                foreach (KeyValuePair<String, String> val in data) {
                    vals += String.Format(" {0} = '{1}',", val.Key.ToString(), val.Value.ToString());
                }
                vals = vals.Substring(0, vals.Length - 1);
            }
            try {
                this.executeNonQuery(String.Format("update {0} set {1} where {2};", tableName, vals, where));
            } catch (Exception e){
                System.Diagnostics.Debug.WriteLine("Error occured! " + e);
                returnCode = false;
            }
            return returnCode;
        }

        public bool deleteData(String tableName, String where) {
            Boolean returnCode = true;
            try {
                this.executeNonQuery(String.Format("delete from {0} where {1};", tableName, where));
            }
            catch (Exception e) {
                System.Diagnostics.Debug.WriteLine("Error occured! " + e);
                returnCode = false;
            }
            return returnCode;
        }

        /// <summary>
        /// Executes a non query, and returns the number of rows affected by the transaction
        /// Modified from: http://www.dreamincode.net/forums/topic/157830-using-sqlite-with-c%23/
        /// </summary>
        /// <param name="sql">SQL Command to execute on the SQLite DB</param>
        /// <returns></returns>
        public int executeNonQuery(string sql) {
            this.openConnection(sql);
                int rowsUpdated = sqlite_cmd.ExecuteNonQuery();
            this.closeConnection();
            return rowsUpdated;
        }

        public DataTable getDataTable(string sql) {
            DataTable dataTable = new DataTable();
            try {
                System.Diagnostics.Debug.WriteLine(string.Format("Attempting to run query[{0}]", sql));
                this.openConnection(sql);
                    sqlite_datareader = sqlite_cmd.ExecuteReader();
                    dataTable.Load(sqlite_datareader);
                    sqlite_datareader.Close();
                this.closeConnection();
        } catch (Exception e) {
            System.Diagnostics.Debug.WriteLine("Error occured!" + e);
        }
            return dataTable;
        }

        public bool userExists(string username, string password) { 
            string where = string.Format("username = {0} AND password = {1}", username, password);
            DataTable resultData = queryTable("UserTable", new string[] { "username", "password", "super" }, where);
            return (resultData.Rows.Count >= 1);
        }

        public DataTable getCurrentUser() {
            string where = "current = 1";
            resultData = queryTable("UserTable", new string[] { "username", "password", "super" }, where);
            if (resultData.Rows.Count == 1) {
                return resultData;
            } else { 
                // Mark all as non current
                markNonCurrent();
            }
            return new DataTable();
        }

        public void markNonCurrent() {
            // Mark all as non current
            data = new Dictionary<String, String>();
            data.Add("current", "0");
            updateData("UserTable", data, String.Format("UserTable.current = {0}", "1"));
        }

        public string[] listAllUsers() {
            List<String> userList = new List<String>();
            DataTable temp = queryTable("UserTable", new string[]{"username"}, "1");
            foreach (DataRow row in temp.Rows) {
                userList.Add(row["username"].ToString());
            }
            return userList.ToArray();
        }


        private DataTable queryTable(string table, string[] values, string where) { 
            DataTable resultData;
            string cols = "";
            foreach (string col in values) { 
                cols += table+"."+col + ", ";
            }
            cols = cols.Substring(0, cols.Length - 2);
            string query = string.Format("SELECT {0} FROM {1} WHERE {2} ;",cols,table,where);
            resultData   = getDataTable(query);
            foreach (DataRow row in resultData.Rows) {
                foreach (string col in values) { 
                    string echoString = string.Format("COLUMN {0} [{1}];" , col, row[col]);
                    System.Diagnostics.Debug.WriteLine(echoString);
                }
            }
            return resultData;
        }
        private void testUpdate(string table) {
            Dictionary<String, String> data = new Dictionary<String, String>();
            data.Add("desc", "Test Insert 2 - Update");
            updateData(table, data, String.Format("Transactions.id = {0}", "2"));
        }

        public void testQuery() {
            DataTable resultData = getDataTable2("SELECT desc, datemodified FROM Transactions");
            string[] values = {"desc", "datemodified"};
            foreach (DataRow row in resultData.Rows) {
                foreach (string col in values) {
                    string echoString = string.Format("COLUMN {0} [{1}];", col, row[col]);
                    System.Diagnostics.Debug.WriteLine(echoString);
                }
            }
        }

        #region Open/Close
        public void openConnection(string sql) {
            //sqlite_conn = new SQLiteConnection(connectionData);
            sqlite_conn.Open();
            sqlite_cmd = new SQLiteCommand(sqlite_conn);
            sqlite_cmd.CommandText = sql;
        }

        public void closeConnection() { sqlite_conn.Close(); }
        #endregion 

        public DataTable getDataTable2(string sql) {
            DataTable dt = new DataTable();
            try {
                SQLiteConnection cnn = new SQLiteConnection(connectionData);
                cnn.Open();
                SQLiteCommand mycommand = new SQLiteCommand(cnn);
                mycommand.CommandText = sql;
                SQLiteDataReader reader = mycommand.ExecuteReader();
                dt.Load(reader);
                reader.Close();
                cnn.Close();
            } catch (Exception e) {
                System.Diagnostics.Debug.WriteLine("Error occured! " + e);
            }
            return dt;
        }

    }

}
