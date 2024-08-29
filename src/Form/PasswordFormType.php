<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'required' => false,
                'label' => 'Mdp actuel',
                'label_attr' => [
                    'class' => 'form-label w-700'
                ],
                'attr' => [
                    'class' => 'form-input'
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Nouveau mdp',
                    'label_attr' => [
                        'class' => 'form-label w-700'
                    ],
                    'attr' => [
                        'class' => 'form-input'
                    ] 
                ],
                'second_options' => [
                    'label' => 'Confirmez nouveau mdp',
                    'label_attr' => [
                        'class' => 'form-label w-700'
                    ],
                    'attr' => [
                        'class' => 'form-input'
                    ] 
                ]
            ]);
            
    }

    
}
