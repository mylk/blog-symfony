<?php

namespace Mylk\Bundle\BlogBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use Mylk\Bundle\BlogBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    private $users = array(
        array(
            "username" => "admin",
            "email" => "admin@admin.com",
            "last_login" => "2016-10-10 16:05:39",
            "password" => '$2y$12$/WR4f2jmqF0V3.x2WD3XcuWiPwoQ6l84Sw3Mr1Cb8A2hffQfmWFZ6',
            "roles" => array("role-administrator"),
            "is_active" => true,
            "reference" => "user-admin"
        ),
        array(
            "username" => "mylk",
            "email" => "milonas.ko@gmail.com",
            "last_login" => "2016-10-10 16:05:40",
            "password" => '$2y$12$ShUiRmvpkFX2u4EtC4LUZOES9eTRyh162.UTsUe546BZiPTmDJ4PG',
            "roles" => array("role-user"),
            "is_active" => false,
            "reference" => "user-mylk"
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->users as $user) {
            $userEntity = new User();
            $userEntity->setUsername($user["username"])
                ->setEmail($user["email"])
                ->setLastLogin($user["last_login"])
                ->setPassword($user["password"])
                ->setIsActive($user["is_active"]);

            $manager->persist($userEntity);

            $this->addReference($user["reference"], $userEntity);
        }
        $manager->flush();

        $userRepo = $manager->getRepository("MylkBlogBundle:User");
        foreach ($this->users as $user) {
            $userEntity = $userRepo->findOneByEmail($user["email"]);

            $roleEntities = array();
            foreach ($user["roles"] as $role) {
                $roleEntities[] = $this->getReference($role);
            }
//            $userEntity->setRoles($roleEntities);
        }
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
