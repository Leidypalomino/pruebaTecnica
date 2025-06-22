import { Component } from '@angular/core'; // Ya no necesitas OnInit si no cargas datos aquí
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router'; // Para routerLink y routerOutlet

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './admin-dashboard.html', // Asegúrate que el nombre del archivo es correcto
  styleUrl: './admin-dashboard.scss'
})
export class AdminDashboard {}