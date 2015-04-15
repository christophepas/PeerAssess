## What is the project?

Daily: a website that lists **the trending news topics** based on Twitter activity.

![ok](http://peerassess.co/img/daily.png)

## What is the subject of the test?

In this test, **you’ll be building its foundation: a simple PHP application that fetches Tweets related to different types of news**. It will be the base of a website that will allow busy people to know what’s going on within minutes, not hours, by understanding what topics are trending in the Tweets. They can than choose to read more if a specific topic interests them particularly.

When this test ends, we’ll select the test result we think is the best, based on feedback from other candidates that take this test. We’ll then work to get it up and running on the Internet so it can be used by real users.

## How hard is it to use the Twitter API?

It’s actually easier than one might think. Your tests will be using the PHP programming language. There is a great library that helps you connect to the Twitter API: TwitterOAuth.

The base file includes some boilerplate code to help you get started. Make sure to read the code comments too. They include useful information.

## What features am I to implement?

### Fetch Tweets

Build a controller that fetches Tweets for the following categories: French News, US Sports and French Tech News. The categories are determined by the Twitter accounts they fetch data from. For instance:

* French News is made of Tweets from BFM and Le Figaro,
* French Tech is made of Tweets from CNet and FrenchWeb,
* US Sports is made of Tweets from ESPN and CBS Sports.

You will find corresponding code examples to help you get started in the base file.

### Saving Tweets in the database

Build a controller that saves the Tweets in a SQlite database. You will need to determine the right database schema and understand how the database library, namely Doctrine DBAL, works. You will find corresponding code examples to help you get started in the base file.

When you are corrected, it must be possible to call the `GET /db-setup` route to build the initial database schema. Search for `/db-setup` in the index.php file for an example.

## Is there enough time to complete both tasks of this test?

Probably not. Do the best you can with the time you have. Keep in mind that this test is made to help you learn new things. If you feel like you have learned something at the end, that’s awesome !
What do I do with the base “.zip” file?

Instructions can be found in its README.md file.

## How can I give feedback on the test?

Feel free to send us an email at [christophe.pasquier@peerassess.co](mailto:christophe.pasquier@peerassess.co). We’d love to know what you thought of the tasks, the difficulty of the test and the Peerassess platform.
