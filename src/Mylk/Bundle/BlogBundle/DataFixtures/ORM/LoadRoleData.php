<?php

namespace Mylk\Bundle\BlogBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use Mylk\Bundle\BlogBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    private $roles = array(
        array(
            "title" => "Administrator",
            "role_name" => "ROLE_ADMIN",
            "reference" => "role-administrator"
        ),
        array(
            "title" => "User",
            "role_name" => "ROLE_USER",
            "reference" => "role-user"
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->roles as $role) {
            $roleEntity = new Role();
            $roleEntity->setTitle($role["title"])
                ->setRoleName($role["role_name"]);

            $manager->persist($roleEntity);

            $this->addReference($role["reference"], $roleEntity);
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
