<?php
namespace App\Form;

use App\DTO\UserDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Email',
        ])
        ->add('firstname', TextType::class, [
            'label' => 'First Name',
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Last Name',
        ])
        ->add('isActive', CheckboxType::class, [
            'label' => 'Active User',
            'required' => false,
        ]);

    // Listen to PRE_SET_DATA to determine if the form is for creation or editing
    $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        $userDTO = $event->getData();
        $form = $event->getForm();

        $isNew = !$userDTO;

        $form->add('plainPassword', PasswordType::class, [
            'label' => 'Password' . ($isNew ? '' : ' (leave blank to keep current)'),
            'required' => $isNew,
        ]);
    });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserDTO::class,
        ]);
    }
}