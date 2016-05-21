<?php

namespace RIX\CoreBundle\DataFixtures\ORM;

use RIX\CoreBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsers implements FixtureInterface,ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setFirstName('admin');
        $admin->setLastName('admin');
        $admin->setEmail('admin@gmail.com');
        $admin->setPlainPassword('Admin1');
        $admin->setPassword($this->encodePassword($admin, $admin->getPlainPassword()));
        $manager->persist($admin);

        $user = new User();
        $user->setFirstName('Cosmin');
        $user->setLastName('Chirica');
        $user->setEmail('chirica3.cosmin@gmail.com');
        $user->setPlainPassword('Cosmin1');
        $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));
        $manager->persist($user);

        $manager->flush();
    }

    public function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}