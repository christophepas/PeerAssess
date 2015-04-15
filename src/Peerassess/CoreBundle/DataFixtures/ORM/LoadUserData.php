<?php

namespace Peerassess\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Peerassess\CoreBundle\Entity\UserType;

class LoadUserData extends BaseFixture
{
    public function load (ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        // User Christophe
        $user = $userManager->createUser();
        $user->setPlainPassword("password");
        $user->setEmail("christophe@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_ADMIN");
        $user->setType(UserType::SUPERVISOR);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User Conrad
        $user = $userManager->createUser();
        $user->setPlainPassword("password");
        $user->setEmail("conradk@conradk.com");
        $user->setEnabled(true);
        $user->addRole("ROLE_ADMIN");
        $user->setType(UserType::SUPERVISOR);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User admin
        $user = $userManager->createUser();
        $user->setPlainPassword("admin");
        $user->setEmail("admin@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_ADMIN");
        $user->setType(UserType::SUPERVISOR);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User demo
        $user = $userManager->createUser();
        $user->setPlainPassword("demo");
        $user->setEmail("demo@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_SUPERVISOR");
        $user->setType(UserType::SUPERVISOR);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User candidate
        $user = $userManager->createUser();
        $user->setPlainPassword("d");
        $user->setEmail("candidate1@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_CANDIDATE");
        $user->setType(UserType::CANDIDATE);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User candidate
        $user = $userManager->createUser();
        $user->setPlainPassword("d");
        $user->setEmail("candidate2@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_CANDIDATE");
        $user->setType(UserType::CANDIDATE);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User candidate
        $user = $userManager->createUser();
        $user->setPlainPassword("d");
        $user->setEmail("candidate3@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_CANDIDATE");
        $user->setType(UserType::CANDIDATE);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User candidate
        $user = $userManager->createUser();
        $user->setPlainPassword("d");
        $user->setEmail("candidate4@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_CANDIDATE");
        $user->setType(UserType::CANDIDATE);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User candidate
        $user = $userManager->createUser();
        $user->setPlainPassword("d");
        $user->setEmail("candidate5@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_CANDIDATE");
        $user->setType(UserType::CANDIDATE);
        $user->setConfirmed(true);
        $userManager->updateUser($user);

        // User candidate
        $user = $userManager->createUser();
        $user->setPlainPassword("d");
        $user->setEmail("candidate6@peerassess.co");
        $user->setEnabled(true);
        $user->addRole("ROLE_CANDIDATE");
        $user->setType(UserType::CANDIDATE);
        $user->setConfirmed(true);
        $userManager->updateUser($user);
    }

    /**
     *
     * @return number
     */
    public function getOrder ()
    {
        return 101;
    }
}
