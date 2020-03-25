<?php

namespace Mylk\Bundle\BlogBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Persistence\ObjectManager;
use Mylk\Bundle\BlogBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
    private $comments = array(
        array(
            "username" => "Kostas",
            "email" => "milonas.ko@gmail.com",
            "content" => "GO!",
            "created_at" => "2016-10-25 20:48:15",
            "post_id" => "post-1",
            "approved" => true
        ),
        array(
            "username" => "Kostas",
            "email" => "milonas.ko@gmail.com",
            "content" => "Self control.",
            "created_at" => "2016-10-25 20:48:15",
            "post_id" => "post-1",
            "approved" => true
        ),
        array(
            "username" => "Kostas",
            "email" => "milonas.ko@gmail.com",
            "content" => "Testing the mailer",
            "created_at" => "2016-10-25 20:48:15",
            "post_id" => "post-1",
            "approved" => false
        ),
        array(
            "username" => "Kostas",
            "email" => "milonas.ko@gmail.com",
            "content" => "Interesting fact...",
            "created_at" => "2016-10-25 20:48:15",
            "post_id" => "post-2",
            "approved" => true
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->comments as $comment) {
            $createdAt = new \DateTime($comment["created_at"]);

            $commentEntity = new Comment();
            $commentEntity->setUsername($comment["username"])
                ->setEmail($comment["email"])
                ->setContent($comment["content"])
                ->setCreatedAt($createdAt)
                ->setPost($this->getReference($comment["post_id"]))
                ->setApproved($comment["approved"]);

            $manager->persist($commentEntity);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 7;
    }
}
