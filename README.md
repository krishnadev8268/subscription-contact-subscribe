# Centralized Subscription & Contact Management System

A lightweight, database-free admin dashboard built with **Core PHP** for managing subscriptions and contact form submissions from multiple websites through a centralized API.

## 🌟 Features

- **No Database Required** - All data stored in JSON files
- **Multi-Website Support** - Manage multiple websites from one dashboard
- **Secure API** - Website key-based authentication
- **CORS Enabled** - Works with external websites
- **Session-Based Auth** - Secure admin login system
- **Advanced Tables** - Search, pagination, and filtering
- **Responsive Design** - Works on all devices

## 📁 Project Structure

```
subscription/
├── admin/              # Admin dashboard
│   ├── assets/        # CSS, JS files
│   ├── ajax/          # AJAX endpoints
│   ├── includes/      # Header, sidebar, footer
│   ├── login.php      # Admin login
│   ├── dashboard.php  # Main dashboard
│   ├── websites.php   # Website management
│   ├── subscribers.php # Subscriber list
│   └── contacts.php   # Contact messages
├── api/               # API endpoint
│   ├── submit.php     # Main API endpoint
│   └── .htaccess      # CORS configuration
├── storage/           # JSON data files
│   ├── admins.json
│   ├── websites.json
│   ├── subscribers.json
│   ├── contacts.json
│   └── .htaccess      # Protect storage
├── config.php         # Core configuration
└── router.php         # Dev server router
```

## 🚀 Installation

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/krishnamaurya-96/centeralized.git
   cd centeralized
   ```

2. **Start PHP development server**
   ```bash
   php -S localhost:8000 router.php
   ```

3. **Access admin panel**
   ```
   http://localhost:8000/admin/login.php
   ```

4. **Default credentials**
   - Email: `admin@example.com`
   - Password: `password`

### Shared Hosting Deployment

1. **Upload files to public_html**
   ```
   public_html/
   ├── admin/
   ├── api/
   ├── storage/
   └── config.php
   ```

2. **Set permissions**
   ```bash
   chmod 755 api/
   chmod 775 storage/
   chmod 664 storage/*.json
   ```

3. **Access URLs**
   ```
   https://yourdomain.com/admin/login.php
   https://yourdomain.com/api/submit.php
   ```

## 🔌 API Usage

### Subscribe Endpoint

```javascript
fetch('https://yourdomain.com/api/submit.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        type: 'subscribe',
        website_key: 'YOUR_WEBSITE_KEY',
        email: 'user@example.com',
        country: 'India' // optional
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Contact Form Endpoint

```javascript
fetch('https://yourdomain.com/api/submit.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        type: 'contact',
        website_key: 'YOUR_WEBSITE_KEY',
        name: 'John Doe',
        email: 'john@example.com',
        message: 'Hello!',
        country: 'USA' // optional
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 🔐 Security Features

- Session-based authentication
- Password hashing (bcrypt)
- Input sanitization
- XSS protection
- File locking for concurrent writes
- Protected storage directory
- CSRF protection ready

## 📋 Requirements

- PHP 7.4 or higher
- JSON extension (enabled by default)
- Apache/Nginx web server (for production)
- mod_headers (for CORS on Apache)

## 🛠️ Configuration

### Change Admin Password

1. Login to admin panel
2. Or manually update `storage/admins.json`:
   ```php
   password_hash('your_new_password', PASSWORD_DEFAULT)
   ```

### Add Website

1. Go to **Websites** section in admin panel
2. Click **Add New Website**
3. Enter website details
4. Copy the generated API key
5. Use the API key in your website's forms

## 📊 Features Overview

### Admin Dashboard
- Total statistics
- Recent activity
- Quick overview

### Website Management
- Add/Delete websites
- Generate unique API keys
- Track website types

### Subscriber Management
- View all subscribers
- Search and filter
- Pagination support
- Export capability

### Contact Management
- View messages
- Search functionality
- Read full messages in modal
- Responsive table layout

## 🎨 UI Features

- Modern, clean design
- Advanced table styling
- Hover effects
- Striped rows
- Custom scrollbars
- Responsive layout
- Modal popups
- Loading states

## 🔧 Troubleshooting

### CORS Issues
- Ensure `.htaccess` exists in `api/` folder
- Check `mod_headers` is enabled on Apache
- Verify CORS headers in `api/submit.php`

### Permission Errors
```bash
chmod 775 storage/
chmod 664 storage/*.json
```

### Headers Already Sent
- Check for whitespace before `<?php`
- Output buffering is enabled in `api/submit.php`

## 📝 License

MIT License - Feel free to use for personal or commercial projects

## 👨‍💻 Author

**Krishna Maurya**
- GitHub: [@krishnamaurya-96](https://github.com/krishnamaurya-96)

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

## ⭐ Show Your Support

Give a ⭐️ if this project helped you!
