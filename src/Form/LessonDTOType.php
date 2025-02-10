<?php
namespace App\Form;

use App\DTO\LessonDTO;
use App\Entity\Teacher;
use App\DTO\TeacherDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LessonDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Lesson Name',
            ])
            ->add('semester', ChoiceType::class, [
                'choices' => [
                    '1st' => '1',
                    '2nd' => '2',
                    '3rd' => '3',
                    '4rd' => '4',
                    '5rd' => '5',
                    '6rd' => '6',
                    '7rd' => '7',
                    '8rd' => '8',
                ],
                'multiple' => false,
                'label' => 'Semester',
            ])
            ->add('teacher', ChoiceType::class, [
                'choices' => $options['teachers'], // Get TeacherDTOs from options
                'choice_label' => function (TeacherDTO $teacherDTO) {
                    return $teacherDTO->getUser()->getFirstname() . ' ' . $teacherDTO->getUser()->getLastname();
                },
                'choice_value' => function (?TeacherDTO $teacherDTO) {
                    return $teacherDTO ? $teacherDTO->getId() : '';
                },
                'label' => 'Teacher',
                'attr' => ['class' => 'select2'],
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LessonDTO::class,
            'teachers' => [], // Default empty array to avoid errors
        ]);
    }
}