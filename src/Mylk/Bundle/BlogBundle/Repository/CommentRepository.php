<?php

namespace Mylk\Bundle\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findLatests()
    {
        $em = $this->getEntityManager();

        $query = $em->createQueryBuilder("c");
        $query->select("c")
            ->from($this->getEntityName(), "c")
            ->where("c.approved = true OR c.approved IS NULL")
            ->orderBy("c.createdAt", "DESC")
            ->setMaxResults(3);

        return $query->getQuery()->getResult();
    }

    public function findPendingApproval()
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT c
            FROM MylkBlogBundle:Comment c
            JOIN MylkBlogBundle:Post p
            WITH c.post = p.id
            WHERE p.commentsProtected = true
            AND c.approved IS NULL"
        );

        return $query->getResult();
    }

    public function findAllowedAndApproved($postId)
    {
        $em = $this->getEntityManager();

        $query = $em->createQueryBuilder("c");
        $query->select("c")
            ->from($this->getEntityName(), "c")
            ->where("c.post = :postId AND (c.approved = true OR c.approved IS NULL)")
            ->orderBy("c.createdAt", "DESC")
            ->setParameter("postId", $postId);

        return $query->getQuery()->getResult();
    }
}
