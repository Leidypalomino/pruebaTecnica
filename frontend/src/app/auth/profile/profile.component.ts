import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders, HttpClientModule } from '@angular/common/http';
import { CommonModule } from '@angular/common';

interface Profile {
  id: number;
  name: string;
  email: string;
  role: string;
  is_active: boolean;
  created_at: string;
}

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [
    CommonModule,
    HttpClientModule
  ],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.scss'
})
export class ProfileComponent implements OnInit {
  profile?: Profile;
  errorMsg = '';

  constructor(private http: HttpClient) {}

  ngOnInit(): void { // <-- MÉTODO ngOnInit CORRECTO
    console.log('ngOnInit ejecutado');
    const token = localStorage.getItem('token');
    if (!token) {
      this.errorMsg = 'No autenticado';
      return;
    }

    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });

    this.http.get<Profile>('http://localhost:8080/api/auth/me', { headers })
      .subscribe({
        next: (data) => this.profile = data,
        error: () => this.errorMsg = 'No se pudo cargar el perfil'
      });
  }
}