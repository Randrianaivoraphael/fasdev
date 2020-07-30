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
        $this->created_at = new \DateTime();
    }
    /**
     * @ORM\PreUpdate()
     */
    public function updatedAt(){
        $this->created_at = new \DateTime();
    }
}

?>