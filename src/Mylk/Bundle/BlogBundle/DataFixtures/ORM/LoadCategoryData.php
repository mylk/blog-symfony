<?php

namespace Mylk\Bundle\BlogBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Persistence\ObjectManager;
use Mylk\Bundle\BlogBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    private $categories = array(
        array(
            "title" => "Misc",
            "reference" => "category-misc"
        ),
        array(
            "title" => "Really interesting",
            "reference" => "category-really-interesting"
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->categories as $category) {
            $categoryEntity = new Category();
            $categoryEntity->setTitle($category["title"]);

            $manager->persist($categoryEntity);

            $this->addReference($category["reference"], $categoryEntity);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}
