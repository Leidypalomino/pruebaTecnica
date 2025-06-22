import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpHeaders, HttpClientModule } from '@angular/common/http';

interface Category {
  id?: number;
  name: string;
  slug: string;
  parent_id?: number;
}

interface ProductImage {
  url: string;
  alt_text: string;
  is_primary: boolean;
}

interface Product {
  id?: number;
  sku: string;
  name: string;
  description: string;
  price: number;
  stock: number;
  status: string;
  featured: boolean;
  categories: Category[];
  images: ProductImage[];
}

@Component({
  selector: 'app-admin-products',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './admin-products.html',
  styleUrl: './admin-products.scss'
})
export class AdminProducts implements OnInit {
  products: Product[] = [];
  currentProduct: Product = this.getEmptyProduct();
  isEditing: boolean = false;
  tempImages: { file: File, url: string }[] = [];
  availableCategories: Category[] = [];
  selectedCategory?: Category;
  message: string = '';
  messageType: 'success' | 'error' | '' = '';

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.loadProducts();
    this.loadCategories();
  }

  getAuthHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'accept': 'application/json'
    });
  }

  loadProducts(): void {
    this.http.get<any>('http://localhost:8080/api/products')
      .subscribe({
        next: (response) => {
          this.products = (response.data || []).map((p: any) => ({
            id: p.id,
            sku: p.sku,
            name: p.name,
            description: p.description,
            price: Number(p.price),
            stock: p.stock,
            status: p.status,
            featured: p.featured,
            categories: p.categories || [],
            images: p.images || []
          }));
        },
        error: () => this.showMessage('No se pudieron cargar los productos', 'error')
      });
  }

  loadCategories(): void {
    this.http.get<any>('http://localhost:8080/api/categories', { headers: this.getAuthHeaders() })
      .subscribe({
        next: (response) => this.availableCategories = response.data,
        error: () => this.showMessage('No se pudieron cargar las categorías', 'error')
      });
  }

  saveProduct(): void {
    if (!this.currentProduct.name || !this.currentProduct.price || !this.currentProduct.sku || !this.selectedCategory) {
      this.showMessage('Por favor, completa todos los campos obligatorios.', 'error');
      return;
    }

    // this.currentProduct.categories = [this.selectedCategory.id];

    const productToSend: any = {
      ...this.currentProduct,
      categories: [this.selectedCategory.id], // Solo IDs para el backend
      images: []
    }

    if (this.isEditing && this.currentProduct.id) {
      this.http.put<Product>(
        `http://localhost:8080/api/products/${this.currentProduct.id}`,
        productToSend,
        { headers: this.getAuthHeaders() }
      ).subscribe({
        next: (product) => {
          this.uploadImages(product.id!);
          this.showMessage('Producto actualizado con éxito!', 'success');
          this.loadProducts();
          this.resetForm();
        },
        error: () => this.showMessage('No se pudo actualizar el producto', 'error')
      });
    } else {
      this.http.post<Product>(
        'http://localhost:8080/api/products',
        productToSend,
        { headers: this.getAuthHeaders() }
      ).subscribe({
        next: (product) => {
          this.uploadImages(product.id!);
          this.showMessage('Producto añadido con éxito!', 'success');
          this.loadProducts();
          this.resetForm();
        },
        error: () => this.showMessage('No se pudo crear el producto', 'error')
      });
    }
  }

  editProduct(product: Product): void {
    this.isEditing = true;
    this.currentProduct = { ...product };
    this.selectedCategory = product.categories[0];
    this.tempImages = [];
  }

  deleteProduct(id?: number): void {
    if (!id) return;
    // Si quieres un modal de confirmación, usa tu propio modal aquí.
    this.http.delete(`http://localhost:8080/api/products/${id}`, { headers: this.getAuthHeaders() })
      .subscribe({
        next: () => {
          this.showMessage('Producto eliminado.', 'success');
          this.loadProducts();
          this.resetForm();
        },
        error: () => this.showMessage('No se pudo eliminar el producto', 'error')
      });
  }

  cancelEdit(): void {
    this.resetForm();
  }

  resetForm(): void {
    this.currentProduct = this.getEmptyProduct();
    this.selectedCategory = undefined;
    this.isEditing = false;
    this.tempImages = [];
  }

  getEmptyProduct(): Product {
    return {
      sku: '',
      name: '',
      description: '',
      price: 0,
      stock: 0,
      status: 'active',
      featured: false,
      categories: [],
      images: []
    };
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      this.processFiles(input.files);
    }
  }

  private processFiles(files: FileList): void {
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e: any) => {
          this.tempImages.push({ file: file, url: e.target.result });
        };
        reader.readAsDataURL(file);
      } else {
        alert('Solo se permiten archivos de imagen.');
      }
    }
  }

  removeImage(index: number, isTemp: boolean): void {
    if (isTemp) {
      this.tempImages.splice(index, 1);
    } else {
      this.currentProduct.images.splice(index, 1);
    }
  }

  onDragOver(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    (event.currentTarget as HTMLElement).classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900');
  }

  onDragLeave(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    (event.currentTarget as HTMLElement).classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900');
  }

  onDrop(event: DragEvent): void {
    event.preventDefault();
    event.stopPropagation();
    (event.currentTarget as HTMLElement).classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900');
    const files = event.dataTransfer?.files;
    if (files) {
      this.processFiles(files);
    }
  }

  showMessage(msg: string, type: 'success' | 'error' = 'success') {
    this.message = msg;
    this.messageType = type;
    setTimeout(() => {
      this.message = '';
      this.messageType = '';
    }, 4000);
  }

  uploadImages(productId: number) {
    if (!this.tempImages.length) return;
    this.tempImages.forEach((img, idx) => {
      const formData = new FormData();
      formData.append('image', img.file);
      formData.append('alt_text', `Imagen ${idx + 1}`);
      formData.append('is_primary', idx === 0 ? 'true' : 'false');
      this.http.post(
        `http://localhost:8080/api/products/${productId}/images`,
        formData,
        { headers: this.getAuthHeaders() }
      ).subscribe({
        next: () => {},
        error: () => this.showMessage('No se pudo subir una imagen', 'error')
      });
    });
  }
}