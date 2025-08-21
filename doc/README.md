# Run
```
docker-composer up -d
```

# Migrate Database
```
php artisan migrate:install #first launch

php artisan migrate
```

# [Compiling Assets (Mix)](https://laravel.com/docs/8.x/mix)
```
// Watching Assets For Changes
npm run watch

// Run all Mix tasks...
npm run dev

// Run all Mix tasks and minify output...
npm run prod
```

# Config verify email
1. Set in env file
```
MAIL_USERNAME={your mail}
MAIL_PASSWORD={your app password}
MAIL_FROM_ADDRESS={your address for sending mail}
MAIL_FROM_NAME="${APP_NAME}"
```
2. [Config on email](https://support.google.com/accounts/answer/185833?hl=en&ctx=ch_b%2F0%2FUnlockCaptcha)
```
1. Go to your Google Account.
2. Select Security.
3. Under "Signing in to Google," select App Passwords. You may need to sign in. If you don’t have this option, it might be because:
2-Step Verification is not set up for your account.
2-Step Verification is only set up for security keys.
Your account is through work, school, or other organization.
You turned on Advanced Protection.
4. At the bottom, choose Select app and choose the app you using and then Select device and choose the device you’re using and then Generate.
5. Follow the instructions to enter the App Password. The App Password is the 16-character code in the yellow bar on your device.
6. Tap Done.
```
