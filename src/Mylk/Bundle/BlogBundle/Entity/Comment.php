<?php
    namespace Mylk\Bundle\BlogBundle\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="comments")
     */
    class Comment{
        /**
         * @ORM\Column(type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;
        
        /**
         * @ORM\Column(type="string", length=100, nullable=false)
         */
        private $username;
        
        /**
         * @ORM\Column(type="string", length=100, nullable=false)
         */
        private $email;
        
        /**
         * @ORM\Column(type="string", length=1000, nullable=false)
         */
        private $content;
        
        /**
         * @ORM\Column(type="string", length=20, nullable=false)
         */
        private $createdAt;
        
        /**
         * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
         * @ORM\JoinColumn(name="postId", referencedColumnName="id")
         */
        private $post;
        
        public function getId(){
            return $this->id;
        }
        
        public function getUsername(){
            return $this->username;
        }
        
        public function setUsername($username){
            $this->username = $username;
        }
        
        public function getEmail(){
            return $this->email;
        }
        
        public function setEmail($email){
            $this->email = $email;
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
        
        public function getPost(){
            return $this->post;
        }
        
        public function setPost($post){
            $this->post = $post;
        }
    }
?>