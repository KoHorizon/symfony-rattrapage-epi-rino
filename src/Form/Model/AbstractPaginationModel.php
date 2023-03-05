<?php

namespace App\Form\Model;

abstract class AbstractPaginationModel
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_LIMIT = 50;

    protected ?int $page  = null;
    protected ?int $limit = null;
    protected ?string $order = null;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function setOrder(?string $order): void
    {
        $this->order = $order;
    }
}
