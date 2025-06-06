# 🍽️ Catering Booking System - Docker Setup

## 📋 Prerequisites

Before starting, make sure you have:
- **Docker Desktop** installed and running
- **Git** (for cloning the repository)
- **Stop XAMPP** if it's running (to avoid port conflicts)

## 🚀 Step-by-Step Setup

### Step 1: Get the Project
```powershell
# Clone the repository
git clone <your-repository-url>
cd catering-booking

# OR if you already have the files, just open PowerShell in the project folder
```

### Step 2: Install Docker Desktop
1. Download from: https://www.docker.com/products/docker-desktop/
2. Install and restart your computer
3. Make sure Docker Desktop is running (check system tray)

### Step 3: Stop XAMPP (Important!)
- Open XAMPP Control Panel
- Stop **Apache** and **MySQL** services
- This prevents port conflicts

### Step 4: Start the Application
```powershell
# In the project directory, run:
docker-compose up -d
```

**Expected output:**
```
[+] Running 4/4
 ✔ Network catering-booking_default         Created
 ✔ Container catering-booking-db-1          Started
 ✔ Container catering-booking-phpmyadmin-1  Started
 ✔ Container catering-booking-web-1         Started
```

### Step 5: Access the Application
- **🌐 Website**: http://localhost:8080
- **🗄️ Database Management**: http://localhost:8081
  - Username: `root`
  - Password: `rootpassword`

## ✅ Verification

### Check if everything is working:
1. **Website**: Go to http://localhost:8080 - you should see the catering booking homepage
2. **Database**: Go to http://localhost:8081 - you should see phpMyAdmin with `catering_booking` database
3. **Tables**: In phpMyAdmin, check that tables like `admin`, `message`, `orders` are present

## 🛠️ Common Commands

### Stop the application:
```powershell
docker-compose down
```

### Start the application:
```powershell
docker-compose up -d
```

### View logs (if something goes wrong):
```powershell
docker-compose logs
```

### Rebuild containers (if you change Dockerfile):
```powershell
docker-compose down
docker-compose up -d --build
```

## 🚨 Troubleshooting

### Error: "Port already in use"
**Problem**: XAMPP or another service is using the ports
**Solution**:
1. Stop XAMPP completely
2. Run: `docker-compose down`
3. Run: `docker-compose up -d`

### Error: "Cannot connect to database"
**Problem**: Database container not ready
**Solution**:
1. Wait 30 seconds for MySQL to start
2. Check logs: `docker-compose logs db`
3. Restart: `docker-compose restart`

### Website shows "Forbidden"
**Problem**: This should not happen with current setup
**Solution**:
1. Go to http://localhost:8080/Project/ directly
2. Check containers are running: `docker-compose ps`

### Database not imported
**Problem**: `cateringdata.sql` not loaded
**Solution**:
1. Stop containers: `docker-compose down`
2. Remove volumes: `docker volume rm catering-booking_db_data`
3. Start again: `docker-compose up -d`

## 📊 Database Connection Details

For your PHP code, the database connection uses:
- **Host**: `db` (Docker internal network)
- **Database**: `catering_booking`
- **Username**: `root`
- **Password**: `rootpassword`
- **Port**: `3306` (internal), `3307` (external)

## 🔄 For Development

- **Live reload**: Your code changes are reflected immediately
- **Database persistence**: Data survives container restarts
- **No XAMPP needed**: Everything runs in Docker

## 👥 Team Collaboration

Each team member just needs to:
1. Install Docker Desktop
2. Clone this repository
3. Run `docker-compose up -d`
4. Access http://localhost:8080

**No more "it works on my machine" problems!** 🎉

## 📞 Need Help?

If you encounter issues:
1. Check this troubleshooting section
2. Share the error message with the team
3. Run `docker-compose logs` to see detailed logs

---

## 📁 Project Structure
```
catering-booking/
├── Project/               # Main application files
│   ├── index.php         # Homepage
│   ├── login.php         # Login page
│   ├── menu.php          # Menu page
│   └── includes/         # CSS, JS, and images
├── docker-compose.yml    # Docker configuration
├── Dockerfile           # PHP/Apache setup
└── cateringdata.sql     # Database schema
```

## Prerequisites
- Install Docker Desktop for Windows from https://www.docker.com/products/docker-desktop/
- Make sure Docker Desktop is running

## Setup Instructions

### 1. First Time Setup
Open PowerShell in your project directory and run:

```powershell
# Build and start all containers
docker-compose up -d

# Check if containers are running
docker-compose ps
```

### 2. Access Your Application
- **Website**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Username: `root`
  - Password: `rootpassword`

### 3. Database Setup
The `cateringdata.sql` file will be automatically imported when you first start the containers.

### 4. Development Workflow
- Make changes to your PHP files
- Changes will be reflected immediately (no need to restart containers)
- Database data persists between container restarts

### 5. Useful Commands

```powershell
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs

# Restart containers
docker-compose restart

# Remove containers and data (careful!)
docker-compose down -v
```

### 6. Team Collaboration
Each team member needs to:
1. Install Docker Desktop
2. Clone this repository
3. Run `docker-compose up -d`
4. Access the application at http://localhost:8080

### 7. Troubleshooting
- If port 8080 is already in use, change it in `docker-compose.yml`
- If containers won't start, check Docker Desktop is running
- Use `docker-compose logs` to see error messages

## Files Added for Docker
- `Dockerfile` - PHP/Apache configuration
- `docker-compose.yml` - Multi-container setup
- `.dockerignore` - Files to exclude from Docker build
