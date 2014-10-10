<?php
    namespace Mylk\Bundle\BlogBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    // you should always initialize the collections of your @OneToMany associations in the constructor of your entity
    use Doctrine\Common\Collections\ArrayCollection;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * @ORM\Entity(repositoryClass="Mylk\Bundle\BlogBundle\Entity\PostRepository")
     * @ORM\Table(name="posts")
     */
    class Post{
        public function __construct(){
            $this->tags = new ArrayCollection();
            $this->createdAt = \date("Y-m-d H:i:s");
            $this->views = 0;
        }
        
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

        /**
         * @ORM\Column(type="string", length=2000, nullable=false)
         * @Assert\NotBlank()
         */
        protected $content;

        /**
         * @ORM\Column(name="created_at", type="string", length=20, nullable=false)
         */
        protected $createdAt;
        
        /**
         * @ORM\ManyToOne(targetEntity="User")
         * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
         */
        protected $createdBy;
        
        /**
         * @ORM\Column(name="updated_at", type="string", length=20, nullable=true)
         */
        protected $updatedAt;
        
        /**
         * @ORM\ManyToOne(targetEntity="User")
         * @ORM\JoinColumn(name="updated_by", referencedColumnName="id", nullable=true)
         */
        protected $updatedBy;
        
        /**
         * @ORM\Column(type="boolean", nullable=false)
         */
        protected $sticky;
        
        /**
         * @ORM\ManyToMany(targetEntity="Tag")
         * @ORM\JoinTable(name="posts_tags",
         *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
         *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
         * )
         **/
        // could keep only the first line of the above annotation, but the defaults wouldn't match our table names
        protected $tags;

        /**
         * @ORM\ManyToOne(targetEntity="Category")
         * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
         * @Assert\NotBlank()
         */
        protected $category;
        
        /**
         * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
         */
        private $comments;
        
        /**
         * @ORM\Column(type="integer", nullable=false)
         */
        protected $views;

        /**
         * @ORM\Column(name="comments_protected", type="boolean", nullable=false)
         */
        protected $commentsProtected;

        /**
         * @ORM\Column(name="comments_closed", type="boolean", nullable=false)
         */
        protected $commentsClosed;

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
        
        public function getCreatedBy(){
            return $this->createdBy;
        }
        
        public function setCreatedBy($createdBy){
            $this->createdBy = $createdBy;
        }
        
        public function getUpdatedAt(){
            return $this->updatedAt;
        }
        
        public function setUpdatedAt(){
            $this->updatedAt = \date("Y-m-d H:i:s");
        }
        
        public function getUpdatedBy(){
            return $this->updatedBy;
        }
        
        public function setUpdatedBy($updatedBy){
            $this->updatedBy = $updatedBy;
        }

        public function getSticky(){
            return $this->sticky;
        }
        
        public function setSticky($sticky){
            $this->sticky = $sticky;
        }
        
        public function getTags(){
            return $this->tags;
        }
        
        public function setTags($tags){
            $this->tags = $tags;
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
        
        public function getCommentators(){
            $commentators = array();
            
            foreach($this->getComments() as $comment){
                $commentators[] = $comment->getEmail();
            };
            
            $commentators = \array_unique($commentators);
            
            return $commentators;
        }

        public function getViews(){
            return $this->views;
        }

        public function setViews($views){
            $this->views = $views;
        }

        public function addView(){
            $this->setViews($this->getViews() + 1);
        }

        public function setCommentsProtected($commentsProtected){
            $this->commentsProtected = $commentsProtected;
        }

        public function getCommentsProtected(){
            return $this->commentsProtected;
        }

        public function setCommentsClosed($commentsClosed){
            $this->commentsClosed = $commentsClosed;
        }

        public function getCommentsClosed(){
            return $this->commentsClosed;
        }
    }
?>