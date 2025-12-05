import React, { useState, useEffect } from 'react';
import { Container, Typography, Box, Card, CardContent, Grid } from '@mui/material';
import axios from 'axios';

function App() {
  const [servers, setServers] = useState([]);
  const [status, setStatus] = useState(null);

  useEffect(() => {
    const apiUrl = process.env.REACT_APP_API_URL || 'http://localhost:8080';
    
    axios.get(`${apiUrl}/api/vpn/servers`)
      .then(res => setServers(res.data))
      .catch(err => console.error('Error fetching servers:', err));
    
    axios.get(`${apiUrl}/api/vpn/status`)
      .then(res => setStatus(res.data))
      .catch(err => console.error('Error fetching status:', err));
  }, []);

  return (
    <Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
      <Typography variant="h3" component="h1" gutterBottom>
        CasperVPN Admin Panel
      </Typography>
      
      <Box sx={{ mt: 4 }}>
        <Typography variant="h5" gutterBottom>
          System Status
        </Typography>
        {status && (
          <Card>
            <CardContent>
              <Typography>Status: {status.status}</Typography>
              <Typography>Server: {status.server}</Typography>
              <Typography>Uptime: {status.uptime}</Typography>
            </CardContent>
          </Card>
        )}
      </Box>

      <Box sx={{ mt: 4 }}>
        <Typography variant="h5" gutterBottom>
          VPN Servers
        </Typography>
        <Grid container spacing={2}>
          {servers.map(server => (
            <Grid item xs={12} md={4} key={server.id}>
              <Card>
                <CardContent>
                  <Typography variant="h6">{server.name}</Typography>
                  <Typography>Location: {server.location}</Typography>
                  <Typography>Load: {server.load}%</Typography>
                </CardContent>
              </Card>
            </Grid>
          ))}
        </Grid>
      </Box>
    </Container>
  );
}

export default App;
