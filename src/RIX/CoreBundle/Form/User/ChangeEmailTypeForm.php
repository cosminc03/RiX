<?php

namespace RIX\CoreBundle\Form\User;

use RIX\CoreBundle\Entity\User;
use RIX\CoreBundle\Form\Model\ChangeEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class ChangeEmailTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email:',
                'constraints' => array(
                    new Email(array(
                        "message" => "The email {{ value }} is not a valid email"
                    ))
                ),
                'required' => true,
            ])
            ->add('oldPassword', PasswordType::class,[
                 'label' => 'Password:',
                 'required' => true,
                 'invalid_message' => 'Password fucked up!',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ChangeEmail::class,
        ));
    }

    public function getName()
    {
        return 'change_email';
    }
}