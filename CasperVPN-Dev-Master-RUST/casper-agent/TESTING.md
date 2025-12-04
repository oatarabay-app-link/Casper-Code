# Quick Testing Guide for Casper Agent

## Quick Start (5 minutes)

### 1. Build the Agent
```bash
cd casper-agent
cargo build --release
```

### 2. Configure
```bash
cp .env.example .env
```

Edit `.env`:
```env
API_BASE_URL=http://localhost:5166
HEALTH_CHECK_INTERVAL_SECS=10
```

### 3. Start API Server
```bash
cd ../Casper.API
dotnet run
```

### 4. Run Agent
```bash
cd ../casper-agent
cargo run
```

## What to Expect

The agent will:
1. Register itself with the API server
2. Send status updates every 10 seconds
3. Fetch and display settings from the server

### Expected Output
```
Server registered: DESKTOP-ABC123 (192.168.1.100)
Starting periodic status updates every 10 seconds...
Status update sent - Load: 15%, CPU: 10.5%, Mem: 35.2%, Users: 0
Fetched settings - MaxUsers: 200, ConnTimeout: 30s, HealthCheck: 60s
Status update sent - Load: 18%, CPU: 12.3%, Mem: 36.1%, Users: 0
...
```

## Testing Checklist

- [ ] Agent successfully registers with API
- [ ] Status updates are sent periodically
- [ ] Settings are fetched from API
- [ ] Metrics show realistic values (CPU, memory, disk)
- [ ] API endpoints respond correctly

## API Endpoints to Test

### 1. Check Server Registration
```bash
curl http://localhost:5166/api/Server/all
```

### 2. Manually Update Status
```bash
curl -X PUT http://localhost:5166/api/Server/status \
  -H "Content-Type: application/json" \
  -d '{
    "serverName": "test-server",
    "serverStatus": 0,
    "connectedUsers": 10,
    "load": 45,
    "cpuUsage": 35.5,
    "memoryUsage": 60.2,
    "diskUsage": 55.8
  }'
```

### 3. Fetch Settings
```bash
curl http://localhost:5166/api/Server/settings/test-server
```

## Common Issues

### "Missing API_BASE_URL env var"
- Ensure `.env` file exists in `casper-agent/` directory
- Check `API_BASE_URL` is set

### "Connection refused"
- Start the ASP.NET API server first
- Verify port 5166 is correct (check `dotnet run` output)

### Metrics showing 0.0%
- This is expected on Windows
- For full metrics, test on Linux

### "Server not found" when fetching settings
- The server must be registered first
- Check server name matches (use hostname from registration output)

## Advanced Testing

### Test on Linux VM
```bash
# Use WSL or a Linux VM for full functionality
wsl
cd /mnt/c/Users/saqib/source/repos/CasperVPN/casper-agent
cargo run
```

### Test with Different Intervals
Edit `.env`:
```env
HEALTH_CHECK_INTERVAL_SECS=5  # Very fast updates
```

### Monitor API Database
```bash
# View servers in database
curl http://localhost:5166/api/Server/all | jq
```

## Performance Testing

### CPU Load Test
Run multiple instances:
```bash
# Terminal 1
API_BASE_URL=http://localhost:5166 SERVER_LOCATION=US-East cargo run

# Terminal 2
API_BASE_URL=http://localhost:5166 SERVER_LOCATION=US-West cargo run
```

### Long-Running Test
```bash
# Run for 1 hour and monitor
cargo run > agent-log.txt 2>&1 &
tail -f agent-log.txt
```

## Success Criteria

✅ Agent registers successfully  
✅ Periodic updates appear in console  
✅ API receives and stores metrics  
✅ Settings endpoint returns configuration  
✅ No errors in agent or API logs  
✅ Metrics update in real-time
