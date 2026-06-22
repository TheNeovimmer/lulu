# LUMA — UML Architecture

> Application MVC PHP native. Routage maison, Repository, Middleware RBAC, templates layouts.

---

## 1. Diagrammes de Cas d'Utilisation

### 1.1 Par Rôle

```mermaid
graph TB
  subgraph VISITEUR["👤 Visiteur (non connecté)"]
    V1[Consulter page d'accueil]
    V2[Lire articles blog]
    V3[Consulter ressources]
    V4[Parcourir communauté]
    V5[Voir annuaire experts]
    V6[Page contact]
    V7[Consulter FAQ]
    V8[S'abonner newsletter]
    V9[Créer un compte]
    V10[Se connecter]
  end
```

```mermaid
graph TB
  subgraph MAMAN["🤰 Maman"]
    M1[Voir tableau de bord]
    M2[Gérer profil grossesse]
    M3[Suivre bébé / croissance]
    M4[Suivi vaccins]
    M5[Écrire souvenirs/étapes]
    M6[Prendre rendez-vous]
    M7[Messagerie expert]
    M8[Créer ticket support]
    M9[Donner témoignage]
    M10[Se déconnecter]
  end
```

```mermaid
graph TB
  subgraph EXPERT["👨‍⚕️ Expert"]
    E1[Voir tableau de bord]
    E2[Gérer articles / publication]
    E3[Publier ressources]
    E4[Répondre questions communauté]
    E5[Gérer tickets assignés]
    E6[Gérer disponibilités]
    E7[Confirmer / annuler RDV]
    E8[Messagerie mamans]
    E9[Se déconnecter]
  end
```

```mermaid
graph TB
  subgraph CTT["📞 CTT (Centre d'appel)"]
    C1[Voir tableau de bord]
    C2[Gérer tickets support]
    C3[Gérer FAQ]
    C4[Consulter rapports / historique]
    C5[Se déconnecter]
  end
```

```mermaid
graph TB
  subgraph ADMIN["🔧 Administrateur"]
    A1[Voir tableau de bord]
    A2[Gérer utilisateurs & rôles]
    A3[Gérer articles & catégories]
    A4[Gérer ressources]
    A5[Modérer communauté]
    A6[Gérer témoignages]
    A7[Gérer tickets]
    A8[Gérer contacts & newsletter]
    A9[Gérer FAQ]
    A10[Paramètres site]
    A11[Se déconnecter]
  end
```

### 1.2 Général (Tous rôles)

```mermaid
graph TB
  subgraph PLATEFORME["Plateforme LUMA"]
    direction TB

    subgraph Public["Publique"]
      V1[Consulter accueil]
      V2[Lire articles / ressources]
      V3[Voir FAQ / Contact]
      V4[Annuaire experts]
      V5[S'abonner newsletter]
      V6[Créer compte / Connexion]
    end

    subgraph Maman["Espace Maman"]
      M1[Tableau de bord]
      M2[Suivi grossesse / bébé]
      M3[Rendez-vous & messagerie]
      M4[Tickets & témoignages]
    end

    subgraph Expert["Espace Expert"]
      E1[Tableau de bord]
      E2[Articles & ressources]
      E3[Agenda & messagerie]
      E4[Tickets & communauté]
    end

    subgraph CTT["Espace CTT"]
      C1[Tableau de bord]
      C2[Tickets & FAQ]
      C3[Rapports]
    end

    subgraph Admin["Espace Admin"]
      A1[Tableau de bord]
      A2[Gestion complète]
      A3[Paramètres & modération]
    end
  end

  V1 --> Public
  V2 --> Public

  M1 --> Maman
  M2 --> Maman

  E1 --> Expert
  E2 --> Expert

  C1 --> CTT

  A1 --> Admin
  A2 --> Admin
```

---

## 2. Diagramme de Classes

```mermaid
classDiagram

  class Router {
    +get(path, handler)
    +post(path, handler)
    +dispatch(method, url)
  }

  class Database {
    +getInstance()
    +getConnection()
  }

  class View {
    +render(view, data, layout)
  }

  class Session {
    +get(key)
    +set(key, value)
    +destroy()
    +csrf_token()
  }

  class Request {
    +method()
    +post(key)
    +get(key)
    +redirect(url)
  }

  class Validator {
    +required(field)
    +email(field)
    +passes()
    +errors()
  }

  class Model {
    +find(id)
    +all(criteria)
    +create(data)
    +update(id, data)
    +delete(id)
  }

  class AuthMiddleware {
    +check()
  }

  class GuestMiddleware {
    +check()
  }

  class RoleMiddleware {
    +check(roleSlug)
  }

  class AdminMiddleware {
    +check()
  }

  Controller <|-- AuthController
  Controller <|-- PageController
  Controller <|-- ArticleController
  Controller <|-- CommunityController
  Controller <|-- DashboardController
  Controller <|-- ExpertController
  Controller <|-- CttController
  Controller <|-- TicketController
  Controller <|-- AdminController
  Controller <|-- AdminUserController
  Controller <|-- AdminArticleController
  Controller <|-- AdminTicketController

  note for Controller "22 controlleurs au total\ntous héritent de Controller"

  BaseRepository <|-- UserRepository
  BaseRepository <|-- ArticleRepository
  BaseRepository <|-- TicketRepository
  BaseRepository <|-- AppointmentRepository
  BaseRepository <|-- MotherRepository
  BaseRepository <|-- BabyRepository
  BaseRepository <|-- NotificationRepository

  note for BaseRepository "19 repositories au total\n1 par table"

  class AuthService {
    +authenticate(email, password)
    +register(name, email, password)
  }

  class EmailService {
    +send(to, subject, body)
  }

  class TicketService {
    +createTicket(userId, subject)
    +assign(ticketId, expertId)
    +close(ticketId)
  }

  class AppointmentService {
    +book(motherId, expertId, date)
    +confirm(appointmentId)
    +cancel(appointmentId)
  }

  class NotificationService {
    +create(userId, type, title)
  }

  class CommunityService {
    +createPost(userId, title)
    +comment(postId, userId)
  }

  AuthController --> AuthService
  AuthController --> Validator

  DashboardController --> UserRepository
  DashboardController --> AppointmentRepository

  ExpertController --> AppointmentRepository
  ExpertController --> AvailabilityRepository

  CttController --> TicketRepository

  AppointmentService --> EmailService
  AppointmentService --> NotificationService
  TicketService --> EmailService
  TicketService --> NotificationService

  AuthService --> UserRepository
  EmailService --> UserRepository
  AppointmentService --> AppointmentRepository
  TicketService --> TicketRepository

  View <-- Controller
  Session <-- Controller
  Session <-- AuthMiddleware
  Request <-- Controller
  Request <-- AuthMiddleware
  Database <-- BaseRepository
  Database <-- Model

  class ArticleStatus {
    <<enum>>
    DRAFT, PUBLISHED
  }

  class UserStatus {
    <<enum>>
    ACTIVE, SUSPENDED, BANNED
  }

  class AppointmentStatus {
    <<enum>>
    PENDING, CONFIRMED, CANCELLED
  }

  class TicketStatus {
    <<enum>>
    OPEN, IN_PROGRESS, CLOSED
  }

  class CommentStatus {
    <<enum>>
    PENDING, APPROVED, REJECTED
  }

  note for ArticleStatus "13 enums au total"
```

---

## 3. Diagrammes de Séquence

### 3.1 Authentification

```mermaid
sequenceDiagram
  actor U as Utilisateur
  participant C as AuthController
  participant Val as Validator
  participant Svc as AuthService
  participant R as UserRepository
  participant DB as Database
  participant S as Session

  U->>C: POST /auth/login (email + password)
  C->>S: validate_csrf(token)
  C->>Val: required('email'), required('password')
  Val-->>C: passes

  alt validation échoue
    C->>U: erreur + formulaire
  else
    C->>Svc: authenticate(email, password)
    Svc->>R: findByEmail(email)
    R->>DB: SELECT * FROM users WHERE email = ?
    DB-->>R: user
    R-->>Svc: user|null

    alt user trouvé + password OK
      Svc-->>C: user
      C->>Svc: login(user)
      Svc->>S: set user_id, name, role
      C->>U: redirect /dashboard
    else
      C->>U: erreur + formulaire
    end
  end
```

### 3.2 Prise de Rendez-vous

```mermaid
sequenceDiagram
  actor M as Maman
  actor E as Expert
  participant DC as DashboardController
  participant Svc as AppointmentService
  participant N as NotificationService
  participant EM as EmailService
  participant DB as Database

  M->>DC: POST /dashboard/rendez-vous/book
  DC->>Svc: book(motherId, expertId, date, type)
  Svc->>DB: INSERT INTO appointments
  DB-->>Svc: id

  Svc->>N: create(expertId, 'appointment')
  Svc->>EM: sendAppointmentBooked(expertId, maman, date)
  DC->>M: confirmation

  E->>DC: POST /expert/appointments/confirm/{id}
  DC->>Svc: confirm(id)
  Svc->>DB: UPDATE appointments SET status = 'confirmed'
  Svc->>N: create(motherId, 'confirmed')
  Svc->>EM: sendAppointmentUpdated(motherId, 'confirmed', date)
  DC->>E: agenda mis à jour
```

### 3.3 Création d'Article

```mermaid
sequenceDiagram
  actor E as Expert
  participant C as ExpertController
  participant S as Session
  participant DB as Database

  E->>C: GET /expert/articles
  C->>DB: SELECT articles WHERE user_id = ?
  DB-->>C: articles
  C->>E: liste articles

  E->>C: POST /expert/articles/create
  C->>S: validate_csrf(token)
  C->>DB: INSERT INTO articles (title, slug, content, ...)
  DB-->>C: id
  C->>E: redirect /expert/articles
```

---

## 4. Architecture Package

```mermaid
graph TB
  subgraph public["public/"]
    index["index.php (entry point)"]
    htaccess[".htaccess"]
    assets["assets/ (css, js, images)"]
  end

  subgraph app["app/"]
    autoload["autoload.php (PSR-4)"]
    core["Core (Router, Database, View, Session, Request, Validator, Model)"]
    controllers["Controllers (22)"]
    repos["Repositories (19)"]
    services["Services (8)"]
    middleware["Middleware (5)"]
    enums["Enums (13)"]
    helpers["Helpers (Avatar)"]
  end

  subgraph views["views/"]
    layouts["layouts/ (front, admin, expert, maman, ctt)"]
    pages["pages/"]
    admin["admin/"]
    dashboard["dashboard/"]
    expert["expert/"]
    ctt["ctt/"]
  end

  subgraph root["racine"]
    env["env.php"]
    sql["database.sql (31 tables)"]
    routes["routes.php (170+ routes)"]
  end

  index --> env
  index --> autoload
  index --> routes
  controllers --> core
  controllers --> services
  services --> repos
  repos --> core
  middleware --> core
  views --> controllers
```

---

## 5. Base de Données (31 tables)

```mermaid
erDiagram
  roles ||--o{ users : has
  users ||--o{ mothers : "1:1"
  users ||--o{ articles : authors
  users ||--o{ tickets : opens
  users ||--o{ appointments : "books as expert"
  users ||--o{ notifications : receives
  mothers ||--o{ pregnancies : has
  mothers ||--o{ babies : has
  mothers ||--o{ appointments : "books as mother"
  babies ||--o{ growth_records : tracked
  babies ||--o{ vaccinations : scheduled
  babies ||--o{ baby_memories : memories
  categories ||--o{ articles : categorized
  articles ||--o{ comments : has
  community_posts ||--o{ community_comments : has
  tickets ||--o{ ticket_messages : has
  users ||--o{ expert_availability : schedules
```

---

## 6. Flux de Requête MVC

```mermaid
sequenceDiagram
  participant B as Navigateur
  participant A as Apache
  participant I as index.php
  participant R as Router
  participant C as Controller
  participant V as View
  participant DB as Database

  B->>A: GET /articles/mon-slug
  alt fichier statique
    A->>B: css/js/image
  else route MVC
    A->>I: rewrite → index.php?url=articles/mon-slug
    I->>R: dispatch('GET', 'articles/mon-slug')
    R->>C: ArticleController->show('mon-slug')
    C->>DB: SELECT * FROM articles WHERE slug = ?
    DB-->>C: article
    C->>V: render('articles/show', {article})
    V-->>B: HTML
  end
```
