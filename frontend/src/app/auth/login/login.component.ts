import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms'; 
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    HttpClientModule
  ]
})
export class LoginComponent implements OnInit {
  loginForm!: FormGroup;
  errorMsg = '';

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private http: HttpClient
  ) { }

  ngOnInit(): void {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      const payload = this.loginForm.value;
      this.http.post<any>('http://localhost:8080/api/auth/login', payload)
        .subscribe({
          next: (res) => {
            localStorage.setItem('token', res.access_token);
            localStorage.setItem('user_email', payload.email);
            this.router.navigate(['/']).then(() => window.location.reload());
          },
          error: (err) => {
            if (err.error?.error === 'Unauthorized') {
              this.errorMsg = 'Usuario o contraseña incorrectos';
            } else {
              this.errorMsg = 'Ocurrió un error al iniciar sesión';
            }
          }
        });
    } else {
      this.loginForm.markAllAsTouched();
    }
  }
}