# Task-4
 Task 4 – Security Enhancements

## 🔐 Objective
To secure the blog application against common web vulnerabilities by implementing:
- Prepared statements
- Form validation
- Role-based access control

---

## ✅ Features Implemented

### 1. Prepared Statements
All database queries now use PDO with bound parameters to prevent SQL injection.
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $input]);
