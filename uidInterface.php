<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Unique ID Generator</title>
	<!-- Bootstrap style sheet  -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<!-- Our style sheet  -->
	<!-- Jquery java file  -->
	<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
	<script>
	
	function genID()
	{
		var uidNumber = $("#uidNumber").val();
		var uidGroups = $("#uidGroups").val();
		var uidPrefix = $("#uidPrefix").val();
		var uidSeperator = $("#uidSeperator").val();
		var databaseCheck = $("#databaseCheck").prop("checked");
		var databaseInput = $("#databaseInput").prop("checked");
		var databaseHost = $("#databaseHost").val();
		var databaseName = $("#databaseName").val();
		var databaseTable = $("#databaseTable").val();
		var databaseColumn = $("#databaseColumn").val();
		var databaseUser = $("#databaseUser").val();
		var databasePass = $("#databasePass").val();
		
		var dbCheck = {"DB Host Name":databaseHost, "DB Name":databaseName, "DB Table":databaseTable, "DB Column":databaseColumn, "DB Username":databaseUser, "DB Password":databasePass};
		
		// if the alert box is shown hide it so we can re-write any messages before displaying
		if($("#alert").hasClass("visible"))
		{
			$("#alert").toggleClass("visible invisible");
		}
		
		if(databaseCheck)
		{
			// if gen pressed then field entered and button pressed again doesn't update for some reason
			if(!databaseHost || !databaseName || !databaseTable || !databaseColumn || !databaseUser || !databasePass)
			{
				var eString = "";
				for(let key in dbCheck)
				{
					if(!dbCheck[key])
					{
						if(!eString)
						{
							eString = key;
						}
						else
						{
							eString = eString + ", " + key;
						}
					}
				}
				if($("#alert").hasClass("invisible"))
				{
					$("#alert").toggleClass("invisible visible");
					$("#alert").text("Missing information from fields - " + eString);
				}
			}
			else
			{
				$.ajax({
					url: "uidGen.php",
					type: "post",
					data: {uidNumber:uidNumber, uidGroups:uidGroups, uidPrefix:uidPrefix, uidSeperator:uidSeperator, databaseHost:databaseHost, databaseName:databaseName, databaseTable:databaseTable, databaseColumn:databaseColumn, databaseUser:databaseUser, databasePass:databasePass, databaseInput:databaseInput},
					success: function(data){
						$("#uid").val(data);
					},
					dataType: "text"
				});
			}
		}
		else
		{
			$.ajax({
				url: "uidGen.php",
				type: "post",
				data: {uidNumber:uidNumber, uidGroups:uidGroups, uidPrefix:uidPrefix, uidSeperator:uidSeperator},
				success: function(data){
					$("#uid").val(data);
				},
				dataType: "text"
			});
		}
		
		/// Show loading message while waiting for ajax to be performed
		
		$(document).ajaxStart(function()
			{
				$("#uid").val("ID generating...");
			});
	}
	</script>
</head>
<body>
<main>
	
	<div class="container p-3 vh-100 justify-content-center align-items-center">
		<div class="row alert alert-danger justify-content-center invisible" id="alert" role="alert">
			A simple primary alert—check it out!
		</div>
		<div class="row p-3 border rounded" id="content">
			<h4>Generate Unique ID Number</h4>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<button class="btn btn-outline-success" type="button" onclick="genID()">Generate UID</button>
				</div>
				<input type="text" class="form-control" placeholder="" id="uid" value="Waiting for input..." aria-label="" aria-describedby="basic-addon1" disabled>
				<div class="input-group-append">
					<button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#config" aria-expanded="false" aria-controls="config"><i class="bi bi-gear"></i></button>
				</div>
			</div>
			<div class="collapse" id="config">
				<form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" class="needs-validation">
					
					<h5>Database Connection Settings</h5>
					<div class="form-text pb-4">If the UID is required to be checked against a database enter connection details below :-</div>
				
					<div class="row">
						<div class="col">
							<div class="mb-3">
								<input type="checkbox" class="form-check-input" name="databaseCheck" id="databaseCheck" aria-describedby="nameHelp"/>
								<label for="databaseCheck" class="form-label">Run a check against a Database?</label>
								<div id="nameHelp" class="form-text">Check the box if you require uid to be checked for duplicates in a database</div>
							</div>
						</div>
						
						<div class="col">				
							<div class="mb-3">
								<input type="checkbox" class="form-check-input" name="databaseInput" id="databaseInput" aria-describedby="nameHelp"/>
								<label for="databaseInput" class="form-label">Insert UID into Database?</label>
								<div id="nameHelp" class="form-text">Check if the generated UID is to be inserted into the database</div>
							</div>
						</div>
					</div>
				
					<div class="row">
						<div class="col">
							<div class="mb-3">
								<label for="databaseHost" class="form-label">Database Host</label>
								<input type="text" class="form-control" name="databaseHost" id="databaseHost" aria-describedby="nameHelp"/>
								<div id="nameHelp" class="form-text">Enter the hostname of the database e.g. localhost</div>
							</div>
						</div>
						
						<div class="col">				
							<div class="mb-3">
								<label for="databaseName" class="form-label">Database Name</label>
								<input type="text" class="form-control" name="databaseName" id="databaseName" aria-describedby="nameHelp"/>
								<div id="nameHelp" class="form-text">Enter the SQL database name</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col">
							<div class="mb-3">
								<label for="databaseTable" class="form-label">Database Table</label>
								<input type="text" class="form-control" name="databaseTable" id="databaseTable" aria-describedby="nameHelp"/>
								<div id="nameHelp" class="form-text">Enter the name of the SQL table to query</div>
							</div>
						</div>
					
						<div class="col">
							<div class="mb-3">
								<label for="databaseColumn" class="form-label">Database Column</label>
								<input type="text" class="form-control" name="databaseColumn" id="databaseColumn" aria-describedby="nameHelp"/>
								<div id="nameHelp" class="form-text">Enter the column name of the table to compare the result to</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col">
							<div class="mb-3">
								<label for="databaseUser" class="form-label">Database Username</label>
								<input type="text" class="form-control" name="databaseUser" id="databaseUser" aria-describedby="nameHelp"/>
								<div id="nameHelp" class="form-text">Enter a username that has access to the table to query</div>
							</div>
						</div>
					
						<div class="col">
							<div class="mb-3">
								<label for="databasePass" class="form-label">Database Password</label>
								<input type="text" class="form-control" name="databasePass" id="databasePass" aria-describedby="nameHelp"/>
								<div id="nameHelp" class="form-text">Enter the password for the username</div>
							</div>
						</div>
					</div>
					
					<h5>UID Options</h5>
					<div class="form-text">The default format for the generated UID is uid-xxxxx-xxxxx-xxxxx-xxxxx-xxxxx :-</div>

					
					<div class="row">
						<div class="col">
							<div class="mb-3">
								<label for="uidNumber" class="form-label">Change the amonut of numbers per group</label>
								<input type="number" class="form-control" name="uidNumber" id="uidNumber" aria-describedby="nameHelp" min="1" max="10" value="5" required />
								<div id="nameHelp" class="form-text">Enter a number between 1 to 10</div>
							</div>
						</div>
					
						<div class="col">
							<div class="mb-3">
								<label for="uidGroups" class="form-label">Change the number of groups that are generated</label>
								<input type="number" class="form-control" name="uidGroups" id="uidGroups" aria-describedby="nameHelp" min="1" max="10" value="5" required />
								<div id="nameHelp" class="form-text">Enter a number between 1 to 10</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col">
							<div class="mb-3">
								<label for="uidPrefix" class="form-label">Prefix</label>
								<input type="text" class="form-control" name="uidPrefix" id="uidPrefix" aria-describedby="nameHelp" value="uid" />
								<div id="nameHelp" class="form-text">If a prefix is required before the number enter it here</div>
							</div>
						</div>
					
						<div class="col">
							<div class="mb-3">
								<label for="uidSeperator" class="form-label">Seperator</label>
								<input type="text" class="form-control" name="uidSeperator" id="uidSeperator" aria-describedby="nameHelp" value="-" maxlength="1" required />
								<div id="nameHelp" class="form-text">If a different seperator is needed enter it here</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</main>

<!-- Bootstrap java file  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>
</html>