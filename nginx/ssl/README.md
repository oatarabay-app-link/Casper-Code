# SSL Certificates Setup

This directory should contain your SSL certificates for HTTPS support.

## Required Files

- `caspervpn.crt` - SSL certificate
- `caspervpn.key` - Private key
- `caspervpn-chain.crt` - Certificate chain (optional)

## Generating Self-Signed Certificates (for testing)

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout caspervpn.key \
  -out caspervpn.crt \
  -subj "/C=US/ST=State/L=City/O=CasperVPN/CN=caspervpn.com"
```

## Let's Encrypt (Production)

For production, use Let's Encrypt:

```bash
# Install certbot
apt-get update
apt-get install certbot python3-certbot-nginx

# Generate certificates
certbot --nginx -d caspervpn.com -d www.caspervpn.com

# Copy to this directory
cp /etc/letsencrypt/live/caspervpn.com/fullchain.pem ./caspervpn.crt
cp /etc/letsencrypt/live/caspervpn.com/privkey.pem ./caspervpn.key
```

## Security Notes

- **NEVER** commit actual SSL certificates to git
- Keep private keys secure with proper permissions (chmod 600)
- Rotate certificates before expiration
- Use strong key sizes (minimum 2048-bit RSA)
