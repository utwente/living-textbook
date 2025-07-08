<?php

namespace App\Validator\Constraint;

use App\Entity\Concept;
use Override;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function array_key_exists;
use function array_merge;
use function assert;
use function in_array;
use function sprintf;

class ConceptRelationValidator extends ConstraintValidator
{
  /** Violation state variable. */
  private array $violations = [];

  /**
   * Checks if the passed value is valid.
   *
   * @param mixed      $value      The value that should be validated
   * @param Constraint $constraint The constraint for the validation
   */
  #[Override]
  public function validate(mixed $value, Constraint $constraint): void
  {
    // Check constraint
    if (!($constraint instanceof ConceptRelation)) {
      throw new UnexpectedTypeException($constraint, ConceptRelation::class);
    }

    // Check value
    if (!($value instanceof Concept)) {
      throw new UnexpectedTypeException($value, Concept::class);
    }

    // Make sure to check the relations, to fill own node
    $value->checkEntityRelations();

    // Get relations
    $incoming = $value->getIncomingRelations();
    $outgoing = $value->getOutgoingRelations();
    $all      = array_merge($incoming->toArray(), $outgoing->toArray());

    $this->violations = [];
    $map              = [];
    foreach ($all as $relation) {
      assert($relation instanceof \App\Entity\ConceptRelation);
      $source = $relation->getSource();
      $target = $relation->getTarget();

      // Skip invalid relations
      if (!$source || !$target) {
        continue;
      }

      $sourceId = $source->getId();
      $targetId = $target->getId();

      // Update map to contain a key for every concept
      if (!array_key_exists($sourceId, $map)) {
        $map[$sourceId] = [];
      }
      if (!array_key_exists($targetId, $map)) {
        $map[$targetId] = [];
      }

      // Check for duplicated relation
      if (in_array($targetId, $map[$sourceId])) {
        $this->addViolation($constraint->duplicatedRelation, $value, $source, $target);
      }

      // Check for inversed relation
      if (in_array($sourceId, $map[$targetId])) {
        $this->addViolation($constraint->inversedRelation, $value, $target, $source);
      }

      // Add to map
      $map[$sourceId][] = $targetId;
    }
  }

  /** Builds the violation and places it at the correct path. */
  private function addViolation(string $message, Concept $value, Concept $source, Concept $target): void
  {
    // Calculate direction
    $direction = $value->getId() === $source->getId();

    // Check for previous notices
    $key = sprintf('%s-%d-%d', $direction ? 'out' : 'in', $source->getId(), $target->getId());
    if (in_array($key, $this->violations)) {
      return;
    }
    $this->violations[] = $key;

    // Build violation
    $this->context
      ->buildViolation($message, [
        '%c1%' => $source->getName(),
        '%c2%' => $target->getName(),
      ])
      ->atPath($direction ? 'outgoingRelations' : 'incomingRelations')
      ->addViolation();
  }
}
