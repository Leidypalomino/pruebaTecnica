import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})

export class Auth {

  constructor(
    private http: HttpClient
  ) { }

  refreshToken(): Observable<any> {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'accept': '*/*'
    });
    return this.http.post<any>('http://localhost:8080/api/auth/refresh', {}, { headers });
  }
}
