import { Component, OnInit, OnDestroy, ViewChild, ElementRef, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { debounceTime, Subject } from 'rxjs';
import { takeUntil } from 'rxjs/operators';
import { RouterModule, ActivatedRoute } from '@angular/router';
import { HttpClient, HttpClientModule } from '@angular/common/http';

interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  category: string;
  image: string;
  inStock: boolean;
  rating: number;
  reviews: number;
}

interface CategoryFilter {
  name: string;
  count: number;
}

@Component({
  selector: 'app-catalog',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, HttpClientModule],
  templateUrl: './catalog.html',
  styleUrl: './catalog.scss'
})
export class Catalog implements OnInit, OnDestroy, AfterViewInit {
  viewMode: 'grid' | 'list' = 'grid';
  showFiltersMobile: boolean = false;

  searchTerm: string = '';
  private searchSubject = new Subject<string>();
  selectedCategories: string[] = [];
  availableCategories: CategoryFilter[] = [];
  minPrice: number = 0;
  maxPrice: number = 1000;
  inStockOnly: boolean = false;
  minRating: number = 0;

  sortBy: string = 'nameAsc';

  allProducts: Product[] = [];
  filteredProducts: Product[] = [];
  displayedProducts: Product[] = [];

  useInfiniteScroll: boolean = true;
  currentPage: number = 1;
  productsPerPage: number = 8;
  totalPages: number = 1;
  loading: boolean = false;
  noMoreProducts: boolean = false;

  private destroy$ = new Subject<void>();

  @ViewChild('scrollTrigger') scrollTrigger!: ElementRef;
  private observer!: IntersectionObserver;

  showQuickViewModal: boolean = false;
  selectedProductForQuickView: Product | null = null;

  constructor(private http: HttpClient, private route: ActivatedRoute) {}

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      const categoryId = params['category'];
      if (categoryId) {
        this.loadProductsByCategory(categoryId);
      } else {
        this.loadProductsFromApi();
      }
    });

    this.searchSubject.pipe(
      debounceTime(400),
      takeUntil(this.destroy$)
    ).subscribe((term) => {
      this.searchProductsFromApi(term);
    });
    // this.loadProductsFromApi();
  }

  searchProductsFromApi(term: string): void {
    this.loading = true;
    let url = 'http://localhost:8080/api/products?include=categories,images';
    if (term && term.trim().length > 0) {
      url += `&search=${encodeURIComponent(term.trim())}`;
    }
    this.http.get<any>(url).subscribe({
      next: (response) => {
        this.allProducts = (response.data || []).map((p: any) => ({
          id: p.id,
          name: p.name,
          description: p.description,
          price: Number(p.price),
          category: p.categories && p.categories.length > 0 ? p.categories[0].name : 'Sin categoría',
          image: p.images && p.images.length > 0
            ? ('http://localhost:8080' + (p.images.find((img: any) => img.is_primary)?.url || p.images[0].url))
            : 'https://via.placeholder.com/400x300?text=Sin+Imagen',
          inStock: p.stock > 0,
          rating: Math.floor(Math.random() * 5) + 1,
          reviews: Math.floor(Math.random() * 200) + 5
        }));
        this.populateCategories();
        this.applyFiltersAndSorting();
        this.loading = false;
      },
      error: () => {
        this.allProducts = [];
        this.filteredProducts = [];
        this.displayedProducts = [];
        this.loading = false;
      }
    });
  }

  loadProductsByCategory(categoryId: number): void {
    this.loading = true;
    this.http.get<any>(`http://localhost:8080/api/products?category=${categoryId}&include=categories,images`)
      .subscribe({
        next: (response) => {
          this.allProducts = (response.data || []).map((p: any) => ({
            id: p.id,
            name: p.name,
            description: p.description,
            price: Number(p.price),
            category: p.categories && p.categories.length > 0 ? p.categories[0].name : 'Sin categoría',
            image: p.images && p.images.length > 0
              ? ('http://localhost:8080' + (p.images.find((img: any) => img.is_primary)?.url || p.images[0].url))
              : 'https://via.placeholder.com/400x300?text=Sin+Imagen',
            inStock: p.stock > 0,
            rating: Math.floor(Math.random() * 5) + 1,
            reviews: Math.floor(Math.random() * 200) + 5
          }));
          this.populateCategories();
          this.applyFiltersAndSorting();
          this.loading = false;
        },
        error: () => {
          this.allProducts = [];
          this.filteredProducts = [];
          this.displayedProducts = [];
          this.loading = false;
        }
      });
  }

  loadProductsFromApi(): void {
    this.loading = true;
    this.http.get<any>('http://localhost:8080/api/products?include=categories,images').subscribe({
      next: (response) => {
        this.allProducts = (response.data || []).map((p: any) => ({
          id: p.id,
          name: p.name,
          description: p.description,
          price: Number(p.price),
          category: p.categories && p.categories.length > 0 ? p.categories[0].name : 'Sin categoría',
          image: p.images && p.images.length > 0
              ? ('http://localhost:8080' + (p.images.find((img: any) => img.is_primary)?.url || p.images[0].url))
              : 'https://via.placeholder.com/400x300?text=Sin+Imagen',
          inStock: p.stock > 0,
          rating: Math.floor(Math.random() * 5) + 1,
          reviews: Math.floor(Math.random() * 200) + 5
        }));
        this.populateCategories();
        this.applyFiltersAndSorting();
        this.loading = false;
      },
      error: () => {
        this.allProducts = [];
        this.filteredProducts = [];
        this.displayedProducts = [];
        this.loading = false;
      }
    });
  }

  ngAfterViewInit(): void {
    if (this.useInfiniteScroll) {
      this.setupInfiniteScroll();
    }
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
    if (this.observer) {
      this.observer.disconnect();
    }
  }

  toggleView(mode: 'grid' | 'list'): void {
    this.viewMode = mode;
  }

  toggleFiltersMobile(): void {
    this.showFiltersMobile = !this.showFiltersMobile;
  }

  onSearchInput(): void {
    this.searchSubject.next(this.searchTerm);
  }

  toggleCategory(categoryName: string): void {
    const index = this.selectedCategories.indexOf(categoryName);
    if (index > -1) {
      this.selectedCategories.splice(index, 1);
    } else {
      this.selectedCategories.push(categoryName);
    }
    this.applyFiltersAndSorting();
  }

  applyFilters(): void {
    if (!this.useInfiniteScroll) {
      this.currentPage = 1;
    }
    this.applyFiltersAndSorting();
  }

  resetFilters(): void {
    this.searchTerm = '';
    this.selectedCategories = [];
    this.minPrice = 0;
    this.maxPrice = 1000;
    this.inStockOnly = false;
    this.minRating = 0;
    this.applyFiltersAndSorting();
  }

  applySorting(): void {
    this.applyFiltersAndSorting();
  }

  private applyFiltersAndSorting(): void {
    this.loading = true;
    setTimeout(() => {
      let tempProducts = [...this.allProducts];

      if (this.searchTerm) {
        const lowerCaseSearchTerm = this.searchTerm.toLowerCase();
        tempProducts = tempProducts.filter(p =>
          p.name.toLowerCase().includes(lowerCaseSearchTerm) ||
          p.description.toLowerCase().includes(lowerCaseSearchTerm)
        );
      }

      if (this.selectedCategories.length > 0) {
        tempProducts = tempProducts.filter(p => this.selectedCategories.includes(p.category));
      }

      tempProducts = tempProducts.filter(p => p.price >= this.minPrice && p.price <= this.maxPrice);

      if (this.inStockOnly) {
        tempProducts = tempProducts.filter(p => p.inStock);
      }

      if (this.minRating > 0) {
        tempProducts = tempProducts.filter(p => p.rating >= this.minRating);
      }

      switch (this.sortBy) {
        case 'nameAsc':
          tempProducts.sort((a, b) => a.name.localeCompare(b.name));
          break;
        case 'nameDesc':
          tempProducts.sort((a, b) => b.name.localeCompare(a.name));
          break;
        case 'priceAsc':
          tempProducts.sort((a, b) => a.price - b.price);
          break;
        case 'priceDesc':
          tempProducts.sort((a, b) => b.price - a.price);
          break;
        case 'ratingDesc':
          tempProducts.sort((a, b) => b.rating - a.rating);
          break;
      }

      this.filteredProducts = tempProducts;

      if (this.useInfiniteScroll) {
        this.displayedProducts = [];
        this.currentPage = 0;
        this.noMoreProducts = false;
        this.loadMoreProducts();
      } else {
        this.totalPages = Math.ceil(this.filteredProducts.length / this.productsPerPage);
        this.updateDisplayedProductsForPagination();
      }
      this.loading = false;
    }, 300);
  }

  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.updateDisplayedProductsForPagination();
    }
  }

  private updateDisplayedProductsForPagination(): void {
    const startIndex = (this.currentPage - 1) * this.productsPerPage;
    const endIndex = startIndex + this.productsPerPage;
    this.displayedProducts = this.filteredProducts.slice(startIndex, endIndex);
  }

  private setupInfiniteScroll(): void {
    const options = {
      root: null,
      rootMargin: '0px',
      threshold: 0.1
    };

    this.observer = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting && !this.loading && !this.noMoreProducts) {
        this.loadMoreProducts();
      }
    }, options);

    if (this.scrollTrigger) {
      this.observer.observe(this.scrollTrigger.nativeElement);
    }
  }

  private loadMoreProducts(): void {
    if (this.loading || this.noMoreProducts) {
      return;
    }

    this.loading = true;
    setTimeout(() => {
      this.currentPage++;
      const startIndex = (this.currentPage - 1) * this.productsPerPage;
      const endIndex = startIndex + this.productsPerPage;

      const newProducts = this.filteredProducts.slice(startIndex, endIndex);
      this.displayedProducts = [...this.displayedProducts, ...newProducts];

      if (this.displayedProducts.length >= this.filteredProducts.length) {
        this.noMoreProducts = true;
      }
      this.loading = false;
    }, 800);
  }

  openQuickView(product: Product): void {
    this.selectedProductForQuickView = { ...product };
    this.showQuickViewModal = true;
  }

  closeQuickView(): void {
    this.showQuickViewModal = false;
    this.selectedProductForQuickView = null;
  }

  private populateCategories(): void {
    const categoryMap = new Map<string, number>();
    this.allProducts.forEach(product => {
      categoryMap.set(product.category, (categoryMap.get(product.category) || 0) + 1);
    });
    this.availableCategories = Array.from(categoryMap.entries()).map(([name, count]) => ({ name, count }));
  }
}