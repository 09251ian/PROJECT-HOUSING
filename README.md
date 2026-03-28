# Housing Marketplace - CodeIgniter 4 Application

## Overview

This is a **full-stack web application** built with **CodeIgniter 4** PHP framework for a **housing/real estate marketplace**. It enables:

- **Sellers** to list, edit, archive, and manage properties with image uploads
- **Buyers** to search/filter properties, make offers, and chat with sellers
- **User authentication** (login/register) with role-based dashboards
- **Offer management** (accept/reject offers)
- **Real-time chat** between buyers and sellers per property
- **Property search/filter** by title, location, price range

**Key Features:**
- Responsive dashboards for buyers/sellers
- Image uploads for properties/user profiles (stored in `public/uploads/`)
- Pagination for property lists
- Form validation & sanitization
- Session-based auth with password hashing
- Property archiving (soft delete)

## Tech Stack
- **Framework**: CodeIgniter 4 (MVC pattern)
- **Database**: MySQL (`housing` database)
- **Frontend**: PHP Views + HTML/CSS/JS (Bootstrap-like)
- **Server**: Apache/XAMPP (`.htaccess` configured)
- **File Storage**: Local `public/uploads/`


## Core Functions & Routes

### Authentication (`app/Controllers/Auth.php`)
- `GET /login` → Login form
- `POST /login` → Authenticate & role-redirect
- `GET/POST /register` → User registration (role selection, profile pic)
- `GET /logout` → Session destroy

### Buyer Dashboard (`app/Controllers/BuyerController.php`)
- `GET /buyer/dashboard` → Filtered properties list, existing offers/chats
- Filters: search (title/desc), location, price ranges (<1M, 1-10M, etc.)

### Seller Dashboard (`app/Controllers/SellerController.php`)
- `GET /seller/dashboard` → Own properties (paginated), offers
- `POST /seller/add_property` → Add property w/ image
- `POST /seller/edit_property/:id` → Edit property
- `POST /seller/offer_action` → Accept/reject offers
- `GET /seller/archived` → Archived properties
- `POST /seller/archive/unarchive/delete` → Manage visibility/permanent delete

### Other
- `POST /make_offer` → Create offer on property
- `GET/POST /message/:receiver/:property` → Chat
- `GET /profile/:id` → User profile view
- `GET /` → Landing page

### Models
- **PropertyModel**: CRUD + `getFilteredProperties()` for buyer search
- **UserModel**, **OfferModel**, **MessageModel**: Standard CRUD

## Setup Instructions

1. **Prerequisites**:
   - PHP 7.4+, MySQL, Apache (XAMPP recommended)
   - Composer

2. **Install Dependencies**:
   ```
   cd c:/xampp/htdocs/Final_PROJECT-HOUSING/PROJECT-HOUSING
   composer install
   ```

3. **Database**:
   - Import `housing (1) (1).sql` into MySQL db named `housing`
   - Update `app/Config/Database.php` if needed (localhost/root/'' → housing)

4. **Permissions**:
   ```
   chmod -R 755 writable/
   chmod -R 755 public/uploads/
   ```

5. **Run**:
   - Start XAMPP (Apache/MySQL)
   - Visit `http://localhost/Final_PROJECT-HOUSING/PROJECT-HOUSING/public/`
   - Register as buyer/seller, upload properties, test offers/chat

## Folder Structure

```
PROJECT-HOUSING/
├── app/                 # CI4 app (Controllers, Models, Views, Config)
├── public/              # Web root (.htaccess, index.php, uploads/)
├── writable/            # Cache/logs/sessions (auto-created)
├── tests/               # PHPUnit tests
├── composer.json        # Dependencies
├── housing*.sql         # DB schema/data
└── websocket/server.php # Future WebSocket chat?
```

## Security Features
- Password hashing (`password_hash`)
- Input sanitization (`FILTER_SANITIZE_*`)
- Form validation rules
- Session role checks
- CSRF protection (CI4 default)
- Image upload validation/moves

## Future Improvements
- WebSocket real-time chat (`websocket/server.php`)
- Email notifications
- Advanced search/pagination for buyers
- Property categories
- Maps integration for locations
- Admin panel

## Testing
```
vendor/bin/phpunit
```

**Enjoy the Housing Marketplace! 🏠✨**
