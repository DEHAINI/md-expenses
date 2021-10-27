<?php
// its a unit test for the controller: ExpenseController which includes our APIs methods
// its just a simple example , just to show the idea
namespace tests\Controller;

use App\Controller\ExpenseController;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ExpenseControllerTest extends TestCase
{

	public function testAddMethod()
	{

		$value="10";
		$toArray=array('id'=>10,'value'=>"2000",'description'=>'description test');

		// use the "mock" tecnique to change the behavior of the method (especially to excluse the database connection)
        $expenseController = $this->createMock(ExpenseController::class);

		$jsonResponse=new JsonResponse(['message'=>'test message','id_returned'=>1000,'errors'=>array('0'=>'we have an error'),'http_code'=>200], 200);

		// change the behavior of the "add" method
        $expenseController->method('add')
        					->willReturn($jsonResponse);


       	$this->assertSame($jsonResponse,$expenseController->add(new Request()));


	}


}

?>