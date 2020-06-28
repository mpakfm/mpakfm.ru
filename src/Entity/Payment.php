<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $merchant;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $money;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_test;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $error;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paymented;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_check;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $check_sent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $organization;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $organization_inn;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMerchant(): ?string
    {
        return $this->merchant;
    }

    public function setMerchant(string $merchant): self
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function getMoney(): ?string
    {
        return $this->money;
    }

    public function setMoney(string $money): self
    {
        $this->money = $money;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setResult(?int $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getIsTest(): ?bool
    {
        return $this->is_test;
    }

    public function setIsTest(bool $is_test): self
    {
        $this->is_test = $is_test;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getPaymented(): ?\DateTimeInterface
    {
        return $this->paymented;
    }

    public function setPaymented(?\DateTimeInterface $paymented): self
    {
        $this->paymented = $paymented;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIsCheck(): ?bool
    {
        return $this->is_check;
    }

    public function setIsCheck(?bool $is_check): self
    {
        $this->is_check = $is_check;

        return $this;
    }

    public function getCheckSent(): ?\DateTimeInterface
    {
        return $this->check_sent;
    }

    public function setCheckSent(?\DateTimeInterface $check_sent): self
    {
        $this->check_sent = $check_sent;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(?string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getOrganizationInn(): ?string
    {
        return $this->organization_inn;
    }

    public function setOrganizationInn(?string $organization_inn): self
    {
        $this->organization_inn = $organization_inn;

        return $this;
    }
}
