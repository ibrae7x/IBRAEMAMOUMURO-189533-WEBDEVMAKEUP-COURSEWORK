# IBRAE MAKEUP - PHP User Management System

## 🎯 **Project Overview**

**IBRAE MAKEUP** is a comprehensive, modern PHP OOP-based user management and article publishing system with role-based access control and stunning UI design.

**Student:** Ibrae Mamo Umuro  
**ID:** 189533  
**Course:** Web Development Makeup Coursework  

---

## 🚀 **Key Features**

### **🔐 User Management System**
- **Three User Types**: Super_User, Administrator, Author
- **Role-Based Access Control**: Different permissions for each user type
- **Secure Authentication**: Password hashing and session management
- **User CRUD Operations**: Complete user lifecycle management
- **Profile Management**: Users can update profiles and change passwords

### **📝 Article Management Platform**
- **Article Publishing**: Create, edit, and manage articles
- **Content Management**: Rich text content with excerpts
- **Author Attribution**: Articles linked to authors
- **Publication Status**: Draft and published states
- **Article Display**: Browse and view published articles

### **🎨 Modern UI/UX Design**
- **Glassmorphism Effects**: Translucent cards with backdrop blur
- **Gradient Backgrounds**: Beautiful animated gradient overlays
- **Smooth Animations**: Hover effects and micro-interactions
- **Responsive Design**: Works perfectly on all devices
- **Professional Styling**: Bootstrap 5 with custom enhancements

### **🔒 Security Features**
- **Session Management**: Secure session handling with timeout
- **Input Validation**: Server-side and client-side validation
- **SQL Injection Protection**: Prepared statements throughout
- **Password Security**: PHP password hashing functions
- **Access Control**: Role-based page restrictions

---

## 🛠️ **Technical Stack**

- **Backend**: PHP 7.4+ (Object-Oriented Programming)
- **Database**: MySQL with MySQLi
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Framework**: Bootstrap 5
- **Architecture**: MVC Pattern
- **Security**: Session management, password hashing, input validation

---

## 📁 **Project Structure**

```
IBRAE MAKEUP/
├── assets/
│   ├── css/
│   │   └── style.css          # Enhanced modern styling
│   └── js/
│       └── script.js          # Interactive functionality
├── config/
│   ├── connection.php         # Database connection (Singleton)
│   └── constants.php          # Application constants
├── dashboard/
│   ├── dashboard.php          # Main dashboard
│   ├── profile.php            # User profile management
│   ├── manage_users.php       # Super User - manage all users
│   ├── manage_authors.php     # Administrator - manage authors
│   ├── manage_articles.php    # Author - manage articles
│   ├── view_articles.php      # View published articles
│   ├── get_article.php        # AJAX article fetching
│   └── logout.php             # Logout functionality
├── database/
│   └── user_management_system.sql  # Complete database schema
├── includes/
│   ├── header.php             # Common header
│   ├── footer.php             # Common footer
│   ├── session.php            # Session management
│   └── functions.php          # Utility functions
├── models/
│   ├── User.php               # User model with CRUD operations
│   └── Article.php            # Article model with CRUD operations
├── setup/
│   └── database_setup.php     # Database initialization
├── index.php                  # Login page
├── debug_login.php            # Debug utilities
└── README.md                  # This file
```

---

## 🗃️ **Database Schema**

### **Users Table**
- `Full_Name` - User's complete name
- `Email` - Unique email address
- `Phone_Number` - Contact information
- `User_Name` - Unique username for login
- `Password` - Hashed password
- `UserType` - Role (Super_User, Administrator, Author)
- `Address` - User's address
- `Profile_Image` - Profile picture filename
- `Status` - Active/Inactive
- `Created_At` - Registration timestamp

### **Articles Table**
- `Title` - Article title
- `Content` - Article content
- `Excerpt` - Brief description
- `Author_ID` - Foreign key to users table
- `Status` - Draft/Published
- `Created_At` - Creation timestamp
- `Updated_At` - Last modification timestamp

---

## 🚀 **Installation & Setup**

### **1. Clone Repository**
```bash
git clone https://github.com/ibrae7x/IBRAEMAMOUMURO-189533-WEBDEVMAKEUP-COURSEWORK.git
cd IBRAEMAMOUMURO-189533-WEBDEVMAKEUP-COURSEWORK/IBRAE\ MAKEUP
```

### **2. Web Server Setup**
```bash
# For XAMPP
cp -r "IBRAE MAKEUP" /xampp/htdocs/

# For WAMP
cp -r "IBRAE MAKEUP" /wamp/www/
```

### **3. Start Services**
- Start Apache and MySQL services
- Ensure PHP 7.4+ is enabled

### **4. Database Configuration**
- Update database credentials in `config/constants.php`
- Import SQL file: `database/user_management_system.sql`
- Or run setup script: `setup/database_setup.php`

### **5. Access Application**
```
http://localhost/IBRAE MAKEUP
```

---

## 👤 **User Roles & Permissions**

### **🔑 Super User**
- Manage all users (CRUD operations)
- Access all system functions
- System administration privileges

### **⚙️ Administrator**
- Manage authors (CRUD operations on authors only)
- View all articles
- Limited administrative access

### **✍️ Author**
- Manage own articles (CRUD operations)
- Update own profile
- Publish and manage content

---

## 🎨 **UI/UX Features**

### **Modern Design Elements**
- **Glassmorphism Login**: Semi-transparent form with blur effects
- **Gradient Backgrounds**: Beautiful animated gradients
- **Hover Animations**: Smooth button and card transitions
- **Professional Typography**: Enhanced fonts and spacing
- **Color Scheme**: Modern blue gradient theme

### **Interactive Features**
- **Loading States**: Spinner animations for form submissions
- **Auto-save**: Automatic draft saving for forms
- **Keyboard Shortcuts**: Ctrl+S to save, Ctrl+N for new items
- **Real-time Search**: Instant filtering with animations
- **Smart Notifications**: Sliding notification system
- **Theme Support**: Dark/light mode capability

---

## 🔒 **Security Measures**

- **Password Hashing**: PHP `password_hash()` and `password_verify()`
- **Session Security**: Timeout management and secure handling
- **SQL Injection Prevention**: Prepared statements throughout
- **Input Validation**: Client-side and server-side validation
- **Access Control**: Role-based page restrictions
- **CSRF Protection**: Form token validation

---

## 📱 **Responsive Design**

Fully responsive and optimized for:
- **Desktop Computers**
- **Tablets**
- **Mobile Phones**
- **Different Screen Sizes**

---

## 🎯 **Key Achievements**

✅ **Modern PHP OOP Architecture**  
✅ **Comprehensive User Management**  
✅ **Role-Based Access Control**  
✅ **Secure Authentication System**  
✅ **Article Publishing Platform**  
✅ **Professional UI/UX Design**  
✅ **Mobile Responsive Design**  
✅ **Production Ready Code**  

---

## 📊 **Project Statistics**

- **Programming Language**: PHP (OOP)
- **Total Files**: 23+
- **Lines of Code**: 5,000+
- **Database Tables**: 2 (Users, Articles)
- **User Roles**: 3 (Super_User, Administrator, Author)
- **Security Features**: 6+ implemented
- **UI Components**: 10+ custom styled

---

## 🤝 **Academic Information**

**Course**: Web Development Makeup Coursework  
**Student**: Ibrae Mamo Umuro  
**Student ID**: 189533  
**Submission**: Complete PHP User Management System  
**Features**: Modern UI, Role-based Access, Article Management  

---

## 📄 **License**

This project is developed as part of academic coursework requirements.

---

**Developed with ❤️ for Web Development Excellence**

*Demonstrating modern web development practices with PHP, MySQL, and responsive design.*
