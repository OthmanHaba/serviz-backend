<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Serviz Backend Database Schema (DBML)

```dbml
// Users Table
Table users {
  id integer [primary key]
  name string
  email string [unique]
  email_verified_at timestamp [null]
  remember_token string [null]
  phone string
  password string
  vehicle_info json [null]
  role string [default: 'user']
  is_active boolean [default: true]
  is_banned boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

// Password Reset Tokens Table
Table password_reset_tokens {
  email string [primary key]
  token string
  created_at timestamp [null]
}

// Sessions Table
Table sessions {
  id string [primary key]
  user_id integer [ref: > users.id, null]
  ip_address string [null]
  user_agent text [null]
  payload longtext
  last_activity integer
}

// Service Types Table
Table servic_types {
  id integer [primary key]
  name string
  description string
  image string
  created_at timestamp
  updated_at timestamp
}

// Provider Services Table
Table provider_services {
  id integer [primary key]
  user_id integer [ref: > users.id]
  servic_type_id integer [ref: > servic_types.id]
  price decimal(10,2)
  created_at timestamp
  updated_at timestamp
}

// Locations Table
Table locations {
  id integer [primary key]
  user_id integer [ref: > users.id]
  latitude string
  longitude string
  created_at timestamp
  updated_at timestamp
}

// Active Requests Table
Table active_request {
  id integer [primary key]
  user_id integer [ref: > users.id]
  provider_id integer [ref: > users.id]
  service_id integer [ref: > servic_types.id]
  price decimal
  status string [default: 'pending']
  created_at timestamp
  updated_at timestamp
}

// Wallets Table
Table wallets {
  id integer [primary key]
  user_id integer [ref: > users.id]
  balance decimal(8,2) [default: 0]
  created_at timestamp
  updated_at timestamp
}

// Transactions Table
Table transactions {
  id integer [primary key]
  sender_id integer [ref: > wallets.id]
  receiver_id integer [ref: > wallets.id]
  amount decimal(8,2)
  created_at timestamp
  updated_at timestamp
}

// Notifications Table
Table notifications {
  id uuid [primary key]
  type string
  notifiable_type string
  notifiable_id integer
  data json
  read_at timestamp [null]
  created_at timestamp
  updated_at timestamp
}

// Expo Tokens Table
Table expo_tokens {
  id integer [primary key]
  user_id integer [ref: > users.id]
  token string [unique]
  created_at timestamp
  updated_at timestamp
}

// Settings Table
Table settings {
  id integer [primary key]
  key string [unique]
  type string [default: 'text']
  value string [null]
  description text [null]
  created_at timestamp
  updated_at timestamp
}

// Support Sessions Table
Table support_sessions {
  id integer [primary key]
  status string
  subject string
  user_id integer [ref: > users.id]
  admin_id integer [ref: > users.id]
  created_at timestamp
  updated_at timestamp
}

// Support Messages Table
Table support_messages {
  id integer [primary key]
  message string
  sender_id integer [ref: > users.id]
  support_session_id integer [ref: > support_sessions.id]
  created_at timestamp
  updated_at timestamp
}

// Composite Index
Ref: notifications.notifiable_id + notifications.notifiable_type > users.id
```

## Relationships Overview

- Users can have multiple provider services, locations, and expo tokens
- Users can create active requests for services
- Users have wallets for financial transactions
- Support sessions are created between users and admins
- Support messages belong to support sessions
- Users can send/receive notifications
