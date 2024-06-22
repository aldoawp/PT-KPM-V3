<p align="center"><img src="https://raw.githubusercontent.com/aldoawp/PT-KPM-NEW/main/public/assets/img/logos/logo-kpm-light%201.png" style="width:16em"></p>

# 🚬 PT Karyamega Putra Mandiri POS System

PT Karyamega Putra Mandiri is a company engaged in cigarette production located in Pakisaji, Malang. The company has many salespeople and regional managers in various regions. However, sales data input is still done manually using Excel by a single admin and WhatsApp for communication. This often leads to delays in reporting due to incomplete records. To address this issue, we present a solution in the form of web-based POS software.

## ✨ Features

The POS system includes several key features:
1. **Authentication and Multi-role Authorization**: Separate access for owners, managers, and staff.
2. **Resource Management**: Inventory, customer, and employee management.
3. **POS System**: Records sales transactions, restocks, and returns.
4. **Reporting**: Generate financial, sales, restock, return, and inventory reports on a daily, weekly, or monthly basis.
5. **Dashboard**: Charts for viewing important information.

## 🚧 Development and Deployment

- **Development Framework**: Laravel PHP framework.
- **Front-end Styling**: Tailwind CSS.
- **Database Management**: Eloquent ORM.
- **Templating Engine**: Laravel Blade.
- **Testing**: Blackbox and Whitebox testing methods.
- **CI/CD Pipeline**: Configured with GitHub Actions.
- **Hosting**: DigitalOcean.

## 📌 Installation Instructions

To set up the application locally, follow these steps:

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL server

### Steps

1. **Clone the repository:**
```bash
git clone https://github.com/aldoawp/PT-KPM-NEW.git
```

2. **Go into the repository**
```bash
cd pt-karyamega-putra-mandiri-pos
``` 

3. **Install PHP dependencies** 
```bash
composer install
```

4. **Install JavaScript dependencies:**
```bash
npm install
```

5. **Setup the `.env` file**
```bash
cp .env.example .env
```

6. **Generate the app key:**
```bash
php artisan key:generate
```

7. **Setting up your database credentials in your `.env` file.**
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=[YOUR_DB]
DB_USERNAME=[YOUR_DB_USERNAME]
DB_PASSWORD=[YOUR_DB_PASSWORD]
```

8. **Run migrations and seed the database:** 
```bash
php artisan migrate:fresh --seed
```

9. **Create Storage Link**
```bash
php artisan storage:link
```

10. **Build assets**
```bash
npm run dev
```

11. **Start the development server:** 
```bash
php artisan serve
```

12. **Login in with these credentials:**
- email: *admin@gmail.com*
- password: *password*

## 📨 Contact
For any inquiries or feedback, please contact us at hello@aldocodes.tech

> ![Github](https://raw.githubusercontent.com/aldoawp/PT-KPM-NEW/main/public/assets/img/logos/skill-icons--github-dark.svg) [@aldoawp](https://github.com/aldoawp/) &nbsp;&middot;&nbsp;
> ![Instagram](https://raw.githubusercontent.com/aldoawp/PT-KPM-NEW/main/public/assets/img/logos/skill-icons--instagram.svg) [@aldocodes](https://instagram.com/aldocodes/) &nbsp;&middot;&nbsp;
> ![LinkedIn](https://raw.githubusercontent.com/aldoawp/PT-KPM-NEW/main/public/assets/img/logos/skill-icons--linkedin.svg) [@Aldo Arista](https://linkedin.com/in/aldo-arista/)
