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
// puting functionalities in "Services" makes the treatements easier (especially for the unit tests and integration tests)
use App\Service\ApiServices;

// its the first version of our API , so lets put "/v1" as prefix for all the endpoints
/**
* @Route("/v1", name="api_v1_")
*/

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
     * @Route("/expenses/", name="add_expense", methods={"POST"})
     *
     */

    public function add(Request $request): JsonResponse
    {
    	return($this->ApiServices->add($request->getContent()));
    }

    // list all the object "expense" from the database
	/**
     * @Route("/expenses", name="list_all_expenses", methods={"GET"})
     */

    public function listAll(): JsonResponse
    {
    	return($this->ApiServices->listAll());
    }

    // list the details of one object "expense" in the database (starting from the value of ID)
	/**
     * @Route("/expenses/{id}", name="list_one_expenses", methods={"GET"})
     */

    public function listOne($id): JsonResponse
    {
    	return($this->ApiServices->listOne($id));
    }

    // this method should update a given object "expense" in the db , it takes only the id for the object
	/**
	 * @Route("/expenses/{id}", name="update_expense", methods={"PUT"})
	 */
	public function update($id, Request $request): JsonResponse
	{
    	return($this->ApiServices->update($id,$request->getContent()));
	}

	// this method deletes a given object "expense" from the db , it takes only the id for the object
	/**
	 * @Route("/expenses/{id}", name="delete_expense", methods={"DELETE"})
	 */
	public function delete($id): JsonResponse
	{
    	return($this->ApiServices->delete($id));
	}
    
}
