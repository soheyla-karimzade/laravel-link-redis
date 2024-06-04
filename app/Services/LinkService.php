<?php

namespace App\Services;

use App\Repositories\LinkRepositoryInterface;

class LinkService
{
    protected LinkRepositoryInterface $linkRepository;

    public function __construct(LinkRepositoryInterface $linkRepository)
    {
        $this->linkRepository = $linkRepository;
    }

    public function getAllLinks()
    {
        return $this->linkRepository->all();
    }

    public function getLinkById($id)
    {
        return $this->linkRepository->find($id);
    }

    public function createLink(array $data)
    {

        return $this->linkRepository->create($data);
    }

    public function updateLink($id, array $data)
    {
        return $this->linkRepository->update($id, $data);
    }

    public function deleteLink($id)
    {
        return $this->linkRepository->delete($id);
    }

    public function paginateLinks($perPage)
    {
        return $this->linkRepository->paginate($perPage);
    }
}
