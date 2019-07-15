<?php

namespace App\Validator\Constraint;

use App\Entity\StudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\ChoiceValidator;

/**
 * Class StudyAreaAccessTypeValidator
 */
class StudyAreaAccessTypeValidator extends ChoiceValidator
{

  /** @var AuthorizationCheckerInterface */
  private $authorizationChecker;

  /** @var EntityManagerInterface */
  private $em;

  public function __construct(AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $em)
  {
    $this->authorizationChecker = $authorizationChecker;
    $this->em                   = $em;
  }

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value      The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  public function validate($value, Constraint $constraint)
  {
    // Check constraint
    if (!($constraint instanceof StudyAreaAccessType)) {
      throw new UnexpectedTypeException($constraint, StudyAreaAccessType::class);
    }

    // Do not validate null values
    if ($value === NULL) {
      return;
    }

    // Get the root object
    $object = $this->context->getObject();
    if (!$object instanceof StudyArea) {
      throw new UnexpectedTypeException($object, StudyArea::class);
    }

    // Forward the call to the Symfony Choice constraint validator, with the allowed values
    parent::validate($value, new Choice([
        'choices' => $object->getAvailableAccessTypes($this->authorizationChecker, $this->em),
    ]));
  }
}
