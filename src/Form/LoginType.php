<?php

/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 26.06.2020
 * Time: 0:54.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Login',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'Password',
                ],
            ])
            ->add('remember_me', CheckboxType::class, [
                'mapped'   => false,
                'required' => false,
                'value'    => 1,
            ])
            ->add('login', SubmitType::class)
        ;
    }
}
