<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationService
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Function to validate a DTO object
     *
     * @param object $dto DTO object to be validated
     * @return array Returns an array with errors
     */
    public function validate(object $dto): array
    {
        $violations = $this->validator->validate($dto);

        if ($violations->count() > 0) {
            return $this->getErrors($violations);
        }

        return [];
    }

    /**
     * Function to get errors from ConstraintViolationListInterface
     *
     * @param ConstraintViolationListInterface $violations ConstraintViolationListInterface object
     * @return array Returns an array with errors
     */
    private function getErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $errors;
    }
}
