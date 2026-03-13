# TrackNet — Inventory Management System

A full-stack inventory management web application built with Laravel 12, deployed on Railway using Docker, with AWS S3 for file storage, Brevo for transactional email, and PayMongo for payment processing.

---

## Architecture Overview

| Layer | Technology |
|---|---|
| **Application Framework** | Laravel 12 (PHP 8.2) |
| **Frontend** | Laravel Blade + Bootstrap 5.3.3 (CDN) |
| **Web Server** | Apache 2.4 (mpm_prefork) |
| **Database** | MySQL 8.0 (Railway managed volume) |
| **Deployment Platform** | Railway (Docker container) |
| **File Storage** | Amazon S3 (ap-southeast-1) |
| **Email / MFA OTP** | Brevo HTTP API |
| **Payment Processing** | PayMongo API |
| **Access Control** | AWS IAM |

---

## System Architecture

```
User's Browser
      │
      │ HTTPS/TLS
      ▼
Railway Docker Container
  ├── Apache 2.4 (Web Server)
  ├── PHP 8.2 (Runtime)
  └── Laravel 12 (Application)
        │
        ├──► MySQL 8.0 (Railway Volume)     — App data
        ├──► AWS S3 (ap-southeast-1)        — Product images
        ├──► AWS IAM                        — S3 access policy
        ├──► Brevo API                      — Email & MFA OTP
        └──► PayMongo API                   — Payment processing

GitHub Repository
      │
      │ Push to main → auto-deploy
      ▼
Railway (rebuilds Docker image → runs entrypoint → serves app)
```

---

## Prerequisites

Before deploying, make sure you have accounts and credentials for:

- [Railway](https://railway.app) — app hosting
- [GitHub](https://github.com) — source code repository
- [AWS](https://aws.amazon.com) — S3 bucket + IAM user
- [Brevo](https://brevo.com) — transactional email
- [PayMongo](https://paymongo.com) — payment gateway

---

# 1. Setting Up the GitHub Repository

## Step 1: Clone or Fork the Repository

```bash
git clone https://github.com/your-username/tracknet.git
cd tracknet
```

## Step 2: Push to Your GitHub Repository

```bash
git add .
git commit -m "Initial commit"
git push origin main
```

---

# 2. Deploying to Railway

## Step 1: Create a Railway Project

1. Go to [https://railway.app](https://railway.app)
2. Click **New Project**
3. Select **Deploy from GitHub repo**
4. Choose your TrackNet repository
5. Railway will detect the `Dockerfile` and build automatically

## Step 2: Add a MySQL Database

1. In your Railway project dashboard, click **+ New**
2. Select **Database → MySQL**
3. Railway will provision a MySQL instance and link it to your service
4. Copy the `DATABASE_URL` or individual connection variables from the MySQL service

## Step 3: Set Environment Variables

In Railway → your service → **Variables**, add the following:

```env
# Application
APP_NAME=TrackNet
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-railway-domain.up.railway.app

# Database (from Railway MySQL service)
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your-mysql-password

# File Storage (AWS S3)
AWS_ACCESS_KEY_ID=your-iam-access-key
AWS_SECRET_ACCESS_KEY=your-iam-secret-key
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=your-bucket-name
AWS_SSL_VERIFY=false
FILESYSTEM_DISK=s3

# Email (Brevo HTTP API)
MAIL_MAILER=brevo
MAIL_FROM_ADDRESS=your-verified-sender@email.com
MAIL_FROM_NAME=TrackNet
BREVO_API_KEY=your-brevo-api-key

# Payment (PayMongo)
PAYMONGO_SECRET_KEY=your-paymongo-secret-key
PAYMONGO_PUBLIC_KEY=your-paymongo-public-key

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=debug
```

> Generate `APP_KEY` locally by running:
> ```bash
> php artisan key:generate --show
> ```

## Step 4: Deploy

Railway auto-deploys on every push to `main`. To manually redeploy:
1. Go to Railway → your service → **Deployments**
2. Click the three-dot menu → **Redeploy**

## Step 5: What Happens During Deployment

The `docker-entrypoint.sh` script runs automatically on every deploy:

```
1. Fix Apache MPM configuration
2. Cache config and routes (php artisan config:cache)
3. Cache routes (php artisan route:cache)
4. Compile Blade views (php artisan view:cache)
5. Run database migrations (php artisan migrate --force)
6. Seed database if empty (checks user and product count)
7. Start Apache
```

---

# 3. Setting Up AWS S3

## Step 1: Create an S3 Bucket

1. Go to [AWS S3](https://s3.console.aws.amazon.com)
2. Click **Create bucket**
3. Set:
   - **Bucket name**: `tracknetapp` (or your preferred name)
   - **Region**: `ap-southeast-1`
   - **Block Public Access**: Uncheck block for public read (required for product images)
4. Click **Create bucket**

## Step 2: Set Bucket Policy (Public Read for Products)

Go to your bucket → **Permissions** → **Bucket Policy**, paste:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "PublicReadProducts",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::your-bucket-name/products/*"
    }
  ]
}
```

## Step 3: Create IAM User for the Application

1. Go to [AWS IAM](https://console.aws.amazon.com/iam)
2. Click **Users → Create user**
3. Name: `tracknet-app`
4. Attach the following custom policy:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:PutObject",
        "s3:GetObject",
        "s3:DeleteObject",
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::your-bucket-name",
        "arn:aws:s3:::your-bucket-name/*"
      ]
    }
  ]
}
```

5. Go to the user → **Security credentials → Create access key**
6. Copy `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` into Railway variables

---

# 4. Setting Up Brevo (Transactional Email)

## Step 1: Create a Brevo Account

1. Go to [https://brevo.com](https://brevo.com) and sign up
2. Navigate to **Transactional → Email → API Settings**
3. Copy your API key

## Step 2: Add a Verified Sender

1. Go to **Settings (gear icon) → Senders, Domains, IPs**
2. Click **Add a sender**
3. Enter:
   - **From Name**: `TrackNet`
   - **From Email**: your Gmail or domain email
4. Click **Add this sender anyway**
5. Check your inbox and click the Brevo verification link

## Step 3: Add to Railway Variables

```env
MAIL_MAILER=brevo
BREVO_API_KEY=your-api-key-here
MAIL_FROM_ADDRESS=your-verified-email@gmail.com
MAIL_FROM_NAME=TrackNet
```

> Brevo uses HTTP API (port 443) — works on Railway which blocks all outbound SMTP ports.

---

# 5. Setting Up PayMongo (Payments)

## Step 1: Create a PayMongo Account

1. Go to [https://dashboard.paymongo.com](https://dashboard.paymongo.com)
2. Register and complete KYB (Know Your Business) verification
3. Go to **Developers → API Keys**
4. Copy your **Secret Key** and **Public Key**

## Step 2: Add to Railway Variables

```env
PAYMONGO_SECRET_KEY=sk_test_your_secret_key
PAYMONGO_PUBLIC_KEY=pk_test_your_public_key
```

---

# 6. Default Staff Accounts

After first deployment, the database is seeded with these accounts:

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Inventory | inventory@example.com | password |
| Sales | sales@example.com | password |

> Change these passwords immediately after first login in production.

---

# 7. Resetting the Database (Railway)

To wipe and reseed the database on Railway:

1. Go to Railway → your service → **Variables**
2. Add: `FORCE_RESEED=true`
3. Redeploy the service
4. After redeployment, **delete** the `FORCE_RESEED` variable

To remove non-staff user accounts (keep products):

1. Add: `CLEAN_TEST_USERS=true`
2. Redeploy
3. Delete the variable after deploy

---

# 8. Local Development Setup

```bash
# Clone the repo
git clone https://github.com/your-username/tracknet.git
cd tracknet

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure your .env with local DB credentials
# Then run migrations and seeders
php artisan migrate:fresh --seed

# Start local server
php artisan serve
```

Visit `http://localhost:8000`

---

# 9. Tech Stack Summary

| Component | Technology | Version |
|---|---|---|
| Framework | Laravel | 12.x |
| Language | PHP | 8.2 |
| Web Server | Apache | 2.4 |
| Database | MySQL | 8.0 |
| Frontend | Bootstrap | 5.3.3 |
| Container | Docker | latest |
| Hosting | Railway | — |
| Storage | AWS S3 | — |
| Email | Brevo API | — |
| Payment | PayMongo | — |
| PDF | DomPDF | — |
| MFA | Email OTP + TOTP | — |

---

## License

This project is developed for academic purposes.
