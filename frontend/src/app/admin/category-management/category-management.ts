import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient, HttpHeaders, HttpClientModule } from '@angular/common/http';

interface Category {
  id?: number;
  name: string;
  parent_id?: number | null;
}

@Component({
  selector: 'app-category-management',
  standalone: true,
  imports: [CommonModule, FormsModule, HttpClientModule],
  templateUrl: './category-management.html',
  styleUrl: './category-management.scss'
})
export class CategoryManagement implements OnInit {
  categories: Category[] = [];
  currentCategory: Category = this.getEmptyCategory();
  isEditing: boolean = false;
  message: string = '';
  messageType: 'success' | 'error' | '' = '';
  showDeleteModal: boolean = false;
  categoryToDelete?: number;

  constructor(private http: HttpClient) { }

  ngOnInit(): void {
    this.loadCategories();
  }

  getAuthHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'accept': 'application/json'
    });
  }

  loadCategories(): void {
    this.http.get<any>('http://localhost:8080/api/categories', { headers: this.getAuthHeaders() })
      .subscribe({
        next: (response) => this.categories = response.data, // <-- Cambia aquí
        error: () => this.showMessage('No se pudieron cargar las categorías', 'error')
      });
  }

  saveCategory(): void {
    if (!this.currentCategory.name) {
      this.showMessage('Por favor, ingresa el nombre de la categoría.', 'error');
      return;
    }

    if (this.isEditing && this.currentCategory.id) {
      this.http.put<Category>(
        `http://localhost:8080/api/categories/${this.currentCategory.id}`,
        { name: this.currentCategory.name, parent_id: this.currentCategory.parent_id },
        { headers: this.getAuthHeaders() }
      ).subscribe({
        next: () => {
          this.showMessage('Categoría actualizada con éxito!', 'success');
          this.loadCategories();
          this.resetForm();
        },
        error: () => this.showMessage('No se pudo actualizar la categoría', 'error')
      });
    } else {
      this.http.post<Category>(
        'http://localhost:8080/api/categories',
        { name: this.currentCategory.name, parent_id: this.currentCategory.parent_id },
        { headers: this.getAuthHeaders() }
      ).subscribe({
        next: () => {
          this.showMessage('Categoría añadida con éxito!', 'success');
          this.loadCategories();
          this.resetForm();
        },
        error: () => this.showMessage('No se pudo crear la categoría', 'error')
      });
    }
  }

  editCategory(category: Category): void {
    this.isEditing = true;
    this.currentCategory = { ...category };
  }

  deleteCategory(id?: number): void {
    if (!id) return;
    if (confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
      this.http.delete(`http://localhost:8080/api/categories/${id}`, { headers: this.getAuthHeaders() })
        .subscribe({
          next: () => {
            this.showMessage('Categoría eliminada.', 'success');
            this.loadCategories();
            this.resetForm();
          },
          error: () => this.showMessage('No se pudo eliminar la categoría', 'error')
        });
    }
  }

  cancelEdit(): void {
    this.resetForm();
  }

  resetForm(): void {
    this.currentCategory = this.getEmptyCategory();
    this.isEditing = false;
  }

  getEmptyCategory(): Category {
    return {
      name: '',
      parent_id: null
    };
  }

  showMessage(msg: string, type: 'success' | 'error' = 'success') {
    this.message = msg;
    this.messageType = type;
    setTimeout(() => {
      this.message = '';
      this.messageType = '';
    }, 4000);
  }

  openDeleteModal(id?: number) {
    if (id === undefined) return;
    this.categoryToDelete = id;
    this.showDeleteModal = true;
  }

  closeDeleteModal() {
    this.showDeleteModal = false;
    this.categoryToDelete = undefined;
  }

  confirmDeleteCategory() {
    if (!this.categoryToDelete) return;
    this.http.delete(`http://localhost:8080/api/categories/${this.categoryToDelete}`, { headers: this.getAuthHeaders() })
      .subscribe({
        next: () => {
          this.showMessage('Categoría eliminada.', 'success');
          this.loadCategories();
          this.resetForm();
          this.closeDeleteModal();
        },
        error: () => {
          this.showMessage('No se pudo eliminar la categoría', 'error');
          this.closeDeleteModal();
        }
      });
  }
}