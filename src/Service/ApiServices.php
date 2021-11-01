<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ExpenseRepository;
use Symfony\Component\HttpFoundation\JsonResponse;


class ApiServices
{
    // inject the entity manager in order to make database connection
    private $entityManager;

    // inject the Expense Repository in order to be able to manage the "Expense" Records
    private $ExpenseRepository;

    // constructor
    public function __construct(EntityManagerInterface $EntityManagerInterface,ExpenseRepository $ExpenseRepository)
    {
        $this->entityManager=$EntityManagerInterface;
        $this->ExpenseRepository=$ExpenseRepository;
    }



    // takes a json formatted data (received from a json request in the controller)
    // creates the object "Expense" and inserts it in the databse
    public function add($jsonData): JsonResponse
    {
        $data=array();

        $data = json_decode($jsonData, true);

        $errors=array(); // in this array, stock all the errors you might find
        $http_code=""; // the returned http code
        $id_returned=-1; // if we got an inserted object in the db, then this value will be the id of that object

        $description=(isset($data['description']))?($data['description']):"";
        $value=(isset($data['value']))?($data['value']):"";

        // check if the given values are empty
        // then add an error
        if( (empty($description)) || (empty($value)) || ((count($data))==0) )
        {
            $errors[]="Expecting mandatory values: description and value !!! try again";
            $http_code=Response::HTTP_NOT_FOUND;
            $message="Your given information are incorrect";
        }
        
        // if all the required values are set
        // and at the same time the given value for the expense is not numeric
        // then add an error
        if( (!( (empty($description)) || (empty($value)) || ((count($data))==0) )) && (!(is_numeric($value))) )
        {
            $errors[]="The given value is not numeric";
            $http_code=Response::HTTP_NOT_FOUND;
            $message="Your given information are incorrect";

        }

        // if we do not have any errors, then proceed with the treatment
        if((count($errors))==0) // no errors found, then insert into the db
        {
            $object_expense=new Expense();
            $object_expense->setDescription($description)->setValue($value);

            $this->entityManager->persist($object_expense);

            $this->entityManager->flush();

            $http_code=Response::HTTP_CREATED;

            $message="The expense has been created successfully";

            $id_returned=$object_expense->getId();
        }

        return new JsonResponse(['message'=>$message,'id_returned'=>$id_returned,'errors'=>$errors,'http_code'=>$http_code], $http_code);
    }

    // list all the objects "expense" from the database
    public function listAll(): JsonResponse
    {
        $errors=array(); // in this array, stock all the errors you might find
        $http_code=""; // the returned http code
        $message=""; // a message to be returned
        $list_expenses=array(); // supposed to include the list of returned expenses

        $http_code=Response::HTTP_OK;
        $message="Displaying of all the records of expenses in the database";
        $result_expenses=$this->ExpenseRepository->findAll();

        if((count($result_expenses))>0)
        {
            foreach($result_expenses as $object_expense)
            {
                $list_expenses[]=$object_expense->toArray();
            }
        }

        return new JsonResponse(['message'=>$message,'errors'=>$errors,'list_expenses'=>$list_expenses,'http_code'=>$http_code], $http_code);
    }

    // list the details of one object "expense" (starting from the value of: ID)
    public function listOne($id): JsonResponse
    {
        $errors=array(); // in this array, stock all the errors you might find
        $http_code=""; // the returned http code
        $message=""; // a message to be returned
        $list_expenses=array(); // supposed to include the list of returned expenses

        $id=(int)$id;

        if(is_int($id)) // then , we will consider this value as the "id" of the object "expense" to be fetched
        {
            $object_expense_check=$this->ExpenseRepository->findOneBy(array('id'=>$id));
        }
        
        if($object_expense_check) // then we really found the object !!
        {
            $http_code=Response::HTTP_OK;
            $message="Displaying of the object 'expense' having the id=".$id;
            $list_expenses[]=$object_expense_check->toArray();
        }

        if(!($object_expense_check)) // then we did not find any object in the database
        {
            $http_code=Response::HTTP_NOT_FOUND;
            $message="The given integer does not correspond to any ID in the database";
            $errors[]="The given ID is not correct";
        }

        if(!(is_int($id))) // the given id is not an integer, raise an error
        {
            // then the given value is not an integer, and its not empty, so its a string
            $http_code=Response::HTTP_NOT_FOUND;
            $message="The given value for 'id' is not valid";
            $errors[]="Error , the parameter 'id' is not valid";

        }


        return new JsonResponse(['message'=>$message,'errors'=>$errors,'list_expenses'=>$list_expenses,'http_code'=>$http_code], $http_code);
    }

    // this method should update a given object "expense" in the db , it takes only the id for the object
    public function update($id,$jsonData): JsonResponse
    {
        $errors=array();

        if(empty($id)) $errors[]="The ID of the expense is not given !!";

        if((count($errors))==0)
        $object_expense = $this->ExpenseRepository->findOneBy(['id' => $id]);

        if(!($object_expense)) $errors[]="The given ID does not correspond to any object in the database";

        $data = json_decode($jsonData, true);

        $object_updated="NO UPDATE"; // by default put the phrase: "NO UPDATE" in this variable, and change it later one we have an update

        if((count($errors))==0)
        {
            empty($data['description']) ? true : $object_expense->setDescription($data['description']);
            ( (empty($data['value'])) || (!(is_numeric($data['value']))) ) ? true : $object_expense->setValue($data['value']);


            $this->entityManager->flush();

            $object_updated=$object_expense->toArray();
        }

        return new JsonResponse(['object_updated'=>$object_updated,'errors'=>$errors], Response::HTTP_OK);
    }

    // this method deletes a given object "expense" from the db , it takes only the id for the object
    public function delete($id): JsonResponse
    {
        $status="";
        $errors=array();

        $object_expense = $this->ExpenseRepository->findOneBy(['id' => $id]);

        // put some default values in the variables
        // and change them later , once the delete process is succeeded
        $status="The given ID does not correspond to any record in the database";
        $errors[]="The given ID does not correspond to any record !!!";

        if($object_expense)
        {
            $this->entityManager->remove($object_expense);
            $this->entityManager->flush();
            $status='Expense deleted';
            $errors=array();
        }

        return new JsonResponse(['status' => $status,'errors'=>$errors], Response::HTTP_OK);
    }



}

?>