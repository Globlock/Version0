<?php
#---------------------------------------------------------------#
# Globe Handler for File Access API - Globlock
# Filename:	h_globe_handler.php
# Version: 	2.0
# Author: 	Alex Quigley, x10205691
# Created: 	04/03/2014
# Updated: 	20/05/2014
#---------------------------------------------------------------#
# Dependencies:
# 	GLOBLOCK.php (parent)
#	s_logWrite.php
#---------------------------------------------------------------#
# Description: 
# 	Handles globe requests, such as globe validation
#	Only accessed with valid SESSION and USER information
#---------------------------------------------------------------#
# Successful Operation Result:
# 	Returns the required information / handles globes 
#	and DB transactions
#---------------------------------------------------------------#
# Usage: 
#	include 'h_globe_handler.php';
#---------------------------------------------------------------#

	/** VALIDATE GLOBE
	 * Checks globe object against the database and assigns assoc project, 
	 * possible actions and project name to the broker and returns true if found.
	 * returns false if not found.
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function gh_validateGlobe(&$broker){
			writeLogInfo("GLOBE[VAL_GLB]:Attempting Validation");
			if (!(isset($_POST["globe_id"]))) throw new Exception("Exception Thrown (EMPTY GLOBE OBJECT):");
			$broker->setValue('globe', "id", $_POST["globe_id"]);
			$globe_object = $broker->brokerData['globe']['id'];
			$query = "select_globe_asset"; $record = "asset_id";
			$result = dbb_selectGlobeAsset($query, $record, "s", $globe_object);
			if($result >= 1){
				$query = "select_globe_project"; $record = "globe_name";
				$project = dbb_selectGlobeProject($query, $record, 's', $globe_object);
				writeLogInfo("GLOBE[VAL_GLB]:ValidGlobe '$globe_object' for '$project'");
				$broker->setValue('globe', "project", $project);
				$broker->setValue('status', "assigned", "true");
				$broker->setValue('action', "drop", "true");
				$broker->setValue('action', "push", "true");
				$broker->setValue('action', "pull", "true");
				$broker->setValue('status', "assigned", "true");
				$broker->setValue('action', "set", "false");
				return -1;
			} else {
				writeLogInfo("GLOBE[VAL_GLB]:Unassigned Globe!");
				$query = "select_globe_project_unnassigned";
				$list = dbb_selectUnnassignedProjects($query);
				listProjectInBroker($broker, $list);
				$broker->setValue('status', "assigned", "false");
				$broker->setValue('action', "set", "true");
				return 1;
			}
		}

	/** LIST PROJECT IN BROKER
	 * Formats a list passed as a param and inserts/updates on the broker passed by reference
	 * [required] Parameter $list, which defines the values to be added to broker.
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function listProjectInBroker(&$broker, $list){
			writeLogInfo("GLOBE[LST_PRJ]:Listing Projects");
			$index = 0;
			$broker->setValue('status', "assigned", "False");
			$broker->setValue('action', "set", "True");
			$broker->setValue('list', "count", $list[$index]);
			$list = array_slice($list, 1);	
			asort($list);	// Sort A-Z
			foreach ($list as $listItem) {
				$broker->setValue('listitem', $index, $listItem);
				$index++;
			}
		}

	/** SET GLOBE PROJECT
	 * Sets the globe objects assignment to a globe project
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function gh_setGlobeProject(&$broker){
			writeLogInfo("GLOBE[SET_PRJ]:Attempting to Set");
			try{
				if ((gh_validateGlobe($broker))==1){
					if (!(isset($_POST["globe_project"]))) throw new Exception("Exception Thrown (EMPTY GLOBE PROJECT)");
					$broker->setValue('globe', "project", $_POST["globe_project"]);
					$globe_project = $broker->brokerData['globe']['project'];
					$globe_object = $broker->brokerData['globe']['id'];
					$query = "select_globe_id"; $record = "globe_id";
					$unassigned = ($broker->brokerData['status']['assigned'] == "false");
					$setable = ($broker->brokerData['action']['set'] == "true");
					if ($unassigned && $setable){
						writeLogInfo("GLOBE[SET_PRJ]:Set Possible!");
						$globe_id = dbb_selectGlobeID($query, $record, 's', $globe_project);
						if($globe_id > 0){
							$query = "insert_globe_asset";
							$insert_id = dbb_insertNewAsset($query, 'si', $globe_object, $globe_id);
							if ($insert_id < 1) throw new Exception("Exception Thrown (Unable to Insert Asset)");
							writeLogInfo("GLOBE[SET_PRJ]:'$globe_object' assigned to '$globe_project'!");
							$broker->setValue('status', "assigned", "true");
							$broker->setValue('action', "set", "false");
							$broker->setValue('header', "message", "SUCCESSFULLY ASSIGNED GLOBE OBJECT TO ".$globe_project."!");
						} else {
							writeLogInfo("GLOBE[SET_PRJ]:Project not found!", -1);
							throw new Exception("Exception Thrown (GLOBE PROJECT NOT FOUND)");
						}
					} else {
						$broker->setValue('header', "message", "GLOBE ALREADY ASSIGNED TO A PROJECT");
						$broker->setValue('status', "assigned", "true");
						$broker->setValue('action', "set", "false");
						writeLogInfo("GLOBE[SET_PRJ]:CANNOT SET OR OBJECT ALREADY ASSIGNED!", -1);
						throw new Exception("Exception Thrown (CANNOT SET OR OBJECT ALREADY ASSIGNED)");
					}
				}
			}catch(Exception $e){
				writeLogInfo("GLOBE[SET_PRJ]:Error occured during SET!", 1);
				$broker->setValue('header', 'message', 'Something aint right: '. $e);
			}
		}

	/** SET GLOBE REVISION DETAILS
	 * Gets the globe revision details for the specific globe project/object
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function gh_getGlobeRevisionDetails(&$broker){
			writeLogInfo("GLOBE[GLB_REV]:Attempting GET");
			try {
				$fileDetails = array("globe_id" => -1, 'asset_revision'=>-1);
				if ((gh_validateGlobe($broker))==-1){
					$globe_project = $broker->brokerData['globe']['project'];
					$globe_object = $broker->brokerData['globe']['id'];
					$query = "select_globe_id"; $record = "globe_id";
					$globe_id = dbb_selectGlobeID($query, $record, 's', $globe_project);
					$query = "select_globe_revision"; $field_name = "asset_revision";
					$revision_id = dbb_selectGlobeRevision($query, $field_name,'s', $globe_object );
					$fileDetails['globe_id']=$globe_id;
					$fileDetails['asset_revision']=$revision_id;
					writeLogInfo("GLOBE[GLB_REV]:GlobeID[$globe_id] is at revision [$revision_id]");
				}
				return $fileDetails;
			} catch(Exception $e){
				writeLogInfo("GLOBE[GLB_REV]:Error occured during GET!", 1);
			}
		}

	/** ASSET REVISION
	 * Gets the asset revision details for the specific globe project/object
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function gh_AssetRevision(&$broker){
			writeLogInfo("GLOBE[ASST_REV]:Attempting GET");
			$globe_object = $broker->brokerData['globe']['id'];
			$query = "select_globe_revision"; $field_name = "asset_revision";
			$revision_id = dbb_selectGlobeRevision($query, $field_name,'s', $globe_object);
			writeLogInfo("GLOBE[ASST_REV]:Revision=$revision_id");
			return $revision_id;
		}

	/** UPDATE REVISION
	 * Updates the globe revision details for the specific globe project/object
	 * [required] Parameter $revision, defines the revision.
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function gh_updateRevision(&$broker, $revision){
			writeLogInfo("GLOBE[UPD_REV]:Attempting Update");
			$globe_object = $broker->brokerData['globe']['id'];
			$query = "update_asset_revision";
			$result = dbb_updateAssetRevision($query, $globe_object);
			writeLogInfo("GLOBE[UPD_REV]:Result=$result");
			return $result;
		}

	/** SEARCH GLOBE PROJECT
	 * Gets the globe id details for the specific globe project for the object supplied
	 * [required] Parameter $broker by reference, defines broker to be used.
	 */
		function gh_searchGlobeProject(&$broker){
			writeLogInfo("GLOBE[SRCH_PRJ]:Attempting Search");
			$globe_object = $broker->brokerData['globe']['id'];
			$query = "select_globe_id_from_object"; 
			$record_id = "globe_id";
			$params = 's';
			$result = dbb_selectGlobeID($query, $record_id, $params, $globe_object);
			writeLogInfo("GLOBE[SRCH_PRJ]:Result=$result");
			return $result;
		}
?>