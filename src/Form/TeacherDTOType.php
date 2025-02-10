<?php
namespace App\Form;

use App\DTO\TeacherDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TeacherDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Embed the entire User Form
            ->add('user', UserDTOType::class, [
            'label' => false, // Hide extra label
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Active Teacher',
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TeacherDTO::class,
        ]);
    }
}
