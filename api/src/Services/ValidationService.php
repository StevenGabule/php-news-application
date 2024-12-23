<?php

namespace App\Services;

use App\Repositories\UserRepository;

class ValidationService
{
  private $userRepo;

  public function __construct()
  {
    $this->userRepo = new UserRepository();
  }

  /**
   * Validate input data against a set of rules.
   * 
   * @param array $data   The input data to validate.
   * @param array $rules  The validation rules.
   * @return true|array   Returns true if validation passes, or an array of errors.
   */
  public function validate(array $data, array $rules)
  {
    $errors = [];
    foreach ($rules as $field => $ruleString) {
      $ruleList = explode('|', $ruleString);
      foreach ($ruleList as $rule) {
        if ($rule === 'required') {
          if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[$field][] = sprintf('The %s field is required.', $field);
          }
        } elseif ($rule === 'email') {
          if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $errors[$field][] = sprintf('The %s field must be a valid email address.', $field);
          }
        } elseif (strpos($rule, 'min:') === 0) {
          list(, $minLength) = explode(':', $rule);
          if (isset($data[$field]) && is_string($data[$field]) && strlen($data[$field]) < (int) $minLength) {
            $errors[$field][] = sprintf(
              'The %s field must be at least %s characters long.',
              $field,
              $minLength
            );
          }
        }
        elseif(strpos($rule, 'unique:') === 0) {
          list(,$tableName) = explode(':', $rule);
          if($tableName === 'users' && isset($data[$field])) {
            $existingRecord = $this->userRepo->findByEmail($data[$field]);
            if($existingRecord) {
              $errors[$field][] = sprintf(
                'The %s has already been taken.',
                $field
              );
            }
          }
        }
      }
    }
    // return true if there are no errors, otherwise return the error array
    return empty($errors) ? true : $errors;
  }
}