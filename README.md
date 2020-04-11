# GuestBook

This app allows users to leave messages on the site. 

## Requirements

* PHP 7.2 =<
* Composer
* Web server **Apache2**

## How to Deploy

1. Cloning repository: `git clone https://github.com/0Jac0k19D01rupal0/GuestBook.git`
2. Rename project forder `mv guestbook <your_domain>` and enter folder `cd <you_domain>`
3. Setting **.env**:
    1. Rename .env.example: `mv .env.example .env`
    2. Configure **.env** 
    ```Apache
    # Changing the development environment to prod
    APP_ENV=dev
    # Build connecting
    DATABASE_URL=mysql://db_user:db_password@db_ip:3306/db_name
    
    # Configure Swift Mailer
    # For Gmail as a transport, use: "gmail://username:password@localhost"
    # For a generic SMTP server, use: "smtp://localhost:25?encryption=&auth_mode="
    MAILER_URL=gmail://gmail_username:gmail_password@localhost
    ```
4. Installing composer packages `composer install`
5. Clearing cache `php bin/console cache:clear --env=prod --no-debug`
6. Install CKEditor Bundle `php bin/console ckeditor:install`
7. Install the Assets `php bin/console assets:install public`
8. Configure database:
    1. Create base `php bin/console doctrine:database:create`
    2. Make migrate `php bin/console doctrine:migrations:migrate`
9. Add permissoins `chmod 777 public/uploads/pictures`
10. Go to your site


## How load fixtures

`php bin/console doctrine:fixtures:load`

## How add Admin

Admin can be added using the command:
`php bin/console guestbook:add-admin <username>`
where `<username>` - it is username new admin
