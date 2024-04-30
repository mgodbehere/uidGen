# uidGen
 uidGen is a function that generates a unique id based on a set of parameters. It also contains functions for updating and checking the id against a SQL database

## Installation
Copy the uidGen.php & uidInterface.php files onto a server running PHP then in a browser navigate to the uidInterface.php file to view a demo of its usage.
Or copy the uidGen.php into your project file then use the function include("PATH TO uidGen.php")

// ADD IN DB CONNECTION DETAILS

## Usage
Call the id_gen(int Length, int LengthGroups, string Prefix, string Seperator, array Database(optional)) function to generate an id number

```PHP
include(uidGen.php);

// returns uid-12345-12345-12345
echo(id_gen(5, 3, "uid", "-"));

// returns pic-99
echo(id_gen(2, 1, "pic", "-"));

// returns git/123/123/123/123/123/123/123/123
echo(id_gen(3, 8, "git", "/"));
```

To check the generated id number is not in use we can enter SQL details for it to check against id_gen expects an array with the following format - 

array("databaseHost" => string Host, "databaseName" => string Name, "databaseTable" => string Table, "databaseColumn" => string Column, "databaseUser" => string User, "databasePass" => string Password, "databaseInput" => bool)

If databaseInput is set to true it will then try to insert the id number to your database.

## Demo
A live running demo of this function with a frontend can be seen [here](http://mark.arklight.technology/code/uidgen/uidinterface.php)