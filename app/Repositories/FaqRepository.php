<?php
namespace App\Repositories;

class FaqRepository extends BaseRepository {
    protected string $table = 'faqs';
    public function findAllOrdered(): array {
        return $this->findAll([], 'display_order ASC');
    }
}
