<?php
// suppress any errors so we only use our error messages
	ini_set('display_errors', 0);
// generates unique id number for db records
// type determines the table and prefix that is used
function sql_connect($host, $user, $pass, $db)
{
	try
	{
		$con = mysqli_connect($host, $user, $pass, $db);
	}
	catch(Exception $e)
	{
		$con = false;
	}

	return $con;
}

function insertData($con, $table, $columns, $values)
{
	//$con = sql_connect();

	if(is_array($columns))
	{
		$cols = implode(", ", $columns);
		$values = array_map(function($val){return "'" . $val . "'";}, $values); // we add quotes to all array elements
		$vals = implode(", ", $values); // we then split the elements up
	}
	else
	{
		$cols = $columns;
		$vals = $values;
	}

	$statement = "INSERT INTO " . $table ." (" . $cols . ") VALUES (" . $vals . ")";

	mysqli_query($con, $statement);
	return mysqli_insert_id($con); // return the id of the instered record to use as an order number for the customer
}

function selectData($con, $column, $table, $where = "")
{
	if(is_array($where)) // checks if query has where parameters only arrays valid
	{
		$stateWhere = ""; // holds the where clause for the statment
		foreach($where as $col => $val)
		{
			if($col == array_key_last($where))
			{
				$stateWhere = $stateWhere . $col . "='" . $val . "'";
			}
			else
			{
				$stateWhere = $stateWhere . $col . "='" . $val . "' AND ";
			}
		}

		$statement = "SELECT " . $column . " FROM " . $table . " WHERE " . $stateWhere;
	}
	else
	{
		$statement = "SELECT " . $column . " FROM " . $table;
	}

	$query = mysqli_query($con, $statement);
	$queryData = mysqli_fetch_all($query, MYSQLI_ASSOC);

	return $queryData;
}

function id_gen($uidNumber, $uidGroups, $uidPrefix, $uidSeperator, $database = false)
{
//		$con = sql_connect();

	/// ***** NO error handling if type not in map throws up errors

	$uid = ""; // var ready to hold the generated id number
	$loopCount = 0; // var used to break loop if the UID is already in the database loops 10 times before exiting POSSIBLE config option
	while(True)
	{
		$numberGroups = range(1, $uidGroups);
		foreach($numberGroups as $group)
		{
			// get a series of random numbers determined by $uidNumber
			foreach(range(1,$uidNumber) as $num)
			{
				//echo($uidNumber."<br/>");
				//echo($num."<br/>");
				//$uid = $uid.mt_rand(0,9);
				$uid = $uid.mt_rand(0,9);
			}

			// Split the groups with the seperator
			if($group != end($numberGroups))
			{
				$uid = $uid.$uidSeperator;
			}
		}

		$uid = $uidPrefix != "" ? $uidPrefix.$uidSeperator.$uid : $uid; // completing the uid with a prefix if entered
//			$uid = "uid-uid-03393-14004-75991-99760-80704";

		// Once the uid is the required length we check if there is a database and handle that or break
		if(strlen($uid) >= ($uidNumber * $uidGroups))
		{
			// if database details have been entered we check the generated uid to database
			//var_dump($database);
			//if(in_array(false, $database, true) == false)
			if($database)
			{
				$conDB = sql_connect($database['databaseHost'], $database['databaseUser'], $database['databasePass'], $database['databaseName']); // try connecting to the database with entered details returns false if there is a failed connection

				if($conDB)
				{
					$uidCheck = selectData($conDB, $database['databaseColumn'], $database['databaseTable'], array("uid" => $uid)); // check if the generated uid is in the database
				}
				else
				{
					$uid = "There is an issue connecting to the database please check the connection details and try again."; // error message to display
				}

				// if the uid isn't in the database then we break the while loop
				if(!isset($uidCheck[0]))
				{
					if($database['databaseInput'])
					{
						insertData($conDB, $database['databaseTable'], array($database['databaseColumn']), array($uid));
					}
					break;
				}
				// if the uid exists then we clear the $uid and generate a new uid
				elseif(isset($uidCheck[0]))
				{
					$loopCount++; // increase the loopCount check var
					if($loopCount > 10)
					{
						$uid = "Tried X times all generated id numbers in use. Please change UID generation options or try again";
						break;
					}
					$uid = "";
				}
			}
			else
			{
//				insertData($conDB, $database['databaseTable'], array($database['databaseColumn']), array($uid));
				break;
			}
		}
	}
	return $uid;
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// check and assign DB connection vars
	$dbHost = !empty($_POST['databaseHost']) ? $_POST['databaseHost'] : false;
	$dbName = !empty($_POST['databaseName']) ? $_POST['databaseName'] : false;
	$dbTable = !empty($_POST['databaseTable']) ? $_POST['databaseTable'] : false;
	$dbColumn = !empty($_POST['databaseColumn']) ? $_POST['databaseColumn'] : false;
	$dbUser = !empty($_POST['databaseUser']) ? $_POST['databaseUser'] : false;
	$dbPass = !empty($_POST['databasePass']) ? $_POST['databasePass'] : false;
	$dbInput = str_contains($_POST['databaseInput'], "true") ? $_POST['databaseInput'] : false;
	$dbDetails = array("databaseHost" => $dbHost, "databaseName" => $dbName, "databaseTable" => $dbTable, "databaseColumn" => $dbColumn, "databaseUser" => $dbUser, "databasePass" => $dbPass, "databaseInput" => $dbInput);
	
	// check if any false elements in dbDetails if there is we throw a false flag so id_gen doesn't process bad db info
	$dbCheck = in_array(false, $dbDetails, true) ? false : $dbDetails;

	echo(id_gen($_POST['uidNumber'], $_POST['uidGroups'], $_POST['uidPrefix'], $_POST['uidSeperator'], $dbCheck));
//	$uid = id_gen($_POST['uidNumber'], $_POST['uidGroups'], $_POST['uidPrefix'], $_POST['uidSeperator'], array("databaseHost" => $dbHost, "databaseName" => $dbName, "databaseTable" => $dbTable, "databaseColumn" => $dbColumn, "databaseUser" => $dbUser, "databasePass" => $dbPass));
//	var_dump($_POST);
	foreach($_POST as $item)
	{
//		echo($item . "<br/>");
	}
}
else
{
	$uid = "Waiting for input...";
}
?>
