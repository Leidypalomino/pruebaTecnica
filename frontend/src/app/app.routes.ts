import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { Home } from './home/home';
import { Catalog } from './catalog/catalog';
import { ProductDetail } from './product-detail/product-detail';

export const routes: Routes = [
  { path: '', component: Home }, // Home como ruta raíz
  { path: 'catalog', component: Catalog },
  { path: 'products/detail/:id', component: ProductDetail },
  {
    path: 'auth',
    loadChildren: () => import('./auth/auth-module').then(m => m.AuthModule)
  },
  {
    path: 'admin', // Ruta para el panel de administración
    loadChildren: () => import('./admin/admin-module').then(m => m.AdminModule)
    // Aquí podrías añadir un canActivate guard para proteger esta ruta
    // canActivate: [AuthGuard]
  },
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })],
  exports: [RouterModule]
})
export class AppRoutingModule { }