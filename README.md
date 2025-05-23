
# ✂️ Cutting Master – Appointment Booking System

A powerful **Laravel-based** appointment booking and management system tailored for **barber shops** and similar service businesses. Built with flexibility, automation, and ease of use in mind.

---

## ✅ Key Features

* 🔐 **Multi-role Support** (Admin, Professional/Employee, Moderator, Subscriber)
* 📅 **Interactive Calendar View** with multiple time slots per day
* 📧 **Automated Email Notifications** (Bookings, Reminders, Cancellations)
* ❌ **Block Holidays** and Unavailable Dates
* 🔄 **Rescheduling and Cancellations**
* 📱 **Responsive Design** (Mobile-friendly)
* 🚀 **Easy Setup and Customization**

---

## 📦 Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/Addy2323/cutting-master.git
cd cutting-master
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Set Up Environment

* Rename `.env.example` to `.env`
* Generate app key:

```bash
php artisan key:generate
```

### 4. Configure Database

Edit your `.env` file with correct DB credentials:

```env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan migrate
php artisan db:seed
```

### 5. Configure Email (SMTP)

In your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@example.com
```

### 6. Start the Server & Queue Worker

```bash
php artisan serve
php artisan queue:listen
```

Now visit: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Admin Credentials

```
Login URL: http://localhost:8000/login
Email: admin@example.com
Password: admin123
```

---

## 🛠️ Usage Instructions

* **Admin**: Manage users, appointments, system settings.
* **Professional**: Set availability, manage personal bookings, mark holidays.
* **Moderator**: Manage all appointments and act with professional-level access.
* **Subscriber (Client)**: Book appointments and view bookings after login.

---

## 💬 Support & Customization

Need help with installation or want to customize features?

📱 **WhatsApp**: [Chat on WhatsApp](https://wa.me/+255768828247)
📧 **Email**: [myambaado@gmail.com](mailto:myambaado@gmail.com)

> 💼 Paid support available for enterprise setups and integrations.

---

## 🙌 Support This Project

If this system helped you:

* ⭐ Star the repo on GitHub
* 🗣️ Share with your developer friends
* 🐞 Report bugs or suggest features via Issues

---

## 📄 License

MIT – feel free to use, modify, and distribute!
