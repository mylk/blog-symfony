<?php
    namespace Mylk\Bundle\BlogBundle\Entity;
    
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="tags")
     */
    class Tag{
        /**
         * @ORM\Column(type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        
        /**
         * @ORM\Column(type="string", length=50, nullable=false)
         */
        protected $title;
        
        public function getId(){
            return $this->id;
        }
        
        public function getTitle(){
            return $this->title;
        }
        
        public function setTitle($title){
            $this->title = $title;
        }
    }
?>
