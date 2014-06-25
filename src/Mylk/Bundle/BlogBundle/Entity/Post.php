<?php
    namespace Mylk\Bundle\BlogBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="posts")
     */
    class Post{
        /**
         * @ORM\Column(type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\Column(type="string", length=100, nullable=false)
         */
        protected $title;

        /**
         * @ORM\Column(type="string", length=2000, nullable=false)
         */
        protected $content;

        /**
         * @ORM\Column(type="string", length=20, nullable=false)
         */
        protected $createdAt;
        
        /**
         * @ORM\ManyToOne(targetEntity="User")
         * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
         */
        protected $user;
        
        /**
         * @ORM\ManyToOne(targetEntity="Tag")
         * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
         */
        protected $tag;

        /**
         * @ORM\ManyToOne(targetEntity="Category")
         * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
         */
        protected $category;
        
        public function getId(){
            return $this->id;
        }
        
        public function getTitle(){
            return $this->title;
        }
        
        public function setTitle($title){
            $this->title = $title;
        }
        
        public function getContent(){
            return $this->content;
        }
        
        public function setContent($content){
            $this->content = $content;
        }
        
        public function getCreatedAt(){
            return $this->createdAt;
        }
        
        public function setCreatedAt($createdAt){
            $this->createdAt = $createdAt;
        }
        
        public function getUser(){
            return $this->user;
        }
        
        public function setUser($user){
            $this->user = $user;
        }
        
        public function getTag(){
            return $this->tag;
        }
        
        public function setTag($tag){
            $this->tag = $tag;
        }
        
        public function getCategory(){
            return $this->category;
        }
        
        public function setCategory($category){
            $this->category = $category;
        }
    }
?>
