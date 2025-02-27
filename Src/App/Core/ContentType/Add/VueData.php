<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add;

class VueData
{

    private array $data;

    public function addElement($key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function getData(): array
    {
        return $this->data;
    }


    public function render(): string
    {
        return json_encode($this->data);
    }
}
