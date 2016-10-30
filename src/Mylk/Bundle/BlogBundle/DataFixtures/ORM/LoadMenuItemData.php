<?php

namespace Mylk\Bundle\BlogBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use Mylk\Bundle\BlogBundle\Entity\MenuItem;

class LoadMenuItemData extends AbstractFixture implements OrderedFixtureInterface
{
    private $menuItems = array(
        array(
            "title" => "Home",
            "parent" => null,
            "url" => "homepage",
            "url_discr" => "route",
            "reference" => "menu-item-home"
        ),
        array(
            "title" => "About",
            "parent" => null,
            "url" => "#",
            "url_discr" => "url",
            "reference" => "menu-item-about"
        ),
        array(
            "title" => "RSS",
            "parent" => null,
            "url" => "rss",
            "url_discr" => "route",
            "reference" => "menu-item-rss"
        ),
        array(
            "title" => "Me",
            "parent" => "menu-item-about",
            "url" => "#",
            "url_discr" => "url",
            "reference" => "menu-item-about-me"
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->menuItems as $menuItem) {
            $menuItemEntity = new MenuItem();
            $menuItemEntity->setTitle($menuItem["title"])
                ->setParent($menuItem["parent"] ? $this->getReference($menuItem["parent"]) : null)
                ->setUrl($menuItem["title"])
                ->setUrlDiscr($menuItem["url_discr"]);

            $manager->persist($menuItemEntity);

            $this->addReference($menuItem["reference"], $menuItemEntity);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }
}
