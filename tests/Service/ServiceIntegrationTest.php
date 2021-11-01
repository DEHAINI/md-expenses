<?php
// here i am going to make an integration test
// so , i will call our real services, and i will talk to a real database (NO MOCKING Technique in this test)
//
// N.B: In this case, I defined a specific database for the test (in the test environment, in ".env.test" file , read the readme.txt file in order to know the details)
// 
namespace Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\ApiServices;
use App\Repository\ExpenseRepository;
use App\Entity\Expense;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;


class ServiceIntegrationTest extends KernelTestCase
{
	private $service; // put here the service container, so we can call it in all the tests of this class
	private $entityManager; // here, put the entity manager, so we can execute queries on the database

	// setup everything we might need in the tests of this whole class
	protected function setUp(): void
    {
        // call the boot kernel in order to be able to use our real services
		self::bootKernel();

		// note for the developpers:
		// in order to be able to call a service directly in the container in the test environment, we need to
		//
		// 	1** add the wanted service in the configuration file: config/services_test.yaml
		//	2** mark the services as "public" in config/services_test.yaml
		//
		// for more info, see the readme.txt file included in this project

		$this->service=self::$kernel->getContainer()->get('test.'.ApiServices::class);

		$this->entityManager=self::$kernel->getContainer()->get('doctrine')->getManager();

    }

    // i will use this function to empty some tables in the test database in order to preserve the integrity of our tests
    // this function should only be called after the last test method of this class
	private function truncateEntities(array $entities)
    {
        $connection = $this->entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        }
        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->entityManager->getClassMetadata($entity)->getTableName()
            );
            $connection->executeUpdate($query);
        }
        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        }
    }

	// test the add method (add an expense to the test database)
	public function testApiAdd()
	{
		// try to add a new Random object "Expense" to the database
		$jsonData='{"description": "description-'.(date('YmdHis')).'", "value": "'.(mt_rand(10,1000000)).'"}';
		$jsonResponse=$this->service->add($jsonData);

		
		// make sure that the received json data has all the required information
		$jsonReceived=$jsonResponse->getContent();
		$dataReceived = json_decode($jsonReceived, true);

		$this->assertArrayHasKey('message', $dataReceived,"the response does not have a value for: message");
		$this->assertArrayHasKey('id_returned', $dataReceived,"the response does not have a value for: id_returned");
		$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");
		$this->assertArrayHasKey('http_code', $dataReceived,"the response does not have a value for: http_code");
		$this->assertIsArray($dataReceived['errors'],"the variable: error should be an array, even if its empty");

		// make sure that we do not have any generated error after the database insert
		if(isset($dataReceived['errors']))
		{
			$tab_errors=$dataReceived['errors'];
			$this->assertCount(0,$tab_errors);

		}

	}

	
	// test the list method (list of only one object)
	public function testApiListOne()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// try to list only one record from the table: "expense" (starting from the ID of the object)
		//
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			// first of all, add a new object "expense" into the database, then try to list it
			$jsonData='{"description": "description-'.(date('YmdHis')).'", "value": "'.(mt_rand(10,1000000)).'"}';
			$jsonResponse=$this->service->add($jsonData);

			// make sure that the received json data has the required information: id_returned (the id of the inserted object)
			$jsonReceived=$jsonResponse->getContent();
			$dataReceived = json_decode($jsonReceived, true);

			$this->assertArrayHasKey('id_returned', $dataReceived,"the response does not have a value for: id_returned");
			$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");

			// make sure that we do not have any generated error after the database insert
			if(isset($dataReceived['errors']))
			{
				$tab_errors=$dataReceived['errors'];
				$this->assertCount(0,$tab_errors);
			}

			// now, call the function which list the details of only one object "expense"
			$jsonResponse=$this->service->listOne($dataReceived['id_returned']);

			// make sure that the received json data has all the required information
			$jsonReceived=$jsonResponse->getContent();
			$dataReceived = json_decode($jsonReceived, true);

			$this->assertArrayHasKey('message', $dataReceived,"the response does not have a value for: message");
			$this->assertArrayHasKey('list_expenses', $dataReceived,"the response does not have a value for: list_expenses");
			$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");
			$this->assertArrayHasKey('http_code', $dataReceived,"the response does not have a value for: http_code");
			$this->assertIsArray($dataReceived['errors'],"the variable: error should be an array, even if its empty");

			// make sure that we do not have any generated error after the call of the list function
			if(isset($dataReceived['errors']))
			{
				$tab_errors=$dataReceived['errors'];
				$this->assertCount(0,$tab_errors);

			}

			// make sure that we have one , and only one result in the list of "expenses" (because we already listed the id=1)
			if(isset($dataReceived['list_expenses']))
			{
				$tab_expenses=$dataReceived['list_expenses'];
				$this->assertCount(1,$tab_expenses);

			}

	}

	// test the list method (list all the objects: expense)
	public function testApiListAll()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// this time, list all the records in the table: "expense"
		//
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			// in order to increase the number of objects in the database, add a new object "expense"
			$jsonData='{"description": "description-'.(date('YmdHis')).'", "value": "'.(mt_rand(10,1000000)).'"}';
			$jsonResponse=$this->service->add($jsonData);

			// just make sure that the received json data has one required information
			$jsonReceived=$jsonResponse->getContent();
			$dataReceived = json_decode($jsonReceived, true);

			$this->assertArrayHasKey('id_returned', $dataReceived,"the response does not have a value for: id_returned");
			$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");

			// make sure that we do not have any generated error after the database insert
			if(isset($dataReceived['errors']))
			{
				$tab_errors=$dataReceived['errors'];
				$this->assertCount(0,$tab_errors);
			}

			// now , list all the objects "expense" from the database
			$jsonResponse=$this->service->listAll();

			// make sure that the received json data has all the required information
			$jsonReceived=$jsonResponse->getContent();
			$dataReceived = json_decode($jsonReceived, true);

			$this->assertArrayHasKey('message', $dataReceived,"the response does not have a value for: message");
			$this->assertArrayHasKey('list_expenses', $dataReceived,"the response does not have a value for: list_expenses");
			$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");
			$this->assertArrayHasKey('http_code', $dataReceived,"the response does not have a value for: http_code");
			$this->assertIsArray($dataReceived['errors'],"the variable: error should be an array, even if its empty");
			$this->assertIsArray($dataReceived['list_expenses'],"the variable: list_expenses should be an array, even if its empty");

			// make sure that we do not have any generated error after the call of the list function
			if(isset($dataReceived['errors']))
			{
				$tab_errors=$dataReceived['errors'];
				$this->assertCount(0,$tab_errors);

			}

			// make sure that we have the correct number of results
			// to do that, i will make a direct query on the database in order to select the number of records
			if(isset($dataReceived['list_expenses']))
			{
				$tab_expenses=$dataReceived['list_expenses'];

				// create a query to selet the total number of records in the table: "expense"
				$count=(int) $this->entityManager->getRepository(Expense::class)
								->createQueryBuilder('E')
								->select('COUNT(E.id)')
								->getQuery()
								->getSingleScalarResult();

				$this->assertCount($count,$tab_expenses);

			}
	}

	
	// test the update method (update an expense in test database, starting from the value of the ID)
	public function testApiUpdate()
	{

		// add a new object "expense" into the database, then try to update it
		$jsonData='{"description": "description-'.(date('YmdHis')).'", "value": "'.(mt_rand(10,1000000)).'"}';
		$jsonResponse=$this->service->add($jsonData);

		// make sure that the received json data has the required information: id_returned (the id of the inserted object)
		$jsonReceived=$jsonResponse->getContent();
		$dataReceived = json_decode($jsonReceived, true);

		$this->assertArrayHasKey('id_returned', $dataReceived,"the response does not have a value for: id_returned");
		$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");

		// make sure that we do not have any generated error after the database insert
		if(isset($dataReceived['errors']))
		{
			$tab_errors=$dataReceived['errors'];
			$this->assertCount(0,$tab_errors);
		}

		// try to update the newly added object "expense"
		$jsonData='{"description": "description updated at '.(time()).'","value":"'.(mt_rand(1000,1000000)).'"}';
		$jsonResponse=$this->service->update($dataReceived['id_returned'],$jsonData);

		
		// make sure that the received json data has all the required information
		$jsonReceived=$jsonResponse->getContent();
		$dataReceived = json_decode($jsonReceived, true);

		$this->assertArrayHasKey('object_updated', $dataReceived,"the response does not have a value for: object_updated");
		$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");
		$this->assertIsArray($dataReceived['errors'],"the variable: error should be an array, even if its empty");

		// make sure that we do not have any generated error after the database insert
		if(isset($dataReceived['errors']))
		{
			$tab_errors=$dataReceived['errors'];
			$this->assertCount(0,$tab_errors);

		}

	}
	
	// test the delete method (delete an expense in test database, starting from the value of the ID)
	public function testApiDelete()
	{
		// add a new object "expense" into the database, then try to delete it
		$jsonData='{"description": "description-'.(date('YmdHis')).'", "value": "'.(mt_rand(10,1000000)).'"}';
		$jsonResponse=$this->service->add($jsonData);

		// make sure that the received json data has the required information: id_returned (the id of the inserted object)
		$jsonReceived=$jsonResponse->getContent();
		$dataReceived = json_decode($jsonReceived, true);

		$this->assertArrayHasKey('id_returned', $dataReceived,"the response does not have a value for: id_returned");
		$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");

		// make sure that we do not have any generated error after the database insert
		if(isset($dataReceived['errors']))
		{
			$tab_errors=$dataReceived['errors'];
			$this->assertCount(0,$tab_errors);
		}

		// try to delete the newly added object "expense"
		$jsonResponse=$this->service->delete($dataReceived['id_returned']);

		// make sure that the received json data has all the required information
		$jsonReceived=$jsonResponse->getContent();
		$dataReceived = json_decode($jsonReceived, true);

		$this->assertArrayHasKey('status', $dataReceived,"the response does not have a value for: status");
		$this->assertArrayHasKey('errors', $dataReceived,"the response does not have a value for: errors");
		$this->assertIsArray($dataReceived['errors'],"the variable: error should be an array, even if its empty");

		// make sure that we do not have any generated error after the database insert
		if(isset($dataReceived['errors']))
		{
			$tab_errors=$dataReceived['errors'];
			$this->assertCount(0,$tab_errors);

		}

	}
	
	// keep this method at the end of the class
	// it will delete everything from the test table
	public function testdeleteTables()
	{
		 $this->truncateEntities([
		            Expense::class,
		        ]);

		$this->assertSame(true,true);
	}
}

?>