# CasperVPN

A comprehensive VPN solution with modern architecture, built for scalability and ease of deployment.

## 🚀 Quick Start

```bash
# Clone the repository
git clone https://github.com/yourusername/caspervpn.git
cd caspervpn

# Initial setup
./scripts/setup.sh

# Start development environment
./scripts/deploy.sh dev

# Check status
./scripts/health-check.sh
```

**Access Services:**
- React Admin: http://localhost:3000
- API: http://localhost:8080
- PHP Admin: http://localhost:9000
- Server Agent: http://localhost:8081
- Grafana: http://localhost:3001

## 📋 Table of Contents

- [Architecture](#architecture)
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Development](#development)
- [Deployment](#deployment)
- [Monitoring](#monitoring)
- [Documentation](#documentation)
- [Contributing](#contributing)
- [License](#license)

## 🏗️ Architecture

CasperVPN uses a microservices architecture with the following components:

```
┌─────────────────────────────────────────────┐
│           Nginx Reverse Proxy               │
│         (Load Balancer & SSL)               │
└────┬──────────┬──────────┬──────────┬───────┘
     │          │          │          │
┌────▼────┐ ┌──▼──────┐ ┌─▼────────┐ ┌▼───────┐
│ .NET    │ │ React   │ │ PHP      │ │ Rust   │
│ API     │ │ Admin   │ │ Admin    │ │ Agent  │
└────┬────┘ └──┬──────┘ └─┬────────┘ └────────┘
     │         │           │
     └─────────┴───────────┘
               │
     ┌─────────┴──────────┐
     │                    │
┌────▼─────┐       ┌─────▼────┐
│PostgreSQL│       │  Redis   │
└──────────┘       └──────────┘
```

### Components

| Component | Technology | Purpose |
|-----------|-----------|---------|
| **API Backend** | .NET Core 8.0 | Main backend API |
| **React Admin** | React 18 + Material-UI | Modern admin interface |
| **PHP Admin** | Laravel 10 | Legacy admin panel |
| **Server Agent** | Rust + Actix-web | Server monitoring |
| **Database** | PostgreSQL 16 | Data persistence |
| **Cache** | Redis 7 | Caching layer |
| **Proxy** | Nginx 1.25 | Reverse proxy & SSL |
| **Monitoring** | Prometheus + Grafana | Metrics & dashboards |

## ✨ Features

### Core Features
- ✅ Multi-service architecture
- ✅ RESTful API
- ✅ Modern admin interface
- ✅ Real-time monitoring
- ✅ Automated backups
- ✅ SSL/TLS support
- ✅ Rate limiting
- ✅ Health checks

### DevOps Features
- ✅ Docker containerization
- ✅ Docker Compose orchestration
- ✅ CI/CD pipeline (GitHub Actions)
- ✅ Prometheus monitoring
- ✅ Grafana dashboards
- ✅ Automated alerts
- ✅ Log aggregation
- ✅ Backup/restore scripts

### Security Features
- ✅ Non-root containers
- ✅ Minimal base images
- ✅ Environment-based secrets
- ✅ CORS configuration
- ✅ Security headers
- ✅ Rate limiting
- ✅ SSL/TLS encryption

## 📦 Prerequisites

### Required
- **Docker** 20.10+
- **Docker Compose** 2.0+
- **Git** 2.0+

### Optional
- **AWS CLI** (for S3 backups)
- **certbot** (for SSL certificates)

### System Requirements

**Development:**
- CPU: 4 cores
- RAM: 8 GB
- Disk: 20 GB

**Production:**
- CPU: 8+ cores
- RAM: 16+ GB
- Disk: 100+ GB SSD

## 🔧 Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/caspervpn.git
cd caspervpn
```

### 2. Run Setup

```bash
chmod +x scripts/*.sh
./scripts/setup.sh
```

### 3. Configure Environment

```bash
# Edit .env file
nano .env

# Update these values:
# - Database passwords
# - Redis password
# - JWT secret
# - SMTP settings
```

### 4. Start Services

```bash
# Development
./scripts/deploy.sh dev

# Production
./scripts/deploy.sh production
```

## 💻 Development

### Running Locally

```bash
# Start development environment
./scripts/deploy.sh dev

# View logs
./scripts/logs.sh all -f

# Run health check
./scripts/health-check.sh
```

### Making Changes

Changes are auto-reloaded in development mode:
- **React:** Instant hot reload
- **.NET:** Hot reload with dotnet watch
- **Rust:** Auto-rebuild with cargo-watch
- **PHP:** Immediate reflection

### Running Tests

```bash
# .NET tests
docker-compose exec api dotnet test

# React tests
docker-compose exec admin-react npm test

# Rust tests
docker-compose exec server-agent cargo test

# PHP tests
docker-compose exec admin-php php artisan test
```

### Database Management

```bash
# Access database
docker-compose exec postgres psql -U casperuser -d caspervpn

# Or use pgAdmin
open http://localhost:5050
```

## 🚀 Deployment

### Staging Deployment

```bash
# On staging server
cd /opt/caspervpn
git pull origin develop
./scripts/backup.sh
./scripts/deploy.sh staging
./scripts/health-check.sh
```

### Production Deployment

```bash
# On production server
cd /opt/caspervpn
./scripts/backup.sh
git pull origin main
./scripts/deploy.sh production
./scripts/health-check.sh
```

### CI/CD

Automated deployments via GitHub Actions:
- **Staging:** Auto-deploy on push to `develop`
- **Production:** Manual approval required for `main`

## 📊 Monitoring

### Access Monitoring Tools

- **Grafana:** http://localhost:3001 (admin/admin123)
- **Prometheus:** http://localhost:9090
- **Alertmanager:** http://localhost:9093

### Start Monitoring Stack

```bash
cd monitoring
docker-compose -f docker-compose.monitoring.yml up -d
```

### Key Metrics

- Service health (up/down)
- CPU/Memory usage
- Request rate & latency
- Error rates
- Database metrics
- Cache performance

## 📚 Documentation

Comprehensive documentation available in `/docs`:

- **[DevOps Overview](docs/DEVOPS.md)** - Architecture & infrastructure
- **[Deployment Guide](docs/DEPLOYMENT.md)** - Step-by-step deployment
- **[Local Development](docs/LOCAL_DEVELOPMENT.md)** - Development workflow
- **[Monitoring Guide](docs/MONITORING.md)** - Metrics & alerting
- **[Troubleshooting](docs/TROUBLESHOOTING.md)** - Common issues & solutions

## 🛠️ Helper Scripts

Located in `/scripts`:

| Script | Purpose |
|--------|---------|
| `setup.sh` | Initial environment setup |
| `deploy.sh` | Deploy to different environments |
| `backup.sh` | Backup database & config |
| `restore.sh` | Restore from backup |
| `logs.sh` | View service logs |
| `health-check.sh` | Check service health |

## 🔐 Security

### Best Practices

- All containers run as non-root
- Secrets via environment variables
- SSL/TLS for production
- Regular security updates
- Automated vulnerability scanning

### Secrets Management

**Development:** `.env` file (not committed)  
**Production:** Environment variables from secure storage  
**CI/CD:** GitHub Secrets

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards

- Follow language-specific style guides
- Write tests for new features
- Update documentation
- Run linters before committing

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **Omar** - Founder & Lead Developer

## 📞 Support

- **Documentation:** [/docs](docs/)
- **Issues:** [GitHub Issues](https://github.com/yourusername/caspervpn/issues)
- **Email:** support@caspervpn.com

## 🎯 Roadmap

- [ ] Kubernetes deployment
- [ ] Multi-region support
- [ ] Advanced analytics
- [ ] Mobile apps (iOS & Android)
- [ ] API v2 with GraphQL
- [ ] Terraform infrastructure

## 🙏 Acknowledgments

- ProtonVPN (iOS app inspiration)
- Open source community
- All contributors

---

**Version:** 1.0.0  
**Last Updated:** December 5, 2025  
**Status:** Production Ready 🚀

For detailed information, see the [documentation](docs/).
