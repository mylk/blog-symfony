<?php

namespace Mylk\Bundle\BlogBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use Mylk\Bundle\BlogBundle\Entity\Tag;

class LoadTagData extends AbstractFixture implements OrderedFixtureInterface
{
    private $tags = array(
        array(
            "title" => "PHP",
            "reference" => "tag-php"
        ),
        array(
            "title" => "Symfony",
            "reference" => "tag-symfony"
        ),
        array(
            "title" => "Life",
            "reference" => "tag-life"
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->tags as $tag) {
            $tagEntity = new Tag();
            $tagEntity->setTitle($tag["title"]);

            $manager->persist($tagEntity);

            $this->addReference($tag["reference"], $tagEntity);
        }

        $manager->flush();
        $manager->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
