####################################
Job Challenge

This is a small, simple and lightweight Laravel application designed for optimal performance and reduced memory consumption for large JSON processing.

This application is a Laravel Command that parses, process and persist JSON objects in a Database Relational Model.

The database structure is made for scalable and future features, separating clients from credit cards and addresses (these could eventually became 1-N relationships, because clients can have multiple credit cards and addresses)

Taking scalability in mind, the addresses table is expanded into multiple attributes and the scripts tries to parse a single line address (but not 100% accurate, because there is no default format for the addresses in JSON given).

Both age and credit card requirements were implemented as simples as it can be.

As a command laravel application, the process can traced throught process manager (ps) and be killed at any moment, aswell with Ctrl+C in terminal.
This program uses batch processing concept where it starts, it checks if there are some batch pending, if so, it continues where it stopped before, or just create a new batch and starting parsing JSON from the beginning. There is no ID defined in the JSON, so the program uses the JSON's array index to know what item is beeing processed or in which has been stopped.

As asked, there were not much used ORM/Eloquent, just the basics for the solution can be object oriented and using models for persistence.

In my machine (both app and database server) I really had a nice performance having the JSON fully processed in around 20 seconds, so I don't think a larger file could became a problem. If this could be the case the script easily can be converted to work with index ranges to process inside the JSON, so that could be many processes of these program to work in different ranges of the JSON file.

The script also can be called with an argument pointing out what file should be processed. challenge.json is the default, if none is set.

I had use Database transactions together with the batches design to avoid duplicity when stopping/starting the process, or if there are any errors, the transactions (per item) is not committed.
Only when a registry is successfully persisted, the transactions is committed. If the JSON is fully processed, the batch is marked as finished, and another new processing will start over again.

Get start: 

git clone https://github.com/danieljs777/holland_challenge.git

composer update

sudo cp .env.example .env

Set the database connection in .env

php artisan migrate

php artisan import:json
