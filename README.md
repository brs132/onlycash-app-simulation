# OnlyCash App Simulation

A responsive web application simulating a cash rewards app with user authentication and interactive features.

## Features

- User registration and login system
- Secure password hashing
- Session management
- Responsive design for mobile and desktop
- Interactive UI elements
- Simulated rewards and withdrawal system

## Installation Instructions

1. Upload all files to your HostGator public_html directory (or a subdirectory)
2. Ensure proper file permissions:
   ```bash
   chmod 644 *.php *.html *.css *.js
   chmod 666 db.json
   ```
3. Make sure PHP is enabled on your hosting (default on HostGator)

## File Structure

- `index.html` - Landing page
- `login.php` - Login page and authentication handler
- `register.php` - Registration page and user creation
- `app.php` - Main application interface
- `logout.php` - Session termination
- `style.css` - Styling for all pages
- `db.json` - User data storage

## Security Considerations

1. The `db.json` file must be writable by PHP but should be protected from direct web access
2. All passwords are securely hashed using PHP's password_hash() function
3. Input validation and sanitization is implemented
4. Session management is properly handled

## Usage

1. Access the application through your domain: `http://yourdomain.com/path-to-app/`
2. Create a new account through the registration page
3. Log in with your credentials
4. Explore the simulated app interface

## Troubleshooting

1. If registration fails:
   - Check if db.json is writable by PHP
   - Verify PHP has write permissions in the directory

2. If login fails:
   - Ensure sessions are enabled in PHP
   - Check if cookies are enabled in the browser

3. If styling is missing:
   - Verify all files were uploaded correctly
   - Check file permissions

## Maintenance

- Regularly backup the db.json file
- Monitor file permissions
- Keep PHP version updated
- Check error logs for potential issues

## Security Best Practices

1. Always use HTTPS in production
2. Regularly update PHP to the latest stable version
3. Implement rate limiting for login attempts
4. Monitor access logs for suspicious activity
5. Keep backups of user data
