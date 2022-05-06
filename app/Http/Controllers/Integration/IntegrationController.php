<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\Integration\Integration;
use App\Models\Integration\IntegrationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class IntegrationController extends Controller
{
    /**
     * @var IntegrationCategory
     */
    private $category;
    /**
     * @var Integration
     */
    private $integration;

    /**
     * @param IntegrationCategory $category
     * @param Integration $integration
     */
    public function __construct(IntegrationCategory $category, Integration $integration)
    {
        $this->category = $category;
        $this->integration = $integration;
    }

    public function index()
    {
        $categories = $this->category->with(['integrations' => function ($query){
            $query->select('id','category_id','name','logo','description','coming_soon');
        }])->orderBy('order_number')->select(['id','name'])->get();

        $categories->prepend(new Collection([
            'id' => 0,
            'name' => 'All Categories',
            'integrations' => $this->integration->select('id','category_id','name','logo','description','coming_soon')->get()
        ]));

        return inertia('integrations/Integrations',compact('categories'));
    }
}
