<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 30.06.2020
 * Time: 0:41.
 */

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Название',
                ],
            ])
            ->add('short_text', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Превью',
                ],
            ])
            ->add('full_text', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Полный текст',
                    'class' => 'textarea-big-size',
                ],
            ])
            ->add('hidden', CheckboxType::class,[
                'label' => 'Невидимое сообщение',
                'required' => false,
                'attr' => [
                    'class' => '',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => [
                    'value' => 'Валуе',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Blog::class,
        ));
    }

}
