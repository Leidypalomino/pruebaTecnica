import { Component, OnInit } from '@angular/core';
import { RouterOutlet, RouterModule, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { Theme } from './core/theme';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders, HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet,
    RouterModule,
    CommonModule,
    HttpClientModule
  ],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App implements OnInit {
  isDarkMode$!: Observable<boolean>;
  isAuthenticated = false;
  isAdmin = false;

  constructor(
    private themeService: Theme, 
    private router: Router,
    private http: HttpClient
  ) {}

  ngOnInit() {
    this.isDarkMode$ = this.themeService.darkMode$;
    this.isAuthenticated = !!localStorage.getItem('token');
    const email = localStorage.getItem('user_email');
    this.isAdmin = email === 'admin@example.com';
  }

  toggleTheme(): void {
    this.themeService.toggleTheme(); // Delega la acción al servicio
  }

  logout() {
    const token = localStorage.getItem('token');
    if (!token) {
      this.finishLogout();
      return;
    }

    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'accept': 'application/json'
    });

    this.http.post('http://localhost:8080/api/auth/logout', {}, { headers })
      .subscribe({
        next: () => this.finishLogout(),
        error: () => this.finishLogout() // Incluso si falla, limpia el estado local
      });
  }

  private finishLogout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_email');
    this.isAuthenticated = false;
    this.isAdmin = false;
    this.router.navigate(['/auth/login']).then(() => {
      window.location.reload();
    });
  }

  updateAuthStatus() {
    this.isAuthenticated = !!localStorage.getItem('token');
  }

  refreshToken() {
    const token = localStorage.getItem('token');
    if (!token) return;

    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'accept': '*/*'
    });

    this.http.post<any>('http://localhost:8080/api/auth/refresh', {}, { headers })
      .subscribe({
        next: (res) => {
          localStorage.setItem('token', res.access_token);
        },
        error: () => {
          this.logout();
        }
      });
  }
}