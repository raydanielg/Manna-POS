<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\ProductionRun;
use App\Models\ProductionUsage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManufacturingController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $user = auth()->user();
        $stats = [
            'recipes' => Recipe::where('user_id', $user->id)->count(),
            'active_recipes' => Recipe::where('user_id', $user->id)->where('status', 'active')->count(),
            'production_runs' => ProductionRun::where('user_id', $user->id)->count(),
            'completed_runs' => ProductionRun::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];
        $recentRuns = ProductionRun::where('user_id', $user->id)->with('recipe.product')->latest()->take(5)->get();
        return view('dashboard.manufacturing.index', compact('stats', 'recentRuns'));
    }

    public function recipes()
    {
        $recipes = Recipe::where('user_id', auth()->id())->with('product')->latest()->get();
        return view('dashboard.manufacturing.recipes', compact('recipes'));
    }

    public function createRecipe()
    {
        $products = Product::where('user_id', auth()->id())->where('is_active', true)->get();
        return view('dashboard.manufacturing.recipe-create', compact('products'));
    }

    public function storeRecipe(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'output_quantity' => 'required|numeric|min:0.0001',
            'output_unit' => 'nullable|string|max:50',
            'labor_cost' => 'nullable|numeric|min:0',
            'overhead_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        $data['user_id'] = auth()->id();
        $recipe = Recipe::create($data);
        return redirect()->route('dashboard.manufacturing.recipe.show', $recipe)->with('success', 'Recipe created. Now add ingredients.');
    }

    public function showRecipe(Recipe $recipe)
    {
        $this->guardModel($recipe);
        $recipe->load('items.product', 'product');
        $products = Product::where('user_id', auth()->id())->where('is_active', true)->get();
        $totalMaterialCost = $recipe->items->sum(function($i) { return ($i->cost ?? 0) * $i->quantity; });
        return view('dashboard.manufacturing.recipe-show', compact('recipe', 'products', 'totalMaterialCost'));
    }

    public function storeRecipeItem(Request $req, Recipe $recipe)
    {
        $this->guardModel($recipe);
        $data = $req->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.0001',
            'unit' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric|min:0',
        ]);
        $data['recipe_id'] = $recipe->id;
        RecipeItem::create($data);
        return redirect()->route('dashboard.manufacturing.recipe.show', $recipe)->with('success', 'Ingredient added');
    }

    public function destroyRecipeItem(RecipeItem $item)
    {
        $recipeId = $item->recipe_id;
        $this->guardModel($item->recipe);
        $item->delete();
        return redirect()->route('dashboard.manufacturing.recipe.show', $recipeId)->with('success', 'Ingredient removed');
    }

    public function production()
    {
        $runs = ProductionRun::where('user_id', auth()->id())->with('recipe.product')->latest()->get();
        return view('dashboard.manufacturing.production', compact('runs'));
    }

    public function createProduction()
    {
        $recipes = Recipe::where('user_id', auth()->id())->where('status', 'active')->with('product')->get();
        return view('dashboard.manufacturing.production-create', compact('recipes'));
    }

    public function storeProduction(Request $req)
    {
        $data = $req->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'planned_quantity' => 'required|numeric|min:0.0001',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $recipe = Recipe::where('id', $data['recipe_id'])->where('user_id', auth()->id())->firstOrFail();
        $data['user_id'] = auth()->id();
        $data['batch_number'] = 'B-' . strtoupper(Str::random(8));
        $data['status'] = 'planned';

        $run = ProductionRun::create($data);

        // Auto-create planned usages from recipe items
        foreach ($recipe->items as $item) {
            $qty = $item->quantity * ($data['planned_quantity'] / $recipe->output_quantity);
            ProductionUsage::create([
                'production_run_id' => $run->id,
                'product_id' => $item->product_id,
                'planned_quantity' => $qty,
                'unit_cost' => $item->cost,
            ]);
        }

        return redirect()->route('dashboard.manufacturing.production.show', $run)->with('success', 'Production run created');
    }

    public function showProduction(ProductionRun $run)
    {
        $this->guardModel($run);
        $run->load('recipe.items.product', 'recipe.product', 'usages.product');
        return view('dashboard.manufacturing.production-show', compact('run'));
    }

    public function updateProductionStatus(Request $req, ProductionRun $run)
    {
        $this->guardModel($run);
        $data = $req->validate(['status' => 'required|in:planned,in_progress,completed,cancelled']);
        $run->update($data);

        if ($req->status === 'completed') {
            $run->update(['end_date' => now()]);
            // Calculate total cost from usages
            $totalCost = $run->usages->sum(function($u) {
                return ($u->actual_quantity ?? $u->planned_quantity) * ($u->unit_cost ?? 0);
            });
            $totalCost += ($run->recipe->labor_cost ?? 0) + ($run->recipe->overhead_cost ?? 0);
            $run->update(['total_cost' => $totalCost]);
        }

        return redirect()->route('dashboard.manufacturing.production.show', $run)->with('success', 'Status updated');
    }

    public function recordUsage(Request $req, ProductionRun $run)
    {
        $this->guardModel($run);
        $data = $req->validate([
            'usage_id' => 'required|exists:production_usages,id',
            'actual_quantity' => 'required|numeric|min:0',
        ]);

        $usage = ProductionUsage::findOrFail($data['usage_id']);
        $usage->update(['actual_quantity' => $data['actual_quantity']]);
        return redirect()->route('dashboard.manufacturing.production.show', $run)->with('success', 'Usage recorded');
    }

    private function guardModel($model)
    {
        if ($model->user_id !== auth()->id()) abort(403);
    }
}
