<?php
/**
 * @OA\Info(title="API for expenses - MD GROUP", version="1.0")
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

// i created my own service which should do anything i might need in this controller
// puting functionalities in "Services" make the treatements easier (especially for the unit tests and integration tests)
use App\Service\ApiServices;

class ExpenseController extends AbstractController
{
	// inject our services
	private $ApiServices;

	// constructor
	public function __construct(ApiServices $ApiServices)
	{
		$this->ApiServices=$ApiServices;
	}

	// insert a new object in the db
	/**
     * @Route("/add/expense/", name="add_expense", methods={"POST"})
     *
     */

    public function add(Request $request): JsonResponse
    {
    	return($this->ApiServices->add($request->getContent()));
    }

    // this method takes the argument: list (inside the json data),
    // if list is an integer then. it will consider it as the id of the object to be listed
    // if list="all", then it will display all the objects "expense" in the db
    // if list is empty, then it will display all the records also
	/**
     * @Route("/list/expenses/", name="list_expenses", methods={"POST"})
     */

    public function list(Request $request): JsonResponse
    {
    	return($this->ApiServices->list($request->getContent()));
    }

    // this method should update a given object "expense" in the db , it takes only the id for the object
	/**
	 * @Route("/expenses/update/{id}/", name="update_expense", methods={"PUT"})
	 */
	public function update($id, Request $request): JsonResponse
	{
    	return($this->ApiServices->update($id,$request->getContent()));
	}

	// this method deletes a given object "expense" from the db , it takes only the id for the object
	/**
	 * @Route("/expenses/delete/{id}/", name="delete_expense", methods={"DELETE"})
	 */
	public function delete($id): JsonResponse
	{
    	return($this->ApiServices->delete($id));
	}
    
}
