<?php

namespace App\Form\StudyArea;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Form\Type\SaveType;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransferOwnerType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $currentOwner = $options['current_owner'];

    $builder
        ->add('owner', EntityType::class, [
            'label'         => 'study-area.owner',
            'class'         => User::class,
            'query_builder' => function (UserRepository $userRepository) use ($currentOwner) {
              return $userRepository->createQueryBuilder('u')
                  ->where('u != :current')
                  ->setParameter('current', $currentOwner)
                  ->orderBy('u.fullName', 'ASC');
            },
            'select2'       => true,
        ])
        ->add('submit', SaveType::class, [
            'enable_save_and_list' => false,
            'enable_list'          => false,
            'enable_cancel'        => true,
            'cancel_route'         => 'app_studyarea_list',
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefaults([
            'data_class' => StudyArea::class,
        ])
        ->setRequired('current_owner')
        ->setAllowedTypes('current_owner', User::class);
  }
}
