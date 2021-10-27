<?php
// unit test for the class: Entity/Expense.php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;

use App\Entity\Expense;

class ExpenseTest extends TestCase
{
    public function testExpenseBehavior()
    {
        // create the object "Expense"
        $object_expense=new Expense();

        // call the "set" methods
        $object_expense->setDescription("description test")->setValue(2000.22);

        // call the "toArray" method
        // just to make sure that the other developpers will always respect the output format of the function "toArray"
        $arrayFromObject=$object_expense->toArray();

        // test the behavior of the "getters" and "setters"
        $this->assertSame("description test",$object_expense->getDescription());
        $this->assertSame("2000.22",$object_expense->getValue());

        // test the "toArray" behavior
        $this->assertIsArray($arrayFromObject,"The function: toArray is not returning an array !!");
        $this->assertArrayHasKey("id",$arrayFromObject,"The function: toArray is not returning an array with the key: id");
        $this->assertArrayHasKey("description",$arrayFromObject,"The function: toArray is not returning an array with the key: description");
        $this->assertArrayHasKey("value",$arrayFromObject,"The function: toArray is not returning an array with the key: value");

    }
}

?>