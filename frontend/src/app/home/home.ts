// frontend/src/app/products/home/home.component.ts
import { Component, OnInit } from '@angular/core'; // Add OnInit if you need ngOnInit lifecycle hook
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { HttpClient, HttpClientModule } from '@angular/common/http';

interface Product {
  id: number;
  name: string;
  price: number;
  category: string;
  image: string;
  discount?: number;
  isNew?: boolean;
}

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterModule, HttpClientModule],
  templateUrl: './home.html',
})
export class Home implements OnInit {
  isAuthenticated = false;
  loading = true;
  loadingCategories = true;

  featuredProducts: Product[] = [];
  mostViewedProducts: Product[] = [];

  mainCategories: {
    id: number;
    name: string;
    image: string;
  }[] = [];

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.isAuthenticated = !!localStorage.getItem('token');

    this.http.get<any>('http://localhost:8080/api/categories').subscribe({
      next: (response) => {
        // Asigna una imagen por defecto a cada categoría
        this.mainCategories = (response.data || []).map((cat: any) => ({
          id: cat.id,
          name: cat.name,
          image: this.getCategoryImage(cat.slug)
        }));
        this.loadingCategories = false;
      },
      error: () => {
        this.mainCategories = [];
        this.loadingCategories = false;
      }
    });

    this.http.get<any>('http://localhost:8080/api/products?featured=true&include=categories,images').subscribe({
      next: (response) => {
        this.featuredProducts = (response.data || []).map((p: any) => ({
          id: p.id,
          name: p.name,
          price: Number(p.price),
          category: p.categories && p.categories.length > 0 ? p.categories[0].name : 'Sin categoría',
          image: p.images && p.images.length > 0
            ? ('http://localhost:8080' + (p.images.find((img: any) => img.is_primary)?.url || p.images[0].url))
            : 'https://via.placeholder.com/400x300?text=Sin+Imagen',
          discount: Math.random() > 0.7 ? Math.floor(Math.random() * 30) + 5 : 0 // Simula descuentos si quieres
        }));
      },
      error: () => {
        this.featuredProducts = [];
      }
    });

    setTimeout(() => {
      // Productos destacados (solo 4)
      this.http.get<any>('http://localhost:8080/api/products?featured=true&include=categories,images').subscribe({
        next: (response) => {
          this.featuredProducts = (response.data || [])
            .sort(() => 0.5 - Math.random()) // Mezcla aleatoriamente si hay más de 4
            .slice(0, 4) // Solo 4 productos
            .map((p: any) => ({
              id: p.id,
              name: p.name,
              price: Number(p.price),
              category: p.categories && p.categories.length > 0 ? p.categories[0].name : 'Sin categoría',
              image: p.images && p.images.length > 0
                ? ('http://localhost:8080' + (p.images.find((img: any) => img.is_primary)?.url || p.images[0].url))
                : 'https://via.placeholder.com/400x300?text=Sin+Imagen',
              discount: Math.random() > 0.7 ? Math.floor(Math.random() * 30) + 5 : 0
            }));
        },
        error: () => {
          this.featuredProducts = [];
        }
      });

      // Lo más visto (simulado: 4 aleatorios)
      this.http.get<any>('http://localhost:8080/api/products?include=categories,images').subscribe({
        next: (response) => {
          const all = (response.data || []).map((p: any) => ({
            id: p.id,
            name: p.name,
            price: Number(p.price),
            category: p.categories && p.categories.length > 0 ? p.categories[0].name : 'Sin categoría',
            image: p.images && p.images.length > 0
              ? ('http://localhost:8080' + (p.images.find((img: any) => img.is_primary)?.url || p.images[0].url))
              : 'https://via.placeholder.com/400x300?text=Sin+Imagen',
            isNew: Math.random() > 0.5
          }));
          this.mostViewedProducts = all.sort(() => 0.5 - Math.random()).slice(0, 4);
          this.loading = false;
        },
        error: () => {
          this.mostViewedProducts = [];
          this.loading = false;
        }
      });
    }, 1200);
  }

  getCategoryImage(slug: string): string {
    const images: { [key: string]: string } = {
    electronicos: 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?auto=format&fit=crop&w=100&q=80',
    celulares: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=100&q=80',
    laptops: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=100&q=80',
    clothing: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=100&q=80',    'mens-clothing': 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=100&q=80',
    'womens-clothing': 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=100&q=80',
    accessories: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=100&q=80',
    watches: 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=100&q=80',
    jewelry: 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=100&q=80',
    books: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=100&q=80'
    };
    return images[slug] || 'https://via.placeholder.com/100x100?text=Categoría';
  }
}