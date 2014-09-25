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
                    ->setMaxResults(3);
            
            return $query->getQuery()->getResult();
        }

        public function findPendingApproval(){
            $em = $this->getEntityManager();
            
            $query = $em->createQueryBuilder("c");
            $query->select("c")
                    ->from($this->getEntityName(), "c")
                    ->where("c.approved = false")
                    ->orderBy("c.createdAt", "ASC");
            
            return $query->getQuery()->getResult();
        }
    }
?>
