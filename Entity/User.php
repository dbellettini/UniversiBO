<?php
namespace Universibo\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="Universibo\Bundle\CoreBundle\Entity\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=255,nullable=true,name="shib_username")
     * @var string
     */
    private $shibUsername;

    /**
     * @ORM\Column(type="string",length=15,nullable=true)
     * @var string
     */
    private $phone;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $notifications;

    /**
     * @ORM\Column(type="integer", name="groups");
     * @var int
     */
    private $legacyGroups;

    /**
     * @return string
     */
    public function getShibUsername()
    {
        return $this->shibUsername;
    }

    /**
     * @param string $shibUsername
     */
    public function setShibUsername($shibUsername)
    {
        $this->shibUsername = $shibUsername;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param  string                                       $phone
     * @return \Universibo\Bundle\CoreBundle\Entity\User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return number
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     *
     * @param  integer                                      $notifications
     * @return \Universibo\Bundle\CoreBundle\Entity\User
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;

        return $this;
    }

    /**
     * @deprecated
     * @return number
     */
    public function getLegacyGroups()
    {
        return $this->legacyGroups;
    }

    /**
     * @deprecated
     * @param  int                                          $legacyGroups
     * @return \Universibo\Bundle\CoreBundle\Entity\User
     */
    public function setLegacyGroups($legacyGroups)
    {
        $this->legacyGroups = $legacyGroups;

        return $this;
    }

    /**
     * @deprecated
     * @param  integer $groups
     * @return boolean
     */
    public function isGroupAllowed($groups)
    {
        return (bool) ($groups & $this->legacyGroups);
    }
}
