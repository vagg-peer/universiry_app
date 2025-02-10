<?php
namespace App\Form;

use App\DTO\GradeDTO;
use App\DTO\LessonDTO;
use App\Entity\Lesson;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GradeDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('score', NumberType::class, [
                'label' => 'Score',
                'scale' => 2,
            ])
            ->add('lesson', ChoiceType::class, [
                'choices' => $options['lessons'], // Pass lessons from controller
                'choice_label' => function (LessonDTO $lesson) {
                    return $lesson->getName() . ' (Semester ' . $lesson->getSemester() . ')';
                },
                'choice_value' => function (?LessonDTO $lesson) {
                    return $lesson ? $lesson->getId() : '';
                },
                'label' => 'Lesson',
                'attr' => ['class' => 'select2'], // Add class for Select2
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GradeDTO::class,
            'lessons' => [], // Default to an empty array to avoid errors
        ]);
    }
}