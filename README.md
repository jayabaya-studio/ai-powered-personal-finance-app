![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black)

# AI-Powered Personal Finance Management (PFM) Web Application

This is a simple, intuitive, and user-friendly Personal Finance Management (PFM) web application designed to help individuals and families manage their finances effectively. As a user-centric platform, all features are accessible after creating an account, providing a secure and personalized environment for your financial data.

## Key Features

-   **Interactive Dashboard:** A customizable visual overview of your income, expenses, and balance.
-   **Transaction Management:** Easily record, categorize, and track your transactions.
-   **Custom Categories:** Create personalized income and expense categories.
-   **Reports & Analysis:** Visualize financial trends with charts and reports.
-   **Budgeting:** Set and monitor budgets for various categories.
-   **Family Space:** A collaborative feature to manage family finances together.
-   **Gemini AI Integration:** Engage in conversations about your finances, get data-driven predictions, and receive financial advice.
-   **Financial Insights:** Advanced analysis of your financial data to identify trends and opportunities for improvement.

## Screenshots

Here are some screenshots of the PFM application:

### Dashboard
![PFM Dashboard Screenshot](https://raw.githubusercontent.com/jayabaya-studio/pfm-finance-app/main/assets/dashboard.jpg)

### Family Space Feature
![Family Spaces Feature Screenshot](https://raw.githubusercontent.com/jayabaya-studio/pfm-finance-app/main/assets/family_spaces.jpg)

## Tech Stack

-   **Backend:** PHP (Laravel 11)
-   **Frontend:** Tailwind CSS, Alpine.js
-   **Database:** MySQL (via XAMPP)
-   **Recommended PHP version:** 8.2 or higher

## System Requirements

-   Web server (Apache/Nginx)
-   PHP 8.2+
-   MySQL Database
-   Composer
-   Node.js & NPM/Yarn (for frontend assets)

## Local Installation Guide

Follow the steps below to run the project locally:

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/jayabaya-studio/pfm-finance-app.git
    cd pfm-finance-app
    ```
2.  **Install Composer dependencies:**
    ```bash
    composer install
    ```
3.  **Create the .env file and generate the app key:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4.  **Configure your database:**
    Open the `.env` file and configure your database details (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

5.  **Configure Gemini AI:**
    Add your Gemini API Key to the `.env` file. You can get it from Google AI Studio.
    ```env
    GEMINI_API_KEY=your_api_key_here
    ```

6.  **Run the database migrations:**
    ```bash
    php artisan migrate
    ```
7.  **Install Node.js dependencies & Compile frontend assets:**
    ```bash
    npm install
    npm run dev  # or npm run build for production
    ```
8.  **Run the Laravel development server:**
    ```bash
    php artisan serve
    ```
    The application will be available at `http://127.0.0.1:8000`.

## Contributing

Contributions are welcome! If you find a bug or have a feature suggestion, please open an issue or submit a pull request.

## License

This project is licensed under the MIT License. See the `LICENSE` file for more details.

## Support the Project

If this project helps you, you can consider supporting it:

---
*Made with love by Fathur Rochim*