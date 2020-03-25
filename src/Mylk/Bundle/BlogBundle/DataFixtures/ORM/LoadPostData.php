<?php

namespace Mylk\Bundle\BlogBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Persistence\ObjectManager;
use Mylk\Bundle\BlogBundle\Entity\Post;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    private $posts = array(
        array(
            "title" => "Welcome to my blog",
            "content" => "This is my blog implemented on top of the Symfony2 framework.\r\n<br />\r\nThat's a sticky post...",
            "sticky" => true,
            "views" => 1,
            "category_id" => "category-misc",
            "tags" => array("tag-php", "tag-symfony"),
            "comments_protected" => true,
            "comments_closed" => false,
            "created_at" => "2016-09-24 20:38:06",
            "updated_at" => "2016-09-24 20:38:06",
            "created_by" => "user-admin",
            "updated_by" => null,
            "reference" => "post-1"
        ),
        array(
            "title" => "A post about flying cats",
            "content" => "The undisputable truth is that cats can fly indeed.",
            "sticky" => false,
            "views" => 0,
            "category_id" => "category-really-interesting",
            "tags" => array("tag-life"),
            "comments_protected" => false,
            "comments_closed" => false,
            "created_at" => "2016-09-25 20:39:44",
            "updated_at" => "2016-09-25 20:39:44",
            "created_by" => "user-admin",
            "updated_by" => null,
            "reference" => "post-2"
        ),
        array(
            "title" => "Yet another interesting post",
            "content" => "If every star of the milky way was a grain of salt, we would fill an olympic sized pool.",
            "sticky" => false,
            "views" => 2,
            "category_id" => "category-really-interesting",
            "tags" => array("tag-life"),
            "comments_protected" => false,
            "comments_closed" => false,
            "created_at" => "2016-09-25 20:44:51",
            "updated_at" => "2016-09-25 20:44:51",
            "created_by" => "user-admin",
            "updated_by" => null,
            "reference" => "post-3"
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->posts as $post) {
            $createdAt = new \DateTime($post["created_at"]);
            $updatedAt = new \DateTime($post["updated_at"]);
            
            $postEntity = new Post();
            $postEntity->setTitle($post["title"])
                ->setContent($post["content"])
                ->setSticky($post["sticky"])
                ->setViews($post["views"])
                ->setCategory($this->getReference($post["category_id"]))
                ->setCommentsProtected($post["comments_protected"])
                ->setCommentsClosed($post["comments_closed"])
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($updatedAt)
                ->setCreatedBy($post["created_by"] ? $this->getReference($post["created_by"]) : null)
                ->setUpdatedBy($post["updated_by"] ? $this->getReference($post["updated_by"]) : null);

            $manager->persist($postEntity);

            $this->addReference($post["reference"], $postEntity);
        }
        $manager->flush();

        $postRepo = $manager->getRepository("MylkBlogBundle:Post");
        foreach ($this->posts as $post) {
            $createdAt = new \DateTime($post["created_at"]);

            $postEntity = $postRepo->findOneBy(array(
                "createdBy" => $this->getReference($post["created_by"]),
                "createdAt" => $createdAt
            ));

            $tagEntities = array();
            foreach ($post["tags"] as $tag) {
                $tagEntities[] = $this->getReference($tag);
            }
            $postEntity->setTags($tagEntities);
        }
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 6;
    }
}
