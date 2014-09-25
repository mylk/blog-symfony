<?php
    namespace Mylk\Bundle\BlogBundle\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    // you should always initialize the collections of your @OneToMany associations in the constructor of your entity
    use Doctrine\Common\Collections\ArrayCollection;
    use Symfony\Component\Validator\Constraints as Assert;

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
         * @Assert\NotBlank()
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