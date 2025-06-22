import { Component, OnInit } from '@angular/core';
import { CommonModule, DatePipe } from '@angular/common';

interface AuditLog {
  id: number;
  user: string;
  action: string;
  details: string;
  timestamp: Date;
}

@Component({
  selector: 'app-audit-logs',
  standalone: true,
  imports: [CommonModule, DatePipe],
  templateUrl: './audit-logs.html',
  styleUrl: './audit-logs.scss'
})
export class AuditLogs implements OnInit {
  auditLogs: AuditLog[] = [];

  constructor() { }

  ngOnInit(): void {
    this.loadAuditLogs();
  }

  loadAuditLogs(): void {
    // Simula la carga de logs desde una API o base de datos
    setTimeout(() => {
      this.auditLogs = this.generateDummyLogs(20); // Genera 20 entradas de log de ejemplo
      // Ordenar por fecha, los más recientes primero
      this.auditLogs.sort((a, b) => b.timestamp.getTime() - a.timestamp.getTime());
    }, 500);
  }

  // --- Métodos de Simulación de Datos ---
  private generateDummyLogs(count: number): AuditLog[] {
    const logs: AuditLog[] = [];
    const users = ['admin@example.com', 'john.doe@example.com', 'jane.smith@example.com'];
    const actions = ['LOGIN', 'PRODUCT_CREATED', 'PRODUCT_UPDATED', 'PRODUCT_DELETED', 'CATEGORY_CREATED', 'CATEGORY_UPDATED', 'ORDER_STATUS_CHANGED'];
    const products = ['Laptop X', 'Smartphone Y', 'Smart TV Z', 'Teclado Mecánico', 'Ratón Gaming'];
    const categories = ['Electrónica', 'Hogar', 'Deportes'];

    for (let i = 1; i <= count; i++) {
      const user = users[Math.floor(Math.random() * users.length)];
      const action = actions[Math.floor(Math.random() * actions.length)];
      let details = '';

      switch (action) {
        case 'LOGIN':
          details = 'Inicio de sesión exitoso.';
          break;
        case 'PRODUCT_CREATED':
          details = `Producto "${products[Math.floor(Math.random() * products.length)]}" creado.`;
          break;
        case 'PRODUCT_UPDATED':
          details = `Producto "${products[Math.floor(Math.random() * products.length)]}" actualizado (precio/stock).`;
          break;
        case 'PRODUCT_DELETED':
          details = `Producto "${products[Math.floor(Math.random() * products.length)]}" eliminado.`;
          break;
        case 'CATEGORY_CREATED':
          details = `Categoría "${categories[Math.floor(Math.random() * categories.length)]}" creada.`;
          break;
        case 'CATEGORY_UPDATED':
          details = `Categoría "${categories[Math.floor(Math.random() * categories.length)]}" actualizada.`;
          break;
        case 'ORDER_STATUS_CHANGED':
          details = `Estado del pedido #${(1000 + i).toString().padStart(4, '0')} cambiado a 'Enviado'.`;
          break;
        default:
          details = 'Acción desconocida.';
      }

      // Generar una fecha aleatoria dentro de los últimos 30 días
      const randomDaysAgo = Math.floor(Math.random() * 30);
      const randomHoursAgo = Math.floor(Math.random() * 24);
      const randomMinutesAgo = Math.floor(Math.random() * 60);
      const timestamp = new Date();
      timestamp.setDate(timestamp.getDate() - randomDaysAgo);
      timestamp.setHours(timestamp.getHours() - randomHoursAgo);
      timestamp.setMinutes(timestamp.getMinutes() - randomMinutesAgo);


      logs.push({
        id: i,
        user: user,
        action: action,
        details: details,
        timestamp: timestamp
      });
    }
    return logs;
  }
}
