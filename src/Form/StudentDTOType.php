<?php
namespace App\Form;

use App\DTO\StudentDTO;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StudentDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UserDTOType::class, [
                'label' => false, // Hide extra label
            ])
            ->add('startOfStudies', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Start of Studies',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Active Student',
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentDTO::class,
        ]);
    }
}