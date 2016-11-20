<?php

namespace Mylk\Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
// you should always initialize the collections of your @OneToMany associations in the constructor of your entity
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Mylk\Bundle\BlogBundle\Repository\PostRepository")
 * @ORM\Table(name="posts")
 */
class Post
{
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->createdAt = new \DateTime();
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
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    protected $createdBy;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
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
     * */
    // could keep only the first line of the above annotation, but the defaults wouldn't match our table names
    protected $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="posts")
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

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getSticky()
    {
        return $this->sticky;
    }

    public function setSticky($sticky)
    {
        $this->sticky = $sticky;

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getCommentators()
    {
        $commentators = array();

        foreach ($this->getComments() as $comment) {
            $commentators[] = $comment->getEmail();
        };

        $commentators = \array_unique($commentators);

        return $commentators;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    public function addView()
    {
        $this->setViews($this->getViews() + 1);
    }

    public function setCommentsProtected($commentsProtected)
    {
        $this->commentsProtected = $commentsProtected;

        return $this;
    }

    public function getCommentsProtected()
    {
        return $this->commentsProtected;
    }

    public function setCommentsClosed($commentsClosed)
    {
        $this->commentsClosed = $commentsClosed;

        return $this;
    }

    public function getCommentsClosed()
    {
        return $this->commentsClosed;
    }
}
