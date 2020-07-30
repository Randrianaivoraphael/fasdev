<?php 
namespace App\Entity;
trait TimsTamp {

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @ORM\PrePersist()
     */
    public function createdAt(){
        $this->createdAt = new \DateTime();
        $this->updateAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updatedAt(){
        $this->updateAt = new \DateTime();
    }
}

?>