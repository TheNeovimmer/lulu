<?php
namespace App\Repositories;

class CategoryRepository extends BaseRepository {
    protected string $table = 'categories';
    public function findAllOrdered(): array {
        return $this->findAll([], 'name ASC');
    }
}
