<?php
    namespace Mylk\Bundle\BlogBundle\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="categories")
     */
    class Category{
        /**
         * @ORM\Column(type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        
        /**
         * @ORM\Column(type="string", length=100, nullable=false)
         * @Assert\NotBlank()
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