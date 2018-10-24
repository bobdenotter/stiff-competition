<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatisticsRepository")
 */
class Statistics
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $open_issues;

    /**
     * @ORM\Column(type="integer")
     */
    private $opened_recently;

    /**
     * @ORM\Column(type="integer")
     */
    private $closed_recently;

    /**
     * @ORM\Column(type="integer")
     */
    private $stargazers;

    /**
     * @ORM\Column(type="integer")
     */
    private $forks;

    /**
     * @ORM\Column(type="integer")
     */
    private $commits_year;

    /**
     * @ORM\Column(type="integer")
     */
    private $commits_month;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

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

    public function getOpenIssues(): ?int
    {
        return $this->open_issues;
    }

    public function setOpenIssues(int $open_issues): self
    {
        $this->open_issues = $open_issues;

        return $this;
    }

    public function getOpenedRecently(): ?int
    {
        return $this->opened_recently;
    }

    public function setOpenedRecently(int $opened_recently): self
    {
        $this->opened_recently = $opened_recently;

        return $this;
    }

    public function getClosedRecently(): ?int
    {
        return $this->closed_recently;
    }

    public function setClosedRecently(int $closed_recently): self
    {
        $this->closed_recently = $closed_recently;

        return $this;
    }

    public function getStargazers(): ?int
    {
        return $this->stargazers;
    }

    public function setStargazers(int $stargazers): self
    {
        $this->stargazers = $stargazers;

        return $this;
    }

    public function getForks(): ?int
    {
        return $this->forks;
    }

    public function setForks(int $forks): self
    {
        $this->forks = $forks;

        return $this;
    }

    public function getCommitsYear(): ?int
    {
        return $this->commits_year;
    }

    public function setCommitsYear(int $commits_year): self
    {
        $this->commits_year = $commits_year;

        return $this;
    }

    public function getCommitsMonth(): ?int
    {
        return $this->commits_month;
    }

    public function setCommitsMonth(int $commits_month): self
    {
        $this->commits_month = $commits_month;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
