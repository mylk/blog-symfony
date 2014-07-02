<?php
    namespace Mylk\Bundle\BlogBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    // you should always initialize the collections of your @OneToMany associations in the constructor of your entitie
    use Doctrine\Common\Collections\ArrayCollection;

    /**
     * @ORM\Entity(repositoryClass="Mylk\Bundle\BlogBundle\Entity\PostRepository")
     * @ORM\Table(name="posts")
     */
    class Post{
        public function __construct(){
            $this->tag = new ArrayCollection();
        }
        
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
         * @ORM\Column(type="boolean", nullable=false)
         */
        protected $sticky;
        
        /**
         * @ORM\ManyToOne(targetEntity="User")
         * @ORM\JoinColumn(name="userId", referencedColumnName="id")
         */
        protected $user;
        
        /**
         * @ORM\ManyToMany(targetEntity="Tag")
         * @ORM\JoinTable(name="posts_tags",
         *      joinColumns={@ORM\JoinColumn(name="postId", referencedColumnName="id")},
         *      inverseJoinColumns={@ORM\JoinColumn(name="tagId", referencedColumnName="id")}
         * )
         **/
        protected $tag;

        /**
         * @ORM\ManyToOne(targetEntity="Category")
         * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
         */
        protected $category;
        
        /**
         * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
         */
        private $comments;
        
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

        public function getSticky(){
            return $this->sticky;
        }
        
        public function setSticky($sticky){
            $this->sticky = $sticky;
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
        
        public function getComments(){
            return $this->comments;
        }
    }
?>