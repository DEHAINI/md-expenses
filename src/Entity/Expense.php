<?php
// this entity class should folow the rules defined in the abstract class: Model/Expense.php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use App\Model\ExpenseAbstract;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExpenseRepository::class)
 */
class Expense extends ExpenseAbstract
{

}
