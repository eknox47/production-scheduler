# Production Scheduling App

Hey, welcome to my little production scheduling app!

This was my first time building a scheduling algorithm, so I got to learn a bit about how they work. I came across a few more complex approaches online, but decided to go with a middle ground: a simple **greedy Earliest Due Date (EDD)** algorithm.

I initially just ordered the production jobs by need-by date. While that worked, it could waste time switching between product types. I then added logic to reduce unnecessary product type changes, which produced better results. I decided to stop there rather than overcomplicate the algorithm, but I think it does the job well for this application.

I decided to have the scheduler operate between **9 AM and 5 PM on weekdays**. This is easily changeable in the code if production needs to run on weekends or during the night as well.

Orders can contain multiple products. The form allows you to add additional product sections, but only products belonging to the same product type can be selected. The frontend simply removes incompatible products from the select options. I did get some help from AI with that part since it's been a good while since I've written raw JavaScript. In React, I probably would have gotten through it on my own!

I also added a **View All Orders** page with the ability to sort by need-by date. It's not strictly necessary for the scheduling functionality, but I figured real users would probably appreciate having an overview of their orders and being able to sort them.

For a real production environment, I imagine the scheduler would probably be more time-based as well, for example scheduling production for a specific day or week. That could be a good direction for a v2.

I added seeders for the products and customers to make testing easier.

## Getting Started

When you first run the app, use the following commands:

### Install PHP dependencies

composer install

### Start the application

composer run dev

### Create the database and seed it

php artisan migrate:fresh --seed

