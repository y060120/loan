# Loan
## Software Requirements

* PHP version above 8.x
* Laravel Framework version above 9.x
* Mysql above 8.x
* Node js latest for npm
* Install latest composer version

## Deploying instruction:

* Clone the Repository.
* Switch to master branch.
* Install latest & necessary Softwares.
* Create a Database of your choice with username and password and configure it in .env file.
* Create a new .env file and copy .env.example content there, replace DB_DATABASE, DB_USERNAME, DB_PASSWORD to your database credentials.
* And in .env file change USER_TOKEN accordingly with the generated User token while logging in, this is necessary for Feature testing only.
* After setting up the environment.
* Run from terminal or command prompt, the below given commands,
```
composer update
npm i & npm run dev
php artisan migrate                             // create tables
php artisan db:seed --class=UsersTableSeeder    // seed the database
php artisan serve                               // start the server
php artisan test                                // to run unit and feature testing               
```
## Points to be Noted

 * After starting the server, application will be launched in `http://localhost:8000/` and all the api's can be tested via Postman.
 * Postman Collection/openAPI documentation is shared via json, inside `public/postman-collection/Loan.postman_collection.json`.
 * If Feature test or Unit test fails change the values in the code accordingly that are stored in the Database.

## Application Architecture & Design

* Separated Authentication & Loan Related api's inside `/Modules` folder, in such way application design will be more clean, maintable and easy to scale.
* Created Traits for reusing the codes wherever its needed, in such a way code can be reused.
* Created localization for confirmation messages, and stored messages in a separate php file.
* Created Unit and Feature testing.
* Attached a postman collection for the api's.
* Created Database Migrations and seeding, after seeding you can use the sample credentials shown below,

    ```
    admin                               user
    uname:admin@admin.com               uname:user@gmail.com
    pwd:admin@1234                      pwd:abcd@1234
    ```
* Used Laravel Sanctum for Token Based Authentication
