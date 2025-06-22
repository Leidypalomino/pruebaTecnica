<?php

namespace App\Http\Controllers\API;

use App\Contracts\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(private ProductServiceInterface $productService)
    {
    }
 /**
     * @OA\Get(
     * path="/api/products",
     * tags={"Products"},
     * summary="Listar y filtrar productos",
     * description="Obtiene una lista paginada de productos con opciones avanzadas de filtrado y ordenamiento.",
     * @OA\Parameter(name="search", in="query", description="Término de búsqueda full-text", @OA\Schema(type="string")),
     * @OA\Parameter(name="category", in="query", description="ID de la categoría para filtrar", @OA\Schema(type="integer")),
     * @OA\Parameter(name="min_price", in="query", description="Precio mínimo", @OA\Schema(type="number")),
     * @OA\Parameter(name="max_price", in="query", description="Precio máximo", @OA\Schema(type="number")),
     * @OA\Parameter(name="status", in="query", description="Filtrar por estado", @OA\Schema(type="string", enum={"active", "inactive", "draft"})),
     * @OA\Parameter(name="featured", in="query", description="Filtrar por productos destacados", @OA\Schema(type="boolean")),
     * @OA\Parameter(name="sort_by", in="query", description="Campo para ordenar", @OA\Schema(type="string", enum={"price", "name", "created_at"})),
     * @OA\Parameter(name="sort_dir", in="query", description="Dirección de ordenamiento", @OA\Schema(type="string", enum={"asc", "desc"})),
     * @OA\Parameter(name="include", in="query", description="Incluir relaciones (ej: categories,images)", @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Lista de productos paginada", @OA\JsonContent(type="object", @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")), @OA\Property(property="path", type="string"), @OA\Property(property="per_page", type="integer"), @OA\Property(property="next_cursor", type="string", nullable=true), @OA\Property(property="next_page_url", type="string", nullable=true), @OA\Property(property="prev_cursor", type="string", nullable=true), @OA\Property(property="prev_page_url", type="string", nullable=true)))
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        $products = $this->productService->searchProducts($filters);
        return response()->json($products);
    }

    /**
     * @OA\Get(
     * path="/api/products/{id}",
     * tags={"Products"},
     * summary="Obtener un producto por su ID",
     * description="Devuelve los detalles completos de un solo producto. Use ?include=categories,images para cargar relaciones.",
     * @OA\Parameter(name="id", in="path", required=true, description="ID del producto", @OA\Schema(type="integer")),
     * @OA\Parameter(
     * name="include",
     * in="query",
     * description="Incluir relaciones (ej: categories,images)",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(response=200, description="Detalles del producto", @OA\JsonContent(ref="#/components/schemas/Product")),
     * @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function show(Request $request, $id)
    {
        $relationsToLoad = [];
        if ($request->has('include')) {
            $relationsToLoad = explode(',', $request->input('include'));
        }

        $product = $this->productService->getProductById((int)$id, $relationsToLoad);
        
        return $product ? response()->json($product) : response()->json(['error' => 'Product not found'], 404);
    }


    /**
     * @OA\Post(
     * path="/api/products",
     * tags={"Products"},
     * summary="Crear un nuevo producto",
     * description="Crea un nuevo producto en la base de datos (Requiere rol de 'admin').",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * description="Datos del nuevo producto. Para asociar categorías, envía un array de IDs de categorías existentes.",
     * @OA\JsonContent(
     * required={"name", "sku", "price", "stock"},
     * @OA\Property(property="sku", type="string", example="SKU123"),
     * @OA\Property(property="name", type="string", example="Nuevo Producto"),
     * @OA\Property(property="price", type="number", format="float", example=99.99),
     * @OA\Property(property="stock", type="integer", example=100),
     * @OA\Property(property="description", type="string", example="Descripción del producto."),
     * @OA\Property(property="status", type="string", enum={"active", "inactive", "draft"}, example="active"),
     * @OA\Property(property="featured", type="boolean", example=false),
     * @OA\Property(property="categories", type="array", @OA\Items(type="integer"), example={1, 2})
     * )
     * ),
     * @OA\Response(response=201, description="Producto creado", @OA\JsonContent(ref="#/components/schemas/Product")),
     * @OA\Response(response=403, description="Acceso denegado (no es admin)"),
     * @OA\Response(response=422, description="Error de validación")
     * )
     */
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'featured' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        $categoryIds = $validatedData['categories'] ?? [];
        unset($validatedData['categories']);
    
        $product = $this->productService->createProduct($validatedData);

        if (!empty($categoryIds)) {
            $product->categories()->attach($categoryIds);
        }

        $product->load('categories', 'images');
        return response()->json($product, 201);
    }

     /**
     * @OA\Put(
     * path="/api/products/{id}",
     * tags={"Products"},
     * summary="Actualizar un producto existente",
     * description="Actualiza los datos de un producto (Requiere rol de 'admin').",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Product")),
     * @OA\Response(response=200, description="Producto actualizado", @OA\JsonContent(ref="#/components/schemas/Product")),
     * @OA\Response(response=403, description="Acceso denegado"),
     * @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    
    public function update(Request $request, $id)
    {
        $product = $this->productService->getProductById((int)$id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        $this->productService->updateProduct((int)$id, $request->all());
        
        $updatedProduct = $this->productService->getProductById((int)$id, ['categories', 'images']);
        return response()->json($updatedProduct); 
    }
     /**
     * @OA\Delete(
     * path="/api/products/{id}",
     * tags={"Products"},
     * summary="Eliminar un producto",
     * description="Elimina un producto de la base de datos (Requiere rol de 'admin').",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=204, description="Producto eliminado exitosamente"),
     * @OA\Response(response=403, description="Acceso denegado"),
     * @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    
    public function destroy($id)
    {
        if ($this->productService->deleteProduct((int)$id)) {
            return response()->json(null, 204);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }
  /**
     * @OA\Post(
     * path="/api/products/{id}/images",
     * tags={"Products"},
     * summary="Subir una imagen para un producto",
     * description="Sube un archivo de imagen y lo asocia a un producto (Requiere rol de 'admin').",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="image", type="string", format="binary", description="El archivo de imagen a subir."),
     * @OA\Property(property="alt_text", type="string", description="Texto alternativo para la imagen."),
     * @OA\Property(property="is_primary", type="boolean", description="Marcar como imagen principal.")
     * )
     * )
     * ),
     * @OA\Response(response=201, description="Imagen subida exitosamente", @OA\JsonContent(ref="#/components/schemas/ProductImage")),
     * @OA\Response(response=403, description="Acceso denegado")
     * )
     */
    public function uploadImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $product = Product::findOrFail($id);
        
        $path = $request->file('image')->store('product-images', 'public');
        
        $image = $product->images()->create([
            'url' => Storage::url($path),
            'alt_text' => $request->input('alt_text', $product->name),
            'is_primary' => $request->input('is_primary', false),
        ]);

        return response()->json($image, 201);
    }
    /**
     * @OA\Get(
     * path="/api/products/{id}/views",
     * tags={"Products"},
     * summary="Registrar una vista de producto",
     * description="Incrementa en uno el contador de vistas de un producto. Este endpoint es público.",
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Vista registrada exitosamente"),
     * @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function registerView($id)
    {
        $updated = Product::where('id', $id)->update(['views_count' => DB::raw('views_count + 1')]);

        if ($updated) {
            return response()->json(['message' => 'Product view registered.']);
        }

        return response()->json(['error' => 'Product not found.'], 404);
    }
}