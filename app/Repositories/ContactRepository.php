<?php
namespace App\Repositories;

class ContactRepository extends BaseRepository {
    protected string $table = 'contacts';
    public function markRead(int $id): void {
        $this->update($id, ['is_read' => 1]);
    }
}
