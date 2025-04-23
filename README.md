# 📸 Pixofix Production Order Management System

A web-based Laravel application designed to streamline the management of production orders consisting of multiple image files. Built with real-time collaboration and task tracking in mind, this system allows employees to claim image batches, work on them, and update statuses—all while giving the admin full visibility of progress.

---

## 🚀 Features (Phase 1)

### ✅ User Authentication & Role Management
- User login/registration using **Laravel Breeze**
- Roles: `Admin`, `Employee`

### 📁 Order & File Management
- Admin can create new **Orders** by uploading a ZIP file containing folders & image files
- System automatically extracts and organizes the uploaded files into a structured format
- Files are categorized and stored within nested folders

### 👩‍💻 Task Assignment & Workflow
- Employees can view available **Orders**
- Claim a batch of 10-20 unclaimed files
- Claimed files are locked for others to prevent duplication
- Files can be updated with status: `In Progress`, `Completed`

### 🔄 Real-Time Tracking & Action Logs
- All major actions (file claimed, status updated) are logged per employee
- Real-time updates using **Laravel Echo + WebSockets**

### 📊 Admin Dashboard
- Overview of all orders with progress indicators
- Track who is working on what, total completed, and remaining files
- View logs and performance per employee

---

## 🧰 Tech Stack

| Layer          | Technology                   |
|----------------|------------------------------|
| Backend        | Laravel (Latest Stable)      |
| PHP Version    | PHP 8.2                      |
| Frontend       | Blade Templates              |
| Auth & Roles   | Laravel Breeze, Enum Roles   |
| Realtime       | Laravel Reverb (WebSockets)  |
| Database       | MySQL                        |
| Version Control | Git + GitHub                 |

---

## 📂 Installation Instructions

> **Requirements:**
> - PHP 8.2+
> - Composer
> - Node.js and npm
> - MySQL

```bash
# Clone the repo
git clone https://github.com/saanchita-paul/pixoflow.git
cd pixofix-order-management

# Install dependencies
```
composer install
npm install && npm run dev

```
# Copy environment file & set credentials
```
cp .env.example .env
php artisan key:generate
```
# Set DB credentials in `.env` then run:
```
php artisan migrate
```
# Set storage link for public access to uploaded files
```
php artisan storage:link
```
# Start the server
```
php artisan serve
```
#Start the queue worker:
```
php artisan queue:work

```
#Start the Reverb WebSocket server:
```
php artisan reverb:start

```
