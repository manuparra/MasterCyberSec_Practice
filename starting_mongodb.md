# Starting with MongoDB (Primeros pasos con MongoDB)

![LogoHeadMasterCES](https://sites.google.com/site/manuparra/home/logo_master_ciber.png)


[UGR](http://www.ugr.es) | [DICITS](http://dicits.ugr.es) | [SCI2S](http://sci2s.ugr.es) | [DECSAI](http://decsai.ugr.es)

Manuel J. Parra Royón (manuelparra@decsai.ugr.es) & José. M. Benítez Sánchez (j.m.benitez@decsai.ugr.es)



Table of Contents
=================



# Using MongoDB, why? and where?

MongoDB main features:

* Document Oriented Storage − Data is stored in the form of JSON style documents.
* Index on any attribute
* Replication and high availability
* Auto-sharding
* Rich queries

Using Mongo:

* Big Data
* Content Management and Delivery
* Mobile and Social Infrastructure
* User Data Management
* Data Hub


## Documents datatypes

* String − This is the most commonly used datatype to store the data.
* Integer − This type is used to store a numerical value.
* Boolean − This type is used to store a boolean (true/ false) value.
* Double − This type is used to store floating point values.
* Min/ Max keys − This type is used to compare a value against the lowest and highest BSON elements.
* Arrays − This type is used to store arrays or list or multiple values into one key.
* Timestamp − ctimestamp. This can be handy for recording when a document has been modified or added.
* Object − This datatype is used for embedded documents.
* Null − This type is used to store a Null value.
* Symbol − This datatype is used identically to a string; however, it's generally reserved for languages that use a specific symbol type.
* Date − This datatype is used to store the current date or time in UNIX time format. You can specify your own date time by creating object of Date and passing day, month, year into it.
* Object ID − This datatype is used to store the document’s ID.
* Binary data − This datatype is used to store binary data.
* Code − This datatype is used to store JavaScript code into the document.
* Regular expression − This datatype is used to store regular expression.


## Documents instead row/cols

MongoDB stores data records as BSON documents. 

BSON is a binary representation of JSON documents, it contains more data types than JSON.

![bsonchema](https://docs.mongodb.com/manual/_images/crud-annotated-document.png)

MongoDB documents are composed of field-and-value pairs and have the following structure:

```
{
   field1: value1,
   field2: value2,
   field3: value3,
   ...
   fieldN: valueN
}
```

Example of document:

```
var mydoc = {
               _id: ObjectId("5099803df3f4948bd2f98391"),
               name: 
               		{ 
               		 first: "Alan", 
               		 last: "Turing" 
               		},
               birth: new Date('Jun 23, 1912'),
               death: new Date('Jun 07, 1954'),
               contribs: [ 
               				"Turing machine", 
               				"Turing test", 
               				"Turingery" ],
               views : NumberLong(1250000)
            }
```

To specify or access a field of an document: use dot notation

```
mydoc.name.first
```

Documents allow embedded documents embedded documents embedded documen ...:

```
{
   ...
   name: { first: "Alan", last: "Turing" },
   contact: { 
   			phone: { 
   					model: { 
   						brand: "LG", 
   						screen: {'maxres': "1200x800"} 
   					},
   					type: "cell", 
   					number: "111-222-3333" } },
   ...
}
```

The maximum BSON document size is **16 megabytes!**.




# Starting with MongoDB

Log and connect to our system with:

```
ssh manuparra@.........es
```

First of all, check that you have access to the mongo tools system, try this command:

``mongo + tab``

it will show:

```
mongo         mongodump     mongoexport   mongofiles    
mongoimport   mongooplog    mongoperf     mongorestore  mongostat     mongotop 
```

## Connecting with MongoDB Service:

The default port for mongodb and mongos instances is 27017. 
You can change this port with port or --port when connect.

Write:

```
mongo
```

It will connect with defaults parameters: ``localhost`` , port: ``27017`` and database: ``test``

```
MongoDB shell version: 2.6.12
connecting to: test
>
```

Exit using ``CTRL+C`` or ``exit``

Each user have an account on mongodb service. To connect:

```
mongo localhost:27017/manuparra -p 
```

It will us ``password``. 

```
mongo localhost:27017/manuparra -p mipasss 
```


MongoDB service is running locally in Docker systems, so, if you connect from docker containers or Virtual Machines, you must to use local docker system IP:

```
mongo 192.168.10.30:27017/manuparra -p mipasss 
```

## Selecting/Creating/Deleting DataBase

The command will create a new database if it doesn't exist, otherwise it will return the existing database.

``
> use manuparra:
``

Now you are using ``manuparra`` database.

If you want to kwnow what database are you using:

``
> db
``

The ```command db.dropDatabase()`` is used to drop a existing database.

DO NOT USE THIS COMMAND, WARNING:

``db.dropDatabase()``

To kwnow the size of databases:

```
show dbs
```

## Creating a Collection

Basic syntax of createCollection() command is as follows:

```
db.createCollection(name, options)
```

where ``options`` is Optional and specify options about memory size and indexing.

Remember that firstly mongodb needs to kwnow what is the Database where it will create the Collection. Use ``show dbs`` and then ``use <your database>``.

``
use manuparra;
``

And then create the collection:

```
db.createCollection("MyFirstCollection")
```

When created check:

```
show collections
```

In MongoDB, you don't need to create the collection. MongoDB creates collection automatically, when you insert some document:

```
db.MySecondCollection.insert({"name" : "Manuel Parra"})
```

You have new collections created:

```
show collections
```

## Delete collections

To remove a collection from the database:

```
db.MySecondCollection.drop();

```


## Working with documents on collections

To insert data into MongoDB collection, you need to use MongoDB's ``insert()`` or ``save()`` method.

```
> db.MyFirstCollection.insert(<document>);
```

Example of document: place 

```
{    
     "bounding_box":
    {
        "coordinates":
        [[
                [-77.119759,38.791645],
                [-76.909393,38.791645],
                [-76.909393,38.995548],
                [-77.119759,38.995548]
        ]],
        "type":"Polygon"
    },
     "country":"United States",
     "country_code":"US",
     "full_name":"Washington, DC",
     "id":"01fbe706f872cb32",
     "name":"Washington",
     "place_type":"city",
     "url": "http://api.twitter.com/1/geo/id/01fbe706f872cb32.json"
}
```

To insert:

```
db.MyFirstCollection.insert(
{    
     "bounding_box":
      {
        "coordinates":
        [[
                [-77.119759,38.791645],
                [-76.909393,38.791645],
                [-76.909393,38.995548],
                [-77.119759,38.995548]
        ]],
        "type":"Polygon"
      },
     "country":"United States",
     "country_code":"US",
     "full_name":"Washington, DC",
     "id":"01fbe706f872cb32",
     "name":"Washington",
     "place_type":"city",
     "url": "http://api.twitter.com/1/geo/id/01fbe706f872cb32.json"
}
);
```

Check if document is stored:

```
> db.MyFirstCollection.find();
```

In the inserted document, if we don't specify the ``_id`` parameter, then MongoDB assigns a unique ObjectId for this document.
You can override value `_id`, using your own ``_id``.

Two methods to save/insert:

```
db.MyFirstCollection.save({username:"myuser",password:"mypasswd"})
db.MyFirstCollection.insert({username:"myuser",password:"mypasswd"})
```

Differences:

>If a document does not exist with the specified ``_id`` value, the ``save()`` method performs an insert with the specified fields in the document.

>If a document exists with the specified ``_id` value, the ``save()`` method performs an update, replacing all field in the existing record with the fields from the document.





