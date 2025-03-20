<?php
namespace App\Http\Controllers\Customer;

use Illuminate\Routing\Controller;
use App\Models\ProjectItem;
use Illuminate\Support\Facades\Auth;

class ProjectItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function show(ProjectItem $projectItem)
    {
        $user = Auth::guard('customer')->user();

        // Kontrola, zda má uživatel přístup k projektové položce
        if (!$user->projectItems()->where('project_items.id', $projectItem->id)->exists()) {
            abort(403, 'Nemáte oprávnění zobrazit tuto projektovou položku.');
        }

        // Načtení relacionálních dat
        $projectItem->load([
            'project.customer',
            'requests' => function($query) use ($user) {
                $query->where('id_custuser', $user->id)
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }
        ]);

        return view('customer.project_items.show', compact('projectItem'));
    }
}
