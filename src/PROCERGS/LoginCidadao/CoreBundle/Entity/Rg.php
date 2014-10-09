<?php
namespace PROCERGS\LoginCidadao\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ ORM\Table(name="rg")
 * @ ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Rg
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="PROCERGS\LoginCidadao\CoreBundle\Entity\Person", inversedBy="rgs")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;

    /**
     * @ORM\ManyToOne(targetEntity="PROCERGS\LoginCidadao\CoreBundle\Entity\State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * @Groups({"rgs"})
     */
    protected $state;

    /**
     * @Assert\Length(min=1,max="80")
     * @ORM\Column(name="issuer", type="string", length=80)
     * @Groups({"rgs"})
     */
    protected $issuer;

    /**
     * @Assert\Length(min=1,max="20")
     * @ORM\Column(name="val",type="string", length=20)
     * @Groups({"rgs"})
     */
    protected $val;

    public function getId()
    {
        return $this->id;
    }

    public function setId($var)
    {
        $this->id = $var;
        return $this;
    }

    public function setState($var)
    {
        $this->state = $var;
        return $this;
    }

    public function setPerson($var)
    {
        $this->person = $var;
        return $this;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setVal($var)
    {
        $this->val = $var;
        return $this;
    }

    public function getVal()
    {
        return $this->val;
    }

    public function setIssuer($var)
    {
        $this->issuer = $var;
        return $this;
    }

    public function getIssuer()
    {
        return $this->issuer;
    }
}
