<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MediaUpload extends Component
{
    public $entityType;
    public $entityId;

    /**
     * Create a new component instance.
     *
     * @param string $entityType The type of entity (request, message, report)
     * @param int $entityId The ID of the entity
     */
    public function __construct(string $entityType, int $entityId)
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.media-upload');
    }
}
