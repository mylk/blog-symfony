<?php
    namespace Mylk\Bundle\BlogBundle\Entity;

    use Doctrine\ORM\EntityRepository;

    class PostRepository extends EntityRepository{
        public function findAllByStickyAndDate(){
            $qb = $this->getEntityManager()->createQueryBuilder();

            $qb->select("p")
                ->from($this->getEntityName(), "p")
                ->orderBy("p.sticky", "DESC")
                ->addOrderBy("p.createdAt", "DESC");

            return $qb->getQuery()->getResult();
        }
    }
?>