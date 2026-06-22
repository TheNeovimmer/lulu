# LUMA — UML Architecture

> Application MVC PHP native. Routage maison, Repository, Middleware RBAC, templates layouts.

---

## 1. Diagrammes de Cas d'Utilisation

### 1.1 Visiteur (non connecté)

```mermaid
graph LR
  V((Visiteur))
  V --> A[Consulter page d'accueil]
  V --> B[Lire articles]
  V --> C[Consulter ressources]
  V --> D[Voir annuaire experts]
  V --> E[Consulter FAQ]
  V --> F[S'abonner newsletter]
  V --> G[Créer un compte]
  V --> H[Se connecter]

  G -.->|«include»| G1[Valider email]
  G -.->|«include»| G2[Choisir rôle]
  H -.->|«include»| H1[Valider identifiants]
  H -.->|«extend»| H2[Réinitialiser mot de passe]
```

### 1.2 Maman

```mermaid
graph LR
  M((Maman))
  M --> A[Gérer profil grossesse]
  M --> B[Suivre bébé]
  M --> C[Prendre rendez-vous]
  M --> D[Consulter messagerie]
  M --> E[Créer ticket support]
  M --> F[Donner témoignage]

  A -.->|«include»| A1[Renseigner date accouchement]
  A -.->|«include»| A2[Ajouter info médicale]

  B -.->|«include»| B1[Ajouter mesure croissance]
  B -.->|«include»| B2[Enregistrer vaccin]
  B -.->|«extend»| B3[Écrire souvenir]

  C -.->|«include»| C1[Choisir expert]
  C -.->|«include»| C2[Choisir créneau]
  C -.->|«extend»| C3[Annuler rendez-vous]

  E -.->|«include»| E1[Choisir catégorie]
  E -.->|«extend»| E2[Joindre fichier]
```

### 1.3 Expert

```mermaid
graph LR
  E((Expert))
  E --> A[Gérer articles]
  E --> B[Publier ressources]
  E --> C[Gérer rendez-vous]
  E --> D[Répondre communauté]
  E --> F[Gérer tickets assignés]
  E --> G[Consulter messagerie]

  A -.->|«include»| A1[Rédiger contenu]
  A -.->|«include»| A2[Choisir catégorie]
  A -.->|«extend»| A3[Programmer publication]

  C -.->|«include»| C1[Consulter agenda]
  C -.->|«include»| C2[Confirmer rendez-vous]
  C -.->|«extend»| C3[Proposer nouveau créneau]
```

### 1.4 CTT (Centre d'appel)

```mermaid
graph LR
  C((CTT))
  C --> A[Gérer tickets support]
  C --> B[Gérer FAQ]
  C --> C1[Consulter rapports]

  A -.->|«include»| A1[Assigner ticket]
  A -.->|«include»| A2[Clôturer ticket]
  A -.->|«extend»| A3[Escalader ticket]

  B -.->|«include»| B1[Ajouter question]
  B -.->|«include»| B2[Modifier réponse]
```

### 1.5 Administrateur

```mermaid
graph LR
  A((Admin))
  A --> A1[Gérer utilisateurs]
  A --> A2[Gérer articles & catégories]
  A --> A3[Modérer communauté]
  A --> A4[Gérer témoignages]
  A --> A5[Gérer tickets]
  A --> A6[Paramètres site]

  A1 -.->|«include»| A1a[Consulter profil]
  A1 -.->|«include»| A1b[Modifier rôle]
  A1 -.->|«extend»| A1c[Suspendre compte]

  A3 -.->|«include»| A3a[Approuver contenu]
  A3 -.->|«include»| A3b[Masquer publication]
  A3 -.->|«extend»| A3c[Bannir utilisateur]

  A4 -.->|«include»| A4a[Approuver]
  A4 -.->|«include»| A4b[Rejeter]
```

### 1.6 Général — Hiérarchie des acteurs

```mermaid
graph TB
  V((Visiteur))
  M((Membre))
  MA((Maman))
  E((Expert))
  CTT((CTT))
  A((Admin))

  M --|> V
  MA --|> M
  E --|> M
  CTT --|> M
  A --|> M

  V --> Pub[Consulter contenu public]
  V --> Auth[S'authentifier]

  M --> Profil[Gérer profil]
  M --> Notif[Notifications]

  MA --> M1[Suivi maternité & rendez-vous]
  E --> E1[Contenus éditoriaux & agenda]
  CTT --> C1[Support client & FAQ]
  A --> AD[Administration & modération]
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
