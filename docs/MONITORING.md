# CasperVPN Monitoring Guide

## 📋 Table of Contents

- [Overview](#overview)
- [Monitoring Stack](#monitoring-stack)
- [Getting Started](#getting-started)
- [Prometheus](#prometheus)
- [Grafana Dashboards](#grafana-dashboards)
- [Alerting](#alerting)
- [Metrics Collection](#metrics-collection)
- [Log Management](#log-management)
- [Performance Monitoring](#performance-monitoring)
- [Capacity Planning](#capacity-planning)

## Overview

CasperVPN uses a comprehensive monitoring stack based on Prometheus and Grafana to provide real-time insights into system health, performance, and user experience.

### Monitoring Architecture

```
┌─────────────────┐
│   Applications  │
│  (API, Admin,   │
│   Agent, etc.)  │
└────────┬────────┘
         │ /metrics
         ▼
┌─────────────────┐
│   Prometheus    │◄──────┐
│ (Metrics Store) │       │
└────────┬────────┘       │
         │                │
         ▼                │
┌─────────────────┐       │
│    Grafana      │       │
│  (Dashboards)   │       │
└─────────────────┘       │
                          │
┌─────────────────┐       │
│  Alertmanager   │◄──────┘
│   (Alerts)      │
└─────────────────┘
         │
         ▼
  Email/Slack/Discord
```

## Monitoring Stack

### Components

| Component | Purpose | Port | URL |
|-----------|---------|------|-----|
| Prometheus | Metrics collection & storage | 9090 | http://localhost:9090 |
| Grafana | Visualization & dashboards | 3001 | http://localhost:3001 |
| Alertmanager | Alert management | 9093 | http://localhost:9093 |
| Node Exporter | System metrics | 9100 | http://localhost:9100/metrics |
| Postgres Exporter | Database metrics | 9187 | http://localhost:9187/metrics |
| Redis Exporter | Cache metrics | 9121 | http://localhost:9121/metrics |
| cAdvisor | Container metrics | 8080 | http://localhost:8080 |

## Getting Started

### Start Monitoring Stack

```bash
# Start main services first
./scripts/deploy.sh dev

# Start monitoring stack
cd monitoring
docker-compose -f docker-compose.monitoring.yml up -d

# Verify monitoring services
docker-compose -f docker-compose.monitoring.yml ps
```

### Access Monitoring Tools

#### Prometheus
- **URL:** http://localhost:9090
- **No authentication required in development**
- **Query metrics, view targets, check alerts**

#### Grafana
- **URL:** http://localhost:3001
- **Username:** `admin`
- **Password:** `admin123` (change in production!)
- **Pre-configured dashboards available**

#### Alertmanager
- **URL:** http://localhost:9093
- **View active alerts and silences**

## Prometheus

### Accessing Prometheus

```bash
# Open in browser
open http://localhost:9090

# Or via curl
curl http://localhost:9090/api/v1/query?query=up
```

### Key Endpoints

- **Targets:** http://localhost:9090/targets - View all scrape targets
- **Alerts:** http://localhost:9090/alerts - View active alerts
- **Config:** http://localhost:9090/config - View Prometheus config
- **Rules:** http://localhost:9090/rules - View alerting rules

### Common Queries

#### Service Health
```promql
# Check if services are up
up

# Service uptime
up * time()

# Services down count
count(up == 0)
```

#### CPU Usage
```promql
# CPU usage by service
rate(process_cpu_seconds_total[5m]) * 100

# System CPU usage
100 - (avg by (instance) (rate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)
```

#### Memory Usage
```promql
# Memory usage percentage
(node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes) / node_memory_MemTotal_bytes * 100

# Memory by service
container_memory_usage_bytes{name=~"caspervpn.*"}
```

#### HTTP Metrics
```promql
# Request rate
rate(http_requests_total[5m])

# Error rate
rate(http_requests_total{status=~"5.."}[5m])

# Request duration (95th percentile)
histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m]))
```

#### Database Metrics
```promql
# Active connections
pg_stat_database_numbackends

# Database size
pg_database_size_bytes

# Query duration
rate(pg_stat_statements_total_time[5m])
```

## Grafana Dashboards

### Access Dashboards

1. Open http://localhost:3001
2. Login with `admin` / `admin123`
3. Navigate to Dashboards → Browse
4. Select "CasperVPN" folder

### Pre-configured Dashboards

#### 1. CasperVPN Overview
**Purpose:** High-level system overview

**Panels:**
- Service status (up/down)
- CPU usage by service
- Memory usage
- HTTP request rate
- Error rate
- Response time (p95, p99)

**Refresh:** 30s auto-refresh

#### 2. API Performance
**Purpose:** Detailed API metrics

**Panels:**
- Request rate by endpoint
- Response time by endpoint
- Error rate by status code
- Request payload size
- Response payload size

#### 3. Database Health
**Purpose:** PostgreSQL monitoring

**Panels:**
- Active connections
- Database size
- Transaction rate
- Query duration
- Cache hit ratio
- Lock wait time

#### 4. Infrastructure
**Purpose:** System resources

**Panels:**
- CPU usage (system)
- Memory usage (system)
- Disk usage
- Network I/O
- Container metrics

#### 5. Redis Monitoring
**Purpose:** Cache performance

**Panels:**
- Connected clients
- Commands per second
- Memory usage
- Hit rate
- Evictions

### Creating Custom Dashboards

1. Click "+" → Dashboard
2. Add Panel
3. Select Prometheus datasource
4. Enter PromQL query
5. Configure visualization
6. Save dashboard

### Dashboard Variables

Use variables for dynamic dashboards:

```
Service: $service
Time Range: $__timeFilter
Refresh: $__rate_interval
```

## Alerting

### Alert Rules

Alerts are defined in `monitoring/prometheus/alerts/service-alerts.yml`

#### Critical Alerts

**ServiceDown**
- **Trigger:** Service unreachable for 2 minutes
- **Severity:** Critical
- **Action:** Page on-call engineer

**HighErrorRate**
- **Trigger:** Error rate > 5% for 5 minutes
- **Severity:** Critical
- **Action:** Investigate immediately

**DatabaseConnectionIssues**
- **Trigger:** Cannot connect to database
- **Severity:** Critical
- **Action:** Check database status

#### Warning Alerts

**HighCPUUsage**
- **Trigger:** CPU > 80% for 5 minutes
- **Severity:** Warning
- **Action:** Check for resource-intensive tasks

**HighMemoryUsage**
- **Trigger:** Memory > 85% for 5 minutes
- **Severity:** Warning
- **Action:** Consider scaling

**DiskSpaceLow**
- **Trigger:** Disk space < 15%
- **Severity:** Warning
- **Action:** Clean up or expand storage

### Viewing Alerts

#### In Prometheus
```bash
# View in browser
open http://localhost:9090/alerts

# Via API
curl http://localhost:9090/api/v1/alerts
```

#### In Alertmanager
```bash
# View active alerts
open http://localhost:9093/#/alerts

# Silence an alert
curl -X POST http://localhost:9093/api/v1/silences \
  -H "Content-Type: application/json" \
  -d @silence.json
```

### Configuring Alert Notifications

Edit `monitoring/alertmanager/alertmanager.yml`:

#### Email Notifications
```yaml
receivers:
  - name: 'email'
    email_configs:
      - to: 'team@caspervpn.com'
        from: 'alerts@caspervpn.com'
        smarthost: 'smtp.gmail.com:587'
        auth_username: 'alerts@caspervpn.com'
        auth_password: 'your-password'
```

#### Slack Notifications
```yaml
receivers:
  - name: 'slack'
    slack_configs:
      - api_url: 'https://hooks.slack.com/services/YOUR/WEBHOOK/URL'
        channel: '#alerts'
        text: '{{ range .Alerts }}{{ .Annotations.description }}{{ end }}'
```

#### Discord Notifications
```yaml
receivers:
  - name: 'discord'
    webhook_configs:
      - url: 'https://discord.com/api/webhooks/YOUR/WEBHOOK'
        send_resolved: true
```

### Testing Alerts

```bash
# Trigger a test alert
curl -X POST http://localhost:9093/api/v1/alerts \
  -H "Content-Type: application/json" \
  -d '[{
    "labels": {
      "alertname": "TestAlert",
      "severity": "warning"
    },
    "annotations": {
      "summary": "Test alert",
      "description": "This is a test alert"
    }
  }]'
```

## Metrics Collection

### Adding Metrics to Your Service

#### .NET Core
```csharp
using Prometheus;

// Counter
private static readonly Counter RequestCounter = Metrics
    .CreateCounter("http_requests_total", "Total HTTP requests");

// Histogram
private static readonly Histogram RequestDuration = Metrics
    .CreateHistogram("http_request_duration_seconds", "HTTP request duration");

// Gauge
private static readonly Gauge ActiveConnections = Metrics
    .CreateGauge("active_connections", "Active database connections");

// Usage
RequestCounter.Inc();
using (RequestDuration.NewTimer())
{
    // Your code
}
```

#### Node.js/React
```javascript
const client = require('prom-client');

// Create metrics
const httpRequestsTotal = new client.Counter({
  name: 'http_requests_total',
  help: 'Total HTTP requests',
  labelNames: ['method', 'status']
});

// Increment
httpRequestsTotal.inc({ method: 'GET', status: 200 });
```

#### Rust
```rust
use prometheus::{Counter, Encoder, TextEncoder};

// Create counter
lazy_static! {
    static ref HTTP_COUNTER: Counter = Counter::new(
        "http_requests_total", "Total HTTP requests"
    ).unwrap();
}

// Increment
HTTP_COUNTER.inc();

// Export metrics
let encoder = TextEncoder::new();
let metric_families = prometheus::gather();
encoder.encode(&metric_families, &mut buffer).unwrap();
```

### Custom Metrics Best Practices

1. **Use descriptive names:** `http_requests_total` not `requests`
2. **Include units:** `http_request_duration_seconds` not `http_request_duration`
3. **Use labels wisely:** Don't create too many unique label combinations
4. **Choose correct metric type:**
   - Counter: Monotonically increasing (requests, errors)
   - Gauge: Can go up and down (memory, connections)
   - Histogram: Distribution of values (latency, size)
   - Summary: Similar to histogram with percentiles

## Log Management

### Viewing Logs

```bash
# All services
./scripts/logs.sh all -f

# Specific service
./scripts/logs.sh api -f

# With grep
./scripts/logs.sh api -f | grep ERROR

# Last 100 lines
./scripts/logs.sh api --tail=100
```

### Log Aggregation

#### Docker Logs Driver

Configure in `docker-compose.yml`:
```yaml
services:
  api:
    logging:
      driver: "json-file"
      options:
        max-size: "10m"
        max-file: "3"
```

#### Future: ELK Stack

For production, consider implementing:
- **Elasticsearch:** Log storage
- **Logstash:** Log processing
- **Kibana:** Log visualization

### Structured Logging

#### .NET Core
```csharp
_logger.LogInformation("User {UserId} logged in from {IpAddress}", 
    userId, ipAddress);
```

#### Node.js
```javascript
logger.info('User logged in', { 
    userId: user.id, 
    ipAddress: req.ip 
});
```

## Performance Monitoring

### Key Performance Indicators (KPIs)

#### Application Performance
- **Response Time:** p50, p95, p99 latency
- **Throughput:** Requests per second
- **Error Rate:** Percentage of failed requests
- **Availability:** Uptime percentage

#### Infrastructure Performance
- **CPU Usage:** Average CPU across all services
- **Memory Usage:** Memory consumption
- **Disk I/O:** Read/write operations
- **Network I/O:** Inbound/outbound traffic

### Performance Queries

```promql
# Average response time (last 5 minutes)
avg(rate(http_request_duration_seconds_sum[5m]) / rate(http_request_duration_seconds_count[5m]))

# 95th percentile response time
histogram_quantile(0.95, rate(http_request_duration_seconds_bucket[5m]))

# Requests per second
sum(rate(http_requests_total[5m]))

# Error rate
sum(rate(http_requests_total{status=~"5.."}[5m])) / sum(rate(http_requests_total[5m])) * 100
```

### Setting Performance Baselines

1. Monitor for 1-2 weeks under normal load
2. Record p50, p95, p99 latencies
3. Set alert thresholds at 2x baseline
4. Review and adjust monthly

## Capacity Planning

### Resource Trends

Monitor these metrics over time:
- CPU usage growth
- Memory usage growth
- Disk usage growth
- Request rate growth

### Scaling Indicators

**Time to scale up:**
- CPU consistently > 70%
- Memory consistently > 80%
- Response time consistently > SLA
- Error rate increasing

**Time to scale out:**
- Single instance at capacity
- Geographic distribution needed
- High availability required

### Forecasting

Use Prometheus queries to predict future needs:

```promql
# Predict memory usage in 7 days (linear regression)
predict_linear(node_memory_MemUsed_bytes[7d], 7 * 24 * 3600)

# Predict disk usage
predict_linear(node_filesystem_size_bytes[30d], 30 * 24 * 3600)
```

## Best Practices

1. **Monitor everything:** Application, infrastructure, business metrics
2. **Set meaningful alerts:** Avoid alert fatigue
3. **Document baselines:** Know what's normal
4. **Review regularly:** Weekly/monthly metric reviews
5. **Test alerts:** Ensure notifications work
6. **Automate responses:** Where possible
7. **Keep dashboards simple:** Focus on actionable metrics
8. **Use tags/labels:** For filtering and grouping

## Troubleshooting Monitoring

### Prometheus Not Scraping

```bash
# Check targets
curl http://localhost:9090/api/v1/targets

# Check service metrics endpoint
curl http://localhost:8080/metrics

# View Prometheus logs
docker-compose -f monitoring/docker-compose.monitoring.yml logs prometheus
```

### Grafana Not Showing Data

1. Check Prometheus datasource: Configuration → Data Sources
2. Test connection
3. Verify query syntax
4. Check time range
5. View browser console for errors

### Missing Metrics

```bash
# Check if service exposes metrics
curl http://service:port/metrics

# Check Prometheus config
curl http://localhost:9090/api/v1/targets

# Reload Prometheus config
curl -X POST http://localhost:9090/-/reload
```

## Support Resources

- [Prometheus Documentation](https://prometheus.io/docs/)
- [Grafana Documentation](https://grafana.com/docs/)
- [PromQL Cheat Sheet](https://promlabs.com/promql-cheat-sheet/)
- Internal: [Troubleshooting Guide](./TROUBLESHOOTING.md)

---

**Last Updated:** December 5, 2025  
**Version:** 1.0.0
