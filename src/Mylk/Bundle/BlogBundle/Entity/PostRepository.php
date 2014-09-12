<?php
    namespace Mylk\Bundle\BlogBundle\Entity;

    use Doctrine\ORM\EntityRepository;

    class PostRepository extends EntityRepository{
        public function findAllByStickyAndDate(){
            $query = $this->getEntityManager()->createQueryBuilder();

            $query->select("p")
                ->from($this->getEntityName(), "p")
                ->orderBy("p.sticky", "DESC")
                ->addOrderBy("p.createdAt", "DESC");

            return $query->getQuery()->getResult();
        }
        
        public function getArchive(){
            $dateTime = new \DateTime;
            
            $query = $this->getEntityManager()->createQuery("
                SELECT DISTINCT(SUBSTRING(p.createdAt, 1, 7)) AS yearMonth
                FROM MylkBlogBundle:Post p
                ORDER BY yearMonth DESC
            ");
            $results = $query->getResult();
            $dates = array();
            
            foreach($results as $date){
                $date = explode("-", $date["yearMonth"]);
                array_push($dates, array(
                    "year" => $date[0],
                    "month" => $date[1],
                    "monthName" => $dateTime->createFromFormat("!m", $date[1])->format("F"))
                );
            };
            
            return $dates;
        }

        public function findByYearMonth($date){
            $query = $this->getEntityManager()->createQuery("
                SELECT p
                FROM MylkBlogBundle:Post p
                WHERE p.createdAt LIKE CONCAT(:year, '-', :month, '%')
                ORDER BY p.createdAt DESC
            ")
            ->setParameters(array("year" => $date["year"], "month" => $date["month"]));

            return $query->getResult();
        }
        
        public function findBySearchTerm($term){
            $query = $this->getEntityManager()->createQueryBuilder();
            
            $query->select("p")
                ->from($this->getEntityName(), "p")
                ->add("where", $query->expr()->orx(
                    $query->expr()->like("p.title", ":term"),
                    $query->expr()->like("p.content", ":term")
                ))
                ->orderBy("p.createdAt", "DESC")
                ->setParameter("term", "%$term%");
            
            return $query->getQuery()->getResult();
        }
        
        public function findLatests(){
            $query = $this->getEntityManager()->createQueryBuilder();
            
            $query->select("p")
                    ->from($this->getEntityName(), "p")
                    ->orderBy("p.createdAt", "DESC")
                    ->setMaxResults(20);

            return $query->getQuery()->getResult();
        }
        
        public function findPopular(){
            $query = $this->getEntityManager()->createQueryBuilder();
            
            $query->select("p")
                    ->from($this->getEntityName(), "p")
                    ->orderBy("p.views", "DESC")
                    ->addOrderBy("p.createdAt", "DESC")
                    ->setMaxResults(3);
            
            return $query->getQuery()->getResult();
        }
        
        public function findMostCommented(){
//            SELECT p.id, p.title, (
//                SELECT COUNT(c.id)
//                FROM MylkBlogBundle:Comment c WHERE c.post = p.id
//            ) AS commentCount,
//            p.createdAt
//            FROM MylkBlogBundle:Post p
//            ORDER BY commentCount DESC,
//            p.createdAt DESC
            
            $query = $this->getEntityManager()->createQueryBuilder();

            $query->select("p.id, p.title")
                ->addSelect(
                    "(
                        SELECT COUNT(c.id)
                        FROM MylkBlogBundle:Comment c WHERE c.post = p.id
                    ) AS commentCount"
                )
                ->from($this->getEntityName(), "p")
                ->orderBy("commentCount", "DESC")
                ->addOrderBy("p.createdAt", "DESC")
                ->setMaxResults(3);

            return $query->getQuery()->getResult();
        }
    }
?>