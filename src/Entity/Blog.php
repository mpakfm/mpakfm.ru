<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Mpakfm\RussianDateTime;

/**
 * @ORM\Entity(repositoryClass=BlogRepository::class)
 */
class Blog
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
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $short_text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $full_text;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * Many Posts have Many Tags.
     *
     * @ORM\ManyToMany(targetEntity="Tags", inversedBy="posts")
     * @ORM\JoinTable(name="posts_tags")
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

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

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getShortText(): ?string
    {
        return $this->short_text;
    }

    public function setShortText(?string $short_text): self
    {
        $this->short_text = $this->textFormatted($short_text);

        return $this;
    }

    public function getFullText(): ?string
    {
        return $this->full_text;
    }

    public function setFullText(?string $full_text): self
    {
        $this->full_text = $this->textFormatted($full_text);

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getCreatedRussian(string $format, int $case = RussianDateTime::FORMAT_BY)
    {
        return RussianDateTime::format($format, $this->created, $case);
    }

    public function getUpdatedRussian(string $format, int $case = RussianDateTime::FORMAT_BY)
    {
        return RussianDateTime::format($format, $this->updated, $case);
    }

    public function textFormatted(string $text, bool $rnToBr = false): string
    {
        $text = strip_tags($text, '<p><span><br><b><i><h2><h3><h4><h5><img><iframe><ul><li><pre><code>');
        if ($rnToBr) {
            preg_match('/[\r\n]{2,}/Uism', $text, $matches);
            $text = preg_replace('/[\r\n]{2,}/Uism', "\n<p>", $text);
            $text = str_replace(['<br>', '<br/>'], '<p>', $text);
        }

        return $text;
    }

    public function toggleHidden()
    {
        if ($this->hidden) {
            $this->hidden = false;
        } else {
            $this->hidden = true;
        }
    }

    public function addTag(Tags $tag)
    {
        $tag->addPost($this); // synchronously updating inverse side
        $this->tags[] = $tag;
    }

    public function getTags()
    {
        return $this->tags;
    }
}
