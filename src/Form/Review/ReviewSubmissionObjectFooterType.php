<?php

namespace App\Form\Review;

use App\Entity\Concept;
use App\Entity\PendingChange;
use App\Entity\User;
use Override;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

use function assert;

class ReviewSubmissionObjectFooterType extends AbstractType
{
  public function __construct(
    private readonly RouterInterface $router,
    private readonly Security $security)
  {
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $pendingChange = $options['pending_change'];
    assert($pendingChange instanceof PendingChange);

    // If it is not for a concept, we currently do not support it
    if ($pendingChange->getObjectType() !== Concept::class) {
      return;
    }

    // Only the owner of the change or the super admin can re-edit a pending change
    $user = $this->security->getUser();
    assert($user instanceof User);
    if ($user->getId() !== $pendingChange->getOwner()->getId() && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
      return;
    }

    $view->vars['showEdit'] = true;
    $view->vars['editPath'] = $this->router->generate('app_concept_editpending', ['pendingChange' => $pendingChange->getId()]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setRequired('pending_change')
      ->setAllowedTypes('pending_change', PendingChange::class);
  }
}
