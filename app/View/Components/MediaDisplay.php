<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MediaDisplay extends Component
{
    public $media;
    public $entityType;
    public $entityId;
    public $showDeleteButton;

    /**
     * Create a new component instance.
     *
     * @param string $entityType The type of entity (request, message, report)
     * @param int $entityId The ID of the entity
     * @param bool $showDeleteButton Whether to show delete buttons
     * @param array $media Optional media collection (if not provided, will be loaded based on entityType and entityId)
     */
    public function __construct(string $entityType, int $entityId, bool $showDeleteButton = true, $media = null)
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->showDeleteButton = $showDeleteButton;

        // If media is not provided, load it based on entity type and ID
        if ($media === null) {
            switch ($entityType) {
                case 'request':
                    $entity = \App\Models\Request::findOrFail($entityId);
                    $this->media = $entity->media;
                    break;
                case 'message':
                    $entity = \App\Models\RequestMessage::findOrFail($entityId);
                    $this->media = $entity->media;
                    break;
                case 'report':
                    $entity = \App\Models\RequestReport::findOrFail($entityId);
                    $this->media = $entity->media;
                    break;
                default:
                    $this->media = collect([]);
            }
        } else {
            $this->media = $media;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.media-display');
    }
}
