<?php
    namespace Mylk\Bundle\BlogBundle\Entity;

    use Doctrine\ORM\EntityRepository;
    
    class CommentRepository extends EntityRepository{
        public function findLatests(){
            $em = $this->getEntityManager();
            
            $query = $em->createQueryBuilder("c");
            $query->select("c")
                    ->from($this->getEntityName(), "c")
                    ->orderBy("c.createdAt", "DESC")
                    ->setMaxResults(10);
            
            return $query->getQuery()->getResult();
        }
    }
?>
