<?php

namespace RIX\CoreBundle\Form\User;

use RIX\CoreBundle\Entity\User;
use RIX\CoreBundle\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newPassword', PasswordType::class, [
                'label' => 'New Password:',
                'required' => true,
            ])
            ->add('oldPassword', PasswordType::class,[
                'label' => 'Current Password:',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ChangePassword::class,
        ));
    }

    public function getName()
    {
        return 'change_password';
    }
}