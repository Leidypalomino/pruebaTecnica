import { Injectable, Renderer2, RendererFactory2 } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { DOCUMENT } from '@angular/common';
import { Inject } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class Theme {
  private _isDarkMode = new BehaviorSubject<boolean>(false);
  public readonly darkMode$: Observable<boolean> = this._isDarkMode.asObservable();

  private renderer: Renderer2;

  constructor(
    rendererFactory: RendererFactory2,
    @Inject(DOCUMENT) private document: Document // Inyecta el objeto Document
  ) {
    this.renderer = rendererFactory.createRenderer(null, null);
    // Inicializa el tema al cargar la aplicación
    this.initializeTheme();
  }

  private initializeTheme(): void {
    // 1. Intenta cargar el tema desde localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      this._isDarkMode.next(savedTheme === 'dark');
    } else {
      // 2. Si no hay nada en localStorage, detecta la preferencia del sistema
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      this._isDarkMode.next(prefersDark);
    }
    // Aplica el tema inicial al HTML
    this.applyThemeClass(this._isDarkMode.getValue());
  }

  toggleTheme(): void {
    const newMode = !this._isDarkMode.getValue();
    this._isDarkMode.next(newMode);
    this.applyThemeClass(newMode); // Aplica la clase inmediatamente
    localStorage.setItem('theme', newMode ? 'dark' : 'light'); // Guarda la preferencia
  }

  private applyThemeClass(isDark: boolean): void {
    if (isDark) {
      this.renderer.addClass(this.document.documentElement, 'dark'); // Aplica al <html>
    } else {
      this.renderer.removeClass(this.document.documentElement, 'dark'); // Remueve del <html>
    }
  }
}