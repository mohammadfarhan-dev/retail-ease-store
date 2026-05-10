# RetailEase Store

## Full-Stack Web Application with Responsible AI Integration

RetailEase Store is a PHP and MySQL web application developed for a small retail business. The system allows customers to register, log in, browse products, search and filter product listings, place product orders, view order status, and use a Smart Assistant for system guidance. Admin users can manage products, update customer orders, view users, monitor activity logs, and review Smart Assistant logs.

This project was developed for **Assessment 3: Full-Stack Web Application with Responsible AI Integration**.

---

## Project Scenario

The selected industry scenario is:

**Small Retail Business — Catalogue and Orders**

The goal of the project is to provide a secure, responsive, database-driven web application for a small retail business where customers can view products and place orders, while admin users can manage the business operations.

---

## Team Members and Roles

| Student Name           | Role                     | Main Contribution                                                                 |
| ---------------------- | ------------------------ | --------------------------------------------------------------------------------- |
| Prannaya Pal Shrestha  | Back-End Lead            | PHP backend, authentication, sessions, role-based access, product and order logic |
| Sandip Shrestha        | Front-End Lead           | User interface, responsive design, navigation, forms, and customer-side pages     |
| Mohammad Farhan Ansari | Data/QA Lead + Team Lead | Database design, testing, documentation, GitHub coordination, and final review    |

---

## Technology Stack

| Area                | Technology                         |
| ------------------- | ---------------------------------- |
| Frontend            | HTML, CSS, JavaScript              |
| Backend             | PHP                                |
| Database            | MySQL                              |
| Local Server        | XAMPP                              |
| Authentication      | PHP Sessions + Password Hashing    |
| Database Access     | PDO Prepared Statements            |
| AI Feature          | Rule-Based Smart Assistant         |
| Version Control     | GitHub                             |
| Deployment Platform | InfinityFree / PHP + MySQL Hosting |

---

## Main Features

### Customer Features

* Customer registration
* Customer login and logout
* Product catalogue browsing
* Real-time product search and category filtering
* Product details page
* Product order placement
* View own order history
* Cancel pending orders
* Smart Assistant for system help
* Responsive mobile-friendly interface with hamburger navigation

### Admin Features

* Admin login and logout
* Admin dashboard with system statistics
* Add products
* Edit products using popup modal
* Delete products
* Manage customer orders
* Update order status
* View registered users
* View activity logs
* View Smart Assistant logs

---

## Responsible AI Feature

The selected AI feature is:

**AI Option 1: Smart Assistant**

RetailEase Store includes a Smart Assistant that answers user questions about how to use the system. The Smart Assistant uses local curated responses and rule-based keyword matching. It does not send user data to external public AI tools.

### Example Questions Supported by the Assistant

* How do I place an order?
* How can I search products?
* How do I check my order status?
* Can I cancel my order?
* What does pending order mean?
* What can admin users do?

### Responsible AI Controls

* The assistant uses local predefined responses.
* No sensitive personal data is sent to external AI tools.
* A disclaimer is shown to users before using the assistant.
* Users must review the assistant response before saving it.
* Reviewed assistant responses are stored in the AI logs.
* Admin users can review AI usage through the AI Logs page.

---

## Security Features

The application includes the following security controls:

* Password hashing using PHP `password_hash()`
* Password verification using PHP `password_verify()`
* PHP session-based authentication
* Role-based access control for admin and customer pages
* PDO prepared statements to reduce SQL injection risk
* Server-side input validation
* Client-side form validation
* Activity logging for important user and admin actions
* Restricted admin pages using access checks

---

## Database Information

Database name:

```sql
retail_ease_store
```

Main database tables:

| Table           | Purpose                                   |
| --------------- | ----------------------------------------- |
| `users`         | Stores admin and customer accounts        |
| `products`      | Stores product catalogue records          |
| `orders`        | Stores customer product orders            |
| `activity_logs` | Stores important user/admin actions       |
| `ai_logs`       | Stores reviewed Smart Assistant responses |

---

## Folder Structure

```text
retail-ease-store/
│
├── admin/
│   ├── dashboard.php
│   ├── products.php
│   ├── orders.php
│   ├── users.php
│   ├── activity_logs.php
│   └── ai_logs.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── images/
│       └── retailease-logo.png
│
├── config/
│   └── database.php
│
├── database/
│   └── retail_ease_store.sql
│
├── docs/
│   ├── Project_Proposal.docx
│   ├── System_Design.docx
│   ├── Security_Risk_Register.docx
│   ├── Test_Evidence.docx
│   └── AI_Governance_Appendix.docx
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── user/
│   ├── dashboard.php
│   ├── products.php
│   ├── product_details.php
│   ├── my_orders.php
│   ├── cancel_order.php
│   └── smart_assistant.php
│
├── index.php
├── login.php
├── register.php
├── logout.php
└── README.md
```

---

## Local Setup Instructions

### Step 1: Install and Start XAMPP

Install XAMPP and start the following services:

```text
Apache
MySQL
```

---

### Step 2: Copy Project Folder

Copy the project folder into:

```text
C:\xampp\htdocs\
```

The final local path should be:

```text
C:\xampp\htdocs\retail-ease-store
```

---

### Step 3: Create Database

Open phpMyAdmin in the browser:

```text
http://localhost/phpmyadmin
```

Create a new database named:

```text
retail_ease_store
```

---

### Step 4: Import SQL File

In phpMyAdmin:

1. Select the `retail_ease_store` database.
2. Click **Import**.
3. Select the SQL file:

```text
database/retail_ease_store.sql
```

4. Click **Go**.

---

### Step 5: Check Database Connection

Open:

```text
config/database.php
```

Default XAMPP settings:

```php
$host = "localhost";
$dbname = "retail_ease_store";
$username = "root";
$password = "";
```

---

### Step 6: Run the Application Locally

Open the application in the browser:

```text
http://localhost/retail-ease-store/
```

---

## Demo Login Accounts

### Admin Account

```text
Email: admin@retailease.com
Password: Admin123!
```

### Customer Account

```text
Email: user@retailease.com
Password: User123!
```

---

## Main Application URLs

### Public Pages

```text
http://localhost/retail-ease-store/
http://localhost/retail-ease-store/login.php
http://localhost/retail-ease-store/register.php
```

### Customer Pages

```text
http://localhost/retail-ease-store/user/dashboard.php
http://localhost/retail-ease-store/user/products.php
http://localhost/retail-ease-store/user/my_orders.php
http://localhost/retail-ease-store/user/smart_assistant.php
```

### Admin Pages

```text
http://localhost/retail-ease-store/admin/dashboard.php
http://localhost/retail-ease-store/admin/products.php
http://localhost/retail-ease-store/admin/orders.php
http://localhost/retail-ease-store/admin/users.php
http://localhost/retail-ease-store/admin/activity_logs.php
http://localhost/retail-ease-store/admin/ai_logs.php
```

---

## Deployment

The application is suitable for PHP and MySQL hosting. The selected free deployment option is:

```text
InfinityFree
```

Deployment steps:

1. Create a hosting account.
2. Create a PHP/MySQL website.
3. Upload the project files to the hosting file manager.
4. Create a MySQL database on the hosting platform.
5. Import `database/retail_ease_store.sql`.
6. Update `config/database.php` with live database credentials.
7. Open and test the deployed website URL.

Deployment link:

```text
To be added after deployment
```

---

## Testing Summary

The system was tested using positive and negative test cases, including:

* Register with valid data
* Login with valid credentials
* Login with incorrect password
* Admin adds a product
* Admin edits a product
* Admin deletes a product
* Customer searches products
* Customer places an order
* Admin updates order status
* Customer uses Smart Assistant
* Admin views AI logs
* Mobile responsiveness and hamburger navigation

Full testing details are available in:

```text
docs/Test_Evidence.docx
```

---

## AI Use Statement

Generative AI was used as an assistant during project planning, code improvement, documentation drafting, and interface refinement. The final project was reviewed and edited by the student team.

The application’s Smart Assistant feature is implemented as a local rule-based assistant using curated responses. It does not call public AI APIs and does not send user data outside the system. Human review is required before assistant responses are saved in the AI logs.

---

## Notes

This project demonstrates:

* Full-stack PHP and MySQL development
* Secure authentication
* Admin and customer role separation
* Product and order management
* Search, filtering, and responsive UI
* Responsible AI integration
* Testing and documentation evidence
