import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpClientModule } from '@angular/common/http';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrl: './register.component.scss',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    HttpClientModule
  ]
})
export class RegisterComponent implements OnInit {
  registerForm!: FormGroup;
  loading = false;
  errorMsg = '';
  showSuccessModal = false;

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private http: HttpClient
  ) { }

  ngOnInit(): void {
    this.registerForm = this.fb.group({
      name: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      confirmPassword: ['', Validators.required]
    }, { validators: this.passwordMatchValidator });
  }

  passwordMatchValidator(form: FormGroup) {
    return form.get('password')?.value === form.get('confirmPassword')?.value
      ? null : { 'mismatch': true };
  }

  onSubmit(): void {
    if (this.registerForm.valid) {
      this.loading = true;
      this.errorMsg = '';
      const formValue = this.registerForm.value;
      const payload = {
        name: formValue.name,
        email: formValue.email,
        password: formValue.password,
        password_confirmation: formValue.confirmPassword
      };

      this.http.post('http://localhost:8080/api/auth/register', payload)
        .subscribe({
          next: (res) => {
            this.showSuccessModal = true; // Muestra el modal
            setTimeout(() => {
              this.showSuccessModal = false;
              this.router.navigate(['/auth/login']);
            }, 2000); // Espera 2 segundos antes de redirigir
          },
          error: (err) => {
            this.errorMsg = err.error?.message || 'Error al registrar usuario';
            this.loading = false;
          },
          complete: () => {
            this.loading = false;
          }
        });
    } else {
      this.registerForm.markAllAsTouched();
    }
  }
}