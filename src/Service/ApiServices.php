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
        if( (empty($description)) || (empty($value)) || ((count($data))==0) )
        {
            $errors[]="Expecting mandatory values: description and value !!! try again";
            $http_code=Response::HTTP_NOT_FOUND;
            $message="Your given information are incorrect";
        }
        else
        {
            // check if the given value is numeric
            if(!(is_numeric($value)))
            {
                $errors[]="The given value is not numeric";
                $http_code=Response::HTTP_NOT_FOUND;
                $message="Your given information are incorrect";
            }

        }

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

    // takes a json formatted data (received from a json request in the controller)
    // this method takes the argument: list (inside the json data),
    // if list is an integer then. it will consider it as the id of the object to be listed
    // if list="all", then it will display all the objects "expense" in the db
    // if list is empty, then it will display all the records also
    public function list($jsonData): JsonResponse
    {
        $data = json_decode($jsonData, true);

        $errors=array(); // in this array, stock all the errors you might find
        $http_code=""; // the returned http code
        $message=""; // a message to be returned
        $list_expenses=array(); // supposed to include the list of returned expenses

        $list=(isset($data['list']))?($data['list']):"";

        // check if the given value of list is empty
        if( (empty($list)) || ($list=="all") )
        {
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
        }
        else
        {
            if(is_int($list)) // then , we will consider this value as the "id" of the object "expense" to be fetched
            {
                $object_expense_check=$this->ExpenseRepository->findOneBy(array('id'=>$list));

                if($object_expense_check)
                {
                    $http_code=Response::HTTP_OK;
                    $message="Displaying of the object 'expense' having the id=".$list;
                    $list_expenses[]=$object_expense_check->toArray();
                }
                else
                {
                    $http_code=Response::HTTP_NOT_FOUND;
                    $message="The given integer does not correspond to any ID in the database";
                    $errors[]="The given ID is not correct";
                }
            }
            else
            {
                // then the given value is not an integer, and its not empty, so its a string
                $http_code=Response::HTTP_NOT_FOUND;
                $message="The given value for 'list' is not valid";
                $errors[]="Error , the parameter 'list' is not valid";

            }

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

        if((count($errors))==0)
        {

            empty($data['description']) ? true : $object_expense->setDescription($data['description']);
            ( (empty($data['value'])) || (!(is_numeric($data['value']))) ) ? true : $object_expense->setValue($data['value']);


            $this->entityManager->flush();

            $object_updated=$object_expense->toArray();
        }
        else
        {
            $object_updated="NO UPDATE";
        }

        return new JsonResponse(['object_updated'=>$object_updated,'errors'=>$errors], Response::HTTP_OK);
    }

    // this method deletes a given object "expense" from the db , it takes only the id for the object
    public function delete($id): JsonResponse
    {
        $status="";
        $errors=array();

        $object_expense = $this->ExpenseRepository->findOneBy(['id' => $id]);

        if($object_expense)
        {
            $this->entityManager->remove($object_expense);
            $this->entityManager->flush();
            $status='Expense deleted';
        }
        else
        {
            $status="The given ID does not correspond to any record in the database";
            $errors[]="The given ID does not correspond to any record !!!";
        }

        return new JsonResponse(['status' => $status,'errors'=>$errors], Response::HTTP_OK);
    }



}

?>