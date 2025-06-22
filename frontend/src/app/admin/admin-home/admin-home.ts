import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router, NavigationEnd } from '@angular/router';

@Component({
  selector: 'app-admin-home',
  standalone: true,
  imports: [CommonModule, RouterModule], // Mantén RouterModule para pipes si los usas, pero ya no para rutas
  templateUrl: './admin-home.html', // Asegúrate que el nombre del archivo es correcto
  styleUrl: './admin-home.scss'
})
export class AdminHome implements OnInit {
  totalSales: number = 0;
  productsInStock: number = 0;
  pendingOrders: number = 0;
  newUsersLast30Days: number = 0;
  topSellingProducts: { name: string; sales: number }[] = [];

  constructor(private router: Router) {}
    ngOnInit(): void {
    this.loadDashboardMetrics();

    // Elimina esta lógica, ya que el sidebar es manejado por AdminDashboardComponent
    /*
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe(() => {
      this.isDashboardRoute = this.router.url === '/admin';
    });
    this.isDashboardRoute = this.router.url === '/admin';
    */
  }

  loadDashboardMetrics(): void {
    setTimeout(() => {
      this.totalSales = 125678.50;
      this.productsInStock = 5230;
      this.pendingOrders = 45;
      this.newUsersLast30Days = 128;
      this.topSellingProducts = [
        { name: 'Laptop Gamer XYZ', sales: 15200.00 },
        { name: 'Auriculares Bluetooth Pro', sales: 8900.50 },
        { name: 'Smartwatch UltraFit', sales: 7500.00 },
        { name: 'Cámara Mirrorless 4K', sales: 6200.00 },
        { name: 'Teclado Mecánico RGB', sales: 4800.75 },
      ];
    }, 1000);
  }
}