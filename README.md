# Personal & Family Finance Management Application

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black)

A modern web application for managing personal and family finances collaboratively. Track income, expenses, create budgets, and achieve your financial goals together. Built with the TALL Stack (Tailwind, Alpine.js, Laravel, Livewire - *although this project uses Blade+Alpine.js*).

This application is designed with a clean interface, an elegant dark theme, and a reactive user experience.

---

## 🌟 Key Features

Here are some of the key features in this application:

*   **Interactive Dashboard**: Visualize your financial health at a glance.
    *   Monthly income & expense summary chart.
    *   List of recent transactions.
    *   Top spending categories.
    *   Monthly budget progress.

*   **Transaction Management**: Easily record every transaction.
    *   CRUD (Create, Read, Update, Delete) for transactions.
    *   Transaction types: Income, Expense, and Transfer between accounts.
    *   Flexible transaction categorization.

*   **Account Management**: Manage all your funding sources.
    *   Create personal accounts (Savings, Wallet, Credit Card, etc.).
    *   View total balance and balance per account.

*   **Monthly Budgeting**: Control your spending.
    *   Set monthly budget limits for each category.
    *   Visually track spending progress against the budget.

*   **Financial Goals**: Realize your financial dreams.
    *   Create savings targets (e.g., "Vacation Fund", "New Laptop").
    *   Allocate savings from transactions to specific goals.

*   **✨ Highlight Feature: Family Space**
    *   **Collaborative Finance**: Create or join a "Family Space" to manage finances together.
    *   **Invite Members**: Invite other family members via email.
    *   **Role Management**: Assign roles as `Admin` or `Member` to control access rights.
    *   **Joint Accounts**: Create accounts that can be accessed and managed jointly by all family members.

*   **Notifications**: Stay up-to-date with important activities like invitations to a Family Space.

*   **AI Assistant**: A floating component for future interactions (conceptual feature).

## 🛠️ Technology Stack

*   **Backend**: Laravel 11
*   **Frontend**:
    *   Blade
    *   Tailwind CSS
    *   Alpine.js
*   **Database**: MySQL / PostgreSQL (configurable)
*   **Build Tool**: Vite

## 🚀 Installation & Local Setup

Follow these steps to run this project in your local environment.

1.  **Clone repository:**
    ```bash
    git clone https://github.com/NAMA_USER_ANDA/NAMA_REPO_ANDA.git
    cd NAMA_REPO_ANDA
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Create the environment file:**
    ```bash
    cp .env.example .env
    ```

4.  **Generate application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Configure the database:**
    Open the `.env` file and adjust your database settings (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

6.  **Run database migrations:**
    This command will create all the necessary tables in your database.
    ```bash
    php artisan migrate
    ```
    *Optional: If there are seeders, run `php artisan migrate --seed`.*

7.  **Install JavaScript dependencies:**
    ```bash
    npm install
    ```

8.  **Compile frontend assets:**
    Run the Vite development server.
    ```bash
    npm run dev
    ```

9.  **Run the Laravel development server:**
    Open a new terminal and run the following command.
    ```bash
    php artisan serve
    ```

10. **Done!**
    The application is now running at `http://127.0.0.1:8000`. You can register a new account to get started.

## ❤️ Supporting This Project

If you find this project useful, if it helps your portfolio, or if you just want to appreciate the hard work, you can show your support by buying me a coffee. Every bit of support is greatly appreciated!

<a href="https://www.buymeacoffee.com/NAMA_ANDA" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>
<a href="https://saweria.co/NAMA_ANDA" target="_blank"><img src="https://user-images.githubusercontent.com/24270415/199109032-b3558c3d-737c-41a1-a88c-2333904396b2.png" alt="Saweria" style="height: 60px !important;"></a>

## 📄 License

This project is licensed under the MIT License.