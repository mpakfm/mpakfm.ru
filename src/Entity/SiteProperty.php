<?php

namespace App\Entity;

use App\Repository\SitePropertyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SitePropertyRepository::class)
 */
class SiteProperty
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_keywords;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $footer_copyright;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $footer_author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->meta_title;
    }

    public function setMetaTitle(?string $meta_title): self
    {
        $this->meta_title = $meta_title;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(?string $meta_description): self
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords(?string $meta_keywords): self
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }

    public function getFooterCopyright(): ?string
    {
        return $this->footer_copyright;
    }

    public function setFooterCopyright(?string $footer_copyright): self
    {
        $this->footer_copyright = $footer_copyright;

        return $this;
    }

    public function getFooterAuthor(): ?string
    {
        return $this->footer_author;
    }

    public function setFooterAuthor(?string $footer_author): self
    {
        $this->footer_author = $footer_author;

        return $this;
    }
}
