<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class ExpenseAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"the description of the expense,unique value"})
     */
    protected $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, options={"comment":"the value of the expense, considered as: decimal"})
     */
    protected $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    // this function shoud be used to covert this object to an array
    // after that, we convert it to json format
    // this function is useful because it lets us specify what variables should be used to convert an object to an array
    // so, even if we have more than 3 variables, we can hide or show the variables as we need
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'value' => $this->getValue(),
        ];
    }

}
