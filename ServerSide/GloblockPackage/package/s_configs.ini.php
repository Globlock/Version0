;<?php
;die(); // For further security
;/*

;Project Information
[project_info]
name="Globlock"
description="Globlock - Concurrency controlled 2 phase file access, version control and repository system"
version= 0.2
runmode=test


;Logfile information for logWrite functions
[logs_transactions]
directory="LogFiles/"
filename="transactions.log"
[logs_security]
directory="LogFiles/"
filename="security_err.log"
[logs_system]
directory="LogFiles/"
filename="system_error.log"session

[file_locations]
server_address="http://localhost/"
sysroot_directory="http://localhost/16052014/"
storage_directory="Storage"
working_directory="Current"
archive_directory="Archive"
publish_directory="Publish"
document_directory="Documents"

;Types of files that can be uploaded through management suite
[file_upload_types]
ext[]="doc"
ext[]="docx"
ext[]="xls"
ext[]="xlsx"
ext[]="txt"
ext[]=""

;Database Login and Host Information
[database_info]
db_host="127.0.0.1"
db_name="gb_production"
;db_name="globlock_test"
db_user="root"
db_pass=""

;SQL Statements
[database_statements]
test_table="SELECT 1 from gb_sessions"
table_sessions="CREATE TABLE IF NOT EXISTS gb_sessions (session_id int(11) NOT NULL AUTO_INCREMENT, session_token VARCHAR(128) NOT NULL DEFAULT '1',session_activity int(11) NOT NULL DEFAULT '0',session_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (session_id))"


insert_session_token="INSERT INTO gb_sessions(session_id , session_token, session_activity, session_create) VALUES (null, ?,?, CURRENT_TIMESTAMP)"
select_active_session="SELECT session_id FROM gb_sessions WHERE session_activity =? AND session_token= ?"
update_active_session="UPDATE gb_sessions SET session_activity=? WHERE session_id = ?"

select_globe_asset="SELECT asset_id FROM gb_assets WHERE asset_object =?"
select_globe_project="SELECT gb_globes.globe_name FROM gb_globes, gb_assets WHERE gb_assets.globe_id = gb_globes.globe_id AND gb_assets.asset_object = ?"
select_globe_id="SELECT globe_id FROM gb_globes WHERE globe_name = ?"
insert_globe_asset="INSERT INTO gb_assets(asset_id , asset_object, asset_revision, asset_create, globe_id) VALUES (null, ?, 0, CURRENT_TIMESTAMP, ?)"
select_globe_project_unnassigned="SELECT gb_globes.globe_name FROM gb_globes LEFT JOIN gb_assets ON gb_globes.globe_id = gb_assets.globe_id WHERE gb_assets.globe_id IS NULL"
select_globe_id_from_object="SELECT globe_id FROM gb_assets WHERE asset_object = ?"
select_globe_revision="SELECT asset_revision FROM gb_assets WHERE asset_object = ?"

insert_new_document="INSERT INTO gb_documents (document_id, doc_owner, doc_name, doc_desc, doc_filename, doc_type, doc_create) VALUES (null, '0',?,?,?,?, CURRENT_TIMESTAMP)"
insert_new_group="INSERT INTO gb_groups (group_id, group_owner, group_name, group_desc, group_create) VALUES (null, '0',?,?, CURRENT_TIMESTAMP)"
insert_new_globe="INSERT INTO gb_globes (globe_id, globe_name, globe_desc, globe_code, globe_create, globe_owner, globe_asset) VALUES (null ,  ?,  ?,  'GB', CURRENT_TIMESTAMP , '0', null)"
insert_new_user="INSERT INTO gb_users (user_id,user_name,user_password,user_first,user_last,user_email,user_dept,group_id,user_super,user_create) VALUES (NULL, ?, ?, ?, ?, ?,  ?,  '0',  ?, CURRENT_TIMESTAMP)"
insert_new_groupuser="INSERT INTO gb_users (user_id,user_name,user_password,user_first,user_last,user_email,user_dept,group_id,user_super,user_create) VALUES (NULL, ?, ?, ?, ?, ?,  ?,  ?,  ?, CURRENT_TIMESTAMP)"

select_all_documents="SELECT doc_name, doc_desc, doc_filename, doc_create FROM gb_documents"
select_all_groups="SELECT group_name, group_desc, group_create FROM gb_groups"
select_all_globes="SELECT globe_name, globe_desc, globe_create FROM gb_globes"
select_all_groupids="SELECT group_id, group_name FROM gb_groups"
select_all_users="SELECT gb_users.user_id, gb_users.user_name AS name, gb_users.user_last AS last, gb_users.user_email AS email, IF( gb_groups.group_name IS NULL , 'Undefined', gb_groups.group_name ) AS groupname, IF( gb_users.user_super =1, 'Yes',  'No' ) AS superuser FROM gb_users LEFT JOIN gb_groups ON gb_groups.group_id = gb_users.group_id"

table_users="CREATE TABLE IF NOT EXISTS gb_users (user_id int(11) NOT NULL AUTO_INCREMENT, user_name VARCHAR(64) NOT NULL DEFAULT '1',user_password int(11) NOT NULL DEFAULT '0',user_super int(1)NOT NULL DEFAULT '0',user_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (user_id))"
table_documents="CREATE TABLE IF NOT EXISTS gb_documents (document_id int(11) NOT NULL AUTO_INCREMENT, doc_owner int(11) NOT NULL DEFAULT '0', doc_name varchar(120) NOT NULL , doc_desc varchar(250) NOT NULL, doc_filename varchar(250) NOT NULL, doc_type varchar(10) NOT NULL, doc_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (document_id))"
table_groups="CREATE TABLE IF NOT EXISTS gb_groups (group_id int(11) NOT NULL AUTO_INCREMENT, group_owner int(11) NOT NULL DEFAULT '0', group_name varchar(120) NOT NULL , group_desc varchar(250) NOT NULL, group_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (group_id))"

search_user="SELECT * FROM gb_users WHERE user_name = ? AND user_password = ?"
search_super="SELECT * FROM gb_users WHERE user_name = ? AND user_pass = ? AND user_super = '1'"
table_globes="CREATE TABLE IF NOT EXISTS gb_globes (globe_id int(11) NOT NULL AUTO_INCREMENT, globe_name varchar(120) NOT NULL, globe_desc varchar(250) DEFAULT NULL, globe_code varchar(10) DEFAULT NULL, globe_create datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, globe_owner int(11) NOT NULL, PRIMARY KEY (globe_id))"
table_assets="CREATE TABLE IF NOT EXISTS gb_assets ( asset_id int(11) NOT NULL AUTO_INCREMENT, asset_object varchar(120) NOT NULL, asset_revision varchar(120) NOT NULL DEFAULT '0', PRIMARY KEY (asset_id))"
insert_placeholder="INSERT INTO gb_sessions (session_id, session_token, session_activity ) VALUES ( NULL, 0, 0 )"
select_session_id="SELECT session_id FROM gb_sessions WHERE session_activity =? AND session_token=?"
select_session="SELECT session_id FROM gb_sessions WHERE session_activity =? AND session_token=?"
select_session_token="SELECT session_id FROM gb_sessions WHERE session_token =? AND session_activity != -1"
update_session_activity="UPDATE gb_sessions SET session_activity=? WHERE session_id = ?;"
update_session_token="UPDATE gb_sessions SET session_token=?,  session_activity=?  WHERE session_id = ?;"

update_token="UPDATE gb_sessions SET session_activity =?, session_token=? WHERE session_id =?"
update_session="UPDATE gb_sessions SET session_activity =?, session_token=? WHERE session_id =?"
dispose_session="UPDATE gb_sessions SET session_activity =-1 WHERE session_token =?  AND session_activity != -1"
dispose_expired="UPDATE gb_sessions SET session_activity =-1 WHERE DATE_SUB(NOW(),INTERVAL 1 HOUR) > session_create AND session_activity != -1"
verify_user="SELECT * FROM gb_users WHERE user_name = ? AND user_pass = ?"
search_globe="SELECT * FROM globe_assets_test WHERE object = ?"
search_project="SELECT * FROM globes_test WHERE globe_name = ?"
search_project_by_globe="SELECT globes_test.globe_name FROM globes_test, globe_assets_test WHERE globe_assets_test.asset_id = globes_test.globe_asset AND globe_assets_test.object = ?"
unassigned_globes="SELECT globe_name FROM globes_test WHERE globe_asset = 0"
ins_new_asset="INSERT INTO globe_assets_test (asset_id, object, Revision_id ) VALUES ( NULL, ?, 0)"
update_asset="UPDATE globes_test SET globe_asset=? WHERE globe_name=?"
search_revision="SELECT globe_assets_test.Revision_id FROM globe_assets_test INNER JOIN globes_test ON globe_assets_test.asset_id = globes_test.globe_asset WHERE globes_test.globe_id = ?"
increment_revision="UPDATE globe_assets_test SET Revision_id = Revision_id + 1 WHERE asset_id = ?"
drop_asset="UPDATE globes_test, globe_assets_test SET globes_test.globe_asset = 0, globe_assets_test.object = "DROPPED" WHERE globe_assets_test.asset_id = globes_test.globe_asset AND globe_assets_test.object = ?"


;Session stages //TO DO (update with latest list)
[session_stages]
dropped_sessions="-1"
initialise_token="0"
session_requests="1"
globe_validation="2"
globe_deallocate="3"
globe_assignment="4"
globe_pull_assoc="5"
globe_push_assoc="6"

;Encryption Salt values
[encryption_salts]
handshake="HANDSHAKE:abc123_GloblockDevelopmentTest"
session="Session:laundrytokens"
other="Other:abc123_GloblockDevelopmentTest"
default="Default:abc123_GloblockDevelopmentTest"

[list_items]
list1[]=1
list1[]=2
list1[]=3
list1[]=4

[sample_broker]
header["type"]="TEST_HEAD"
header["message"]="Test messsage"
error["code"]=0001
error["message"]="No errors"
user["name"]="sampleuser@gmail.com"
user["pass"]="pass123"
session["token"]="abcd1234"
globe["id"]=0000
globe["project"]=0000
status["assigned"]=true
action["test"]=true
action["set"]=false
action["abort"]=false
action["redo"]=false
action["drop"]=false
action["pull"]=false
action["push"]=false
list["count"]=2
list["size"]='6.6mb'
list["root"]='globlock.com/ABCD1234/'
list[]="file0.txt"
list[]="file1.txt"
list[]="file2.txt"
list[]="file3.txt"
list[]="file4.txt"
list[]="file5.txt"
list[]="file6.txt"
list[]="file7.txt"
list[]="file8.txt"
list[]="file9.txt"

;
[empty_broker]
header["type"]="-"
header["message"]="-"
error["code"]=0000
error["message"]="-"
user["name"]="-"
user["pass"]="-"
session["token"]="-"
globe["id"]=0000
globe["project"]=0000
status["assigned"]=false
action["test"]=false
action["set"]=false
action["abort"]=false
action["redo"]=false
action["drop"]=false
action["pull"]=false
action["push"]=false
list["count"]=0
list["size"]='-'
list["root"]='-'
listitem[]=""


;List of different error codes for return
[error_codes]
code[404]="Not Found"


;*/

;?>