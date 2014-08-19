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
         * @ORM\ManyToMany(targetEntity="Post", mappedBy="tags")
         * @ORM\OrderBy({"createdAt" = "DESC"})
         */
        // while searching posts by tag, order by creation date of the related posts
        protected $posts;
        
        /**
         * @ORM\Column(type="string", length=50, nullable=false)
         */
        protected $title;
        
        public function __construct(){
            $this->posts = new ArrayCollection();
        }
        
        public function getId(){
            return $this->id;
        }
        
        public function getTitle(){
            return $this->title;
        }
        
        public function setTitle($title){
            $this->title = $title;
        }
        
        public function getPosts(){
            return $this->posts;
        }
    }
?>