<?php

namespace NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="NewsBundle\Repository\NewsRepository")
 */
class News
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posted", type="datetime")
     */
    private $posted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="views", type="boolean",options={"default":0})
     */
    private $views;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="NewsBundle\Entity\User",inversedBy="news")
     * @ORM\JoinTable(name="userId")
     */
    private $user;

    /**
     * @var string
     */
    private $summary;

    /**
     * @Assert\Image()
     *
     * @ORM\Column(name="image" ,type="string")
     */
    private $image;

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }



    public function __construct()
    {
        $this->posted=new \DateTime('now');
        $this->views=true;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        if($this->summary==null){
            $this->setSummary();
        }
        return $this->summary;
    }


    public function setSummary()
    {

        $this->summary = substr($this->getContent(),0,strlen($this->getContent())/2)."...";
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return News
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set posted
     *
     * @param \DateTime $posted
     *
     * @return News
     */
    public function setPosted($posted)
    {
        $this->posted = $posted;

        return $this;
    }

    /**
     * Get posted
     *
     * @return \DateTime
     */
    public function getPosted()
    {
        return $this->posted;
    }

    public function getFormattedDate()
    {
        return $this->getPosted()->format('Y-m-d H:i:s');
    }

    /**
     * @return boolean
     */
    public function isViews(): bool
    {
        return $this->views;
    }

    /**
     * @param boolean $views
     */
    public function setViews(bool $views)
    {
        $this->views = $views;
    }


}

