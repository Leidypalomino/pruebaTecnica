import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AdminDashboard } from './admin-dashboard/admin-dashboard'; // Asegúrate del nombre correcto
import { AdminProducts } from './products/admin-products/admin-products'; // El que tenías como ProductCrud
import { CategoryManagement } from './category-management/category-management';
import { AdminHome } from './admin-home/admin-home'; // Importa AdminHome para la ruta del dashboard
import { AuditLogs } from './audit-logs/audit-logs'; // ¡Importa AuditLogsComponent!

const routes: Routes = [
  {
    path: '', // La ruta padre /admin
    component: AdminDashboard, // Este es el componente que contendrá el sidebar y el router-outlet
    children: [
      {
        path: '', // Esto cargará AdminHome cuando la ruta sea exactamente /admin
        component: AdminHome // Usamos AdminHome aquí como el contenido del dashboard
      },
      {
        path: 'products',
        component: AdminProducts // Aquí el componente de gestión de productos
      },
      {
        path: 'categories',
        component: CategoryManagement // Aquí el componente de gestión de categorías
      },
      {
        path: 'logs', // Ruta para los logs de auditoría
        component: AuditLogs
      }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminRoutingModule { }