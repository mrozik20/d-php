<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="merge_accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MergeAccountsRepository")
 */
class MergeAccounts
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="mergeAccountsFirst")
     * @ORM\JoinColumn(name="author1_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $authorBase;

    /**
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="mergeAccountsSecond")
     * @ORM\JoinColumn(name="author2_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $authorSecond;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="mergeAccounts")
     * @ORM\JoinColumn(name="instance_id", referencedColumnName="id", nullable=false)
     */
    protected $instance;

    /**
     * @ORM\Column(name="is_phone", type="boolean", options={"default" : false})
     */
    protected $isPhone = false;

    /**
     * @ORM\Column(name="is_email", type="boolean", options={"default" : false})
     */
    protected $isEmail = false;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $orderNumber = null;
    
    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $content = null;
    
    /**
     * @var integer
     *  0-new
     *  1-close
     * 
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    protected $type = 0;

    function getId() {
        return $this->id;
    }

    function getAuthorBase() {
        return $this->authorBase;
    }

    function getAuthorSecond() {
        return $this->authorSecond;
    }

    function getInstance() {
        return $this->instance;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setAuthorBase($authorBase) {
        $this->authorBase = $authorBase;
        return $this;
    }

    function setAuthorSecond($authorSecond) {
        $this->authorSecond = $authorSecond;
        return $this;
    }

    function setInstance($instance) {
        $this->instance = $instance;
        return $this;
    }

    function getIsPhone() {
        return $this->isPhone;
    }

    function setIsPhone($isPhone) {
        $this->isPhone = $isPhone;
        return $this;
    }

    function getIsEmail() {
        return $this->isEmail;
    }

    function setIsEmail($isEmail) {
        $this->isEmail = $isEmail;
        return $this;
    }

    function getOrderNumber() {
        return $this->orderNumber;
    }

    function setOrderNumber($orderNumber) {
        $this->orderNumber = $orderNumber;
        return $this;
    }
    
    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
        return $this;
    }
    
    function getContent() {
        return $this->content;
    }

    function setContent($content) {
        $this->content = $content;
        return $this;
    }
}
