# Sistema MVC - Laravel + Angular

Sistema web implementado con arquitectura MVC utilizando **Laravel** en el backend y **Angular** en el frontend, aplicando patrones de diseño modernos para garantizar escalabilidad, mantenibilidad y testabilidad.

## 📋 Tabla de Contenidos

- [Arquitectura del Sistema](#arquitectura-del-sistema)
- [Decisiones Técnicas](#decisiones-técnicas-justificadas)
- [Patrones de Diseño](#patrones-de-diseño-implementados)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Guía de Instalación](#guía-de-instalación)
- [Comandos Principales](#glosario-técnico-de-comandos)
- [Troubleshooting](#troubleshooting-común)
- [Pendientes](#cosas-faltantes)

## Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              ARQUITECTURA MVC                                  │
│                          Laravel Backend + Angular Frontend                    │
└─────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────┐         ┌──────────────────────────────────┐
│          FRONTEND - ANGULAR      │         │        BACKEND - LARAVEL         │
│                                  │         │                                  │
│  ┌─────────────────────────────┐ │         │  ┌─────────────────────────────┐ │
│  │        COMPONENTS           │ │         │  │        CONTROLLERS          │ │
│  │  • User Interface           │ │         │  │  • Request Handling         │ │
│  │  • Event Handling           │ │   HTTP  │  │  • Input Validation         │ │
│  │  • Template Binding         │ │<------->│  │  • Response Formatting      │ │
│  │  • Data Presentation        │ │  JSON   │  │  • API Endpoints            │ │
│  └─────────────────────────────┘ │         │  └─────────────────────────────┘ │
│              │                   │         │              │                   │
│              ▼                   │         │              ▼                   │
│  ┌─────────────────────────────┐ │         │  ┌─────────────────────────────┐ │
│  │         SERVICES            │ │         │  │      SERVICE LAYER          │ │
│  │  • HTTP Client              │ │         │  │  • Business Logic           │ │
│  │  • Business Logic           │ │         │  │  • Data Processing          │ │
│  │  • Data Management          │ │         │  │  • Complex Operations       │ │
│  │  • State Management         │ │         │  │  • External API Calls       │ │
│  └─────────────────────────────┘ │         │  └─────────────────────────────┘ │
│              │                   │         │              │                   │
│              ▼                   │         │              ▼                   │
│  ┌─────────────────────────────┐ │         │  ┌─────────────────────────────┐ │
│  │    DEPENDENCY INJECTION     │ │         │  │    REPOSITORY PATTERN       │ │
│  │  • Service Provider         │ │         │  │  • Data Abstraction         │ │
│  │  • Dependency Resolution    │ │         │  │  • Query Methods            │ │
│  │  • Singleton Management     │ │         │  │  • Unit Testing Support     │ │
│  │  • Constructor Injection    │ │         │  │  • Cache Layer              │ │
│  └─────────────────────────────┘ │         │  └─────────────────────────────┘ │
│                                  │         │              │                   │
│  ┌─────────────────────────────┐ │         │              ▼                   │
│  │      ANGULAR ROUTER         │ │         │  ┌─────────────────────────────┐ │
│  │  • Navigation               │ │         │  │    MODELS (ELOQUENT ORM)    │ │
│  │  • Route Guards             │ │         │  │  • Data Structure           │ │
│  │  • Lazy Loading             │ │         │  │  • Relationships            │ │
│  │  • Route Parameters         │ │         │  │  • Validation Rules         │ │
│  └─────────────────────────────┘ │         │  │  • Mutators/Accessors       │ │
│                                  │         │  └─────────────────────────────┘ │
│  ┌─────────────────────────────┐ │         │              │                   │
│  │   HTTP COMMUNICATION        │ │         │              ▼                   │
│  │  • HTTP Interceptors        │ │         │  ┌─────────────────────────────┐ │
│  │  • Error Handling           │ │         │  │     LARAVEL FEATURES        │ │
│  │  • Authentication           │ │         │  │  • Middleware               │ │
│  │  • Request/Response         │ │         │  │  • Authentication           │ │
│  └─────────────────────────────┘ │         │  │  • Validation               │ │
└──────────────────────────────────┘         │  │  • Queue Jobs               │ │
                                             │  │  • Event System             │ │
                                             │  │  • Caching                  │ │
                                             │  └─────────────────────────────┘ │
                                             └──────────────────────────────────┘
                                                            │
                                                            ▼
                                             ┌──────────────────────────────────┐
                                             │        DATABASE LAYER            │
                                             │                                  │
                                             │  ┌─────────────────────────────┐ │
                                             │  │    MySQL / PostgreSQL       │ │
                                             │  │  • Tables & Relationships   │ │
                                             │  │  • Indexes & Constraints    │ │
                                             │  │  • Stored Procedures        │ │
                                             │  │  • Views & Triggers         │ │
                                             │  └─────────────────────────────┘ │
                                             │                                  │
                                             │  ┌─────────────────────────────┐ │
                                             │  │   MIGRATIONS & SEEDERS      │ │
                                             │  │  • Schema Management        │ │
                                             │  │  • Database Versioning      │ │
                                             │  │  • Test Data Population     │ │
                                             │  └─────────────────────────────┘ │
                                             └──────────────────────────────────┘
```

## Decisiones Técnicas Justificadas

Para este proyecto, se ha seleccionado un stack de tecnologías robusto y escalable priorizando la seguridad, rendimiento y la facilidad de desarrollo.

### 🔧 Backend: Laravel (PHP Framework)

Se optó por Laravel como el framework por:

- **🛡️ Sanitización**: Ofrece herramientas automáticas para sanitizar los datos de entrada
- **💉 Protección contra Inyección SQL**: Sus métodos de base de datos protegen contra ataques de inyección SQL
- **🔒 Middleware Robusto**: Permite capas de seguridad (autenticación, autorización, CORS, etc.), logueo y otras lógicas antes de que las peticiones lleguen a los controladores
- **🔐 Manejo seguro de contraseñas**: Laravel facilita la gestión segura mediante el uso de funciones de hash modernas que aseguran el almacenamiento protegido y resistente a ataques

### ⚡ Base de datos Cache/NoSQL: Redis

Redis fue seleccionado estratégicamente por su versatilidad y rendimiento:

- **🚀 Velocidad Extrema (In-Memory Data Store)**: Ofrece una velocidad de lectura y escritura alta, para aplicaciones de alto rendimiento
- **💾 Cache y persistencia**: Puede funcionar tanto como un sistema de caché para acelerar las peticiones de datos frecuentes
- **⚡ Optimización de peticiones**: Su uso como cache permite servir rápidamente datos de peticiones muy comunes

### 🐘 Base de Datos Relacional: PostgreSQL

La elección como la base de datos relacional principal es por sus capacidades avanzadas y su robustez:

- **🎯 Rendimiento y fiabilidad**: Tiene un excelente rendimiento, ofrece una alta fiabilidad y consistencia transaccional
- **📊 Flexibilidad de indexación**: Proporciona una amplia variedad de tipos de indexación, lo que permite optimizar las consultas de manera muy granular para diferentes patrones de acceso de datos
- **🔧 Extensibilidad y plugins externos**: Su arquitectura permite la adición de funcionalidades a través de plugins externos
- **📋 Soporte JSONB**: Facilita el almacenamiento y consulta de datos semi-estructurados, ofreciendo flexibilidad en el esquema

### 🅰️ Frontend: Angular (Framework JavaScript)

Para el desarrollo del frontend, se decidió por Angular:

- **🧩 Desarrollo orientado a componentes**: Angular promueve una arquitectura basada en componentes, esto facilita la construcción de interfaces de usuarios modulares
- **💉 Inyección de dependencias robusta**: Permite que las clases (componentes, servicios, directivas) soliciten sus dependencias en el constructor, en lugar de crearlas
- **🛠️ Ecosistema completo y opinado**: Proporciona herramientas para el enrutamiento, la gestión de formularios, la comunicación HTTP, el manejo del estado y más
- **📝 Soporte de TypeScript**: Ayuda a detectar errores en tiempo de desarrollo en lugar de en producción, mejora la legibilidad del código
- **⚡ Rendimiento**: Ofrece herramientas para optimizar el rendimiento de la aplicación, como la carga diferida de módulos, la compilación

## Patrones de Diseño Implementados

### 🏗️ **MVC (Model-View-Controller)**
- **Model**: Eloquent ORM para manejo de datos
- **View**: Angular Components para presentación
- **Controller**: Laravel Controllers para lógica de control

### 🗃️ **Repository Pattern**
```php
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}

class UserRepository implements UserRepositoryInterface
{
    // Implementación de métodos de acceso a datos
}
```

### 🛠️ **Service Layer Pattern**
```php
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EmailService $emailService
    ) {}
    
    public function createUser(array $userData): User
    {
        // Lógica de negocio compleja
        $user = $this->userRepository->create($userData);
        $this->emailService->sendWelcomeEmail($user);
        return $user;
    }
}
```

### 💉 **Dependency Injection (Angular)**
```typescript
@Injectable({
  providedIn: 'root'
})
export class UserService {
  constructor(
    private http: HttpClient,
    private authService: AuthService
  ) {}
  
  getUsers(): Observable<User[]> {
    return this.http.get<User[]>('/api/users');
  }
}

@Component({
  selector: 'app-user-list',
  templateUrl: './user-list.component.html'
})
export class UserListComponent {
  constructor(private userService: UserService) {}
}
```

## Tecnologías Utilizadas

### Frontend
- **Angular 17+**
- **TypeScript**
- **RxJS** para programación reactiva
- **Angular Material** para UI components
- **Angular CLI** para desarrollo

### Backend
- **Laravel 10+**
- **PHP 8.2+**
- **Eloquent ORM**
- **Laravel Sanctum** para autenticación
- **Laravel Telescope** para debugging
- **JWT Authentication** (tymon/jwt-auth)
- **Swagger Documentation** (darkaonline/l5-swagger)

### Base de Datos
- **PostgreSQL** como base de datos principal
- **Redis** para cache y sessions
- **Database Migrations** para versionado

### DevOps
- **Docker & Docker Compose** para containerización
- **Nginx** como servidor web
- **PHPUnit** para testing

## Guía de Instalación

### 📋 Prerequisitos

- Docker y Docker Compose instalados
- Node.js (versión LTS) y npm
- Git

### 🔧 Instalación del Backend (Laravel)

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repositorio>
   cd proyecto
   ```

2. **Configurar el entorno**
   ```bash
   # Crear directorios para arquitectura limpia
   mkdir -p app/Services app/Repositories app/Contracts
   
   # Crear archivo de configuración
   cp .env.example .env
   
   # Configurar .gitignore si es necesario
   touch .gitignore
   ```

3. **Levantar servicios con Docker**
   ```bash
   # Construir y levantar contenedores
   docker-compose up -d --build
   ```

4. **Instalar dependencias PHP**
   ```bash
   # Instalar dependencias base
   docker-compose exec app composer install
   
   # Instalar paquetes adicionales
   docker-compose exec app composer require tymon/jwt-auth
   docker-compose exec app composer require darkaonline/l5-swagger
   ```

5. **Configurar Laravel**
   ```bash
   # Generar clave de aplicación
   docker-compose exec app php artisan key:generate
   
   # Generar clave JWT
   docker-compose exec app php artisan jwt:secret
   
   # Publicar configuraciones de paquetes
   docker-compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
   docker-compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"
   ```

6. **Configurar base de datos**
   ```bash
   # Ejecutar migraciones
   docker-compose exec app php artisan migrate
   
   # Ejecutar seeders (opcional)
   docker-compose exec app php artisan db:seed
   ```

7. **Generar documentación**
   ```bash
   docker-compose exec app php artisan l5-swagger:generate
   ```

### 🅰️ Instalación del Frontend (Angular)

1. **Instalar Node.js**
   - Descargar desde [nodejs.org](https://nodejs.org/es/download/)
   - Verificar instalación:
     ```bash
     node -v
     npm -v
     ```

2. **Instalar Angular CLI**
   ```bash
   npm install -g @angular/cli
   ng version
   ```

3. **Instalar dependencias del proyecto**
   ```bash
   cd frontend
   npm install
   ```

4. **Ejecutar el proyecto Angular**
   ```bash
   # Iniciar servidor de desarrollo
   ng serve
   
   # O abrir automáticamente en el navegador
   ng serve --open
   ```

## Glosario Técnico de Comandos

### 🐳 Comandos de Docker

| Comando | Descripción |
|---------|-------------|
| `docker-compose up -d --build` | Construye y levanta todos los servicios en segundo plano |
| `docker-compose exec app <comando>` | Ejecuta un comando dentro del contenedor 'app' |
| `docker-compose down` | Detiene y elimina todos los contenedores |

### 📦 Comandos de Composer

| Comando | Descripción |
|---------|-------------|
| `composer install` | Instala todas las dependencias del proyecto |
| `composer require <paquete>` | Añade una nueva dependencia al proyecto |
| `composer update` | Actualiza todas las dependencias |

### ⚔️ Comandos de Laravel Artisan

#### Generación de Código
| Comando | Descripción |
|---------|-------------|
| `php artisan make:model <Nombre>` | Crea un nuevo modelo |
| `php artisan make:controller <NombreController>` | Crea un nuevo controlador |
| `php artisan make:migration <nombre>` | Crea una nueva migración |
| `php artisan make:middleware <NombreMiddleware>` | Crea un nuevo middleware |

#### Configuración
| Comando | Descripción |
|---------|-------------|
| `php artisan key:generate` | Genera la clave de aplicación |
| `php artisan jwt:secret` | Genera la clave secreta JWT |
| `php artisan vendor:publish --provider="..."` | Publica archivos de configuración |

#### Base de Datos
| Comando | Descripción |
|---------|-------------|
| `php artisan migrate` | Ejecuta las migraciones pendientes |
| `php artisan db:seed` | Ejecuta los seeders |
| `php artisan migrate:fresh --seed` | Recrea la BD y ejecuta seeders |

#### Utilidades
| Comando | Descripción |
|---------|-------------|
| `php artisan l5-swagger:generate` | Genera documentación Swagger |
| `php artisan test` | Ejecuta las pruebas |
| `php artisan storage:link` | Crea enlace simbólico para storage |

### 🅰️ Comandos de Angular

| Comando | Descripción |
|---------|-------------|
| `ng serve` | Inicia el servidor de desarrollo |
| `ng build` | Construye la aplicación para producción |
| `ng test` | Ejecuta las pruebas unitarias |
| `ng generate component <nombre>` | Genera un nuevo componente |
| `ng generate service <nombre>` | Genera un nuevo servicio |

## Troubleshooting Común

### 🔧 Problemas Frecuentes

**Generar la documentación**
```bash
docker compose exec app php artisan l5-swagger:generate
```

**Si las imágenes no cargan**
```bash
docker-compose exec app php artisan storage:link
```

**Limpiar caché de Laravel**
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

**Reinstalar dependencias de Node**
```bash
rm -rf node_modules package-lock.json
npm install
```

**Permisos en Linux/Mac**
```bash
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
```

## Cosas Faltantes

### 🚧 Pendientes de Implementación

- [ ] **Testing**

- [ ] **Optimización**

- [ ] **Configuración de Producción**

- [ ] **SEO**

- [ ] **Funcionalidades Avanzadas**
