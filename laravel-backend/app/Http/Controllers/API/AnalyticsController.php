<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
     /**
     * @OA\Get(
     * path="/api/analytics/low-stock",
     * tags={"Analytics"},
     * summary="Obtener productos con bajo stock",
     * description="Devuelve una lista de productos cuyo stock actual es menor o igual a su umbral de bajo stock (Requiere rol de 'admin').",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Lista de productos con bajo stock",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     * ),
     * @OA\Response(response=403, description="Acceso denegado")
     * )
     */
    public function lowStock()
    {
        // whereColumn compara el valor de dos columnas en la misma fila
        $products = Product::whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock', 'asc')
            ->get();
            
        return response()->json($products);
    }
    
    /**
     * @OA\Get(
     * path="/api/analytics/popular",
     * tags={"Analytics"},
     * summary="Obtener productos más vistos",
     * description="Devuelve una lista de los productos más populares basado en su contador de vistas (Requiere rol de 'admin').",
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Lista de productos populares",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     * ),
     * @OA\Response(response=403, description="Acceso denegado")
     * )
     */
    public function popularProducts()
    {
        $popularProducts = Product::where('status', 'active')
            ->orderBy('views_count', 'desc')
            ->take(10) // Tomamos el top 10
            ->get();
            
        return response()->json($popularProducts);
    }
}