# Data Analytics Management System

Modern PHP-based project management and analytics system for community development projects.

## 🚀 Version 2.0 Features

- **Modern Tech Stack**: Bootstrap 5.3.2, jQuery 3.7.1, Chart.js 4.4.1
- **Enhanced Security**: CSRF protection, XSS prevention, secure password hashing
- **Responsive Design**: Mobile-first approach with modern CSS
- **Performance Optimized**: Service Worker, lazy loading, optimized assets
- **Accessibility**: ARIA labels, screen reader support, keyboard navigation

## 📁 Project Structure

```text
├── _sys/           # System core files
├── _page/          # Page templates
├── _dist/          # Compiled assets
├── main.php        # Main layout
├── new_index.php   # Entry point
└── admin/          # Legacy admin files
```

## 🛠 Installation

1. Clone or download the project
2. Configure database in `_sys/_config.php`
3. Import database schema
4. Access via `new_index.php`

## 🎯 Usage

### New Structure (Recommended)

```url
http://localhost/Data-Analytics-Management/new_index.php?page=home
```

### Legacy Structure (Still supported)

```url
http://localhost/Data-Analytics-Management/index.php
```

## 📊 Available Pages

- `?page=home` - Dashboard homepage
- `?page=dashboard` - Analytics dashboard
- `?page=projects` - Project management
- `?page=analytics` - Data visualization
- `?page=reports` - Report generation

## 🔧 Development

The system uses a modular approach:

- **Config**: `_sys/_config.php`
- **Functions**: `_sys/_func.php`
- **API**: `_sys/_api.php`
- **Styles**: `_dist/css/style.css`
- **Scripts**: `_dist/js/main.js`

## 📝 License

© 2025 Data Analytics Management System. All rights reserved.
