<?php

namespace App\Concerns;

trait DefaultGuarded
{
    protected $defaultGuarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function initializeDefaultGuarded()
    {
        $this->guarded = [...$this->guarded, ...$this->defaultGuarded];
    }
}
