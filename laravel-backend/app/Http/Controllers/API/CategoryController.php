<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    public function __construct()
    {
       
    }

    // ... (index, store, show, update, destroy methods remain the same) ...
    
    /**
     * @OA\Get(
     * path="/api/categories",
     * tags={"Categories"},
     * summary="List all categories",
     * description="Returns a collection of all categories for filtering or display.",
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Category")
     * )
     * )
     * )
     */
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    /**
     * @OA\Post(
     * path="/api/categories",
     * tags={"Categories"},
     * summary="Create a new category",
     * description="Creates a new category (admin only).",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="Laptops"),
     * @OA\Property(property="parent_id", type="integer", nullable=true, example=1)
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Category created",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(response=403, description="Access denied")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id'
        ]);
        
        $validatedData['slug'] = Str::slug($validatedData['name']);
        
        $category = Category::create($validatedData);

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     * path="/api/categories/{category}",
     * tags={"Categories"},
     * summary="Get a specific category",
     * description="Returns details of a single category.",
     * @OA\Parameter(
     * name="category",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="The category ID"
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * @OA\Put(
     * path="/api/categories/{category}",
     * tags={"Categories"},
     * summary="Update a category",
     * description="Updates an existing category (admin only).",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="category",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="The category ID"
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="Updated Laptops"),
     * @OA\Property(property="parent_id", type="integer", nullable=true, example=1)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Category updated",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(response=403, description="Access denied")
     * )
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.$category->id,
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        if (isset($validatedData['name'])) {
            $validatedData['slug'] = Str::slug($validatedData['name']);
        }
        
        $category->update($validatedData);

        return new CategoryResource($category);
    }

    /**
     * @OA\Delete(
     * path="/api/categories/{category}",
     * tags={"Categories"},
     * summary="Delete a category",
     * description="Deletes a category (admin only).",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="category",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="The category ID"
     * ),
     * @OA\Response(
     * response=204,
     * description="Category deleted"
     * ),
     * @OA\Response(response=403, description="Access denied")
     * )
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }
    
    /**
     * @OA\Get(
     * path="/api/categories/{category}/products",
     * tags={"Categories"},
     * summary="List products in a specific category",
     * description="Returns a paginated list of all products belonging to a specific category.",
     * @OA\Parameter(
     * name="category",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="The category ID"
     * ),
     * @OA\Parameter(
     * name="include",
     * in="query",
     * description="Include product relationships (e.g., 'images')",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="A paginated list of products",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     * @OA\Property(property="path", type="string"),
     * @OA\Property(property="per_page", type="integer"),
     * @OA\Property(property="next_cursor", type="string", nullable=true),
     * @OA\Property(property="next_page_url", type="string", nullable=true),
     * @OA\Property(property="prev_cursor", type="string", nullable=true),
     * @OA\Property(property="prev_page_url", type="string", nullable=true)
     * )
     * ),
     * @OA\Response(response=404, description="Category not found")
     * )
     */
    public function products(Request $request, Category $category)
    {
        $query = $category->products();

        // Carga ansiosa de imágenes si se solicita
        if ($request->has('include') && in_array('images', explode(',', $request->input('include')))) {
            $query->with('images');
        }

        $products = $query->cursorPaginate(15);

        return response()->json($products);
    }
}