==========================
Monday, 01 November 2021
==========================

This Application was updated according to the email I received on Monday, 01 November 2021, you can find the text of the email in the file: "email-20211101.txt" (included in the folder of this App).

This application was made under the latest version of Symfony framework (at the date of: 27 October 2021), and according to the requirements given in the file: "PHP_Backend_Developer_Technical_Test.pdf" (included in the root file of the application).

This aplication uses the MYSQL server to persist data, it uses 2 databases, the first is called: "md-expenses" (the main database), and the other is: "md-expenses_test" (to be used by php unit/integration tests).

This application is tested under Linux operating system and apache server.

================================================================================================================
================================================================================================================

Installation
============

In order to run the app, follow these steps:

	0) this application includes the apache pack, so, you can call the urls of APIs wihthout the need to run the SYMFONY Server
	1) extract the folder: "md-expenses" to your root html
	2) the database connection parameter is in the file: ".env.local" (the variable: DATABASE_URL), change it according to your mysql server (but keep the name of database as it is)
	3) the database parameter for the test database is in the file: ".env.test", change it according to your mysql server (but keep the name of database as it is)
	4) to create the main database, go to your terminal, and type the following command: "php bin/console doctrine:database:create"
	5) to create the tables in the database, execute: "php bin/console doctrine:migrations:migrate"
	6) in order to create your test database, execute this command: "php bin/console doctrine:database:create --env=test"
	7) execute the command: "php bin/console doctrine:schema:create --env=test" to create the tables inside the test database

	8) As requested in the given test for the routes, in order to be able to call the URLs of this API , you need to change the default value of the variable: "DocumentRoot" inside the apache configuration,

	so, search your apache configuration file , and then change it (according to your file structure):
		EXAMPLE: replace: "DocumentRoot /var/www/html" by "DocumentRoot /var/www/html/md-expenses/public"

================================================================================================================
================================================================================================================

How to use the API functionnalities?, Examples
===============================================

In this section, I will use "curl" in order to make requests for the APIs,

All of the following commands, return json formated data as a response, the json returned includes some fields, the most important field is the one called "errors", the field "errors" is always an array, and it should be empty, otherwise, consider that your request has not succeeded.

Open the terminal of your computer,

1) Insert an expense into the database, the following command will insert a new record in the database:
	* curl -X POST http://localhost/v1/expenses/ -H 'Content-Type: application/json' -d '{"description": "description test", "value": "100.23"}'

2) Fetch an expense from the database:
		* the following command example will list the details of the expense of ID=1
		  curl -X GET http://localhost/v1/expenses/1

		* the following command example will list all the records in the table "expense"
		  curl -X GET http://localhost/v1/expenses

3) Update a given object "expense" (starting from the value of the ID), example, the following command will update the fields of the object having: ID=1
	* curl -X PUT http://localhost/v1/expenses/1 -H 'Content-Type: application/json' -d '{"description": "description test - updated","value":"20000.23"}'

4) Delete a given object "expense", starting from the ID value, example, the following command will delete the object having: ID=1
	* curl -X DELETE http://localhost/v1/expenses/1

================================================================================================================
================================================================================================================

Unit tests and Integration tests
=================================

** The application includes 2 simple unit test (just to show how to test a single class and mocking the depedencies) , and one integration test which call the test database and make the needed assertions (its under: "md-expenses/tests/Service")

** In order to execute all the tests, you just need to run this command: "php ./vendor/bin/phpunit"

** Unit tests are not dependent on data from a previous unit test. The tests are able to run independently or in a random order.
	For example: to run only the test which update an expense, run the following command: "php ./vendor/bin/phpunit --filter testApiUpdate"

	In the following list, you can find a list of the names of all the tests we have in this app:

		-- testApiAdd: integration test, tests the add a new expense function
		-- testApiListOne: integration test, tests the function which lists only one object "expense"
		-- testApiListAll: integration test, tests the function which lists all the objects "expense" from the database
		-- testApiUpdate: integration test, tests the function which updates a given object "expense"
		-- testApiDelete: integration test, tests the function which deletes a given object "expense" from the database
		-- testAddMethod: unit test, tests the function "add" in the controller "expense" 
		-- testExpenseBehavior: unit test, tests some funtionnality in the entity "expense"

** All the functionnalities used in the controller are grouped as Services, and then, this integration test is very important to be sure that the core of the app is always good and working.

** Remark: in order to make integration tests , and to be able to call the services directly in my container , I defined the configuration file: config/services_test.yaml (to set my service as public in the test environment).

================================================================================================================
================================================================================================================

Some last remarks:
==================

** I created an event listener in (/md-expenses/src/EventListener/ExceptionListener.php) in order to catch and handle the errors exceptions related to the call of our API (example: errors in the typing of the routes), and I customized the returned message to be compatible with the returns given by our API. This listener is defined as a service in: "/md-expenses/config/services.yaml"


** Each application with API system should include an authentication phase in order to get some token with expiry date to acces the functions of the API , I did not include this point because its not mentioned in the requirements.

** This app could be made by using the Symfony bundle: "FOSRestBundle", but I decided to build it in another way

** About the "Open API" Schema , i have already began to make it , and I installed the "Swagger Package" inside the APP , and I began to add the necessary annotations, I also installed the "Swagger UI" in the project , you can find it in this link: http://localhost/md-expenses/api/documentation/ , and also, in this link: http://localhost/md-expenses/api/documentation/api.php , i started to put the JSON result, but unfortunately, I have no time to complete it, because I need to submit the test today. If I had more time , I would do it (frankly its the first time that I do a such documentation).

